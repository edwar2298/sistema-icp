<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\KardexRepositoryInterface;
use PDO;

class KardexRepository extends BaseRepository implements KardexRepositoryInterface
{
    public function record(
        int $productId,
        int $userId,
        string $movementType,
        string $referenceType,
        ?string $referenceId,
        int $quantity,
        int $previousStock,
        int $newStock,
        float $unitCost,
        string $reason
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO kardex (
                product_id, user_id, movement_type, reference_type, reference_id, 
                quantity, previous_stock, new_stock, unit_cost, reason, created_at
            ) VALUES (
                :product_id, :user_id, :movement_type, :reference_type, :reference_id,
                :quantity, :previous_stock, :new_stock, :unit_cost, :reason, NOW()
            )
        ");
        $stmt->execute([
            'product_id' => $productId,
            'user_id' => $userId,
            'movement_type' => $movementType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'unit_cost' => $unitCost,
            'reason' => $reason,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getByProduct(int $productId, int $limit = 100): array
    {
        $stmt = $this->db->prepare("
            SELECT k.*, p.name as product_name, p.code as product_code, u.name as user_name 
            FROM kardex k
            INNER JOIN products p ON k.product_id = p.id
            INNER JOIN users u ON k.user_id = u.id
            WHERE k.product_id = :product_id
            ORDER BY k.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = [], int $limit = 200): array
    {
        $sql = "
            SELECT k.*, p.name as product_name, p.code as product_code, u.name as user_name 
            FROM kardex k
            INNER JOIN products p ON k.product_id = p.id
            INNER JOIN users u ON k.user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['product_id'])) {
            $sql .= " AND k.product_id = :product_id";
            $params['product_id'] = (int)$filters['product_id'];
        }
        if (!empty($filters['movement_type'])) {
            $sql .= " AND k.movement_type = :movement_type";
            $params['movement_type'] = $filters['movement_type'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(k.created_at) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(k.created_at) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY k.id DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":{$k}", $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

