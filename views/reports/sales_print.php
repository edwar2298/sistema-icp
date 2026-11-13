<!-- Printable Sales Report Layout -->
<div class="space-y-6">
    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 uppercase"><?= e($company['company_name'] ?? 'ICP Informatics Company of Perú') ?></h1>
            <p class="text-xs text-slate-600">RUC: <?= e($company['company_ruc'] ?? '20608976541') ?></p>
            <p class="text-xs text-slate-600">REPORTE FISCAL Y CONSOLIDADO DE VENTAS</p>
        </div>
        <div class="text-right text-xs font-mono">
            <p><strong>PERÍODO:</strong> <?= e($filters['start_date']) ?> al <?= e($filters['end_date']) ?></p>
            <p class="text-slate-500">Impreso el <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>

    <!-- Summary metrics in print -->
    <div class="grid grid-cols-4 gap-4 text-xs font-mono border border-slate-300 p-3 bg-slate-50 rounded-lg">
        <div>Total Facturado: <strong class="block text-sm"><?= format_money($report['stats']['total_sales']) ?></strong></div>
        <div>Comprobantes: <strong class="block text-sm"><?= $report['stats']['total_vouchers'] ?></strong></div>
        <div>En Efectivo: <strong class="block text-sm"><?= format_money($report['stats']['total_cash']) ?></strong></div>
        <div>En Transferencia: <strong class="block text-sm"><?= format_money($report['stats']['total_transfer']) ?></strong></div>
    </div>

    <table class="w-full text-left text-xs border border-slate-300">
        <thead class="bg-slate-100 font-bold uppercase text-slate-700 border-b border-slate-300">
            <tr>
                <th class="py-2 px-2 border-r border-slate-300">Comprobante</th>
                <th class="py-2 px-2 border-r border-slate-300">Fecha</th>
                <th class="py-2 px-2 border-r border-slate-300">Cliente</th>
                <th class="py-2 px-2 border-r border-slate-300">Condición</th>
                <th class="py-2 px-2 border-r border-slate-300 text-right">Subtotal</th>
                <th class="py-2 px-2 border-r border-slate-300 text-right">IGV</th>
                <th class="py-2 px-2 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            <?php foreach ($report['sales'] as $s): ?>
            <tr>
                <td class="py-1.5 px-2 border-r border-slate-200 font-mono"><?= e($s['voucher_type']) ?> <?= e($s['series']) ?>-<?= str_pad((string)$s['correlative'], 6, '0', STR_PAD_LEFT) ?></td>
                <td class="py-1.5 px-2 border-r border-slate-200 font-mono"><?= format_date($s['created_at'], 'd/m/Y') ?></td>
                <td class="py-1.5 px-2 border-r border-slate-200 truncate max-w-[200px]"><?= e($s['client_name']) ?></td>
                <td class="py-1.5 px-2 border-r border-slate-200"><?= e($s['payment_condition']) ?></td>
                <td class="py-1.5 px-2 border-r border-slate-200 text-right font-mono"><?= format_money($s['subtotal']) ?></td>
                <td class="py-1.5 px-2 border-r border-slate-200 text-right font-mono"><?= format_money($s['igv']) ?></td>
                <td class="py-1.5 px-2 text-right font-mono font-bold"><?= format_money($s['total']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

