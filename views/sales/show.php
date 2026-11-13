<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Bar Navigation & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <span class="inline-block px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider <?= $sale['voucher_type'] === 'FACTURA' ? 'bg-blue-600 text-white' : ($sale['voucher_type'] === 'BOLETA' ? 'bg-cyan-600 text-white' : 'bg-slate-800 text-white') ?>">
                    <?= e($sale['voucher_type']) ?>
                </span>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight font-mono"><?= e($sale['series']) ?>-<?= str_pad((string)$sale['correlative'], 6, '0', STR_PAD_LEFT) ?></h2>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold <?= $sale['status'] === 'EMITIDA' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                    <?= e($sale['status']) ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Emitido el <?= format_date($sale['created_at']) ?> por <?= e($sale['user_name']) ?></p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="<?= url('/sales') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                &larr; Volver al Listado
            </a>
            <a href="<?= url("/sales/print/{$sale['id']}") ?>" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors flex items-center space-x-1">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Imprimir Comprobante</span>
            </a>

            <?php if ($sale['status'] === 'EMITIDA'): ?>
            <button type="button" onclick="document.getElementById('modal-cancel-sale').classList.remove('hidden')" class="px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-all">
                Anular Venta
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Voucher Container -->
    <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm space-y-8">
        <!-- Client & Issuer Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pb-6 border-b border-slate-100">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Adquiriente / Cliente</span>
                <p class="text-base font-bold text-slate-900"><?= e($sale['client_name']) ?></p>
                <p class="text-xs text-slate-600 mt-1 font-mono"><?= e($sale['client_doc_type']) ?>: <?= e($sale['client_doc_number']) ?></p>
                <p class="text-xs text-slate-500 mt-0.5"><?= e($sale['client_address'] ?? 'Dirección no especificada') ?></p>
                <p class="text-xs text-slate-500 mt-0.5">Teléfono: <?= e($sale['client_phone'] ?? '-') ?> &bull; Email: <?= e($sale['client_email'] ?? '-') ?></p>
            </div>

            <div class="sm:text-right space-y-1 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Detalles de Operación</span>
                <p><span class="text-slate-500">Condición de Pago:</span> <strong class="text-slate-800"><?= e($sale['payment_condition']) ?></strong></p>
                <p><span class="text-slate-500">Estado del Pago:</span> <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold <?= $sale['payment_status'] === 'PAGADO' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>"><?= e($sale['payment_status']) ?></span></p>
                <?php if (!empty($sale['quotation_number'])): ?>
                    <p><span class="text-slate-500">Cotización Origen:</span> <strong class="font-mono text-blue-600"><?= e($sale['quotation_number']) ?></strong></p>
                <?php endif; ?>
                <p><span class="text-slate-500">Moneda:</span> <strong>Soles (S/.)</strong></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Descripción del Ítem</th>
                        <th class="py-3 px-4 text-center">Cantidad</th>
                        <th class="py-3 px-4 text-right">Precio Unitario</th>
                        <th class="py-3 px-4 text-right">Importe Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($sale['items'] as $idx => $item): ?>
                    <tr>
                        <td class="py-3.5 px-4 font-mono text-xs text-slate-400"><?= $idx + 1 ?></td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800"><?= e($item['product_name']) ?></span>
                            <?php if (!empty($item['product_code'])): ?>
                                <span class="block text-[11px] font-mono text-slate-400"><?= e($item['product_code']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold font-mono text-slate-900"><?= $item['quantity'] ?></td>
                        <td class="py-3.5 px-4 text-right font-mono"><?= format_money($item['unit_price']) ?></td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900"><?= format_money($item['total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals Breakdown -->
        <div class="flex flex-col sm:flex-row justify-between items-start pt-6 border-t border-slate-100 gap-6">
            <div class="text-xs text-slate-500 max-w-sm">
                <span class="font-bold uppercase text-slate-700 block mb-1">Notas / Observaciones:</span>
                <p class="italic bg-slate-50 p-3 rounded-xl border border-slate-100"><?= nl2br(e($sale['notes'] ?? 'Sin observaciones.')) ?></p>
            </div>

            <div class="w-full sm:w-64 space-y-2 text-xs font-mono">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Operación Gravada (Subtotal):</span>
                    <span class="font-bold text-slate-800"><?= format_money($sale['subtotal']) ?></span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">I.G.V. (<?= e($company['company_igv_rate'] ?? '18') ?>%):</span>
                    <span class="font-bold text-slate-800"><?= format_money($sale['igv']) ?></span>
                </div>
                <div class="flex justify-between py-2 text-base font-black text-slate-900 font-sans border-t-2 border-slate-900">
                    <span>IMPORTE TOTAL:</span>
                    <span class="font-mono text-blue-600 text-lg"><?= format_money($sale['total']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cancel Sale -->
<?php if ($sale['status'] === 'EMITIDA'): ?>
<div id="modal-cancel-sale" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-rose-700">Anulación de Comprobante</h3>
            <button type="button" onclick="document.getElementById('modal-cancel-sale').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <p class="text-xs text-slate-600 mb-4">
            ¿Está seguro de anular la venta <strong class="font-mono"><?= e($sale['series']) ?>-<?= str_pad((string)$sale['correlative'], 6, '0', STR_PAD_LEFT) ?></strong>? Esta operación devolverá automáticamente las cantidades al inventario de almacén mediante una transacción y registrará la anulación en el Kardex y en la bitácora de auditoría.
        </p>

        <form action="<?= url("/sales/cancel/{$sale['id']}") ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="cancel_reason" class="block text-xs font-bold uppercase text-slate-700 mb-1">Motivo de Anulación *</label>
                <textarea 
                    name="cancel_reason" 
                    id="cancel_reason" 
                    rows="3" 
                    required 
                    placeholder="Ej. Error de digitación, devolución por desistimiento del cliente..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-rose-500"
                ></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modal-cancel-sale').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                    Cerrar
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                    Confirmar Anulación
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

