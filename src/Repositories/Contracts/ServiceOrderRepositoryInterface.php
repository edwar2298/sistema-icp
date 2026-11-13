<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface ServiceOrderRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByNumber(string $number): ?array;
    public function getParts(int $orderId): array;
    public function getStatusHistory(int $orderId): array;
    public function getAll(array $filters = [], int $limit = 100): array;
    public function create(array $data, array $parts = []): int;
    public function update(int $id, array $data): bool;
    public function updateStatus(int $id, int $userId, string $newStatus, ?string $comments = null): bool;
    public function addParts(int $orderId, array $parts): bool;
    public function countByStatus(): array;
}

