<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Bitácora de Auditoría del Sistema</h2>
        <p class="text-xs text-slate-500">Registro inmutable de actividades: creaciones, modificaciones, anulaciones e inicios de sesión</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <form action="<?= url('/audit') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Usuario</label>
            <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los usuarios</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] == $u['id']) ? 'selected' : '' ?>>
                        <?= e($u['name']) ?> (<?= e($u['role']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Acción</label>
            <select name="action" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las acciones</option>
                <option value="CREACION" <?= ($filters['action'] === 'CREACION') ? 'selected' : '' ?>>CREACIÓN</option>
                <option value="MODIFICACION" <?= ($filters['action'] === 'MODIFICACION') ? 'selected' : '' ?>>MODIFICACIÓN</option>
                <option value="ANULACION" <?= ($filters['action'] === 'ANULACION') ? 'selected' : '' ?>>ANULACIÓN</option>
                <option value="LOGIN" <?= ($filters['action'] === 'LOGIN') ? 'selected' : '' ?>>INICIO DE SESIÓN</option>
                <option value="LOGOUT" <?= ($filters['action'] === 'LOGOUT') ? 'selected' : '' ?>>CIERRE DE SESIÓN</option>
                <option value="INTENTO_FALLIDO" <?= ($filters['action'] === 'INTENTO_FALLIDO') ? 'selected' : '' ?>>INTENTO FALLIDO</option>
                <option value="BLOQUEO_CUENTA" <?= ($filters['action'] === 'BLOQUEO_CUENTA') ? 'selected' : '' ?>>BLOQUEO DE CUENTA</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tabla Afectada</label>
            <input type="text" name="entity_table" value="<?= e($filters['entity_table'] ?? '') ?>" placeholder="sales, products, etc." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Fecha Desde</label>
            <input type="date" name="start_date" value="<?= e($filters['start_date'] ?? '') ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 py-2.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-colors">
                Filtrar
            </button>
            <a href="<?= url('/audit') ?>" class="px-3 py-2.5 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-colors">
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Audit Log Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                <tr>
                    <th class="py-3 px-6">Fecha & Hora</th>
                    <th class="py-3 px-6">Usuario & Rol</th>
                    <th class="py-3 px-6 text-center">Acción</th>
                    <th class="py-3 px-6">Registro Afectado</th>
                    <th class="py-3 px-6">Dirección IP</th>
                    <th class="py-3 px-6">Detalles / Metadata</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                        No se encontraron registros de auditoría.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <?php 
                        $actionBadge = match($log['action']) {
                            'CREACION' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'MODIFICACION' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'ANULACION' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'LOGIN' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                            'LOGOUT' => 'bg-slate-100 text-slate-600',
                            'INTENTO_FALLIDO', 'BLOQUEO_CUENTA' => 'bg-rose-100 text-rose-900 border-rose-300 font-bold',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 px-6 whitespace-nowrap text-xs font-mono text-slate-500">
                            <?= format_date($log['created_at'], 'd/m/Y H:i:s') ?>
                        </td>
                        <td class="py-3.5 px-6">
                            <div class="font-bold text-slate-900 text-xs"><?= e($log['user_name'] ?? 'Sistema / Visitante') ?></div>
                            <span class="text-[10px] text-slate-400"><?= e($log['user_role'] ?? 'Anónimo') ?></span>
                        </td>
                        <td class="py-3.5 px-6 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?= $actionBadge ?>">
                                <?= e($log['action']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-xs font-mono">
                            <span class="text-slate-800 font-bold"><?= e($log['entity_table']) ?></span>
                            <?php if (!empty($log['entity_id'])): ?>
                                <span class="text-slate-400">#<?= e($log['entity_id']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-6 text-xs font-mono text-slate-500 whitespace-nowrap">
                            <?= e($log['ip_address']) ?>
                        </td>
                        <td class="py-3.5 px-6 text-xs font-mono text-slate-500 max-w-xs truncate" title="<?= e($log['details'] ?? '') ?>">
                            <?= e($log['details'] ?? '-') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

