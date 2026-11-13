<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\AuditService;
use Throwable;

class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepository;
    private AuditService $auditService;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        AuditService $auditService
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->auditService = $auditService;
    }

    public function index(Request $request): Response
    {
        $categories = $this->categoryRepository->getAll();

        return $this->render('categories.index', [
            'title' => 'Categorías de Productos',
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): Response
    {
        $this->validateCsrf($request);

        $name = trim((string)$request->post('name', ''));
        $description = trim((string)$request->post('description', ''));

        if ($name === '') {
            Session::setFlash('error', 'El nombre de la categoría es obligatorio.');
            return $this->redirect('/categories');
        }

        try {
            $id = $this->categoryRepository->create([
                'name' => $name,
                'description' => $description,
                'status' => 'Activo',
            ]);

            $this->auditService->log('CREACION', 'categories', (string)$id, [
                'name' => $name,
            ], $request);

            Session::setFlash('success', "Categoría '{$name}' creada correctamente.");
        } catch (Throwable $e) {
            Session::setFlash('error', 'Error al crear la categoría: ' . $e->getMessage());
        }

        return $this->redirect('/categories');
    }

    public function update(Request $request, string $id): Response
    {
        $this->validateCsrf($request);

        $name = trim((string)$request->post('name', ''));
        $description = trim((string)$request->post('description', ''));
        $status = $request->post('status', 'Activo');

        try {
            $this->categoryRepository->update((int)$id, [
                'name' => $name,
                'description' => $description,
                'status' => $status,
            ]);

            $this->auditService->log('MODIFICACION', 'categories', (string)$id, [
                'name' => $name,
                'status' => $status,
            ], $request);

            Session::setFlash('success', 'Categoría actualizada con éxito.');
        } catch (Throwable $e) {
            Session::setFlash('error', 'Error al actualizar: ' . $e->getMessage());
        }

        return $this->redirect('/categories');
    }
}

