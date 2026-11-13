<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface ClientRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByDocument(string $documentNumber): ?array;
    public function getAll(string $search = '', int $limit = 100): array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}

