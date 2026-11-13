<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class AuditController extends Controller
{
    private AuditLogRepositoryInterface $auditRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        AuditLogRepositoryInterface $auditRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->auditRepository = $auditRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request): Response
    {
        $filters = [
            'user_id' => $request->get('user_id'),
            'action' => $request->get('action'),
            'entity_table' => $request->get('entity_table'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ];

        $logs = !empty(array_filter($filters))
            ? $this->auditRepository->filter($filters)
            : $this->auditRepository->getLatest(150);

        $users = $this->userRepository->getAll();

        return $this->render('audit.index', [
            'title' => 'Bitácora de Auditoría del Sistema',
            'logs' => $logs,
            'users' => $users,
            'filters' => $filters,
        ]);
    }
}

