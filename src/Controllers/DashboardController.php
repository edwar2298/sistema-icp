<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request): Response
    {
        $metrics = $this->dashboardService->getDashboardMetrics();

        return $this->render('dashboard.index', [
            'title' => 'Dashboard Principal',
            'metrics' => $metrics,
        ]);
    }
}

