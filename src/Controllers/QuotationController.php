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
use App\Services\QuotationService;
use App\Services\SaleService;
use Throwable;

class QuotationController extends Controller
{
    private QuotationService $quotationService;
    private SaleService $saleService;
    private ClientRepositoryInterface $clientRepository;
    private ProductRepositoryInterface $productRepository;
    private SettingRepositoryInterface $settingRepository;

    public function __construct(
        QuotationService $quotationService,
        SaleService $saleService,
        ClientRepositoryInterface $clientRepository,
        ProductRepositoryInterface $productRepository,
        SettingRepositoryInterface $settingRepository
    ) {
        $this->quotationService = $quotationService;
        $this->saleService = $saleService;
        $this->clientRepository = $clientRepository;
        $this->productRepository = $productRepository;
        $this->settingRepository = $settingRepository;
    }

    public function index(Request $request): Response
    {
        $search = trim((string)$request->get('search', ''));
        $status = $request->get('status');

        $quotations = $this->quotationService->getAll($search, $status);

        return $this->render('quotations.index', [
            'title' => 'Cotizaciones Comerciales',
            'quotations' => $quotations,
            'search' => $search,
            'selectedStatus' => $status,
        ]);
    }

    public function create(Request $request): Response
    {
        $clients = $this->clientRepository->getAll();
        $products = $this->productRepository->getAll();
        $igvRate = $this->settingRepository->get('company_igv_rate', '18.00');

        return $this->render('quotations.create', [
            'title' => 'Nueva Cotización',
            'clients' => $clients,
            'products' => $products,
            'igvRate' => $igvRate,
        ]);
    }

    public function store(Request $request): Response
    {
        $this->validateCsrf($request);

        $clientId = $request->post('client_id');
        $discount = (float)$request->post('discount', 0);
        $validUntil = $request->post('valid_until');
        $notes = $request->post('notes');
        $items = $request->post('items', []);

        try {
            $id = $this->quotationService->create([
                'client_id' => $clientId,
                'discount' => $discount,
                'valid_until' => $validUntil,
                'notes' => $notes,
            ], $items, $request);

            Session::setFlash('success', 'Cotización registrada exitosamente.');
            return $this->redirect("/quotations/show/{$id}");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
            return $this->redirect('/quotations/create');
        }
    }

    public function show(Request $request, string $id): Response
    {
        $quotation = $this->quotationService->find((int)$id);
        if (!$quotation) {
            Session::setFlash('error', 'Cotización no encontrada.');
            return $this->redirect('/quotations');
        }

        $company = $this->settingRepository->getAll();

        return $this->render('quotations.show', [
            'title' => 'Detalle Cotización ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'company' => $company,
        ]);
    }

    public function print(Request $request, string $id): Response
    {
        $quotation = $this->quotationService->find((int)$id);
        if (!$quotation) {
            Session::setFlash('error', 'Cotización no encontrada.');
            return $this->redirect('/quotations');
        }

        $company = $this->settingRepository->getAll();

        return $this->render('quotations.print', [
            'title' => 'Imprimir Cotización ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'company' => $company,
        ], 'print');
    }

    public function updateStatus(Request $request, string $id): Response
    {
        $this->validateCsrf($request);
        $status = $request->post('status', 'PENDIENTE');

        try {
            $this->quotationService->updateStatus((int)$id, $status, $request);
            Session::setFlash('success', 'Estado de la cotización actualizado.');
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect("/quotations/show/{$id}");
    }

    public function convertToSale(Request $request, string $id): Response
    {
        $this->validateCsrf($request);

        $voucherType = $request->post('voucher_type', 'BOLETA');
        $paymentCondition = $request->post('payment_condition', 'EFECTIVO');

        try {
            $saleId = $this->saleService->convertQuotationToSale((int)$id, $voucherType, $paymentCondition, $request);
            Session::setFlash('success', '¡Cotización convertida en Venta exitosamente! Stock descontado automáticamente.');
            return $this->redirect("/sales/show/{$saleId}");
        } catch (Throwable $e) {
            Session::setFlash('error', 'Error al convertir cotización: ' . $e->getMessage());
            return $this->redirect("/quotations/show/{$id}");
        }
    }
}

