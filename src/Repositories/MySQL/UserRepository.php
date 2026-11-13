<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\UserRepositoryInterface;
use PDO;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1");
        $stmt->execute(['email' => trim($email)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT id, name, email, role, status, failed_attempts, lockout_until, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password_hash, role, status, failed_attempts)
            VALUES (:name, :email, :password_hash, :role, :status, 0)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'email' => trim($data['email']),
            'password_hash' => $data['password_hash'],
            'role' => $data['role'],
            'status' => $data['status'] ?? 'Activo',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach (['name', 'email', 'role', 'status'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (!empty($data['password_hash'])) {
            $fields[] = "password_hash = :password_hash";
            $params['password_hash'] = $data['password_hash'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateFailedAttempts(int $id, int $attempts, ?string $lockoutUntil = null): bool
    {
        $status = ($lockoutUntil !== null) ? 'Bloqueado' : 'Activo';
        $stmt = $this->db->prepare("
            UPDATE users 
            SET failed_attempts = :attempts, 
                lockout_until = :lockout_until,
                status = CASE WHEN :status = 'Bloqueado' THEN 'Bloqueado' ELSE status END
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'attempts' => $attempts,
            'lockout_until' => $lockoutUntil,
            'status' => $status,
        ]);
    }

    public function resetFailedAttempts(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET failed_attempts = 0, 
                lockout_until = NULL,
                status = 'Activo'
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }
}

