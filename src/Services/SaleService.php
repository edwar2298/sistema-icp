<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\Contracts\KardexRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\QuotationRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use InvalidArgumentException;
use RuntimeException;

class SaleService
{
    private SaleRepositoryInterface $saleRepository;
    private ProductRepositoryInterface $productRepository;
    private KardexRepositoryInterface $kardexRepository;
    private QuotationRepositoryInterface $quotationRepository;
    private SettingRepositoryInterface $settingRepository;
    private AuditService $auditService;

    public function __construct(
        SaleRepositoryInterface $saleRepository,
        ProductRepositoryInterface $productRepository,
        KardexRepositoryInterface $kardexRepository,
        QuotationRepositoryInterface $quotationRepository,
        SettingRepositoryInterface $settingRepository,
        AuditService $auditService
    ) {
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->kardexRepository = $kardexRepository;
        $this->quotationRepository = $quotationRepository;
        $this->settingRepository = $settingRepository;
        $this->auditService = $auditService;
    }

    public function getAll(array $filters = [], int $limit = 100): array
    {
        return $this->saleRepository->getAll($filters, $limit);
    }

    public function find(int $id): ?array
    {
        $sale = $this->saleRepository->findById($id);
        if ($sale) {
            $sale['items'] = $this->saleRepository->getItems($id);
        }
        return $sale;
    }

