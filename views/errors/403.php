<div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-6">
    <div class="w-20 h-20 bg-rose-100 rounded-3xl flex items-center justify-center text-rose-600 mb-6 shadow-inner">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
    </div>
    <span class="text-xs font-bold uppercase tracking-widest text-rose-600 mb-2">Error 403 &bull; Prohibido</span>
    <h2 class="text-2xl font-black text-slate-800 mb-2">Acceso Denegado</h2>
    <p class="text-sm text-slate-500 max-w-md mb-6">
        <?= e($message ?? 'No posee los permisos ni el rol requerido en su cuenta para acceder a este módulo del sistema.') ?>
    </p>
    <div class="flex items-center space-x-3">
        <a href="<?= url('/dashboard') ?>" class="px-5 py-2.5 bg-slate-900 text-white font-semibold text-sm rounded-xl hover:bg-slate-800 transition-colors">
            Volver al Dashboard
        </a>
    </div>
</div>

