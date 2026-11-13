<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Response;
use App\Repositories\Contracts\KardexRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceOrderRepositoryInterface;

class ReportService
{
    private SaleRepositoryInterface $saleRepository;
    private KardexRepositoryInterface $kardexRepository;
    private ServiceOrderRepositoryInterface $orderRepository;

    public function __construct(
        SaleRepositoryInterface $saleRepository,
        KardexRepositoryInterface $kardexRepository,
        ServiceOrderRepositoryInterface $orderRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->kardexRepository = $kardexRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getSalesReport(array $filters): array
    {
        $sales = $this->saleRepository->getAll($filters, 500);
        $stats = $this->saleRepository->getStats($filters['start_date'] ?? null, $filters['end_date'] ?? null);

        return [
            'sales' => $sales,
            'stats' => $stats,
        ];
    }

    public function exportSalesCsv(array $filters): void
    {
        $sales = $this->saleRepository->getAll($filters, 1000);
        $filename = 'reporte_ventas_' . date('Ymd_His') . '.csv';

        $headers = [
            'ID',
            'Comprobante',
            'Fecha Emisión',
            'Cliente',
            'Doc. Cliente',
            'Condición Pago',
            'Estado Pago',
            'Subtotal (S/.)',
            'IGV (S/.)',
            'Total (S/.)',
            'Estado',
            'Vendedor',
        ];

        $rows = [];
        foreach ($sales as $s) {
            $voucher = sprintf("%s %s-%06d", $s['voucher_type'], $s['series'], $s['correlative']);
            $rows[] = [
                $s['id'],
                $voucher,
                $s['created_at'],
                $s['client_name'],
                $s['client_doc_number'],
                $s['payment_condition'],
                $s['payment_status'],
                number_format((float)$s['subtotal'], 2, '.', ''),
                number_format((float)$s['igv'], 2, '.', ''),
                number_format((float)$s['total'], 2, '.', ''),
                $s['status'],
                $s['user_name'],
            ];
        }

        Response::csv($filename, $headers, $rows);
    }

    public function exportKardexCsv(array $filters): void
    {
        $kardex = $this->kardexRepository->getAll($filters, 2000);
        $filename = 'reporte_kardex_' . date('Ymd_His') . '.csv';

        $headers = [
            'ID',
            'Fecha',
            'Código Producto',
            'Producto',
            'Tipo Movimiento',
            'Tipo Referencia',
            'Nro Referencia',
            'Cantidad',
            'Stock Anterior',
            'Stock Nuevo',
            'Costo Unitario (S/.)',
            'Motivo',
            'Usuario',
        ];

        $rows = [];
        foreach ($kardex as $k) {
            $rows[] = [
                $k['id'],
                $k['created_at'],
                $k['product_code'],
                $k['product_name'],
                $k['movement_type'],
                $k['reference_type'],
                $k['reference_id'] ?? '-',
                $k['quantity'],
                $k['previous_stock'],
                $k['new_stock'],
                number_format((float)$k['unit_cost'], 2, '.', ''),
                $k['reason'],
                $k['user_name'],
            ];
        }

        Response::csv($filename, $headers, $rows);
    }

    public function exportServiceCsv(array $filters): void
    {
        $orders = $this->orderRepository->getAll($filters, 1000);
        $filename = 'reporte_servicio_tecnico_' . date('Ymd_His') . '.csv';

        $headers = [
            'Nro OST',
            'Fecha Ingreso',
            'Cliente',
            'Equipo',
            'Marca / Modelo',
            'Nro Serie',
            'Prioridad',
            'Estado',
            'Técnico Asignado',
            'Horas Trabajadas',
            'Costo Mano Obra (S/.)',
            'Costo Repuestos (S/.)',
            'Costo Total (S/.)',
        ];

        $rows = [];
        foreach ($orders as $o) {
            $rows[] = [
                $o['order_number'],
                $o['created_at'],
                $o['client_name'],
                $o['equipment_type'],
                "{$o['brand']} {$o['model']}",
                $o['serial_number'] ?? 'N/A',
                $o['priority'],
                $o['status'],
                $o['technician_name'],
                $o['hours_worked'],
                number_format((float)$o['labor_cost'], 2, '.', ''),
                number_format((float)$o['parts_cost'], 2, '.', ''),
                number_format((float)$o['total_cost'], 2, '.', ''),
            ];
        }

        Response::csv($filename, $headers, $rows);
    }
}

