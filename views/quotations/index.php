<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Cotizaciones Comerciales</h2>
        <p class="text-xs text-slate-500">Propuestas comerciales valorizadas con opción de conversión a venta</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/quotations/create') ?>" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Nueva Cotización</span>
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/quotations') ?>" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <input 
                type="text" 
                name="search" 
                value="<?= e($search ?? '') ?>" 
                placeholder="Buscar por correlativo COT, cliente o documento..." 
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div class="w-full sm:w-48">
            <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los Estados</option>
                <option value="PENDIENTE" <?= ($selectedStatus === 'PENDIENTE') ? 'selected' : '' ?>>PENDIENTE</option>
                <option value="APROBADA" <?= ($selectedStatus === 'APROBADA') ? 'selected' : '' ?>>APROBADA</option>
                <option value="RECHAZADA" <?= ($selectedStatus === 'RECHAZADA') ? 'selected' : '' ?>>RECHAZADA</option>
                <option value="CONVERTIDA" <?= ($selectedStatus === 'CONVERTIDA') ? 'selected' : '' ?>>CONVERTIDA A VENTA</option>
            </select>
        </div>

        <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
            Filtrar
        </button>
        <?php if (!empty($search) || !empty($selectedStatus)): ?>
            <a href="<?= url('/quotations') ?>" class="px-3 py-2.5 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center">
                Limpiar
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Quotations Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-6">Nro. Cotización</th>
                    <th class="py-3.5 px-6">Fecha Emisión</th>
                    <th class="py-3.5 px-6">Cliente</th>
                    <th class="py-3.5 px-6 text-right">Total (S/.)</th>
                    <th class="py-3.5 px-6 text-center">Estado</th>
                    <th class="py-3.5 px-6">Vendedor</th>
                    <th class="py-3.5 px-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($quotations)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                        No se encontraron cotizaciones registradas.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($quotations as $q): ?>
                    <?php 
                        $statusBadge = match($q['status']) {
                            'PENDIENTE' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'APROBADA' => 'bg-blue-50 text-blue-800 border-blue-200',
                            'CONVERTIDA' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'RECHAZADA' => 'bg-rose-50 text-rose-800 border-rose-200',
                            default => 'bg-slate-100 text-slate-800',
                        };
                    ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 whitespace-nowrap font-mono font-bold text-slate-900">
                            <?= e($q['quotation_number']) ?>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-500 font-mono">
                            <?= format_date($q['created_at'], 'd/m/Y') ?>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900"><?= e($q['client_name']) ?></div>
                            <span class="text-[11px] text-slate-400 font-mono"><?= e($q['document_number'] ?? '') ?></span>
                        </td>
                        <td class="py-4 px-6 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                            <?= format_money($q['total']) ?>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $statusBadge ?>">
                                <?= e($q['status']) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                            <?= e($q['user_name']) ?>
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                            <a href="<?= url("/quotations/show/{$q['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                Ver Detalle
                            </a>
                            <a href="<?= url("/quotations/print/{$q['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
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

