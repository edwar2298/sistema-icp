<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface AuditLogRepositoryInterface
{
    public function log(
        ?int $userId,
        string $action,
        string $entityTable,
        ?string $entityId,
        string $ipAddress,
        ?string $userAgent,
        ?array $details = null
    ): int;

    public function getLatest(int $limit = 100, int $offset = 0): array;
    public function filter(array $filters): array;
    public function count(): int;
}

