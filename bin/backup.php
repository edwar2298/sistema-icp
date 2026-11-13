<?php

declare(strict_types=1);

/**
 * CLI Backup Automation Script
 * Can be run directly or scheduled via cron / Windows Task Scheduler.
 * Usage: php bin/backup.php
 */

require_once __DIR__ . '/../src/autoload.php';

use App\Services\BackupService;

echo "========================================================\n";
echo "ICP Informatics Company of Perú - Backup Programado\n";
echo "Fecha y hora: " . date('Y-m-d H:i:s') . "\n";
echo "========================================================\n\n";

try {
    $service = new BackupService();
    echo "[1/2] Extrayendo estructura y datos de la base de datos MySQL...\n";
    $file = $service->createBackupFile();
    $size = filesize($file);
    $sizeKb = round($size / 1024, 2);

    echo "[2/2] Archivo guardado con éxito:\n";
    echo "  - Ruta: {$file}\n";
    echo "  - Tamaño: {$sizeKb} KB\n\n";
    echo ">>> Respaldo finalizado correctamente. <<<\n";
    exit(0);
} catch (Throwable $e) {
    echo "\n[ERROR CRÍTICO] Falló la creación del respaldo:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

