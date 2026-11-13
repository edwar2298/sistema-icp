<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Kardex de Movimientos de Almacén</h2>
        <p class="text-xs text-slate-500">Trazabilidad física y documental de ingresos, salidas, ventas y ajustes</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/reports/kardex/csv?' . http_build_query($filters)) ?>" class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Exportar CSV</span>
        </a>
        <a href="<?= url('/kardex/adjustment') ?>" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Registrar Ajuste</span>
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/kardex') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Producto</label>
            <select name="product_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los productos</option>
                <?php foreach ($products as $prod): ?>
                    <option value="<?= $prod['id'] ?>" <?= ($filters['product_id'] == $prod['id']) ? 'selected' : '' ?>>
                        <?= e($prod['code']) ?> - <?= e($prod['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tipo de Movimiento</label>
            <select name="movement_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="INGRESO" <?= ($filters['movement_type'] === 'INGRESO') ? 'selected' : '' ?>>INGRESO</option>
                <option value="SALIDA" <?= ($filters['movement_type'] === 'SALIDA') ? 'selected' : '' ?>>SALIDA</option>
                <option value="VENTA" <?= ($filters['movement_type'] === 'VENTA') ? 'selected' : '' ?>>VENTA</option>
                <option value="ANULACION_VENTA" <?= ($filters['movement_type'] === 'ANULACION_VENTA') ? 'selected' : '' ?>>ANULACIÓN VENTA</option>
                <option value="AJUSTE_POSITIVO" <?= ($filters['movement_type'] === 'AJUSTE_POSITIVO') ? 'selected' : '' ?>>AJUSTE (+)</option>
                <option value="AJUSTE_NEGATIVO" <?= ($filters['movement_type'] === 'AJUSTE_NEGATIVO') ? 'selected' : '' ?>>AJUSTE (-)</option>
                <option value="SERVICIO_TECNICO" <?= ($filters['movement_type'] === 'SERVICIO_TECNICO') ? 'selected' : '' ?>>SERVICIO TÉCNICO</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Desde</label>
            <input type="date" name="start_date" value="<?= e($filters['start_date'] ?? '') ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Hasta</label>
            <input type="date" name="end_date" value="<?= e($filters['end_date'] ?? '') ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 py-2.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
                Filtrar
            </button>
            <a href="<?= url('/kardex') ?>" class="px-3 py-2.5 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-colors">
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Kardex Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-6">Fecha & Hora</th>
                    <th class="py-3.5 px-6">Producto</th>
                    <th class="py-3.5 px-6 text-center">Tipo Movimiento</th>
                    <th class="py-3.5 px-6">Referencia</th>
                    <th class="py-3.5 px-6 text-center">Cant.</th>
                    <th class="py-3.5 px-6 text-center">Previo &rarr; Nuevo</th>
                    <th class="py-3.5 px-6">Motivo / Detalle</th>
                    <th class="py-3.5 px-6">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($movements)): ?>
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                        No se encontraron movimientos registrados con los filtros aplicados.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($movements as $m): ?>
                    <?php 
                        $isPositive = in_array($m['movement_type'], ['INGRESO', 'AJUSTE_POSITIVO', 'ANULACION_VENTA'], true);
                        $badgeBg = $isPositive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200';
                    ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 px-6 whitespace-nowrap text-xs text-slate-500 font-mono">
                            <?= format_date($m['created_at']) ?>
                        </td>
                        <td class="py-3.5 px-6">
                            <div class="font-bold text-slate-900 text-xs"><?= e($m['product_name']) ?></div>
                            <span class="font-mono text-[10px] text-slate-400"><?= e($m['product_code']) ?></span>
                        </td>
                        <td class="py-3.5 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border <?= $badgeBg ?>">
                                <?= $isPositive ? '+' : '-' ?> <?= e($m['movement_type']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-6 whitespace-nowrap text-xs font-mono text-slate-700">
                            <?= e($m['reference_id'] ?? '-') ?>
                        </td>
                        <td class="py-3.5 px-6 text-center font-bold whitespace-nowrap font-mono <?= $isPositive ? 'text-emerald-600' : 'text-rose-600' ?>">
                            <?= $isPositive ? '+' : '-' ?><?= $m['quantity'] ?>
                        </td>
                        <td class="py-3.5 px-6 text-center whitespace-nowrap text-xs font-mono">
                            <span class="text-slate-400"><?= $m['previous_stock'] ?></span>
                            <span class="text-slate-300 mx-1">&rarr;</span>
                            <span class="font-bold text-slate-800"><?= $m['new_stock'] ?></span>
                        </td>
                        <td class="py-3.5 px-6 text-xs text-slate-600 max-w-xs truncate" title="<?= e($m['reason']) ?>">
                            <?= e($m['reason']) ?>
                        </td>
                        <td class="py-3.5 px-6 whitespace-nowrap text-xs text-slate-500">
                            <?= e($m['user_name']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

