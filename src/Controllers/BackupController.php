<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuditService;
use App\Services\BackupService;
use Throwable;

class BackupController extends Controller
{
    private BackupService $backupService;
    private AuditService $auditService;

    public function __construct(BackupService $backupService, AuditService $auditService)
    {
        $this->backupService = $backupService;
        $this->auditService = $auditService;
    }

    public function index(Request $request): Response
    {
        $backups = $this->backupService->getBackupFiles();

        return $this->render('settings.backup', [
            'title' => 'Respaldos de Base de Datos (Backups)',
            'backups' => $backups,
        ]);
    }

    public function create(Request $request): Response
    {
        $this->validateCsrf($request);

        try {
            $filePath = $this->backupService->createBackupFile();
            $filename = basename($filePath);

            $this->auditService->log('CREACION_BACKUP', 'database', $filename, [
                'filename' => $filename,
                'size' => filesize($filePath),
            ], $request);

            Session::setFlash('success', "Copia de seguridad '{$filename}' generada con éxito.");
        } catch (Throwable $e) {
            Session::setFlash('error', 'Error al generar el respaldo: ' . $e->getMessage());
        }

        return $this->redirect('/backups');
    }

    public function download(Request $request, string $filename): void
    {
        $this->backupService->downloadBackup($filename);
    }
}

