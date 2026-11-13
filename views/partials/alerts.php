<?php
use App\Core\Session;

$success = Session::getFlash('success');
$error = Session::getFlash('error');
$warning = Session::getFlash('warning');
$info = Session::getFlash('info');
?>

<?php if ($success): ?>
<div class="mb-5 flex items-center p-4 text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200 shadow-sm transition-all" role="alert">
    <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
    </svg>
    <div class="text-sm font-medium flex-1">
        <?= e($success) ?>
    </div>
    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 ml-3">
        <span class="sr-only">Cerrar</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="mb-5 flex items-center p-4 text-rose-800 bg-rose-50 rounded-xl border border-rose-200 shadow-sm transition-all" role="alert">
    <svg class="flex-shrink-0 w-5 h-5 mr-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
    </svg>
    <div class="text-sm font-medium flex-1">
        <?= e($error) ?>
    </div>
    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 ml-3">
        <span class="sr-only">Cerrar</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
<?php endif; ?>

<?php if ($warning): ?>
<div class="mb-5 flex items-center p-4 text-amber-800 bg-amber-50 rounded-xl border border-amber-200 shadow-sm transition-all" role="alert">
    <svg class="flex-shrink-0 w-5 h-5 mr-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
    </svg>
    <div class="text-sm font-medium flex-1">
        <?= e($warning) ?>
    </div>
    <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700 ml-3">
        <span class="sr-only">Cerrar</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
<?php endif; ?>

