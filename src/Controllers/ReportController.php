<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\ReportService;

class ReportController extends Controller
{
    private ReportService $reportService;
    private SettingRepositoryInterface $settingRepository;

    public function __construct(ReportService $reportService, SettingRepositoryInterface $settingRepository)
    {
        $this->reportService = $reportService;
        $this->settingRepository = $settingRepository;
    }

    public function index(Request $request): Response
    {
        return $this->render('reports.index', [
            'title' => 'Centro de Reportes Gerenciales',
        ]);
    }

    public function sales(Request $request): Response
    {
        $filters = [
            'start_date' => $request->get('start_date', date('Y-m-01')),
            'end_date' => $request->get('end_date', date('Y-m-d')),
            'voucher_type' => $request->get('voucher_type'),
            'payment_condition' => $request->get('payment_condition'),
            'status' => $request->get('status'),
        ];

        $reportData = $this->reportService->getSalesReport($filters);
        $company = $this->settingRepository->getAll();

        if ($request->get('print') === '1') {
            return $this->render('reports.sales_print', [
                'title' => 'Reporte Imprimible de Ventas',
                'report' => $reportData,
                'filters' => $filters,
                'company' => $company,
            ], 'print');
        }

        return $this->render('reports.sales', [
            'title' => 'Reporte Comercial y de Ventas',
            'report' => $reportData,
            'filters' => $filters,
        ]);
    }

    public function salesCsv(Request $request): void
    {
        $filters = [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'voucher_type' => $request->get('voucher_type'),
            'payment_condition' => $request->get('payment_condition'),
            'status' => $request->get('status'),
        ];

        $this->reportService->exportSalesCsv($filters);
    }

    public function kardexCsv(Request $request): void
    {
        $filters = [
            'product_id' => $request->get('product_id'),
            'movement_type' => $request->get('movement_type'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ];

        $this->reportService->exportKardexCsv($filters);
    }

    public function serviceCsv(Request $request): void
    {
        $filters = [
            'status' => $request->get('status'),
            'priority' => $request->get('priority'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ];

        $this->reportService->exportServiceCsv($filters);
    }
}

