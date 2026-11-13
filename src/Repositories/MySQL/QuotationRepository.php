<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\QuotationRepositoryInterface;
use PDO;

class QuotationRepository extends BaseRepository implements QuotationRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT q.*, c.business_name as client_name, c.document_type as client_doc_type, 
                   c.document_number as client_doc_number, c.address as client_address,
                   c.phone as client_phone, c.email as client_email,
                   u.name as user_name
            FROM quotations q
            INNER JOIN clients c ON q.client_id = c.id
            INNER JOIN users u ON q.user_id = u.id
            WHERE q.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByNumber(string $number): ?array
    {
        $stmt = $this->db->prepare("
            SELECT q.*, c.business_name as client_name, c.document_number as client_doc_number, u.name as user_name
            FROM quotations q
            INNER JOIN clients c ON q.client_id = c.id
            INNER JOIN users u ON q.user_id = u.id
            WHERE q.quotation_number = :number LIMIT 1
        ");
        $stmt->execute(['number' => trim($number)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getItems(int $quotationId): array
    {
        $stmt = $this->db->prepare("
            SELECT qi.*, p.code as product_code, p.stock as current_stock 
            FROM quotation_items qi
            LEFT JOIN products p ON qi.product_id = p.id
            WHERE qi.quotation_id = :id
            ORDER BY qi.id ASC
        ");
        $stmt->execute(['id' => $quotationId]);
        return $stmt->fetchAll();
    }

    public function getAll(string $search = '', ?string $status = null): array
    {
        $sql = "
            SELECT q.*, c.business_name as client_name, c.document_number as client_doc_number, u.name as user_name
            FROM quotations q
            INNER JOIN clients c ON q.client_id = c.id
            INNER JOIN users u ON q.user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND q.status = :status";
            $params['status'] = $status;
        }

        if ($search !== '') {
            $sql .= " AND (q.quotation_number LIKE :search_number OR c.business_name LIKE :search_name OR c.document_number LIKE :search_doc)";
            $searchTerm = "%{$search}%";
            $params['search_number'] = $searchTerm;
            $params['search_name'] = $searchTerm;
            $params['search_doc'] = $searchTerm;
        }

        $sql .= " ORDER BY q.id DESC LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data, array $items): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO quotations (
                quotation_number, client_id, user_id, subtotal, discount, igv, total, status, valid_until, notes
            ) VALUES (
                :quotation_number, :client_id, :user_id, :subtotal, :discount, :igv, :total, :status, :valid_until, :notes
            )
        ");
        $stmt->execute([
            'quotation_number' => $data['quotation_number'],
            'client_id' => (int)$data['client_id'],
            'user_id' => (int)$data['user_id'],
            'subtotal' => (float)$data['subtotal'],
            'discount' => (float)($data['discount'] ?? 0),
            'igv' => (float)$data['igv'],
            'total' => (float)$data['total'],
            'status' => $data['status'] ?? 'PENDIENTE',
            'valid_until' => $data['valid_until'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        $quotationId = (int)$this->db->lastInsertId();

        $stmtItem = $this->db->prepare("
            INSERT INTO quotation_items (
                quotation_id, product_id, product_name, quantity, unit_price, discount, subtotal, total
            ) VALUES (
                :quotation_id, :product_id, :product_name, :quantity, :unit_price, :discount, :subtotal, :total
            )
        ");

        foreach ($items as $item) {
            $stmtItem->execute([
                'quotation_id' => $quotationId,
                'product_id' => (int)$item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => (int)$item['quantity'],
                'unit_price' => (float)$item['unit_price'],
                'discount' => (float)($item['discount'] ?? 0),
                'subtotal' => (float)$item['subtotal'],
                'total' => (float)$item['total'],
            ]);
        }

        return $quotationId;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE quotations SET status = :status WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM quotations");
        return (int)$stmt->fetchColumn();
    }
}

