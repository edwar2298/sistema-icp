<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use PDO;

class ServiceOrderRepository extends BaseRepository implements ServiceOrderRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT ost.*, 
                   c.business_name as client_name, c.document_type as client_doc_type,
                   c.document_number as client_doc_number, c.address as client_address,
                   c.phone as client_phone, c.email as client_email,
                   u.name as technician_name, u.email as technician_email
            FROM technical_service_orders ost
            INNER JOIN clients c ON ost.client_id = c.id
            INNER JOIN users u ON ost.technician_id = u.id
            WHERE ost.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByNumber(string $number): ?array
    {
        $stmt = $this->db->prepare("
            SELECT ost.*, c.business_name as client_name, u.name as technician_name
            FROM technical_service_orders ost
            INNER JOIN clients c ON ost.client_id = c.id
            INNER JOIN users u ON ost.technician_id = u.id
            WHERE ost.order_number = :number LIMIT 1
        ");
        $stmt->execute(['number' => trim($number)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getParts(int $orderId): array
    {
        $stmt = $this->db->prepare("
            SELECT sop.*, p.code as product_code 
            FROM service_order_parts sop
            LEFT JOIN products p ON sop.product_id = p.id
            WHERE sop.service_order_id = :id
            ORDER BY sop.id ASC
        ");
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function getStatusHistory(int $orderId): array
    {
        $stmt = $this->db->prepare("
            SELECT sosh.*, u.name as user_name, u.role as user_role
            FROM service_order_status_history sosh
            INNER JOIN users u ON sosh.user_id = u.id
            WHERE sosh.service_order_id = :id
            ORDER BY sosh.id ASC
        ");
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = [], int $limit = 100): array
    {
        $sql = "
            SELECT ost.*, 
                   c.business_name as client_name, c.document_number as client_doc_number,
                   u.name as technician_name
            FROM technical_service_orders ost
            INNER JOIN clients c ON ost.client_id = c.id
            INNER JOIN users u ON ost.technician_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND ost.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $sql .= " AND ost.priority = :priority";
            $params['priority'] = $filters['priority'];
        }
        if (!empty($filters['technician_id'])) {
            $sql .= " AND ost.technician_id = :technician_id";
            $params['technician_id'] = (int)$filters['technician_id'];
        }
        if (!empty($filters['client_id'])) {
            $sql .= " AND ost.client_id = :client_id";
            $params['client_id'] = (int)$filters['client_id'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(ost.created_at) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(ost.created_at) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (ost.order_number LIKE :search_order OR c.business_name LIKE :search_client OR ost.equipment_type LIKE :search_equip OR ost.serial_number LIKE :search_serial)";
            $searchTerm = "%{$filters['search']}%";
            $params['search_order'] = $searchTerm;
            $params['search_client'] = $searchTerm;
            $params['search_equip'] = $searchTerm;
            $params['search_serial'] = $searchTerm;
        }

        $sql .= " ORDER BY ost.id DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":{$k}", $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data, array $parts = []): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO technical_service_orders (
                order_number, client_id, technician_id, equipment_type, brand, model,
                serial_number, problem_description, diagnostic, work_performed, priority,
                status, hours_worked, hourly_rate, labor_cost, parts_cost, total_cost,
                customer_notes
            ) VALUES (
                :order_number, :client_id, :technician_id, :equipment_type, :brand, :model,
                :serial_number, :problem_description, :diagnostic, :work_performed, :priority,
                :status, :hours_worked, :hourly_rate, :labor_cost, :parts_cost, :total_cost,
                :customer_notes
            )
        ");

        $stmt->execute([
            'order_number' => $data['order_number'],
            'client_id' => (int)$data['client_id'],
            'technician_id' => (int)$data['technician_id'],
            'equipment_type' => $data['equipment_type'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'serial_number' => $data['serial_number'] ?? null,
            'problem_description' => $data['problem_description'],
            'diagnostic' => $data['diagnostic'] ?? null,
            'work_performed' => $data['work_performed'] ?? null,
            'priority' => $data['priority'] ?? 'MEDIA',
            'status' => $data['status'] ?? 'REGISTRADA',
            'hours_worked' => (float)($data['hours_worked'] ?? 0),
            'hourly_rate' => (float)($data['hourly_rate'] ?? 0),
            'labor_cost' => (float)($data['labor_cost'] ?? 0),
            'parts_cost' => (float)($data['parts_cost'] ?? 0),
            'total_cost' => (float)($data['total_cost'] ?? 0),
            'customer_notes' => $data['customer_notes'] ?? null,
        ]);

        $orderId = (int)$this->db->lastInsertId();

        // Initial history record
        $this->recordHistory($orderId, (int)$data['technician_id'], null, $data['status'] ?? 'REGISTRADA', 'Creación de orden de servicio');

        if (!empty($parts)) {
            $this->addParts($orderId, $parts);
        }

        return $orderId;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE technical_service_orders
            SET client_id = :client_id,
                technician_id = :technician_id,
                equipment_type = :equipment_type,
                brand = :brand,
                model = :model,
                serial_number = :serial_number,
                problem_description = :problem_description,
                diagnostic = :diagnostic,
                work_performed = :work_performed,
                priority = :priority,
                hours_worked = :hours_worked,
                hourly_rate = :hourly_rate,
                labor_cost = :labor_cost,
                parts_cost = :parts_cost,
                total_cost = :total_cost,
                customer_notes = :customer_notes
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'client_id' => (int)$data['client_id'],
            'technician_id' => (int)$data['technician_id'],
            'equipment_type' => $data['equipment_type'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'serial_number' => $data['serial_number'] ?? null,
            'problem_description' => $data['problem_description'],
            'diagnostic' => $data['diagnostic'] ?? null,
            'work_performed' => $data['work_performed'] ?? null,
            'priority' => $data['priority'],
            'hours_worked' => (float)$data['hours_worked'],
            'hourly_rate' => (float)$data['hourly_rate'],
            'labor_cost' => (float)$data['labor_cost'],
            'parts_cost' => (float)$data['parts_cost'],
            'total_cost' => (float)$data['total_cost'],
            'customer_notes' => $data['customer_notes'] ?? null,
        ]);
    }

    public function updateStatus(int $id, int $userId, string $newStatus, ?string $comments = null): bool
    {
        $current = $this->findById($id);
        if (!$current) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE technical_service_orders 
            SET status = :status,
                customer_signature_date = CASE WHEN :status = 'ENTREGADA' AND customer_signature_date IS NULL THEN NOW() ELSE customer_signature_date END
            WHERE id = :id
        ");
        $success = $stmt->execute([
            'id' => $id,
            'status' => $newStatus,
        ]);

        if ($success) {
            $this->recordHistory($id, $userId, $current['status'], $newStatus, $comments);
        }

        return $success;
    }

    public function addParts(int $orderId, array $parts): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO service_order_parts (service_order_id, product_id, part_name, quantity, unit_price, subtotal)
            VALUES (:service_order_id, :product_id, :part_name, :quantity, :unit_price, :subtotal)
        ");

        foreach ($parts as $part) {
            $stmt->execute([
                'service_order_id' => $orderId,
                'product_id' => !empty($part['product_id']) ? (int)$part['product_id'] : null,
                'part_name' => $part['part_name'],
                'quantity' => (int)$part['quantity'],
                'unit_price' => (float)$part['unit_price'],
                'subtotal' => (float)$part['subtotal'],
            ]);
        }

        return true;
    }

    private function recordHistory(int $orderId, int $userId, ?string $fromStatus, string $toStatus, ?string $comments): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO service_order_status_history (service_order_id, user_id, from_status, to_status, comments)
            VALUES (:order_id, :user_id, :from_status, :to_status, :comments)
        ");
        $stmt->execute([
            'order_id' => $orderId,
            'user_id' => $userId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'comments' => $comments,
        ]);
    }

    public function countByStatus(): array
    {
        $stmt = $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM technical_service_orders 
            GROUP BY status
        ");
        $rows = $stmt->fetchAll();
        $counts = [
            'REGISTRADA' => 0,
            'EN_PROCESO' => 0,
            'EN_ESPERA_REPUESTO' => 0,
            'ATENDIDA' => 0,
            'ENTREGADA' => 0,
            'TOTAL' => 0,
        ];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int)$row['count'];
            $counts['TOTAL'] += (int)$row['count'];
        }
        return $counts;
    }
}

