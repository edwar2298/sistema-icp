<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\QuotationRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use InvalidArgumentException;

class QuotationService
{
    private QuotationRepositoryInterface $quotationRepository;
    private ProductRepositoryInterface $productRepository;
    private SettingRepositoryInterface $settingRepository;
    private AuditService $auditService;

    public function __construct(
        QuotationRepositoryInterface $quotationRepository,
        ProductRepositoryInterface $productRepository,
        SettingRepositoryInterface $settingRepository,
        AuditService $auditService
    ) {
        $this->quotationRepository = $quotationRepository;
        $this->productRepository = $productRepository;
        $this->settingRepository = $settingRepository;
        $this->auditService = $auditService;
    }

    public function getAll(string $search = '', ?string $status = null): array
    {
        return $this->quotationRepository->getAll($search, $status);
    }

    public function find(int $id): ?array
    {
        $quote = $this->quotationRepository->findById($id);
        if ($quote) {
            $quote['items'] = $this->quotationRepository->getItems($id);
        }
        return $quote;
    }

    public function create(array $data, array $rawItems, Request $request): int
    {
        if (empty($data['client_id'])) {
            throw new InvalidArgumentException("Debe seleccionar un cliente.");
        }

        if (empty($rawItems)) {
            throw new InvalidArgumentException("La cotización debe contener al menos un producto.");
        }

        $igvRate = (float)($this->settingRepository->get('company_igv_rate', '18.00')) / 100.0;

        $items = [];
        $subtotal = 0.0;
        $totalDiscount = (float)($data['discount'] ?? 0.0);

        foreach ($rawItems as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $quantity = (int)($item['quantity'] ?? 1);
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $discount = (float)($item['discount'] ?? 0);

            if ($quantity <= 0 || $unitPrice < 0) {
                continue;
            }

            $product = $this->productRepository->findById($productId);
            if (!$product) {
                throw new InvalidArgumentException("Producto ID {$productId} no encontrado.");
            }

            $itemSubtotal = ($quantity * $unitPrice) - $discount;
            $items[] = [
                'product_id' => $productId,
                'product_name' => $product['name'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'subtotal' => $itemSubtotal,
                'total' => $itemSubtotal,
            ];

            $subtotal += $itemSubtotal;
        }

        if (empty($items)) {
            throw new InvalidArgumentException("No se enviaron ítems válidos para la cotización.");
        }

        $taxableAmount = max(0, $subtotal - $totalDiscount);
        $igv = round($taxableAmount * $igvRate, 2);
        $total = round($taxableAmount + $igv, 2);

        // Correlative series for quotes
        $corr = $this->settingRepository->getNextCorrelative('COTIZACION');
        $quotationNumber = sprintf("%s-%05d", $corr['series'], $corr['correlative']);

        return Database::transaction(function () use ($data, $quotationNumber, $subtotal, $totalDiscount, $igv, $total, $items, $request): int {
            $userId = Auth::id() ?? 1;

            $quotationId = $this->quotationRepository->create([
                'quotation_number' => $quotationNumber,
                'client_id' => (int)$data['client_id'],
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'igv' => $igv,
                'total' => $total,
                'status' => 'PENDIENTE',
                'valid_until' => !empty($data['valid_until']) ? $data['valid_until'] : date('Y-m-d', strtotime('+15 days')),
                'notes' => $data['notes'] ?? null,
            ], $items);

            $this->settingRepository->incrementCorrelative('COTIZACION');

            $this->auditService->log('CREACION', 'quotations', (string)$quotationId, [
                'quotation_number' => $quotationNumber,
                'client_id' => $data['client_id'],
                'total' => $total,
            ], $request);

            return $quotationId;
        });
    }

    public function updateStatus(int $id, string $status, Request $request): bool
    {
        $quote = $this->quotationRepository->findById($id);
        if (!$quote) {
            throw new InvalidArgumentException("Cotización no encontrada.");
        }

        $success = $this->quotationRepository->updateStatus($id, $status);

        if ($success) {
            $this->auditService->log('MODIFICACION', 'quotations', (string)$id, [
                'action' => 'Cambio de estado',
                'old_status' => $quote['status'],
                'new_status' => $status,
            ], $request);
        }

        return $success;
    }
}

