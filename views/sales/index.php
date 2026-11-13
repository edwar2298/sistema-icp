<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Registro y Emisión de Ventas</h2>
        <p class="text-xs text-slate-500">Comprobantes comerciales con correlativos secuenciales (Boletas, Facturas y Notas)</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/reports/sales/csv?' . http_build_query($filters)) ?>" class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Exportar CSV</span>
        </a>
        <a href="<?= url('/sales/create') ?>" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Emitir Venta</span>
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/sales') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Buscar</label>
            <input 
                type="text" 
                name="search" 
                value="<?= e($filters['search'] ?? '') ?>" 
                placeholder="Cliente, RUC o Nro Comprobante..." 
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Comprobante</label>
            <select name="voucher_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los Tipos</option>
                <option value="FACTURA" <?= ($filters['voucher_type'] === 'FACTURA') ? 'selected' : '' ?>>FACTURA</option>
                <option value="BOLETA" <?= ($filters['voucher_type'] === 'BOLETA') ? 'selected' : '' ?>>BOLETA</option>
                <option value="NOTA_VENTA" <?= ($filters['voucher_type'] === 'NOTA_VENTA') ? 'selected' : '' ?>>NOTA DE VENTA</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Condición Pago</label>
            <select name="payment_condition" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todas</option>
                <option value="EFECTIVO" <?= ($filters['payment_condition'] === 'EFECTIVO') ? 'selected' : '' ?>>EFECTIVO</option>
                <option value="TRANSFERENCIA" <?= ($filters['payment_condition'] === 'TRANSFERENCIA') ? 'selected' : '' ?>>TRANSFERENCIA</option>
                <option value="CREDITO" <?= ($filters['payment_condition'] === 'CREDITO') ? 'selected' : '' ?>>CRÉDITO</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Desde</label>
            <input type="date" name="start_date" value="<?= e($filters['start_date'] ?? '') ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 py-2.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
                Filtrar
            </button>
            <a href="<?= url('/sales') ?>" class="px-3 py-2.5 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-colors">
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Sales Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-6">Comprobante</th>
                    <th class="py-3.5 px-6">Fecha Emisión</th>
                    <th class="py-3.5 px-6">Cliente</th>
                    <th class="py-3.5 px-6 text-center">Condición Pago</th>
                    <th class="py-3.5 px-6 text-right">Subtotal</th>
                    <th class="py-3.5 px-6 text-right">IGV</th>
                    <th class="py-3.5 px-6 text-right">Total (S/.)</th>
                    <th class="py-3.5 px-6 text-center">Estado</th>
                    <th class="py-3.5 px-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($sales)): ?>
                <tr>
                    <td colspan="9" class="py-8 text-center text-slate-400 text-sm">
                        No se registraron ventas con los parámetros seleccionados.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($sales as $s): ?>
                    <?php 
                        $isCancelled = $s['status'] === 'ANULADA';
                    ?>
                    <tr class="hover:bg-slate-50/60 transition-colors <?= $isCancelled ? 'opacity-60 bg-rose-50/20' : '' ?>">
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold <?= $s['voucher_type'] === 'FACTURA' ? 'bg-blue-100 text-blue-800' : ($s['voucher_type'] === 'BOLETA' ? 'bg-cyan-100 text-cyan-800' : 'bg-slate-100 text-slate-800') ?>">
                                <?= e($s['voucher_type']) ?>
                            </span>
                            <span class="font-mono font-bold text-slate-900 ml-1.5"><?= e($s['series']) ?>-<?= str_pad((string)$s['correlative'], 6, '0', STR_PAD_LEFT) ?></span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-500 font-mono">
                            <?= format_date($s['created_at']) ?>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900 text-xs"><?= e($s['client_name']) ?></div>
                            <span class="font-mono text-[10px] text-slate-400"><?= e($s['client_doc_number']) ?></span>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                <?= e($s['payment_condition']) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right font-mono text-xs text-slate-600 whitespace-nowrap">
                            <?= format_money($s['subtotal']) ?>
                        </td>
                        <td class="py-4 px-6 text-right font-mono text-xs text-slate-600 whitespace-nowrap">
                            <?= format_money($s['igv']) ?>
                        </td>
                        <td class="py-4 px-6 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                            <?= format_money($s['total']) ?>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $isCancelled ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' ?>">
                                <?= e($s['status']) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                            <a href="<?= url("/sales/show/{$s['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                Ver
                            </a>
                            <a href="<?= url("/sales/print/{$s['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                Imprimir
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

