<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Documento Imprimible') ?> - ICP Informatics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; font-size: 11pt; }
            @page { margin: 1.2cm; size: A4; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 p-4 sm:p-8">
    <!-- Print toolbar -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center space-x-2">
            <button onclick="window.history.back()" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
                &larr; Volver
            </button>
            <span class="text-xs text-slate-500">Vista previa para impresión o exportación a PDF</span>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-5 py-2 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-md transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Imprimir / Guardar como PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Paper Area -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-lg border border-slate-200 printable-document">
        <?= $content ?? '' ?>
    </div>
</body>
</html>

