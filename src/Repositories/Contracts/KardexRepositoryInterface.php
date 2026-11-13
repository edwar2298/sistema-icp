<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface KardexRepositoryInterface
{
    public function record(
        int $productId,
        int $userId,
        string $movementType,
        string $referenceType,
        ?string $referenceId,
        int $quantity,
        int $previousStock,
        int $newStock,
        float $unitCost,
        string $reason
    ): int;

    public function getByProduct(int $productId, int $limit = 100): array;
    public function getAll(array $filters = [], int $limit = 200): array;
}

