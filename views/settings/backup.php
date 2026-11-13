<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Respaldos de Base de Datos (Backups)</h2>
            <p class="text-xs text-slate-500">Copias de seguridad completas de esquemas, relaciones y datos transaccionales</p>
        </div>
        <form action="<?= url('/backups/create') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Crear Copia de Seguridad Ahora</span>
            </button>
        </form>
    </div>

    <!-- Instructions for Automated Daily Backups -->
    <div class="bg-gradient-to-r from-slate-900 to-blue-950 p-6 rounded-3xl text-white shadow-xl space-y-3">
        <h3 class="text-sm font-bold text-cyan-400 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Programación de Respaldos Diarios Automáticos (CLI / Cron)
        </h3>
        <p class="text-xs text-slate-300 leading-relaxed">
            El sistema incluye un script CLI puro ejecutable sin dependencias externas en <code class="text-cyan-300 bg-slate-800/80 px-2 py-0.5 rounded">bin/backup.php</code>. Para programar la ejecución automática diaria a las 02:00 AM, configure su programador del servidor:
        </p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div class="bg-slate-950/80 p-3 rounded-xl border border-slate-800 font-mono text-[11px] text-cyan-300 overflow-x-auto">
                <span class="text-slate-500 font-sans block text-[10px] uppercase font-bold mb-1">Linux / macOS (Cron):</span>
                0 2 * * * php /ruta/proyecto/bin/backup.php >> /dev/null 2>&1
            </div>
            <div class="bg-slate-950/80 p-3 rounded-xl border border-slate-800 font-mono text-[11px] text-cyan-300 overflow-x-auto">
                <span class="text-slate-500 font-sans block text-[10px] uppercase font-bold mb-1">Windows Task Scheduler:</span>
                php.exe "C:\Users\...\bin\backup.php"
            </div>
        </div>
    </div>

    <!-- Backups List Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Archivos SQL Almacenados en el Servidor</h3>
            <span class="text-xs font-semibold text-slate-500"><?= count($backups) ?> respaldos disponibles</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Archivo SQL</th>
                        <th class="py-3 px-6">Fecha y Hora de Creación</th>
                        <th class="py-3 px-6 text-right">Tamaño</th>
                        <th class="py-3 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($backups)): ?>
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-400 text-sm">
                            No se han generado copias de seguridad aún. Haga clic en el botón superior para crear la primera.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($backups as $b): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 text-xs flex items-center space-x-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span><?= e($b['filename']) ?></span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500 font-mono">
                                <?= format_date($b['created_at']) ?>
                            </td>
                            <td class="py-4 px-6 text-right text-xs font-mono font-bold text-slate-700">
                                <?= round($b['size'] / 1024, 2) ?> KB
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="<?= url("/backups/download/{$b['filename']}") ?>" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Descargar</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

