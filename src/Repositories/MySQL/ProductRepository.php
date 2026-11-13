<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\ProductRepositoryInterface;
use PDO;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.code = :code LIMIT 1
        ");
        $stmt->execute(['code' => trim($code)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAll(string $search = '', ?int $categoryId = null, bool $onlyActive = true): array
    {
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if ($onlyActive) {
            $sql .= " AND p.status = 'Activo'";
        }

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        if ($search !== '') {
            $sql .= " AND (p.name LIKE :search_name OR p.code LIKE :search_code OR p.description LIKE :search_desc)";
            $searchTerm = "%{$search}%";
            $params['search_name'] = $searchTerm;
            $params['search_code'] = $searchTerm;
            $params['search_desc'] = $searchTerm;
        }

        $sql .= " ORDER BY p.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getLowStock(): array
    {
        $stmt = $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'Activo' AND p.stock <= p.min_stock
            ORDER BY (p.stock - p.min_stock) ASC, p.stock ASC
        ");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO products (category_id, code, name, description, purchase_price, sale_price, stock, min_stock, status)
            VALUES (:category_id, :code, :name, :description, :purchase_price, :sale_price, :stock, :min_stock, :status)
        ");
        $stmt->execute([
            'category_id' => (int)$data['category_id'],
            'code' => trim($data['code']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'purchase_price' => (float)($data['purchase_price'] ?? 0),
            'sale_price' => (float)($data['sale_price'] ?? 0),
            'stock' => (int)($data['stock'] ?? 0),
            'min_stock' => (int)($data['min_stock'] ?? 5),
            'status' => $data['status'] ?? 'Activo',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET category_id = :category_id,
                code = :code,
                name = :name,
                description = :description,
                purchase_price = :purchase_price,
                sale_price = :sale_price,
                min_stock = :min_stock,
                status = :status
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'category_id' => (int)$data['category_id'],
            'code' => trim($data['code']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'purchase_price' => (float)($data['purchase_price'] ?? 0),
            'sale_price' => (float)($data['sale_price'] ?? 0),
            'min_stock' => (int)($data['min_stock'] ?? 5),
            'status' => $data['status'] ?? 'Activo',
        ]);
    }

    public function updateStock(int $id, int $newStock): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET stock = :stock WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'stock' => $newStock,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET status = 'Inactivo' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE status = 'Activo'");
        return (int)$stmt->fetchColumn();
    }
}

