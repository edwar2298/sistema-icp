<div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-6">
    <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center text-amber-600 mb-6 shadow-inner">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <span class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-2">Error 404 &bull; No Encontrado</span>
    <h2 class="text-2xl font-black text-slate-800 mb-2">Página o Recurso No Encontrado</h2>
    <p class="text-sm text-slate-500 max-w-md mb-6">
        La ruta solicitada no existe o el registro ha sido reubicado dentro del sistema.
    </p>
    <a href="<?= url('/dashboard') ?>" class="px-5 py-2.5 bg-slate-900 text-white font-semibold text-sm rounded-xl hover:bg-slate-800 transition-colors">
        Volver al Dashboard
    </a>
</div>

