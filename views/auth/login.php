<form action="<?= url('/login') ?>" method="POST" class="space-y-5">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
            Correo Electrónico Corporativo
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
            </div>
            <input 
                type="email" 
                name="email" 
                id="email" 
                required 
                autofocus
                placeholder="ejemplo@icp.com.pe"
                value="<?= e(old('email', 'admin@icp.com.pe')) ?>"
                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent text-sm transition-all"
            >
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                Contraseña
            </label>
            <span class="text-[11px] text-slate-400">Máx. 5 intentos</span>
        </div>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <input 
                type="password" 
                name="password" 
                id="password" 
                required 
                placeholder="••••••••••••"
                value="Admin123*"
                class="w-full pl-11 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent text-sm transition-all"
            >
        </div>
    </div>

    <div class="pt-2">
        <button 
            type="submit" 
            class="w-full py-3.5 px-4 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold rounded-xl shadow-lg shadow-cyan-600/30 hover:shadow-cyan-600/50 transition-all flex items-center justify-center space-x-2 text-sm"
        >
            <span>Acceder al Sistema</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
    </div>
</form>

<div class="mt-6 pt-5 border-t border-slate-800/80 text-xs text-slate-400 space-y-1.5">
    <p class="font-semibold text-slate-300">Accesos Demo Disponibles:</p>
    <div class="grid grid-cols-3 gap-2 pt-1 text-[11px]">
        <div class="p-2 rounded-lg bg-slate-800/50 border border-slate-700/50">
            <p class="font-bold text-cyan-400">Admin</p>
            <p class="truncate text-slate-400">admin@icp.com.pe</p>
        </div>
        <div class="p-2 rounded-lg bg-slate-800/50 border border-slate-700/50">
            <p class="font-bold text-blue-400">Ventas</p>
            <p class="truncate text-slate-400">ventas@icp.com.pe</p>
        </div>
        <div class="p-2 rounded-lg bg-slate-800/50 border border-slate-700/50">
            <p class="font-bold text-amber-400">Técnico</p>
            <p class="truncate text-slate-400">tecnico@icp.com.pe</p>
        </div>
    </div>
    <p class="text-slate-400 text-center text-[10px] pt-1">Clave general: <code class="text-cyan-300">Admin123*</code></p>
</div>

