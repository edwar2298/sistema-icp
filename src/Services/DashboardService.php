<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;

class DashboardService
{
    private SaleRepositoryInterface $saleRepository;
    private ProductRepositoryInterface $productRepository;
    private ServiceOrderRepositoryInterface $orderRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        SaleRepositoryInterface $saleRepository,
        ProductRepositoryInterface $productRepository,
        ServiceOrderRepositoryInterface $orderRepository,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->clientRepository = $clientRepository;
    }

    public function getDashboardMetrics(): array
    {
        $today = date('Y-m-d');
        $startOfMonth = date('Y-m-01');

        $monthStats = $this->saleRepository->getStats($startOfMonth, $today);
        $todayStats = $this->saleRepository->getStats($today, $today);

        $topProducts = $this->saleRepository->getTopSellingProducts(5);
        $lowStockProducts = $this->productRepository->getLowStock();
        $serviceStats = $this->orderRepository->countByStatus();
        $recentSales = $this->saleRepository->getAll([], 6);
        $recentOrders = $this->orderRepository->getAll([], 6);
        $totalClients = $this->clientRepository->count();

        return [
            'month_sales' => (float)$monthStats['total_sales'],
            'month_vouchers' => (int)$monthStats['total_vouchers'],
            'month_avg_ticket' => (float)$monthStats['average_ticket'],
            'today_sales' => (float)$todayStats['total_sales'],
            'today_vouchers' => (int)$todayStats['total_vouchers'],
            'top_products' => $topProducts,
            'low_stock_count' => count($lowStockProducts),
            'low_stock_products' => array_slice($lowStockProducts, 0, 5),
            'service_stats' => $serviceStats,
            'recent_sales' => $recentSales,
            'recent_orders' => $recentOrders,
            'total_clients' => $totalClients,
        ];
    }
}

