<?php

declare(strict_types=1);

/**
 * Migration & Database Seeder Script for ICP Management System
 * Run via CLI: php database/migrate.php
 */

require_once __DIR__ . '/../src/autoload.php';

$config = require __DIR__ . '/../config/database.php';

echo "========================================================\n";
echo "ICP Informatics Company of Perú - Database Installer\n";
echo "========================================================\n\n";

try {
    $host = $config['host'];
    $port = $config['port'];
    $database = $config['database'];
    $username = $config['username'];
    $password = $config['password'];

    echo "[1/4] Conectando al servidor MySQL en {$host}:{$port}...\n";
    $dsnServer = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdoServer = new PDO($dsnServer, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "[2/4] Creando base de datos '{$database}' si no existe...\n";
    $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    $dsnDb = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsnDb, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    echo "[3/4] Ejecutando schema.sql (Estructura de tablas InnoDB e índices)...\n";
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    if ($schemaSql === false) {
        throw new RuntimeException("No se pudo leer schema.sql");
    }
    $pdo->exec($schemaSql);

    echo "[4/4] Ejecutando seeds.sql (Datos iniciales y configuraciones)...\n";
    $seedsSql = file_get_contents(__DIR__ . '/seeds.sql');
    if ($seedsSql === false) {
        throw new RuntimeException("No se pudo leer seeds.sql");
    }
    $pdo->exec($seedsSql);

    // Asegurar hashes de contraseñas exactos con password_hash de PHP 8.x
    $passHash = password_hash('Admin123*', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE email IN ('admin@icp.com.pe', 'ventas@icp.com.pe', 'tecnico@icp.com.pe')");
    $stmt->execute(['hash' => $passHash]);

    echo "\n>>> ¡Migración completada con éxito! <<<\n";
    echo "Base de datos '{$database}' configurada e inicializada.\n\n";
    echo "Credenciales de acceso predeterminadas:\n";
    echo "  - Administrador : admin@icp.com.pe   / Clave: Admin123*\n";
    echo "  - Vendedor      : ventas@icp.com.pe  / Clave: Admin123*\n";
    echo "  - Técnico       : tecnico@icp.com.pe / Clave: Admin123*\n";
    echo "========================================================\n";

} catch (Throwable $e) {
    echo "\n[ERROR] Falló la migración: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

