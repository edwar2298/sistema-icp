<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Sistema de Gestión') ?> | ICP Informatics Company of Perú</title>
    <!-- Tailwind CSS 3.4 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50 flex flex-col">
    <!-- Sidebar -->
    <?php \App\Core\View::partial('sidebar'); ?>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm hidden lg:hidden"></div>

    <!-- Main Content Container with Left Margin on Desktop -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <!-- Navbar -->
        <?php \App\Core\View::partial('navbar', ['title' => $title ?? '']); ?>

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Alerts -->
            <?php \App\Core\View::partial('alerts'); ?>

            <!-- View Payload -->
            <?= $content ?? '' ?>
        </main>

        <!-- Footer -->
        <footer class="py-4 px-6 md:px-8 border-t border-slate-200 text-center sm:flex sm:justify-between sm:items-center text-xs text-slate-500 bg-white">
            <p>&copy; <?= date('Y') ?> <strong>ICP Informatics Company of Perú</strong>. Todos los derechos reservados.</p>
            <p class="mt-1 sm:mt-0 font-medium text-slate-400">Arquitectura Limpia &bull; PHP 8.x Puro &bull; MySQL 8.0</p>
        </footer>
    </div>

    <!-- Global Scripts -->
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>

