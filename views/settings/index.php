<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Configuración del Sistema</h2>
            <p class="text-xs text-slate-500">Datos fiscales de la empresa, tasa de IGV y correlativos de comprobantes</p>
        </div>
        <a href="<?= url('/backups') ?>" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3M4 7h16m-5 4v6m-4-6v6m-4-6v6"></path></svg>
            <span>Ir a Respaldos (Backups)</span>
        </a>
    </div>

    <form action="<?= url('/settings/update') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Fiscal Data Section -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Identidad Fiscal de la Empresa</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="company_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Razón Social *</label>
                    <input type="text" name="company_name" id="company_name" required value="<?= e($settings['company_name'] ?? 'ICP Informatics Company of Perú') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="company_trade_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Nombre Comercial</label>
                    <input type="text" name="company_trade_name" id="company_trade_name" value="<?= e($settings['company_trade_name'] ?? 'ICP Informatics Perú') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="company_ruc" class="block text-xs font-bold uppercase text-slate-700 mb-1">Número de RUC *</label>
                    <input type="text" name="company_ruc" id="company_ruc" required value="<?= e($settings['company_ruc'] ?? '20608976541') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="company_phone" class="block text-xs font-bold uppercase text-slate-700 mb-1">Teléfonos de Contacto</label>
                    <input type="text" name="company_phone" id="company_phone" value="<?= e($settings['company_phone'] ?? '+51 1 719-5400') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="company_email" class="block text-xs font-bold uppercase text-slate-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="company_email" id="company_email" value="<?= e($settings['company_email'] ?? 'contacto@icp.com.pe') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="company_address" class="block text-xs font-bold uppercase text-slate-700 mb-1">Dirección Fiscal Completa</label>
                <input type="text" name="company_address" id="company_address" value="<?= e($settings['company_address'] ?? 'Av. Rivera Navarrete 501, San Isidro, Lima, Perú') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Impuestos & Parámetros -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">2. Parámetros Tributarios y Monetarios</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="company_igv_rate" class="block text-xs font-bold uppercase text-slate-700 mb-1">Porcentaje Actual del IGV (%) *</label>
                    <input type="number" step="0.01" min="0" max="100" name="company_igv_rate" id="company_igv_rate" required value="<?= e($settings['company_igv_rate'] ?? '18.00') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500">
                    <p class="text-[11px] text-slate-400 mt-1">Tasa aplicada a cotizaciones y comprobantes.</p>
                </div>

                <div>
                    <label for="company_currency_symbol" class="block text-xs font-bold uppercase text-slate-700 mb-1">Símbolo de Moneda</label>
                    <input type="text" name="company_currency_symbol" id="company_currency_symbol" value="<?= e($settings['company_currency_symbol'] ?? 'S/.') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Series y Correlativos de Comprobantes -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">3. Configuración de Series y Correlativos</h3>
            <p class="text-xs text-slate-500">Defina las series numéricas y los siguientes correlativos a emitir por el sistema.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Boletas -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="text-xs font-bold uppercase text-slate-800">Boletas de Venta</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Serie</label>
                            <input type="text" name="serie_boleta" value="<?= e($settings['serie_boleta'] ?? 'B001') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Siguiente Correlativo</label>
                            <input type="number" min="1" name="correlativo_boleta" value="<?= e($settings['correlativo_boleta'] ?? '1') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono">
                        </div>
                    </div>
                </div>

                <!-- Facturas -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="text-xs font-bold uppercase text-slate-800">Facturas de Venta</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Serie</label>
                            <input type="text" name="serie_factura" value="<?= e($settings['serie_factura'] ?? 'F001') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Siguiente Correlativo</label>
                            <input type="number" min="1" name="correlativo_factura" value="<?= e($settings['correlativo_factura'] ?? '1') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono">
                        </div>
                    </div>
                </div>

                <!-- Notas de Venta -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="text-xs font-bold uppercase text-slate-800">Notas de Venta Internas</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Serie</label>
                            <input type="text" name="serie_nota" value="<?= e($settings['serie_nota'] ?? 'NV01') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Siguiente Correlativo</label>
                            <input type="number" min="1" name="correlativo_nota" value="<?= e($settings['correlativo_nota'] ?? '1') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono">
                        </div>
                    </div>
                </div>

                <!-- Órdenes de Servicio OST -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <span class="text-xs font-bold uppercase text-slate-800">Servicio Técnico (OST)</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Serie</label>
                            <input type="text" name="serie_ost" value="<?= e($settings['serie_ost'] ?? 'OST-2026') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Siguiente Correlativo</label>
                            <input type="number" min="1" name="correlativo_ost" value="<?= e($settings['correlativo_ost'] ?? '1') ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-mono">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all">
                Guardar Configuraciones
            </button>
        </div>
    </form>
</div>

