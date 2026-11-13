<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Catálogo de Productos e Inventario</h2>
        <p class="text-xs text-slate-500">Gestión de stock, precios comerciales y control de existencias mínimas</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/kardex/adjustment') ?>" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            <span>Ajustar Stock</span>
        </a>
        <a href="<?= url('/products/create') ?>" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Nuevo Producto</span>
        </a>
    </div>
</div>

<!-- Low Stock Banner Notification if present -->
<?php if ($lowStockCount > 0): ?>
<div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 rounded-lg bg-rose-200/80 text-rose-700 flex items-center justify-center font-bold text-sm">
            !
        </div>
        <div>
            <p class="text-sm font-bold">¡Atención! Hay <?= $lowStockCount ?> producto(s) en o por debajo del umbral de stock mínimo.</p>
            <p class="text-xs text-rose-600">Los productos señalizados en rojo requieren orden de reposición o compra.</p>
        </div>
    </div>
    <a href="<?= url('/kardex/adjustment') ?>" class="text-xs font-bold underline hover:text-rose-950">
        Reabastecer ahora &rarr;
    </a>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/products') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="sm:col-span-2 relative">
            <input 
                type="text" 
                name="search" 
                value="<?= e($search ?? '') ?>" 
                placeholder="Buscar por código SKU, nombre o descripción..." 
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div>
            <select name="category_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las Categorías</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($selectedCategory == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-center space-x-2">
            <button type="submit" class="flex-1 py-2.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
                Filtrar
            </button>
            <?php if (!empty($search) || !empty($selectedCategory)): ?>
                <a href="<?= url('/products') ?>" class="px-3 py-2.5 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-colors">
                    Limpiar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-6">Código / SKU</th>
                    <th class="py-3.5 px-6">Producto & Categoría</th>
                    <th class="py-3.5 px-6 text-right">P. Compra</th>
                    <th class="py-3.5 px-6 text-right">P. Venta</th>
                    <th class="py-3.5 px-6 text-center">Stock Actual</th>
                    <th class="py-3.5 px-6 text-center">Estado Alerta</th>
                    <th class="py-3.5 px-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                        No se encontraron productos registrados.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                    <?php 
                        $isLowStock = (int)$prod['stock'] <= (int)$prod['min_stock'];
                        $isZero = (int)$prod['stock'] === 0;
                    ?>
                    <tr class="<?= $isLowStock ? 'bg-rose-50/40 hover:bg-rose-50/80' : 'hover:bg-slate-50/60' ?> transition-colors">
                        <td class="py-4 px-6 whitespace-nowrap font-mono font-bold text-slate-800 text-xs">
                            <?= e($prod['code']) ?>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900"><?= e($prod['name']) ?></div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 mt-0.5">
                                <?= e($prod['category_name'] ?? 'Sin categoría') ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right text-xs font-mono text-slate-500 whitespace-nowrap">
                            <?= format_money($prod['purchase_price']) ?>
                        </td>
                        <td class="py-4 px-6 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                            <?= format_money($prod['sale_price']) ?>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-3 py-1 rounded-xl text-sm font-black <?= $isZero ? 'bg-rose-600 text-white shadow-sm' : ($isLowStock ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-800') ?>">
                                <?= $prod['stock'] ?>
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">Mín: <?= $prod['min_stock'] ?></div>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <?php if ($isZero): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800 animate-pulse border border-rose-300">
                                    Agotado (0)
                                </span>
                            <?php elseif ($isLowStock): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    Stock Bajo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">
                                    Normal
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                            <a href="<?= url("/products/edit/{$prod['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                Editar
                            </a>
                            <a href="<?= url("/kardex?product_id={$prod['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                Kardex
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

