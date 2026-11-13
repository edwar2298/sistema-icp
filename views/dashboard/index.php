<!-- Welcome Banner -->
<div class="mb-8 p-6 bg-gradient-to-r from-slate-900 via-blue-950 to-cyan-900 rounded-3xl text-white shadow-xl relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 mr-2 animate-ping"></span>
                Operaciones en Tiempo Real
            </div>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight">Bienvenido, <?= e(\App\Core\Auth::name()) ?></h2>
            <p class="text-sm text-slate-300 mt-1 max-w-xl">
                Panel central de control: Ventas, Kardex de inventario, Servicio Técnico y Facturación para ICP Informatics Company of Perú.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <?php if (has_role('Administrador', 'Vendedor')): ?>
            <a href="<?= url('/sales/create') ?>" class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Nueva Venta</span>
            </a>
            <?php endif; ?>
            <?php if (has_role('Administrador', 'Técnico')): ?>
            <a href="<?= url('/technical-service/create') ?>" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs uppercase tracking-wider rounded-xl border border-slate-700 transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                <span>Nueva OST</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- KPI Metrics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Ventas del Mes -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ventas del Mes</span>
            <div class="w-10 h-10 rounded-2xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-slate-900"><?= format_money($metrics['month_sales']) ?></p>
        <p class="text-xs text-slate-500 mt-1 flex items-center">
            <span class="text-emerald-600 font-semibold mr-1">Hoy: <?= format_money($metrics['today_sales']) ?></span>
            &bull; <?= $metrics['month_vouchers'] ?> comprobantes
        </p>
    </div>

    <!-- Comprobantes Emitidos -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Comprobantes Emitidos</span>
            <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-slate-900"><?= $metrics['month_vouchers'] ?></p>
        <p class="text-xs text-slate-500 mt-1">
            <span class="text-blue-600 font-semibold"><?= $metrics['today_vouchers'] ?> emitidos hoy</span> (Boletas y Facturas)
        </p>
    </div>

    <!-- Ticket Promedio -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ticket Promedio</span>
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-slate-900"><?= format_money($metrics['month_avg_ticket']) ?></p>
        <p class="text-xs text-slate-500 mt-1">
            Promedio de facturación por cliente en el período
        </p>
    </div>

    <!-- Alertas de Stock Mínimo -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Alertas de Stock</span>
            <div class="w-10 h-10 rounded-2xl <?= $metrics['low_stock_count'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' ?> flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
        <p class="text-2xl font-black <?= $metrics['low_stock_count'] > 0 ? 'text-rose-600' : 'text-slate-900' ?>">
            <?= $metrics['low_stock_count'] ?> <span class="text-sm font-semibold text-slate-500">productos</span>
        </p>
        <p class="text-xs text-slate-500 mt-1">
            <?= $metrics['low_stock_count'] > 0 ? '<span class="text-rose-600 font-bold">Atención requerida</span> en almacén' : 'Niveles de inventario óptimos' ?>
        </p>
    </div>
</div>

<!-- Stock Low Alert Banner if any -->
<?php if (!empty($metrics['low_stock_products'])): ?>
<div class="mb-8 p-5 rounded-2xl bg-rose-50/90 border border-rose-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-rose-900">Alerta Crítica de Inventario: <?= count($metrics['low_stock_products']) ?> producto(s) en umbral mínimo</h3>
            <p class="text-xs text-rose-700">
                <?php foreach ($metrics['low_stock_products'] as $lp): ?>
                    <span class="inline-block mr-2 font-medium">&bull; <strong><?= e($lp['name']) ?></strong> (Stock: <span class="underline"><?= $lp['stock'] ?></span> / Mín: <?= $lp['min_stock'] ?>)</span>
                <?php endforeach; ?>
            </p>
        </div>
    </div>
    <a href="<?= url('/kardex/adjustment') ?>" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all whitespace-nowrap">
        Ajustar / Reabastecer Stock
    </a>
</div>
<?php endif; ?>

