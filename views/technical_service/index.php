<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Servicio Técnico y Taller Especializado</h2>
        <p class="text-xs text-slate-500">Control de Órdenes de Servicio Técnico (OST), diagnósticos y control de repuestos</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/reports/service/csv?' . http_build_query($filters)) ?>" class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Exportar CSV</span>
        </a>
        <a href="<?= url('/technical-service/create') ?>" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Nueva Orden (OST)</span>
        </a>
    </div>
</div>

<!-- Status Quick Pills -->
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
    <a href="<?= url('/technical-service?status=REGISTRADA') ?>" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-blue-400 transition-colors shadow-sm flex items-center justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Registradas</span>
            <span class="text-lg font-black text-blue-900"><?= $statusCounts['REGISTRADA'] ?></span>
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
    </a>
    <a href="<?= url('/technical-service?status=EN_PROCESO') ?>" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-amber-400 transition-colors shadow-sm flex items-center justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 block">En Proceso</span>
            <span class="text-lg font-black text-amber-900"><?= $statusCounts['EN_PROCESO'] ?></span>
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
    </a>
    <a href="<?= url('/technical-service?status=EN_ESPERA_REPUESTO') ?>" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-purple-400 transition-colors shadow-sm flex items-center justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 block">En Espera Repuesto</span>
            <span class="text-lg font-black text-purple-900"><?= $statusCounts['EN_ESPERA_REPUESTO'] ?></span>
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
    </a>
    <a href="<?= url('/technical-service?status=ATENDIDA') ?>" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-emerald-400 transition-colors shadow-sm flex items-center justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Atendidas</span>
            <span class="text-lg font-black text-emerald-900"><?= $statusCounts['ATENDIDA'] ?></span>
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
    </a>
    <a href="<?= url('/technical-service?status=ENTREGADA') ?>" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-slate-400 transition-colors shadow-sm flex items-center justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Entregadas</span>
            <span class="text-lg font-black text-slate-700"><?= $statusCounts['ENTREGADA'] ?></span>
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
    </a>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/technical-service') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
        <div class="sm:col-span-2">
            <input 
                type="text" 
                name="search" 
                value="<?= e($filters['search'] ?? '') ?>" 
                placeholder="Nro OST, cliente, equipo o serie..." 
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <div>
            <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los Estados</option>
                <option value="REGISTRADA" <?= ($filters['status'] === 'REGISTRADA') ? 'selected' : '' ?>>REGISTRADA</option>
                <option value="EN_PROCESO" <?= ($filters['status'] === 'EN_PROCESO') ? 'selected' : '' ?>>EN PROCESO</option>
                <option value="EN_ESPERA_REPUESTO" <?= ($filters['status'] === 'EN_ESPERA_REPUESTO') ? 'selected' : '' ?>>EN ESPERA DE REPUESTO</option>
                <option value="ATENDIDA" <?= ($filters['status'] === 'ATENDIDA') ? 'selected' : '' ?>>ATENDIDA</option>
                <option value="ENTREGADA" <?= ($filters['status'] === 'ENTREGADA') ? 'selected' : '' ?>>ENTREGADA</option>
            </select>
        </div>

        <div>
            <select name="priority" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las Prioridades</option>
                <option value="BAJA" <?= ($filters['priority'] === 'BAJA') ? 'selected' : '' ?>>BAJA</option>
                <option value="MEDIA" <?= ($filters['priority'] === 'MEDIA') ? 'selected' : '' ?>>MEDIA</option>
                <option value="ALTA" <?= ($filters['priority'] === 'ALTA') ? 'selected' : '' ?>>ALTA</option>
                <option value="URGENTE" <?= ($filters['priority'] === 'URGENTE') ? 'selected' : '' ?>>URGENTE</option>
            </select>
        </div>

        <div class="flex items-center space-x-2">
            <button type="submit" class="flex-1 py-2 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
                Filtrar
            </button>
            <a href="<?= url('/technical-service') ?>" class="px-3 py-2 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-colors">
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- OST Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-6">Nro. OST</th>
                    <th class="py-3.5 px-6">Ingreso</th>
                    <th class="py-3.5 px-6">Cliente</th>
                    <th class="py-3.5 px-6">Equipo & Serie</th>
                    <th class="py-3.5 px-6 text-center">Prioridad</th>
                    <th class="py-3.5 px-6 text-center">Estado</th>
                    <th class="py-3.5 px-6 text-right">Costo Total</th>
                    <th class="py-3.5 px-6">Técnico</th>
                    <th class="py-3.5 px-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="9" class="py-8 text-center text-slate-400 text-sm">
                        No se encontraron órdenes de servicio técnico.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                    <?php 
                        $statusBadge = match($o['status']) {
                            'REGISTRADA' => 'bg-blue-50 text-blue-800 border-blue-200',
                            'EN_PROCESO' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'EN_ESPERA_REPUESTO' => 'bg-purple-50 text-purple-800 border-purple-200',
                            'ATENDIDA' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'ENTREGADA' => 'bg-slate-100 text-slate-800 border-slate-200',
                            default => 'bg-slate-100 text-slate-700',
                        };

                        $priorityBadge = match($o['priority']) {
                            'URGENTE' => 'bg-rose-100 text-rose-800 font-black',
                            'ALTA' => 'bg-amber-100 text-amber-800 font-bold',
                            'MEDIA' => 'bg-blue-100 text-blue-800',
                            'BAJA' => 'bg-slate-100 text-slate-600',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 whitespace-nowrap font-mono font-bold text-slate-900">
                            <?= e($o['order_number']) ?>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-500 font-mono">
                            <?= format_date($o['created_at'], 'd/m/Y') ?>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900 text-xs"><?= e($o['client_name']) ?></div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-semibold text-slate-800 text-xs"><?= e($o['equipment_type']) ?> <?= e($o['brand']) ?> <?= e($o['model']) ?></div>
                            <span class="font-mono text-[10px] text-slate-400">S/N: <?= e($o['serial_number'] ?? 'N/A') ?></span>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] uppercase <?= $priorityBadge ?>">
                                <?= e($o['priority']) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $statusBadge ?>">
                                <?= str_replace('_', ' ', e($o['status'])) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                            <?= format_money($o['total_cost']) ?>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                            <?= e($o['technician_name']) ?>
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                            <a href="<?= url("/technical-service/show/{$o['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                Gestionar
                            </a>
                            <a href="<?= url("/technical-service/print/{$o['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
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

