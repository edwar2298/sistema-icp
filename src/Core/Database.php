<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $configFile = dirname(__DIR__, 2) . '/config/database.php';
        if (!file_exists($configFile)) {
            throw new RuntimeException("Archivo de configuración de base de datos no encontrado.");
        }

        $config = require $configFile;

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset'] ?? 'utf8mb4'
        );

        try {
            self::$instance = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException("Error de conexión a la Base de Datos: " . $e->getMessage(), (int)$e->getCode(), $e);
        }

        return self::$instance;
    }

    /**
     * Executes a callback within an ACID database transaction.
     * Rolls back automatically if any exception is thrown.
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::getConnection();
        $isAlreadyInTransaction = $pdo->inTransaction();

        if (!$isAlreadyInTransaction) {
            $pdo->beginTransaction();
        }

        try {
            $result = $callback($pdo);

            if (!$isAlreadyInTransaction) {
                $pdo->commit();
            }

            return $result;
        } catch (\Throwable $e) {
            if (!$isAlreadyInTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}