<!-- Central 2-Column Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Columna 1 & 2: Productos Más Vendidos y Estado OST -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Top Selling Products -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Productos Más Vendidos</h3>
                    <p class="text-xs text-slate-500">Ranking según volumen comercial y recaudación</p>
                </div>
                <a href="<?= url('/products') ?>" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">Ver todo &rarr;</a>
            </div>

            <?php if (empty($metrics['top_products'])): ?>
                <p class="text-sm text-slate-400 py-6 text-center">No hay registros de ventas para el período seleccionado.</p>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($metrics['top_products'] as $index => $prod): ?>
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                                <?= $index + 1 ?>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-800"><?= e($prod['product_name']) ?></p>
                                <p class="text-xs text-slate-400 font-mono"><?= e($prod['product_code'] ?? 'SKU-N/A') ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-900"><?= format_money($prod['total_revenue']) ?></p>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-700">
                                <?= $prod['total_sold'] ?> unidades vendidas
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Órdenes de Servicio Técnico (Estado) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Servicio Técnico (OST) en Taller</h3>
                    <p class="text-xs text-slate-500">Distribución de estados en soporte técnico</p>
                </div>
                <a href="<?= url('/technical-service') ?>" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">Ver Taller &rarr;</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-center">
                    <span class="text-xs font-semibold text-blue-700 uppercase">Registradas</span>
                    <p class="text-2xl font-black text-blue-900 mt-1"><?= $metrics['service_stats']['REGISTRADA'] ?></p>
                </div>
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-100 text-center">
                    <span class="text-xs font-semibold text-amber-700 uppercase">En Proceso</span>
                    <p class="text-2xl font-black text-amber-900 mt-1"><?= $metrics['service_stats']['EN_PROCESO'] ?></p>
                </div>
                <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-100 text-center">
                    <span class="text-xs font-semibold text-purple-700 uppercase">Espera Repuesto</span>
                    <p class="text-2xl font-black text-purple-900 mt-1"><?= $metrics['service_stats']['EN_ESPERA_REPUESTO'] ?></p>
                </div>
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-center">
                    <span class="text-xs font-semibold text-emerald-700 uppercase">Atendidas</span>
                    <p class="text-2xl font-black text-emerald-900 mt-1"><?= $metrics['service_stats']['ATENDIDA'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna 3: Ventas Recientes -->
    <div class="space-y-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-800">Últimas Ventas</h3>
                <a href="<?= url('/sales') ?>" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">Historial &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                <?php if (empty($metrics['recent_sales'])): ?>
                    <p class="text-xs text-slate-400 py-4 text-center">No hay ventas recientes.</p>
                <?php else: ?>
                    <?php foreach ($metrics['recent_sales'] as $sale): ?>
                    <div class="py-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs font-bold text-slate-800">
                                <?= e($sale['voucher_type']) ?> <?= e($sale['series']) ?>-<?= str_pad((string)$sale['correlative'], 6, '0', STR_PAD_LEFT) ?>
                            </span>
                            <span class="text-xs font-black text-slate-900"><?= format_money($sale['total']) ?></span>
                        </div>
                        <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                            <span class="truncate max-w-[150px]"><?= e($sale['client_name']) ?></span>
                            <span><?= format_date($sale['created_at'], 'd/m H:i') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Atajos Rápidos -->
        <div class="bg-slate-900 text-slate-200 p-6 rounded-3xl shadow-xl">
            <h4 class="text-sm font-bold text-white mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Atajos y Acciones Rápidas
            </h4>
            <div class="space-y-2 text-xs">
                <?php if (has_role('Administrador', 'Vendedor')): ?>
                <a href="<?= url('/quotations/create') ?>" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
                    <span>Generar Cotización</span>
                    <span>&rarr;</span>
                </a>
                <a href="<?= url('/clients/create') ?>" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
                    <span>Registrar Cliente (RUC/DNI)</span>
                    <span>&rarr;</span>
                </a>
                <?php endif; ?>
                <a href="<?= url('/kardex') ?>" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
                    <span>Consultar Kardex de Almacén</span>
                    <span>&rarr;</span>
                </a>
                <?php if (has_role('Administrador')): ?>
                <a href="<?= url('/backups') ?>" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
                    <span>Generar Respaldo BD</span>
                    <span>&rarr;</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

