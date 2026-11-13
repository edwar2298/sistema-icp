<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Request;
use App\Repositories\Contracts\ClientRepositoryInterface;
use InvalidArgumentException;

class ClientService
{
    private ClientRepositoryInterface $clientRepository;
    private AuditService $auditService;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        AuditService $auditService
    ) {
        $this->clientRepository = $clientRepository;
        $this->auditService = $auditService;
    }

    public function getAll(string $search = '', int $limit = 100): array
    {
        return $this->clientRepository->getAll($search, $limit);
    }

    public function find(int $id): ?array
    {
        return $this->clientRepository->findById($id);
    }

    public function findByDocument(string $doc): ?array
    {
        return $this->clientRepository->findByDocument($doc);
    }

    public function create(array $data, Request $request): int
    {
        $this->validateData($data);

        $existing = $this->clientRepository->findByDocument($data['document_number']);
        if ($existing) {
            throw new InvalidArgumentException("Ya existe un cliente registrado con el número de documento '{$data['document_number']}'.");
        }

        $id = $this->clientRepository->create($data);

        $this->auditService->log('CREACION', 'clients', (string)$id, [
            'document_number' => $data['document_number'],
            'business_name' => $data['business_name'],
        ], $request);

        return $id;
    }

    public function update(int $id, array $data, Request $request): bool
    {
        $current = $this->clientRepository->findById($id);
        if (!$current) {
            throw new InvalidArgumentException("Cliente no encontrado.");
        }

        $this->validateData($data);

        // Check if another client has the same document number
        $existing = $this->clientRepository->findByDocument($data['document_number']);
        if ($existing && (int)$existing['id'] !== $id) {
            throw new InvalidArgumentException("El documento '{$data['document_number']}' ya pertenece a otro cliente.");
        }

        $success = $this->clientRepository->update($id, $data);

        if ($success) {
            $this->auditService->log('MODIFICACION', 'clients', (string)$id, [
                'old' => $current,
                'new' => $data,
            ], $request);
        }

        return $success;
    }

    public function delete(int $id, Request $request): bool
    {
        $current = $this->clientRepository->findById($id);
        if (!$current) {
            throw new InvalidArgumentException("Cliente no encontrado.");
        }

        $success = $this->clientRepository->delete($id);

        if ($success) {
            $this->auditService->log('ANULACION', 'clients', (string)$id, [
                'business_name' => $current['business_name'],
                'document_number' => $current['document_number'],
            ], $request);
        }

        return $success;
    }

    private function validateData(array $data): void
    {
        if (empty($data['document_number']) || empty($data['business_name'])) {
            throw new InvalidArgumentException("El documento y la razón social/nombre son obligatorios.");
        }

        $type = $data['document_type'] ?? 'RUC';
        $doc = trim($data['document_number']);

        if ($type === 'RUC' && (!ctype_digit($doc) || strlen($doc) !== 11)) {
            throw new InvalidArgumentException("El RUC debe tener exactamente 11 dígitos numéricos.");
        }

        if ($type === 'DNI' && (!ctype_digit($doc) || strlen($doc) !== 8)) {
            throw new InvalidArgumentException("El DNI debe tener exactamente 8 dígitos numéricos.");
        }
    }
}

