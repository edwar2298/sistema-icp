<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Iniciar Sesión') ?> | ICP Informatics Company of Perú</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full antialiased flex flex-col justify-center items-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-slate-100">
    <div class="w-full max-w-md">
        <!-- Logo / Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 items-center justify-center text-white font-black text-2xl shadow-xl shadow-cyan-500/20 mb-4 ring-4 ring-cyan-500/10">
                ICP
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white uppercase">ICP Informatics</h1>
            <p class="text-xs font-semibold text-cyan-400 tracking-wider uppercase mt-0.5">Company of Perú</p>
            <p class="text-sm text-slate-400 mt-2">Sistema Centralizado de Gestión Empresarial</p>
        </div>

        <!-- Card Container -->
        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Flash Alerts -->
            <?php \App\Core\View::partial('alerts'); ?>

            <!-- View content -->
            <?= $content ?? '' ?>
        </div>

        <div class="mt-8 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> ICP Informatics Company of Perú. Sistema Seguro.
        </div>
    </div>
</body>
</html>

