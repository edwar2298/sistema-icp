<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByCode(string $code): ?array;
    public function getAll(string $search = '', ?int $categoryId = null, bool $onlyActive = true): array;
    public function getLowStock(): array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function updateStock(int $id, int $newStock): bool;
    public function delete(int $id): bool;
    public function count(): int;
}

