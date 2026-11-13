<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use PDO;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.status = 'Activo'
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, description, status)
            VALUES (:name, :description, :status)
        ");
        $stmt->execute([
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'Activo',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE categories 
            SET name = :name, description = :description, status = :status 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'Activo',
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET status = 'Inactivo' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

