<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\Contracts\KardexRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use InvalidArgumentException;
use RuntimeException;

class ServiceOrderService
{
    private ServiceOrderRepositoryInterface $orderRepository;
    private ProductRepositoryInterface $productRepository;
    private KardexRepositoryInterface $kardexRepository;
    private SettingRepositoryInterface $settingRepository;
    private AuditService $auditService;

    // Allowed state machine transitions
    private const ALLOWED_TRANSITIONS = [
        'REGISTRADA' => ['EN_PROCESO'],
        'EN_PROCESO' => ['EN_ESPERA_REPUESTO', 'ATENDIDA'],
        'EN_ESPERA_REPUESTO' => ['EN_PROCESO', 'ATENDIDA'],
        'ATENDIDA' => ['ENTREGADA', 'EN_PROCESO'],
        'ENTREGADA' => [], // Final state
    ];

    public function __construct(
        ServiceOrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        KardexRepositoryInterface $kardexRepository,
        SettingRepositoryInterface $settingRepository,
        AuditService $auditService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->kardexRepository = $kardexRepository;
        $this->settingRepository = $settingRepository;
        $this->auditService = $auditService;
    }

    public function getAll(array $filters = [], int $limit = 100): array
    {
        return $this->orderRepository->getAll($filters, $limit);
    }

    public function find(int $id): ?array
    {
        $order = $this->orderRepository->findById($id);
        if ($order) {
            $order['parts'] = $this->orderRepository->getParts($id);
            $order['history'] = $this->orderRepository->getStatusHistory($id);
        }
        return $order;
    }

    public function create(array $data, array $rawParts, Request $request): int
    {
        if (empty($data['client_id']) || empty($data['technician_id']) || empty($data['equipment_type']) || empty($data['problem_description'])) {
            throw new InvalidArgumentException("Cliente, técnico, tipo de equipo y descripción del problema son obligatorios.");
        }

        // Calculate hours and labor
        $hoursWorked = (float)($data['hours_worked'] ?? 0);
        $hourlyRate = (float)($data['hourly_rate'] ?? 0);
        $laborCost = round($hoursWorked * $hourlyRate, 2);

        // Process parts
        $partsCost = 0.0;
        $parts = [];

        foreach ($rawParts as $p) {
            $partName = trim($p['part_name'] ?? '');
            $qty = (int)($p['quantity'] ?? 1);
            $price = (float)($p['unit_price'] ?? 0);
            $productId = !empty($p['product_id']) ? (int)$p['product_id'] : null;

            if ($partName === '' || $qty <= 0) {
                continue;
            }

            $subtotal = round($qty * $price, 2);
            $partsCost += $subtotal;

            $parts[] = [
                'product_id' => $productId,
                'part_name' => $partName,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $subtotal,
            ];
        }

        $totalCost = round($laborCost + $partsCost, 2);

        // Correlative series for OST
        $corr = $this->settingRepository->getNextCorrelative('OST');
        $orderNumber = sprintf("%s-%05d", $corr['series'], $corr['correlative']);

        return Database::transaction(function () use (
            $data, $orderNumber, $hoursWorked, $hourlyRate, $laborCost, $partsCost, $totalCost, $parts, $request
        ): int {
            $userId = Auth::id() ?? 1;

            $orderId = $this->orderRepository->create([
                'order_number' => $orderNumber,
                'client_id' => (int)$data['client_id'],
                'technician_id' => (int)$data['technician_id'],
                'equipment_type' => $data['equipment_type'],
                'brand' => $data['brand'] ?? 'Genérico',
                'model' => $data['model'] ?? 'N/A',
                'serial_number' => $data['serial_number'] ?? null,
                'problem_description' => $data['problem_description'],
                'diagnostic' => $data['diagnostic'] ?? null,
                'work_performed' => $data['work_performed'] ?? null,
                'priority' => $data['priority'] ?? 'MEDIA',
                'status' => 'REGISTRADA',
                'hours_worked' => $hoursWorked,
                'hourly_rate' => $hourlyRate,
                'labor_cost' => $laborCost,
                'parts_cost' => $partsCost,
                'total_cost' => $totalCost,
                'customer_notes' => $data['customer_notes'] ?? null,
            ], $parts);

            // Deduct parts from inventory if selected from product catalog
            foreach ($parts as $part) {
                if ($part['product_id']) {
                    $product = $this->productRepository->findById($part['product_id']);
                    if ($product) {
                        $currentStock = (int)$product['stock'];
                        $newStock = max(0, $currentStock - $part['quantity']);

                        $this->productRepository->updateStock($part['product_id'], $newStock);

                        $this->kardexRepository->record(
                            $part['product_id'],
                            $userId,
                            'SERVICIO_TECNICO',
                            'SERVICIO',
                            $orderNumber,
                            $part['quantity'],
                            $currentStock,
                            $newStock,
                            (float)$product['purchase_price'],
                            "Repuesto utilizado en Orden de Servicio {$orderNumber}"
                        );
                    }
                }
            }

            $this->settingRepository->incrementCorrelative('OST');

            $this->auditService->log('CREACION', 'technical_service_orders', (string)$orderId, [
                'order_number' => $orderNumber,
                'client_id' => $data['client_id'],
                'equipment' => "{$data['equipment_type']} {$data['brand']} {$data['model']}",
                'total_cost' => $totalCost,
            ], $request);

            return $orderId;
        });
    }

