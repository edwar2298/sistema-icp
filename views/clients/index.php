<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Gestión de Clientes</h2>
        <p class="text-xs text-slate-500">Cartera de clientes corporativos y personas naturales</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/clients/create') ?>" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Nuevo Cliente</span>
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/clients') ?>" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <input 
                type="text" 
                name="search" 
                value="<?= e($search ?? '') ?>" 
                placeholder="Buscar por razón social, nombre o número de RUC/DNI..." 
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
            Filtrar
        </button>
        <?php if (!empty($search)): ?>
            <a href="<?= url('/clients') ?>" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center">
                Limpiar
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Clients Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-6">Tipo & Documento</th>
                    <th class="py-3.5 px-6">Razón Social / Nombre</th>
                    <th class="py-3.5 px-6">Dirección</th>
                    <th class="py-3.5 px-6">Contacto</th>
                    <th class="py-3.5 px-6 text-center">Estado</th>
                    <th class="py-3.5 px-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                        No se encontraron clientes registrados con ese criterio.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($clients as $client): ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold <?= $client['document_type'] === 'RUC' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' ?> mr-1.5">
                                <?= e($client['document_type']) ?>
                            </span>
                            <span class="font-mono font-bold text-slate-900"><?= e($client['document_number']) ?></span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900"><?= e($client['business_name']) ?></div>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 max-w-xs truncate">
                            <?= e($client['address'] ?? '-') ?>
                        </td>
                        <td class="py-4 px-6 text-xs">
                            <div class="text-slate-800"><?= e($client['phone'] ?? '-') ?></div>
                            <div class="text-slate-400 text-[11px]"><?= e($client['email'] ?? '-') ?></div>
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $client['status'] === 'Activo' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                                <?= e($client['status']) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                            <a href="<?= url("/clients/edit/{$client['id']}") ?>" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                Editar
                            </a>
                            <?php if ($client['status'] === 'Activo'): ?>
                            <form action="<?= url("/clients/delete/{$client['id']}") ?>" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de desactivar a este cliente?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors">
                                    Desactivar
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

