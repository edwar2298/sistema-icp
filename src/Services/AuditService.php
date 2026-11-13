<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Request;
use App\Repositories\Contracts\AuditLogRepositoryInterface;

class AuditService
{
    private AuditLogRepositoryInterface $auditRepository;

    public function __construct(AuditLogRepositoryInterface $auditRepository)
    {
        $this->auditRepository = $auditRepository;
    }

    public function log(
        string $action,
        string $entityTable,
        ?string $entityId,
        ?array $details = null,
        ?Request $request = null,
        ?int $userId = null
    ): int {
        $user = $userId ?? Auth::id();
        $ip = $request ? $request->ip() : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = $request ? $request->userAgent() : ($_SERVER['HTTP_USER_AGENT'] ?? 'CLI/System');

        return $this->auditRepository->log(
            $user,
            $action,
            $entityTable,
            $entityId,
            $ip,
            $userAgent,
            $details
        );
    }
}

