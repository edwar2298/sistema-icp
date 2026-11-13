<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface QuotationRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByNumber(string $number): ?array;
    public function getItems(int $quotationId): array;
    public function getAll(string $search = '', ?string $status = null): array;
    public function create(array $data, array $items): int;
    public function updateStatus(int $id, string $status): bool;
    public function count(): int;
}

