<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;
use RuntimeException;

class BackupService
{
    private string $storagePath;

    public function __construct()
    {
        $this->storagePath = dirname(__DIR__, 2) . '/storage/backups';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    public function generateBackupSql(): string
    {
        $pdo = Database::getConnection();
        $dbName = (string)$pdo->query("SELECT DATABASE()")->fetchColumn();

        $out = "-- ======================================================================\n";
        $out .= "-- COPIA DE SEGURIDAD AUTOMÁTICA - BASE DE DATOS: {$dbName}\n";
        $out .= "-- ICP Informatics Company of Perú\n";
        $out .= "-- Generado el: " . date('Y-m-d H:i:s') . "\n";
        $out .= "-- ======================================================================\n\n";
        $out .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $out .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $out .= "SET NAMES utf8mb4;\n\n";

        // Get list of tables
        $tablesStmt = $pdo->query("SHOW TABLES");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $out .= "-- ----------------------------------------------------------------------\n";
            $out .= "-- Estructura de tabla para `{$table}`\n";
            $out .= "-- ----------------------------------------------------------------------\n";
            $out .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createTableStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $createTable = $createTableStmt->fetch();
            $out .= $createTable['Create Table'] . ";\n\n";

            // Dump data
            $rowsStmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $out .= "-- Volcado de datos para la tabla `{$table}`\n";

                $columns = array_keys($rows[0]);
                $escapedColumns = array_map(static fn($c) => "`{$c}`", $columns);
                $columnsList = implode(', ', $escapedColumns);

                $out .= "INSERT INTO `{$table}` ({$columnsList}) VALUES\n";

                $valuesList = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $rowValues[] = "NULL";
                        } else {
                            $rowValues[] = $pdo->quote((string)$val);
                        }
                    }
                    $valuesList[] = "(" . implode(', ', $rowValues) . ")";
                }

                $out .= implode(",\n", $valuesList) . ";\n\n";
            }
        }

        $out .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $out .= "-- Fin del respaldo\n";

        return $out;
    }

    public function createBackupFile(): string
    {
        $sql = $this->generateBackupSql();
        $filename = "backup_icp_" . date('Y_m_d_His') . ".sql";
        $filePath = $this->storagePath . '/' . $filename;

        if (file_put_contents($filePath, $sql) === false) {
            throw new RuntimeException("No se pudo escribir el archivo de respaldo en: {$filePath}");
        }

        return $filePath;
    }

    public function getBackupFiles(): array
    {
        $files = glob($this->storagePath . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        // Sort latest first
        usort($backups, static fn($a, $b) => strcmp($b['filename'], $a['filename']));

        return $backups;
    }

    public function downloadBackup(string $filename): void
    {
        $filePath = $this->storagePath . '/' . basename($filename);
        if (!file_exists($filePath)) {
            throw new RuntimeException("Archivo de respaldo no encontrado.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}

