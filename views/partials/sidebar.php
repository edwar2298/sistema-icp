<?php
use App\Core\Auth;
$role = Auth::role() ?? 'Invitado';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

$isActive = static function(string $path) use ($currentPath): bool {
    if ($path === '/dashboard' && ($currentPath === '/' || $currentPath === '/dashboard')) {
        return true;
    }
    return str_starts_with($currentPath, $path) && $path !== '/';
};
?>
<aside id="app-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 transition-transform -translate-x-full lg:translate-x-0 flex flex-col shadow-2xl">
    <!-- Brand / Logo -->
    <div class="h-16 flex items-center px-6 bg-slate-950/80 border-b border-slate-800/80">
        <a href="<?= url('/dashboard') ?>" class="flex items-center space-x-3 group">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-500 flex items-center justify-center text-white font-black text-lg shadow-md group-hover:scale-105 transition-transform">
                ICP
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-bold tracking-wide text-white uppercase leading-none">Informatics</span>
                <span class="text-[10px] text-cyan-400 font-semibold tracking-wider">Company of Perú</span>
            </div>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto custom-scrollbar">
        <!-- Dashboard -->
        <a href="<?= url('/dashboard') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/dashboard') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/dashboard') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
        </a>

        <?php if (has_role('Administrador', 'Vendedor')): ?>
        <!-- Sección Comercial -->
        <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Comercial y Ventas</div>
        
        <a href="<?= url('/sales') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/sales') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/sales') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Ventas y Comprobantes
        </a>

        <a href="<?= url('/quotations') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/quotations') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/quotations') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Cotizaciones
        </a>

        <a href="<?= url('/clients') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/clients') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/clients') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Clientes
        </a>
        <?php endif; ?>

        <!-- Sección Inventario -->
        <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Inventario y Almacén</div>
        
        <a href="<?= url('/products') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/products') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/products') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            Productos y Stock
        </a>

        <?php if (has_role('Administrador')): ?>
        <a href="<?= url('/categories') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/categories') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/categories') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Categorías
        </a>
        <?php endif; ?>

        <a href="<?= url('/kardex') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/kardex') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/kardex') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            Kardex Histórico
        </a>

        <!-- Sección Servicio Técnico -->
        <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Soporte Técnico</div>
        
        <a href="<?= url('/technical-service') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/technical-service') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/technical-service') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Órdenes de Servicio (OST)
        </a>

        <?php if (has_role('Administrador')): ?>
        <!-- Sección Administración & Configuración -->
        <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Sistema y Gerencia</div>
        
        <a href="<?= url('/reports') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/reports') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/reports') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Reportes Gerenciales
        </a>

        <a href="<?= url('/settings') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/settings') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/settings') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Configuración Fiscal & Series
        </a>

        <a href="<?= url('/backups') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/backups') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/backups') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3M4 7h16m-5 4v6m-4-6v6m-4-6v6"></path></svg>
            Respaldos de Base de Datos
        </a>

        <a href="<?= url('/audit') ?>" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive('/audit') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
            <svg class="w-5 h-5 mr-3 <?= $isActive('/audit') ? 'text-cyan-400' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Bitácora de Auditoría
        </a>
        <?php endif; ?>
    </nav>

    <!-- User Profile Footer in Sidebar -->
    <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between">
        <div class="flex items-center space-x-3 overflow-hidden">
            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-white text-xs flex-shrink-0">
                <?= strtoupper(substr(Auth::name() ?? 'U', 0, 2)) ?>
            </div>
            <div class="truncate">
                <p class="text-xs font-semibold text-white truncate"><?= e(Auth::name()) ?></p>
                <span class="inline-block text-[10px] font-medium text-cyan-400 uppercase tracking-wider"><?= e($role) ?></span>
            </div>
        </div>
        <form action="<?= url('/logout') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" title="Cerrar Sesión" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>
</aside>

