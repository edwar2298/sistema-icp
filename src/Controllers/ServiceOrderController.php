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
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ServiceOrderService;
use Throwable;

class ServiceOrderController extends Controller
{
    private ServiceOrderService $orderService;
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;
    private ProductRepositoryInterface $productRepository;
    private SettingRepositoryInterface $settingRepository;

    public function __construct(
        ServiceOrderService $orderService,
        ClientRepositoryInterface $clientRepository,
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
        SettingRepositoryInterface $settingRepository
    ) {
        $this->orderService = $orderService;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->settingRepository = $settingRepository;
    }

    public function index(Request $request): Response
    {
        $filters = [
            'status' => $request->get('status'),
            'priority' => $request->get('priority'),
            'search' => $request->get('search'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ];

        $orders = $this->orderService->getAll($filters);
        $statusCounts = $this->orderService->countByStatus();

        return $this->render('technical_service.index', [
            'title' => 'Gestión de Servicio Técnico (OST)',
            'orders' => $orders,
            'filters' => $filters,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create(Request $request): Response
    {
        $clients = $this->clientRepository->getAll();
        $users = $this->userRepository->getAll();
        $technicians = array_filter($users, static fn($u) => in_array($u['role'], ['Técnico', 'Administrador'], true));
        $products = $this->productRepository->getAll();

        return $this->render('technical_service.create', [
            'title' => 'Registrar Orden de Servicio Técnico',
            'clients' => $clients,
            'technicians' => $technicians,
            'products' => $products,
        ]);
    }

    public function store(Request $request): Response
    {
        $this->validateCsrf($request);

        $data = [
            'client_id' => $request->post('client_id'),
            'technician_id' => $request->post('technician_id'),
            'equipment_type' => $request->post('equipment_type'),
            'brand' => $request->post('brand'),
            'model' => $request->post('model'),
            'serial_number' => $request->post('serial_number'),
            'problem_description' => $request->post('problem_description'),
            'priority' => $request->post('priority', 'MEDIA'),
            'diagnostic' => $request->post('diagnostic'),
            'work_performed' => $request->post('work_performed'),
            'hours_worked' => $request->post('hours_worked', 0),
            'hourly_rate' => $request->post('hourly_rate', 0),
            'customer_notes' => $request->post('customer_notes'),
        ];

        $parts = $request->post('parts', []);

        try {
            $orderId = $this->orderService->create($data, $parts, $request);
            Session::setFlash('success', 'Orden de Servicio Técnico creada exitosamente.');
            return $this->redirect("/technical-service/show/{$orderId}");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
            return $this->redirect('/technical-service/create');
        }
    }

    public function show(Request $request, string $id): Response
    {
        $order = $this->orderService->find((int)$id);
        if (!$order) {
            Session::setFlash('error', 'Orden de servicio técnico no encontrada.');
            return $this->redirect('/technical-service');
        }

        $users = $this->userRepository->getAll();
        $technicians = array_filter($users, static fn($u) => in_array($u['role'], ['Técnico', 'Administrador'], true));
        $company = $this->settingRepository->getAll();

        return $this->render('technical_service.show', [
            'title' => 'Orden de Servicio: ' . $order['order_number'],
            'order' => $order,
            'technicians' => $technicians,
            'company' => $company,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $this->validateCsrf($request);

        $data = [
            'client_id' => (int)$request->post('client_id'),
            'technician_id' => (int)$request->post('technician_id'),
            'equipment_type' => (string)$request->post('equipment_type'),
            'brand' => (string)$request->post('brand'),
            'model' => (string)$request->post('model'),
            'serial_number' => (string)$request->post('serial_number'),
            'problem_description' => (string)$request->post('problem_description'),
            'priority' => (string)$request->post('priority'),
            'diagnostic' => (string)$request->post('diagnostic'),
            'work_performed' => (string)$request->post('work_performed'),
            'hours_worked' => (float)$request->post('hours_worked', 0),
            'hourly_rate' => (float)$request->post('hourly_rate', 0),
            'customer_notes' => (string)$request->post('customer_notes'),
        ];

        try {
            $this->orderService->update((int)$id, $data, $request);
            Session::setFlash('success', 'Detalles técnicos y costos actualizados.');
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect("/technical-service/show/{$id}");
    }

    public function changeStatus(Request $request, string $id): Response
    {
        $this->validateCsrf($request);

        $newStatus = (string)$request->post('status');
        $comments = (string)$request->post('comments', '');

        try {
            $this->orderService->transitionStatus((int)$id, $newStatus, $comments, $request);
            Session::setFlash('success', "Estado actualizado a '{$newStatus}' correctamente.");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect("/technical-service/show/{$id}");
    }

    public function print(Request $request, string $id): Response
    {
        $order = $this->orderService->find((int)$id);
        if (!$order) {
            Session::setFlash('error', 'Orden no encontrada.');
            return $this->redirect('/technical-service');
        }

        $company = $this->settingRepository->getAll();

        return $this->render('technical_service.print', [
            'title' => 'Imprimir OST ' . $order['order_number'],
            'order' => $order,
            'company' => $company,
        ], 'print');
    }
}

