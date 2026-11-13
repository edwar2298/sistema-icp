<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\KardexRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use InvalidArgumentException;

class InventoryService
{
    private ProductRepositoryInterface $productRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private KardexRepositoryInterface $kardexRepository;
    private AuditService $auditService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        KardexRepositoryInterface $kardexRepository,
        AuditService $auditService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->kardexRepository = $kardexRepository;
        $this->auditService = $auditService;
    }

    public function getAllProducts(string $search = '', ?int $categoryId = null): array
    {
        return $this->productRepository->getAll($search, $categoryId);
    }

    public function getLowStockProducts(): array
    {
        return $this->productRepository->getLowStock();
    }

    public function findProduct(int $id): ?array
    {
        return $this->productRepository->findById($id);
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->getAll();
    }

    public function createProduct(array $data, Request $request): int
    {
        if (empty($data['code']) || empty($data['name']) || empty($data['category_id'])) {
            throw new InvalidArgumentException("El código, nombre y categoría del producto son obligatorios.");
        }

        $existing = $this->productRepository->findByCode($data['code']);
        if ($existing) {
            throw new InvalidArgumentException("El código '{$data['code']}' ya está registrado en otro producto.");
        }

        $initialStock = (int)($data['stock'] ?? 0);
        $purchasePrice = (float)($data['purchase_price'] ?? 0);

        return Database::transaction(function () use ($data, $initialStock, $purchasePrice, $request): int {
            $productId = $this->productRepository->create($data);

            if ($initialStock > 0) {
                $userId = Auth::id() ?? 1;
                $this->kardexRepository->record(
                    $productId,
                    $userId,
                    'INGRESO',
                    'INICIAL',
                    "PROD-{$productId}",
                    $initialStock,
                    0,
                    $initialStock,
                    $purchasePrice,
                    'Stock inicial al registrar el producto'
                );
            }

            $this->auditService->log('CREACION', 'products', (string)$productId, [
                'code' => $data['code'],
                'name' => $data['name'],
                'initial_stock' => $initialStock
            ], $request);

            return $productId;
        });
    }

    public function updateProduct(int $id, array $data, Request $request): bool
    {
        $current = $this->productRepository->findById($id);
        if (!$current) {
            throw new InvalidArgumentException("Producto no encontrado.");
        }

        $existing = $this->productRepository->findByCode($data['code']);
        if ($existing && (int)$existing['id'] !== $id) {
            throw new InvalidArgumentException("El código '{$data['code']}' ya está registrado en otro producto.");
        }

        $success = $this->productRepository->update($id, $data);

        if ($success) {
            $this->auditService->log('MODIFICACION', 'products', (string)$id, [
                'old' => $current,
                'new' => $data,
            ], $request);
        }

        return $success;
    }

    public function adjustStock(int $productId, int $adjustmentQuantity, string $movementType, string $reason, Request $request): bool
    {
        if ($adjustmentQuantity <= 0) {
            throw new InvalidArgumentException("La cantidad de ajuste debe ser un entero positivo.");
        }

        if (empty($reason)) {
            throw new InvalidArgumentException("El motivo del ajuste es obligatorio.");
        }

        return Database::transaction(function () use ($productId, $adjustmentQuantity, $movementType, $reason, $request): bool {
            $product = $this->productRepository->findById($productId);
            if (!$product) {
                throw new InvalidArgumentException("Producto no encontrado.");
            }

            $currentStock = (int)$product['stock'];
            $newStock = $currentStock;

            if ($movementType === 'INGRESO' || $movementType === 'AJUSTE_POSITIVO') {
                $newStock = $currentStock + $adjustmentQuantity;
            } elseif ($movementType === 'SALIDA' || $movementType === 'AJUSTE_NEGATIVO') {
                if ($currentStock < $adjustmentQuantity) {
                    throw new InvalidArgumentException("Stock insuficiente. Stock actual: {$currentStock}, solicitado: {$adjustmentQuantity}.");
                }
                $newStock = $currentStock - $adjustmentQuantity;
            } else {
                throw new InvalidArgumentException("Tipo de movimiento de ajuste no válido.");
            }

            $userId = Auth::id() ?? 1;

            $this->productRepository->updateStock($productId, $newStock);

            $this->kardexRepository->record(
                $productId,
                $userId,
                $movementType,
                'AJUSTE_MANUAL',
                "AJUSTE-" . date('YmdHis'),
                $adjustmentQuantity,
                $currentStock,
                $newStock,
                (float)$product['purchase_price'],
                $reason
            );

            $this->auditService->log('MODIFICACION', 'kardex', (string)$productId, [
                'product_code' => $product['code'],
                'movement' => $movementType,
                'quantity' => $adjustmentQuantity,
                'prev_stock' => $currentStock,
                'new_stock' => $newStock,
                'reason' => $reason
            ], $request);

            return true;
        });
    }

    public function getKardex(int $productId): array
    {
        return $this->kardexRepository->getByProduct($productId);
    }

    public function getAllKardex(array $filters = []): array
    {
        return $this->kardexRepository->getAll($filters);
    }
}