    /**
     * Creates a new sale with strict ACID transaction and stock reduction.
     */
    public function create(array $data, array $rawItems, Request $request): int
    {
        if (empty($data['client_id'])) {
            throw new InvalidArgumentException("Debe seleccionar un cliente.");
        }

        if (empty($rawItems)) {
            throw new InvalidArgumentException("La venta debe contener al menos un producto.");
        }

        $voucherType = strtoupper($data['voucher_type'] ?? 'BOLETA');
        if (!in_array($voucherType, ['BOLETA', 'FACTURA', 'NOTA_VENTA'], true)) {
            throw new InvalidArgumentException("Tipo de comprobante inválido.");
        }

        $paymentCondition = strtoupper($data['payment_condition'] ?? 'EFECTIVO');
        if (!in_array($paymentCondition, ['EFECTIVO', 'TRANSFERENCIA', 'CREDITO'], true)) {
            throw new InvalidArgumentException("Condición de pago inválida.");
        }

        $igvRate = (float)($this->settingRepository->get('company_igv_rate', '18.00')) / 100.0;

        // Process and validate items & stock
        $items = [];
        $totalAmount = 0.0;

        foreach ($rawItems as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $quantity = (int)($item['quantity'] ?? 1);
            $unitPrice = (float)($item['unit_price'] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $product = $this->productRepository->findById($productId);
            if (!$product) {
                throw new InvalidArgumentException("Producto ID {$productId} no existe.");
            }

            if ((int)$product['stock'] < $quantity) {
                throw new RuntimeException("Stock insuficiente para '{$product['name']}'. Disponible: {$product['stock']}, Solicitado: {$quantity}.");
            }

            $lineTotal = round($quantity * $unitPrice, 2);
            $totalAmount += $lineTotal;

            $items[] = [
                'product_id' => $productId,
                'product_name' => $product['name'],
                'product_code' => $product['code'],
                'current_stock' => (int)$product['stock'],
                'purchase_price' => (float)$product['purchase_price'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $lineTotal,
                'total' => $lineTotal,
            ];
        }

        if (empty($items)) {
            throw new InvalidArgumentException("No se enviaron ítems válidos para la venta.");
        }

        // Calculation: In Peru, total includes IGV for retail or is separated as subtotal + IGV
        $subtotal = round($totalAmount / (1.0 + $igvRate), 2);
        $igv = round($totalAmount - $subtotal, 2);
        $total = $totalAmount;

        return Database::transaction(function () use (
            $data, $voucherType, $paymentCondition, $subtotal, $igv, $total, $items, $request
        ): int {
            // Get current series & correlative
            $corr = $this->settingRepository->getNextCorrelative($voucherType);
            $series = $corr['series'];
            $correlative = $corr['correlative'];
            $userId = Auth::id() ?? 1;

            $paymentStatus = ($paymentCondition === 'CREDITO') ? 'PENDIENTE' : 'PAGADO';

            $saleId = $this->saleRepository->create([
                'quotation_id' => !empty($data['quotation_id']) ? (int)$data['quotation_id'] : null,
                'client_id' => (int)$data['client_id'],
                'user_id' => $userId,
                'voucher_type' => $voucherType,
                'series' => $series,
                'correlative' => $correlative,
                'payment_condition' => $paymentCondition,
                'payment_status' => $paymentStatus,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'status' => 'EMITIDA',
                'notes' => $data['notes'] ?? null,
            ], $items);

            $voucherStr = sprintf("%s-%06d", $series, $correlative);

            // Discount stock and create Kardex entry for each product
            foreach ($items as $item) {
                $newStock = $item['current_stock'] - $item['quantity'];

                $this->productRepository->updateStock($item['product_id'], $newStock);

                $this->kardexRepository->record(
                    $item['product_id'],
                    $userId,
                    'VENTA',
                    'VENTA',
                    $voucherStr,
                    $item['quantity'],
                    $item['current_stock'],
                    $newStock,
                    $item['purchase_price'],
                    "Venta con comprobante {$voucherType} {$voucherStr}"
                );
            }

            // Increment correlative for this voucher type
            $this->settingRepository->incrementCorrelative($voucherType);

            // If generated from quotation, update quotation status to CONVERTIDA
            if (!empty($data['quotation_id'])) {
                $this->quotationRepository->updateStatus((int)$data['quotation_id'], 'CONVERTIDA');
            }

            // Audit
            $this->auditService->log('CREACION', 'sales', (string)$saleId, [
                'voucher' => $voucherStr,
                'type' => $voucherType,
                'client_id' => $data['client_id'],
                'total' => $total,
                'payment' => $paymentCondition,
                'quotation_id' => $data['quotation_id'] ?? null,
            ], $request);

            return $saleId;
        });
    }

    /**
     * Converts a quotation directly into a sale order without retyping.
     */
    public function convertQuotationToSale(int $quotationId, string $voucherType, string $paymentCondition, Request $request): int
    {
        $quote = $this->quotationRepository->findById($quotationId);
        if (!$quote) {
            throw new InvalidArgumentException("Cotización no encontrada.");
        }

        if ($quote['status'] === 'CONVERTIDA') {
            throw new RuntimeException("Esta cotización ya fue convertida previamente en una venta.");
        }

        $items = $this->quotationRepository->getItems($quotationId);
        if (empty($items)) {
            throw new RuntimeException("La cotización no posee ítems.");
        }

        $saleData = [
            'quotation_id' => $quotationId,
            'client_id' => $quote['client_id'],
            'voucher_type' => $voucherType,
            'payment_condition' => $paymentCondition,
            'notes' => "Generado automáticamente a partir de la Cotización {$quote['quotation_number']}. " . ($quote['notes'] ?? ''),
        ];

        return $this->create($saleData, $items, $request);
    }

    /**
     * Cancels a sale, reverts stock, and registers Kardex cancellation.
     */
    public function cancelSale(int $id, string $reason, Request $request): bool
    {
        $sale = $this->find($id);
        if (!$sale) {
            throw new InvalidArgumentException("Venta no encontrada.");
        }

        if ($sale['status'] === 'ANULADA') {
            throw new RuntimeException("Esta venta ya se encuentra anulada.");
        }

        return Database::transaction(function () use ($sale, $id, $reason, $request): bool {
            $userId = Auth::id() ?? 1;
            $voucherStr = sprintf("%s-%06d", $sale['series'], $sale['correlative']);

            // Revert stock for all items
            foreach ($sale['items'] as $item) {
                $product = $this->productRepository->findById((int)$item['product_id']);
                if ($product) {
                    $currentStock = (int)$product['stock'];
                    $restoredStock = $currentStock + (int)$item['quantity'];

                    $this->productRepository->updateStock((int)$item['product_id'], $restoredStock);

                    $this->kardexRepository->record(
                        (int)$item['product_id'],
                        $userId,
                        'ANULACION_VENTA',
                        'VENTA',
                        $voucherStr,
                        (int)$item['quantity'],
                        $currentStock,
                        $restoredStock,
                        (float)$product['purchase_price'],
                        "Anulación de comprobante {$sale['voucher_type']} {$voucherStr}. Motivo: {$reason}"
                    );
                }
            }

            $this->saleRepository->cancel($id);

            $this->auditService->log('ANULACION', 'sales', (string)$id, [
                'voucher' => $voucherStr,
                'reason' => $reason,
                'total' => $sale['total']
            ], $request);

            return true;
        });
    }
}

