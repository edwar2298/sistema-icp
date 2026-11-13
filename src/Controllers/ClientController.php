<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\ClientService;
use Throwable;

class ClientController extends Controller
{
    private ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request): Response
    {
        $search = trim((string)$request->get('search', ''));
        $clients = $this->clientService->getAll($search);

        return $this->render('clients.index', [
            'title' => 'Gestión de Clientes',
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('clients.create', [
            'title' => 'Registrar Nuevo Cliente',
        ]);
    }

    public function store(Request $request): Response
    {
        $this->validateCsrf($request);

        $data = [
            'document_type' => $request->post('document_type', 'RUC'),
            'document_number' => $request->post('document_number', ''),
            'business_name' => $request->post('business_name', ''),
            'address' => $request->post('address', ''),
            'phone' => $request->post('phone', ''),
            'email' => $request->post('email', ''),
            'status' => 'Activo',
        ];

        try {
            $this->clientService->create($data, $request);
            Session::setFlash('success', 'Cliente registrado exitosamente.');
            return $this->redirect('/clients');
        } catch (Throwable $e) {
            Session::flashInput($data);
            Session::setFlash('error', $e->getMessage());
            return $this->redirect('/clients/create');
        }
    }

    public function edit(Request $request, string $id): Response
    {
        $client = $this->clientService->find((int)$id);
        if (!$client) {
            Session::setFlash('error', 'Cliente no encontrado.');
            return $this->redirect('/clients');
        }

        return $this->render('clients.edit', [
            'title' => 'Editar Cliente: ' . $client['business_name'],
            'client' => $client,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $this->validateCsrf($request);

        $data = [
            'document_type' => $request->post('document_type', 'RUC'),
            'document_number' => $request->post('document_number', ''),
            'business_name' => $request->post('business_name', ''),
            'address' => $request->post('address', ''),
            'phone' => $request->post('phone', ''),
            'email' => $request->post('email', ''),
            'status' => $request->post('status', 'Activo'),
        ];

        try {
            $this->clientService->update((int)$id, $data, $request);
            Session::setFlash('success', 'Datos del cliente actualizados correctamente.');
            return $this->redirect('/clients');
        } catch (Throwable $e) {
            Session::flashInput($data);
            Session::setFlash('error', $e->getMessage());
            return $this->redirect("/clients/edit/{$id}");
        }
    }

    public function delete(Request $request, string $id): Response
    {
        $this->validateCsrf($request);

        try {
            $this->clientService->delete((int)$id, $request);
            Session::setFlash('success', 'Cliente desactivado del sistema.');
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/clients');
    }

    public function searchJson(Request $request): Response
    {
        $q = trim((string)$request->get('q', ''));
        $clients = $this->clientService->getAll($q, 20);
        return $this->json($clients);
    }
}

