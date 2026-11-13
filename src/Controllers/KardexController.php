<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\InventoryService;
use Throwable;

class KardexController extends Controller
{
    private InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request): Response
    {
        $filters = [
            'product_id' => $request->get('product_id'),
            'movement_type' => $request->get('movement_type'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ];

        $kardexMovements = $this->inventoryService->getAllKardex($filters);
        $products = $this->inventoryService->getAllProducts();

        return $this->render('inventory.kardex', [
            'title' => 'Kardex - Movimientos de Almacén',
            'movements' => $kardexMovements,
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function adjustment(Request $request): Response
    {
        $products = $this->inventoryService->getAllProducts();
        $selectedProductId = $request->get('product_id') ? (int)$request->get('product_id') : null;

        return $this->render('inventory.adjustment', [
            'title' => 'Ajuste Manual de Existencias',
            'products' => $products,
            'selectedProductId' => $selectedProductId,
        ]);
    }

    public function processAdjustment(Request $request): Response
    {
        $this->validateCsrf($request);

        $productId = (int)$request->post('product_id', 0);
        $quantity = (int)$request->post('quantity', 0);
        $type = (string)$request->post('movement_type', 'AJUSTE_POSITIVO');
        $reason = trim((string)$request->post('reason', ''));

        try {
            $this->inventoryService->adjustStock($productId, $quantity, $type, $reason, $request);
            Session::setFlash('success', 'Ajuste de inventario aplicado y registrado en el Kardex.');
            return $this->redirect('/kardex');
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
            return $this->redirect('/kardex/adjustment');
        }
    }
}

