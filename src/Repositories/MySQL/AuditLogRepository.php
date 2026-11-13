<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use PDO;

class AuditLogRepository extends BaseRepository implements AuditLogRepositoryInterface
{
    public function log(
        ?int $userId,
        string $action,
        string $entityTable,
        ?string $entityId,
        string $ipAddress,
        ?string $userAgent,
        ?array $details = null
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO audit_logs (user_id, action, entity_table, entity_id, ip_address, user_agent, details)
            VALUES (:user_id, :action, :entity_table, :entity_id, :ip_address, :user_agent, :details)
        ");

        $stmt->execute([
            'user_id' => $userId,
            'action' => strtoupper($action),
            'entity_table' => $entityTable,
            'entity_id' => $entityId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'details' => $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getLatest(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as user_name, u.email as user_email, u.role as user_role
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function filter(array $filters): array
    {
        $sql = "
            SELECT a.*, u.name as user_name, u.email as user_email, u.role as user_role
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = :user_id";
            $params['user_id'] = (int)$filters['user_id'];
        }
        if (!empty($filters['action'])) {
            $sql .= " AND a.action = :action";
            $params['action'] = $filters['action'];
        }
        if (!empty($filters['entity_table'])) {
            $sql .= " AND a.entity_table = :entity_table";
            $params['entity_table'] = $filters['entity_table'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(a.created_at) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(a.created_at) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY a.id DESC LIMIT 300";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM audit_logs");
        return (int)$stmt->fetchColumn();
    }
}

