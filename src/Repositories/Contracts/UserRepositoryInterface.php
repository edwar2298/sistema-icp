<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function getAll(): array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function updateFailedAttempts(int $id, int $attempts, ?string $lockoutUntil = null): bool;
    public function resetFailedAttempts(int $id): bool;
}

