<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Categorías de Productos</h2>
        <p class="text-xs text-slate-500">Clasificación para el catálogo y control de almacén</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form to create a Category -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm h-fit">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Nueva Categoría</h3>
        <form action="<?= url('/categories/store') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Nombre de Categoría *</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    placeholder="Ej. Servidores & Redes" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div>
                <label for="description" class="block text-xs font-bold uppercase text-slate-700 mb-1">Descripción</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3" 
                    placeholder="Detalles sobre esta línea de productos..." 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                Crear Categoría
            </button>
        </form>
    </div>

    <!-- Categories List -->
    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Listado Existente</h3>
            <span class="text-xs text-slate-400 font-semibold"><?= count($categories) ?> categorías</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Nombre</th>
                        <th class="py-3 px-6">Descripción</th>
                        <th class="py-3 px-6 text-center">Productos</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-400 text-sm">No hay categorías registradas.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-900"><?= e($cat['name']) ?></td>
                            <td class="py-3.5 px-6 text-xs text-slate-500"><?= e($cat['description'] ?? '-') ?></td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    <?= (int)($cat['product_count'] ?? 0) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $cat['status'] === 'Activo' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' ?>">
                                    <?= e($cat['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

