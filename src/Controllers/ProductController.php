<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\InventoryService;
use Throwable;

class ProductController extends Controller
{
    private InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request): Response
    {
        $search = trim((string)$request->get('search', ''));
        $categoryId = $request->get('category_id') ? (int)$request->get('category_id') : null;

        $products = $this->inventoryService->getAllProducts($search, $categoryId);
        $categories = $this->inventoryService->getAllCategories();
        $lowStockProducts = $this->inventoryService->getLowStockProducts();

        return $this->render('products.index', [
            'title' => 'Control de Inventario - Productos',
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $categoryId,
            'lowStockCount' => count($lowStockProducts),
        ]);
    }

    public function create(Request $request): Response
    {
        $categories = $this->inventoryService->getAllCategories();

        return $this->render('products.create', [
            'title' => 'Registrar Nuevo Producto',
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): Response
    {
        $this->validateCsrf($request);

        $data = [
            'category_id' => $request->post('category_id', ''),
            'code' => $request->post('code', ''),
            'name' => $request->post('name', ''),
            'description' => $request->post('description', ''),
            'purchase_price' => $request->post('purchase_price', 0),
            'sale_price' => $request->post('sale_price', 0),
            'stock' => $request->post('stock', 0),
            'min_stock' => $request->post('min_stock', 5),
            'status' => 'Activo',
        ];

        try {
            $this->inventoryService->createProduct($data, $request);
            Session::setFlash('success', 'Producto registrado e inventario inicial ingresado al Kardex.');
            return $this->redirect('/products');
        } catch (Throwable $e) {
            Session::flashInput($data);
            Session::setFlash('error', $e->getMessage());
            return $this->redirect('/products/create');
        }
    }

    public function edit(Request $request, string $id): Response
    {
        $product = $this->inventoryService->findProduct((int)$id);
        if (!$product) {
            Session::setFlash('error', 'Producto no encontrado.');
            return $this->redirect('/products');
        }

        $categories = $this->inventoryService->getAllCategories();

        return $this->render('products.edit', [
            'title' => 'Editar Producto: ' . $product['name'],
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $this->validateCsrf($request);

        $data = [
            'category_id' => $request->post('category_id', ''),
            'code' => $request->post('code', ''),
            'name' => $request->post('name', ''),
            'description' => $request->post('description', ''),
            'purchase_price' => $request->post('purchase_price', 0),
            'sale_price' => $request->post('sale_price', 0),
            'min_stock' => $request->post('min_stock', 5),
            'status' => $request->post('status', 'Activo'),
        ];

        try {
            $this->inventoryService->updateProduct((int)$id, $data, $request);
            Session::setFlash('success', 'Producto actualizado correctamente.');
            return $this->redirect('/products');
        } catch (Throwable $e) {
            Session::flashInput($data);
            Session::setFlash('error', $e->getMessage());
            return $this->redirect("/products/edit/{$id}");
        }
    }

    public function searchJson(Request $request): Response
    {
        $q = trim((string)$request->get('q', ''));
        $products = $this->inventoryService->getAllProducts($q);
        return $this->json($products);
    }
}

