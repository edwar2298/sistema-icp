<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface SaleRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByVoucher(string $type, string $series, int $correlative): ?array;
    public function getItems(int $saleId): array;
    public function getAll(array $filters = [], int $limit = 100): array;
    public function create(array $data, array $items): int;
    public function cancel(int $id): bool;
    public function getStats(?string $startDate = null, ?string $endDate = null): array;
    public function getTopSellingProducts(int $limit = 5): array;
}