    public function update(int $id, array $data, Request $request): bool
    {
        $order = $this->find($id);
        if (!$order) {
            throw new InvalidArgumentException("Orden de servicio no encontrada.");
        }

        $hoursWorked = (float)($data['hours_worked'] ?? $order['hours_worked']);
        $hourlyRate = (float)($data['hourly_rate'] ?? $order['hourly_rate']);
        $laborCost = round($hoursWorked * $hourlyRate, 2);
        $partsCost = (float)$order['parts_cost'];
        $totalCost = round($laborCost + $partsCost, 2);

        $updateData = array_merge($data, [
            'hours_worked' => $hoursWorked,
            'hourly_rate' => $hourlyRate,
            'labor_cost' => $laborCost,
            'parts_cost' => $partsCost,
            'total_cost' => $totalCost,
        ]);

        $success = $this->orderRepository->update($id, $updateData);

        if ($success) {
            $this->auditService->log('MODIFICACION', 'technical_service_orders', (string)$id, [
                'diagnostic' => $data['diagnostic'] ?? null,
                'work_performed' => $data['work_performed'] ?? null,
                'total_cost' => $totalCost,
            ], $request);
        }

        return $success;
    }

    /**
     * Changes order status enforcing state machine rules.
     */
    public function transitionStatus(int $id, string $targetStatus, ?string $comments, Request $request): bool
    {
        $order = $this->find($id);
        if (!$order) {
            throw new InvalidArgumentException("Orden de servicio no encontrada.");
        }

        $currentStatus = $order['status'];
        $allowedNext = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];

        if (!in_array($targetStatus, $allowedNext, true)) {
            $allowedList = empty($allowedNext) ? 'Ninguno (estado final)' : implode(', ', $allowedNext);
            throw new RuntimeException("Transición inválida: no es posible pasar de '{$currentStatus}' a '{$targetStatus}'. Estados permitidos: {$allowedList}.");
        }

        $userId = Auth::id() ?? 1;
        $success = $this->orderRepository->updateStatus($id, $userId, $targetStatus, $comments);

        if ($success) {
            $this->auditService->log('CAMBIO_ESTADO_OST', 'technical_service_orders', (string)$id, [
                'order_number' => $order['order_number'],
                'from' => $currentStatus,
                'to' => $targetStatus,
                'comments' => $comments,
            ], $request);
        }

        return $success;
    }

    public function countByStatus(): array
    {
        return $this->orderRepository->countByStatus();
    }
}

