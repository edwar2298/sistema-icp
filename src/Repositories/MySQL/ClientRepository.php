<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\ClientRepositoryInterface;
use PDO;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $client = $stmt->fetch();
        return $client ?: null;
    }

    public function findByDocument(string $documentNumber): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE document_number = :doc LIMIT 1");
        $stmt->execute(['doc' => trim($documentNumber)]);
        $client = $stmt->fetch();
        return $client ?: null;
    }

    public function getAll(string $search = '', int $limit = 100): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare("
                SELECT * FROM clients 
                WHERE business_name LIKE :search_name 
                   OR document_number LIKE :search_doc 
                   OR phone LIKE :search_phone
                ORDER BY business_name ASC 
                LIMIT :limit
            ");
            $searchTerm = "%{$search}%";
            $stmt->bindValue(':search_name', $searchTerm);
            $stmt->bindValue(':search_doc', $searchTerm);
            $stmt->bindValue(':search_phone', $searchTerm);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare("SELECT * FROM clients ORDER BY business_name ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO clients (document_type, document_number, business_name, address, phone, email, status)
            VALUES (:document_type, :document_number, :business_name, :address, :phone, :email, :status)
        ");
        $stmt->execute([
            'document_type' => $data['document_type'] ?? 'RUC',
            'document_number' => trim($data['document_number']),
            'business_name' => trim($data['business_name']),
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => $data['status'] ?? 'Activo',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE clients 
            SET document_type = :document_type,
                document_number = :document_number,
                business_name = :business_name,
                address = :address,
                phone = :phone,
                email = :email,
                status = :status
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'document_type' => $data['document_type'],
            'document_number' => trim($data['document_number']),
            'business_name' => trim($data['business_name']),
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => $data['status'] ?? 'Activo',
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE clients SET status = 'Inactivo' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM clients WHERE status = 'Activo'");
        return (int)$stmt->fetchColumn();
    }
}

