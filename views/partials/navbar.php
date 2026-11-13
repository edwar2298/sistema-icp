<?php
use App\Core\Auth;
$role = Auth::role() ?? 'Invitado';
$roleBadgeClass = match($role) {
    'Administrador' => 'bg-purple-100 text-purple-700 border-purple-200',
    'Vendedor' => 'bg-blue-100 text-blue-700 border-blue-200',
    'Técnico' => 'bg-amber-100 text-amber-700 border-amber-200',
    default => 'bg-slate-100 text-slate-700 border-slate-200',
};
?>
<header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 md:px-8 bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm">
    <div class="flex items-center space-x-3">
        <!-- Sidebar Toggle for Mobile -->
        <button id="sidebar-toggle-btn" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 lg:hidden focus:outline-none focus:ring-2 focus:ring-cyan-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <div class="hidden sm:block">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sistema de Gestión</span>
            <h1 class="text-base font-bold text-slate-800 leading-tight"><?= e($title ?? 'ICP Informatics') ?></h1>
        </div>
    </div>

    <!-- Right Header Controls -->
    <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- Role Badge -->
        <div class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border <?= $roleBadgeClass ?>">
            <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current animate-pulse"></span>
            <?= e($role) ?>
        </div>

        <!-- Quick Action: Nueva Venta o Nueva OST -->
        <?php if (has_role('Administrador', 'Vendedor')): ?>
        <a href="<?= url('/sales/create') ?>" class="hidden md:inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-white bg-gradient-to-r from-cyan-600 to-blue-600 rounded-xl hover:from-cyan-700 hover:to-blue-700 shadow-sm transition-all">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nueva Venta
        </a>
        <?php endif; ?>

        <?php if (has_role('Administrador', 'Técnico')): ?>
        <a href="<?= url('/technical-service/create') ?>" class="hidden md:inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-300 rounded-xl hover:bg-slate-200 transition-all">
            <svg class="w-4 h-4 mr-1.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
            Nueva OST
        </a>
        <?php endif; ?>

        <!-- User Dropdown & Profile -->
        <div class="relative flex items-center">
            <div class="flex items-center space-x-2 pl-2">
                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                    <?= strtoupper(substr(Auth::name() ?? 'U', 0, 2)) ?>
                </div>
                <div class="hidden lg:block text-left">
                    <p class="text-xs font-bold text-slate-800 leading-tight"><?= e(Auth::name()) ?></p>
                    <p class="text-[11px] text-slate-500 leading-tight"><?= e(Auth::user()['email'] ?? '') ?></p>
                </div>
            </div>
        </div>
    </div>
</header>

