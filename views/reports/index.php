<div class="mb-8">
    <h2 class="text-xl font-black text-slate-900 tracking-tight">Centro de Reportes Gerenciales</h2>
    <p class="text-xs text-slate-500">Módulo de exportación, análisis de ventas, control de almacén y servicios técnicos</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Reporte 1: Ventas y Comprobantes -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Reporte de Ventas y Facturación</h3>
            <p class="text-xs text-slate-500 mb-6">
                Filtre ventas por rango de fechas, tipo de comprobante (Boleta/Factura) y método de pago (Efectivo/Transferencia/Crédito). Visualice métricas y exporte en formatos CSV y PDF.
            </p>
        </div>
        <div class="space-y-2">
            <a href="<?= url('/reports/sales') ?>" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center space-x-2">
                <span>Ver y Filtrar Reporte</span>
                <span>&rarr;</span>
            </a>
            <a href="<?= url('/reports/sales/csv') ?>" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-all flex items-center justify-center space-x-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Descarga Rápida CSV</span>
            </a>
        </div>
    </div>

    <!-- Reporte 2: Kardex y Movimientos de Almacén -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Reporte de Kardex e Inventario</h3>
            <p class="text-xs text-slate-500 mb-6">
                Historial completo de entradas, salidas, ventas y ajustes manuales por producto y fecha. Trazabilidad exacta de existencias.
            </p>
        </div>
        <div class="space-y-2">
            <a href="<?= url('/kardex') ?>" class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center space-x-2">
                <span>Ver Movimientos</span>
                <span>&rarr;</span>
            </a>
            <a href="<?= url('/reports/kardex/csv') ?>" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-all flex items-center justify-center space-x-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Descarga Rápida CSV</span>
            </a>
        </div>
    </div>

    <!-- Reporte 3: Taller y Órdenes de Servicio Técnico -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Reporte de Servicio Técnico (OST)</h3>
            <p class="text-xs text-slate-500 mb-6">
                Indicadores de reparaciones realizadas, horas facturadas de mano de obra y repuestos utilizados en el laboratorio técnico de ICP.
            </p>
        </div>
        <div class="space-y-2">
            <a href="<?= url('/technical-service') ?>" class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center space-x-2">
                <span>Ver Taller Técnico</span>
                <span>&rarr;</span>
            </a>
            <a href="<?= url('/reports/service/csv') ?>" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-all flex items-center justify-center space-x-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Descarga Rápida CSV</span>
            </a>
        </div>
    </div>
</div>

