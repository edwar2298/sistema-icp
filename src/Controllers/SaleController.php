<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\SaleService;
use Throwable;

class SaleController extends Controller
{
    private SaleService $saleService;
    private ClientRepositoryInterface $clientRepository;
    private ProductRepositoryInterface $productRepository;
    private SettingRepositoryInterface $settingRepository;

    public function __construct(
        SaleService $saleService,
        ClientRepositoryInterface $clientRepository,
        ProductRepositoryInterface $productRepository,
        SettingRepositoryInterface $settingRepository
    ) {
        $this->saleService = $saleService;
        $this->clientRepository = $clientRepository;
        $this->productRepository = $productRepository;
        $this->settingRepository = $settingRepository;
    }

    public function index(Request $request): Response
    {
        $filters = [
            'voucher_type' => $request->get('voucher_type'),
            'payment_condition' => $request->get('payment_condition'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ];

        $sales = $this->saleService->getAll($filters);

        return $this->render('sales.index', [
            'title' => 'Registro y Emisión de Ventas',
            'sales' => $sales,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): Response
    {
        $clients = $this->clientRepository->getAll();
        $products = $this->productRepository->getAll();
        $igvRate = $this->settingRepository->get('company_igv_rate', '18.00');

        $correlatives = [
            'BOLETA' => $this->settingRepository->getNextCorrelative('BOLETA'),
            'FACTURA' => $this->settingRepository->getNextCorrelative('FACTURA'),
            'NOTA_VENTA' => $this->settingRepository->getNextCorrelative('NOTA_VENTA'),
        ];

        return $this->render('sales.create', [
            'title' => 'Nueva Venta / Emisión de Comprobante',
            'clients' => $clients,
            'products' => $products,
            'igvRate' => $igvRate,
            'correlatives' => $correlatives,
        ]);
    }

    public function store(Request $request): Response
    {
        $this->validateCsrf($request);

        $clientId = $request->post('client_id');
        $voucherType = $request->post('voucher_type', 'BOLETA');
        $paymentCondition = $request->post('payment_condition', 'EFECTIVO');
        $notes = $request->post('notes');
        $items = $request->post('items', []);

        try {
            $saleId = $this->saleService->create([
                'client_id' => $clientId,
                'voucher_type' => $voucherType,
                'payment_condition' => $paymentCondition,
                'notes' => $notes,
            ], $items, $request);

            Session::setFlash('success', 'Venta registrada con éxito y comprobante emitido. Stock actualizado.');
            return $this->redirect("/sales/show/{$saleId}");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
            return $this->redirect('/sales/create');
        }
    }

    public function show(Request $request, string $id): Response
    {
        $sale = $this->saleService->find((int)$id);
        if (!$sale) {
            Session::setFlash('error', 'Comprobante de venta no encontrado.');
            return $this->redirect('/sales');
        }

        $company = $this->settingRepository->getAll();

        return $this->render('sales.show', [
            'title' => 'Comprobante ' . $sale['voucher_type'] . ' ' . $sale['series'] . '-' . str_pad((string)$sale['correlative'], 6, '0', STR_PAD_LEFT),
            'sale' => $sale,
            'company' => $company,
        ]);
    }

    public function print(Request $request, string $id): Response
    {
        $sale = $this->saleService->find((int)$id);
        if (!$sale) {
            Session::setFlash('error', 'Comprobante no encontrado.');
            return $this->redirect('/sales');
        }

        $company = $this->settingRepository->getAll();

        return $this->render('sales.print', [
            'title' => 'Imprimir ' . $sale['voucher_type'] . ' ' . $sale['series'] . '-' . str_pad((string)$sale['correlative'], 6, '0', STR_PAD_LEFT),
            'sale' => $sale,
            'company' => $company,
        ], 'print');
    }

    public function cancel(Request $request, string $id): Response
    {
        $this->validateCsrf($request);
        $reason = trim((string)$request->post('cancel_reason', 'Anulación a solicitud'));

        try {
            $this->saleService->cancelSale((int)$id, $reason, $request);
            Session::setFlash('success', 'Comprobante de venta anulado. El stock ha sido restituido al almacén.');
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect("/sales/show/{$id}");
    }
}

