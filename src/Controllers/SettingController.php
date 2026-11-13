<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\AuditService;
use Throwable;

class SettingController extends Controller
{
    private SettingRepositoryInterface $settingRepository;
    private AuditService $auditService;

    public function __construct(SettingRepositoryInterface $settingRepository, AuditService $auditService)
    {
        $this->settingRepository = $settingRepository;
        $this->auditService = $auditService;
    }

    public function index(Request $request): Response
    {
        $settings = $this->settingRepository->getAll();

        return $this->render('settings.index', [
            'title' => 'Configuración General del Sistema',
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): Response
    {
        $this->validateCsrf($request);

        $keys = [
            'company_name',
            'company_trade_name',
            'company_ruc',
            'company_address',
            'company_phone',
            'company_email',
            'company_igv_rate',
            'company_currency_symbol',
            'serie_boleta',
            'correlativo_boleta',
            'serie_factura',
            'correlativo_factura',
            'serie_nota',
            'correlativo_nota',
            'serie_ost',
            'correlativo_ost',
            'serie_cotizacion',
            'correlativo_cotizacion',
        ];

        try {
            $updatedValues = [];
            foreach ($keys as $key) {
                if ($request->post($key) !== null) {
                    $val = trim((string)$request->post($key));
                    $this->settingRepository->set($key, $val);
                    $updatedValues[$key] = $val;
                }
            }

            $this->auditService->log('MODIFICACION', 'company_settings', 'all', [
                'updated_fields' => $updatedValues,
            ], $request);

            Session::setFlash('success', 'Configuraciones de la empresa y comprobantes actualizadas correctamente.');
        } catch (Throwable $e) {
            Session::setFlash('error', 'Error al guardar la configuración: ' . $e->getMessage());
        }

        return $this->redirect('/settings');
    }
}

