<?php

declare(strict_types=1);

/**
 * ==============================================================================
 * SISTEMA DE GESTIÓN WEB - ICP INFORMATICS COMPANY OF PERÚ
 * Front Controller & Application Bootstrapper
 * ==============================================================================
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Autoloading: Use Composer autoloader if available, fallback to PSR-4 autoloader
$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    require_once dirname(__DIR__) . '/src/autoload.php';
}

use App\Controllers\AuditController;
use App\Controllers\AuthController;
use App\Controllers\BackupController;
use App\Controllers\CategoryController;
use App\Controllers\ClientController;
use App\Controllers\DashboardController;
use App\Controllers\KardexController;
use App\Controllers\ProductController;
use App\Controllers\QuotationController;
use App\Controllers\ReportController;
use App\Controllers\SaleController;
use App\Controllers\ServiceOrderController;
use App\Controllers\SettingController;
use App\Core\Auth;
use App\Core\Container;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\KardexRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\QuotationRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\MySQL\AuditLogRepository;
use App\Repositories\MySQL\CategoryRepository;
use App\Repositories\MySQL\ClientRepository;
use App\Repositories\MySQL\KardexRepository;
use App\Repositories\MySQL\ProductRepository;
use App\Repositories\MySQL\QuotationRepository;
use App\Repositories\MySQL\SaleRepository;
use App\Repositories\MySQL\ServiceOrderRepository;
use App\Repositories\MySQL\SettingRepository;
use App\Repositories\MySQL\UserRepository;

// Start secure session
Session::start();

// Initialize Dependency Injection Container
$container = Container::getInstance();

// Register Database Connection Singleton
$container->singleton(\PDO::class, static fn() => \App\Core\Database::getConnection());

// Register Repository Interfaces with MySQL Implementations
$container->singleton(UserRepositoryInterface::class, UserRepository::class);
$container->singleton(AuditLogRepositoryInterface::class, AuditLogRepository::class);
$container->singleton(SettingRepositoryInterface::class, SettingRepository::class);
$container->singleton(ClientRepositoryInterface::class, ClientRepository::class);
$container->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);
$container->singleton(ProductRepositoryInterface::class, ProductRepository::class);
$container->singleton(KardexRepositoryInterface::class, KardexRepository::class);
$container->singleton(QuotationRepositoryInterface::class, QuotationRepository::class);
$container->singleton(SaleRepositoryInterface::class, SaleRepository::class);
$container->singleton(ServiceOrderRepositoryInterface::class, ServiceOrderRepository::class);

// Create HTTP Router
$router = new Router($container);

// ------------------------------------------------------------------------------
// RUTAS: PÚBLICAS Y AUTENTICACIÓN
// ------------------------------------------------------------------------------
$router->get('/', static function () {
    if (Auth::check()) {
        redirect('/dashboard');
    }
    redirect('/login');
});

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);

// ------------------------------------------------------------------------------
// RUTAS: DASHBOARD PRINCIPAL
// ------------------------------------------------------------------------------
$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

// ------------------------------------------------------------------------------
// RUTAS: MÓDULO COMERCIAL (CLIENTES)
// ------------------------------------------------------------------------------
$router->group(['middleware' => ['auth', 'role:Administrador,Vendedor']], function (Router $r) {
    $r->get('/clients', [ClientController::class, 'index']);
    $r->get('/clients/create', [ClientController::class, 'create']);
    $r->post('/clients/store', [ClientController::class, 'store']);
    $r->get('/clients/edit/{id}', [ClientController::class, 'edit']);
    $r->post('/clients/update/{id}', [ClientController::class, 'update']);
    $r->post('/clients/delete/{id}', [ClientController::class, 'delete']);
    $r->get('/api/clients/search', [ClientController::class, 'searchJson']);
});

// ------------------------------------------------------------------------------
// RUTAS: MÓDULO COMERCIAL (COTIZACIONES)
// ------------------------------------------------------------------------------
$router->group(['middleware' => ['auth', 'role:Administrador,Vendedor']], function (Router $r) {
    $r->get('/quotations', [QuotationController::class, 'index']);
    $r->get('/quotations/create', [QuotationController::class, 'create']);
    $r->post('/quotations/store', [QuotationController::class, 'store']);
    $r->get('/quotations/show/{id}', [QuotationController::class, 'show']);
    $r->get('/quotations/print/{id}', [QuotationController::class, 'print']);
    $r->post('/quotations/status/{id}', [QuotationController::class, 'updateStatus']);
    $r->post('/quotations/convert/{id}', [QuotationController::class, 'convertToSale']);
});

// ------------------------------------------------------------------------------
// RUTAS: MÓDULO COMERCIAL (VENTAS Y FACTURACIÓN)
// ------------------------------------------------------------------------------
$router->group(['middleware' => ['auth', 'role:Administrador,Vendedor']], function (Router $r) {
    $r->get('/sales', [SaleController::class, 'index']);
    $r->get('/sales/create', [SaleController::class, 'create']);
    $r->post('/sales/store', [SaleController::class, 'store']);
    $r->get('/sales/show/{id}', [SaleController::class, 'show']);
    $r->get('/sales/print/{id}', [SaleController::class, 'print']);
    $r->post('/sales/cancel/{id}', [SaleController::class, 'cancel']);
});

// ------------------------------------------------------------------------------
// RUTAS: MÓDULO DE INVENTARIO Y PRODUCTOS
// ------------------------------------------------------------------------------
$router->group(['middleware' => ['auth']], function (Router $r) {
    $r->get('/products', [ProductController::class, 'index']);
    $r->get('/api/products/search', [ProductController::class, 'searchJson']);
    $r->get('/kardex', [KardexController::class, 'index']);
    $r->get('/kardex/adjustment', [KardexController::class, 'adjustment']);
    $r->post('/kardex/adjustment', [KardexController::class, 'processAdjustment']);
});

$router->group(['middleware' => ['auth', 'role:Administrador']], function (Router $r) {
    $r->get('/products/create', [ProductController::class, 'create']);
    $r->post('/products/store', [ProductController::class, 'store']);
    $r->get('/products/edit/{id}', [ProductController::class, 'edit']);
    $r->post('/products/update/{id}', [ProductController::class, 'update']);
    $r->get('/categories', [CategoryController::class, 'index']);
    $r->post('/categories/store', [CategoryController::class, 'store']);
    $r->post('/categories/update/{id}', [CategoryController::class, 'update']);
});

// ------------------------------------------------------------------------------
// RUTAS: MÓDULO DE SERVICIO TÉCNICO (OST)
// ------------------------------------------------------------------------------
$router->group(['middleware' => ['auth', 'role:Administrador,Técnico']], function (Router $r) {
    $r->get('/technical-service', [ServiceOrderController::class, 'index']);
    $r->get('/technical-service/create', [ServiceOrderController::class, 'create']);
    $r->post('/technical-service/store', [ServiceOrderController::class, 'store']);
    $r->get('/technical-service/show/{id}', [ServiceOrderController::class, 'show']);
    $r->post('/technical-service/update/{id}', [ServiceOrderController::class, 'update']);
    $r->post('/technical-service/status/{id}', [ServiceOrderController::class, 'changeStatus']);
    $r->get('/technical-service/print/{id}', [ServiceOrderController::class, 'print']);
});

// ------------------------------------------------------------------------------
// RUTAS: REPORTES GERENCIALES
// ------------------------------------------------------------------------------
$router->group(['middleware' => ['auth', 'role:Administrador,Vendedor']], function (Router $r) {
    $r->get('/reports', [ReportController::class, 'index']);
    $r->get('/reports/sales', [ReportController::class, 'sales']);
    $r->get('/reports/sales/csv', [ReportController::class, 'salesCsv']);
    $r->get('/reports/kardex/csv', [ReportController::class, 'kardexCsv']);
    $r->get('/reports/service/csv', [ReportController::class, 'serviceCsv']);
});

// ------------------------------------------------------------------------------
// RUTAS: ADMINISTRACIÓN, CONFIGURACIÓN FISCAL, BACKUPS Y AUDITORÍA
// ------------------------------------------------------------------------------
$router->group(['middleware' => ['auth', 'role:Administrador']], function (Router $r) {
    $r->get('/settings', [SettingController::class, 'index']);
    $r->post('/settings/update', [SettingController::class, 'update']);
    $r->get('/backups', [BackupController::class, 'index']);
    $r->post('/backups/create', [BackupController::class, 'create']);
    $r->get('/backups/download/{filename}', [BackupController::class, 'download']);
    $r->get('/audit', [AuditController::class, 'index']);
});

// Capture and dispatch Request
$request = Request::capture();
$router->dispatch($request);

