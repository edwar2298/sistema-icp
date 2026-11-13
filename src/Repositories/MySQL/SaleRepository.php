<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\SaleRepositoryInterface;
use PDO;

class SaleRepository extends BaseRepository implements SaleRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, 
                   c.business_name as client_name, c.document_type as client_doc_type, 
                   c.document_number as client_doc_number, c.address as client_address,
                   c.phone as client_phone, c.email as client_email,
                   u.name as user_name,
                   q.quotation_number
            FROM sales s
            INNER JOIN clients c ON s.client_id = c.id
            INNER JOIN users u ON s.user_id = u.id
            LEFT JOIN quotations q ON s.quotation_id = q.id
            WHERE s.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByVoucher(string $type, string $series, int $correlative): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, c.business_name as client_name, c.document_number as client_doc_number, u.name as user_name
            FROM sales s
            INNER JOIN clients c ON s.client_id = c.id
            INNER JOIN users u ON s.user_id = u.id
            WHERE s.voucher_type = :type AND s.series = :series AND s.correlative = :correlative
            LIMIT 1
        ");
        $stmt->execute([
            'type' => $type,
            'series' => $series,
            'correlative' => $correlative,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getItems(int $saleId): array
    {
        $stmt = $this->db->prepare("
            SELECT si.*, p.code as product_code 
            FROM sale_items si
            LEFT JOIN products p ON si.product_id = p.id
            WHERE si.sale_id = :id
            ORDER BY si.id ASC
        ");
        $stmt->execute(['id' => $saleId]);
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = [], int $limit = 100): array
    {
        $sql = "
            SELECT s.*, 
                   c.business_name as client_name, c.document_number as client_doc_number,
                   u.name as user_name
            FROM sales s
            INNER JOIN clients c ON s.client_id = c.id
            INNER JOIN users u ON s.user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['voucher_type'])) {
            $sql .= " AND s.voucher_type = :voucher_type";
            $params['voucher_type'] = $filters['voucher_type'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['payment_condition'])) {
            $sql .= " AND s.payment_condition = :payment_condition";
            $params['payment_condition'] = $filters['payment_condition'];
        }
        if (!empty($filters['client_id'])) {
            $sql .= " AND s.client_id = :client_id";
            $params['client_id'] = (int)$filters['client_id'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(s.created_at) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(s.created_at) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (c.business_name LIKE :search_name OR c.document_number LIKE :search_doc OR CONCAT(s.series, '-', LPAD(s.correlative, 6, '0')) LIKE :search_correlative)";
            $searchTerm = "%{$filters['search']}%";
            $params['search_name'] = $searchTerm;
            $params['search_doc'] = $searchTerm;
            $params['search_correlative'] = $searchTerm;
        }

        $sql .= " ORDER BY s.id DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":{$k}", $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data, array $items): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO sales (
                quotation_id, client_id, user_id, voucher_type, series, correlative,
                payment_condition, payment_status, subtotal, igv, total, status, notes
            ) VALUES (
                :quotation_id, :client_id, :user_id, :voucher_type, :series, :correlative,
                :payment_condition, :payment_status, :subtotal, :igv, :total, :status, :notes
            )
        ");
        $stmt->execute([
            'quotation_id' => $data['quotation_id'] ?? null,
            'client_id' => (int)$data['client_id'],
            'user_id' => (int)$data['user_id'],
            'voucher_type' => $data['voucher_type'],
            'series' => $data['series'],
            'correlative' => (int)$data['correlative'],
            'payment_condition' => $data['payment_condition'] ?? 'EFECTIVO',
            'payment_status' => $data['payment_status'] ?? 'PAGADO',
            'subtotal' => (float)$data['subtotal'],
            'igv' => (float)$data['igv'],
            'total' => (float)$data['total'],
            'status' => $data['status'] ?? 'EMITIDA',
            'notes' => $data['notes'] ?? null,
        ]);
        $saleId = (int)$this->db->lastInsertId();

        $stmtItem = $this->db->prepare("
            INSERT INTO sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal, total)
            VALUES (:sale_id, :product_id, :product_name, :quantity, :unit_price, :subtotal, :total)
        ");

        foreach ($items as $item) {
            $stmtItem->execute([
                'sale_id' => $saleId,
                'product_id' => (int)$item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => (int)$item['quantity'],
                'unit_price' => (float)$item['unit_price'],
                'subtotal' => (float)$item['subtotal'],
                'total' => (float)$item['total'],
            ]);
        }

        return $saleId;
    }

    public function cancel(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE sales SET status = 'ANULADA' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getStats(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "
            SELECT 
                COALESCE(SUM(total), 0) as total_sales,
                COUNT(id) as total_vouchers,
                COALESCE(AVG(total), 0) as average_ticket,
                COALESCE(SUM(CASE WHEN payment_condition = 'EFECTIVO' THEN total ELSE 0 END), 0) as total_cash,
                COALESCE(SUM(CASE WHEN payment_condition = 'TRANSFERENCIA' THEN total ELSE 0 END), 0) as total_transfer,
                COALESCE(SUM(CASE WHEN payment_condition = 'CREDITO' THEN total ELSE 0 END), 0) as total_credit
            FROM sales 
            WHERE status = 'EMITIDA'
        ";
        $params = [];

        if ($startDate !== null) {
            $sql .= " AND DATE(created_at) >= :start_date";
            $params['start_date'] = $startDate;
        }
        if ($endDate !== null) {
            $sql .= " AND DATE(created_at) <= :end_date";
            $params['end_date'] = $endDate;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: [
            'total_sales' => 0,
            'total_vouchers' => 0,
            'average_ticket' => 0,
            'total_cash' => 0,
            'total_transfer' => 0,
            'total_credit' => 0,
        ];
    }

    public function getTopSellingProducts(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT si.product_id, si.product_name, p.code as product_code,
                   SUM(si.quantity) as total_sold,
                   SUM(si.total) as total_revenue
            FROM sale_items si
            INNER JOIN sales s ON si.sale_id = s.id
            LEFT JOIN products p ON si.product_id = p.id
            WHERE s.status = 'EMITIDA'
            GROUP BY si.product_id, si.product_name, p.code
            ORDER BY total_sold DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

