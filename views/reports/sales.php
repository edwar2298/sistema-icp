<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Reporte Comercial y de Ventas</h2>
        <p class="text-xs text-slate-500">Consolidado por período fiscal, comprobante y condición de pago</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/reports/sales/csv?' . http_build_query($filters)) ?>" class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Exportar CSV</span>
        </a>
        <a href="<?= url('/reports/sales?' . http_build_query(array_merge($filters, ['print' => 1]))) ?>" class="px-3.5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Imprimir / PDF</span>
        </a>
    </div>
</div>

<!-- Date & Category Filter Box -->
<div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/reports/sales') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Fecha Desde *</label>
            <input type="date" name="start_date" required value="<?= e($filters['start_date']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Fecha Hasta *</label>
            <input type="date" name="end_date" required value="<?= e($filters['end_date']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tipo Comprobante</label>
            <select name="voucher_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="FACTURA" <?= ($filters['voucher_type'] === 'FACTURA') ? 'selected' : '' ?>>FACTURA</option>
                <option value="BOLETA" <?= ($filters['voucher_type'] === 'BOLETA') ? 'selected' : '' ?>>BOLETA</option>
                <option value="NOTA_VENTA" <?= ($filters['voucher_type'] === 'NOTA_VENTA') ? 'selected' : '' ?>>NOTA DE VENTA</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Condición de Pago</label>
            <select name="payment_condition" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todas</option>
                <option value="EFECTIVO" <?= ($filters['payment_condition'] === 'EFECTIVO') ? 'selected' : '' ?>>EFECTIVO</option>
                <option value="TRANSFERENCIA" <?= ($filters['payment_condition'] === 'TRANSFERENCIA') ? 'selected' : '' ?>>TRANSFERENCIA</option>
                <option value="CREDITO" <?= ($filters['payment_condition'] === 'CREDITO') ? 'selected' : '' ?>>CRÉDITO</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full py-2.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
                Generar Reporte
            </button>
        </div>
    </form>
</div>

<!-- Summary Metrics Bar -->
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400">Total Facturado</span>
        <p class="text-xl font-black text-slate-900 mt-1"><?= format_money($report['stats']['total_sales']) ?></p>
    </div>
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400">Comprobantes Válidos</span>
        <p class="text-xl font-black text-blue-600 mt-1"><?= $report['stats']['total_vouchers'] ?></p>
    </div>
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400">En Efectivo</span>
        <p class="text-xl font-black text-emerald-600 mt-1"><?= format_money($report['stats']['total_cash']) ?></p>
    </div>
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400">En Transferencia</span>
        <p class="text-xl font-black text-indigo-600 mt-1"><?= format_money($report['stats']['total_transfer']) ?></p>
    </div>
</div>

<!-- Results Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-6">Comprobante</th>
                    <th class="py-3.5 px-6">Fecha</th>
                    <th class="py-3.5 px-6">Cliente</th>
                    <th class="py-3.5 px-6">Pago</th>
                    <th class="py-3.5 px-6 text-right">Subtotal</th>
                    <th class="py-3.5 px-6 text-right">IGV</th>
                    <th class="py-3.5 px-6 text-right">Total</th>
                    <th class="py-3.5 px-6 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($report['sales'])): ?>
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                        No hay ventas para el rango de fechas indicado.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($report['sales'] as $s): ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                            <?= e($s['voucher_type']) ?> <?= e($s['series']) ?>-<?= str_pad((string)$s['correlative'], 6, '0', STR_PAD_LEFT) ?>
                        </td>
                        <td class="py-3.5 px-6 font-mono text-xs text-slate-500">
                            <?= format_date($s['created_at'], 'd/m/Y') ?>
                        </td>
                        <td class="py-3.5 px-6 text-xs font-semibold text-slate-800">
                            <?= e($s['client_name']) ?>
                        </td>
                        <td class="py-3.5 px-6 text-xs font-mono">
                            <?= e($s['payment_condition']) ?>
                        </td>
                        <td class="py-3.5 px-6 text-right font-mono text-xs">
                            <?= format_money($s['subtotal']) ?>
                        </td>
                        <td class="py-3.5 px-6 text-right font-mono text-xs">
                            <?= format_money($s['igv']) ?>
                        </td>
                        <td class="py-3.5 px-6 text-right font-mono font-bold text-slate-900 text-xs">
                            <?= format_money($s['total']) ?>
                        </td>
                        <td class="py-3.5 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold <?= $s['status'] === 'EMITIDA' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                                <?= e($s['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

