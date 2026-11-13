<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Bar Navigation & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight font-mono"><?= e($quotation['quotation_number']) ?></h2>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold 
                    <?= $quotation['status'] === 'CONVERTIDA' ? 'bg-emerald-100 text-emerald-800' : ($quotation['status'] === 'APROBADA' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') ?>">
                    <?= e($quotation['status']) ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Emitida el <?= format_date($quotation['created_at']) ?> por <?= e($quotation['user_name']) ?></p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="<?= url('/quotations') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                &larr; Volver
            </a>
            <a href="<?= url("/quotations/print/{$quotation['id']}") ?>" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors flex items-center space-x-1">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Imprimir</span>
            </a>

            <?php if ($quotation['status'] !== 'CONVERTIDA'): ?>
            <!-- Trigger Convert to Sale Modal -->
            <button type="button" onclick="document.getElementById('modal-convert-sale').classList.remove('hidden')" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Convertir a Venta</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Quotation Card -->
    <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm space-y-8">
        <!-- Client & Issuer Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pb-6 border-b border-slate-100">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Información del Cliente</span>
                <p class="text-base font-bold text-slate-900"><?= e($quotation['client_name']) ?></p>
                <p class="text-xs text-slate-600 mt-1 font-mono"><?= e($quotation['client_doc_type']) ?>: <?= e($quotation['client_doc_number']) ?></p>
                <p class="text-xs text-slate-500 mt-0.5"><?= e($quotation['client_address'] ?? 'Dirección no especificada') ?></p>
                <p class="text-xs text-slate-500 mt-0.5">Teléfono: <?= e($quotation['client_phone'] ?? '-') ?> &bull; Correo: <?= e($quotation['client_email'] ?? '-') ?></p>
            </div>

            <div class="sm:text-right space-y-1 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Condiciones de Cotización</span>
                <p><span class="text-slate-500">Validez hasta:</span> <strong class="font-mono text-slate-800"><?= format_date($quotation['valid_until'], 'd/m/Y') ?></strong></p>
                <p><span class="text-slate-500">Moneda:</span> <strong>Soles (S/.)</strong></p>
                <p><span class="text-slate-500">Emitido por:</span> <strong><?= e($quotation['user_name']) ?></strong></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Descripción del Producto</th>
                        <th class="py-3 px-4 text-center">Cant.</th>
                        <th class="py-3 px-4 text-right">P. Unitario</th>
                        <th class="py-3 px-4 text-right">Descuento</th>
                        <th class="py-3 px-4 text-right">Importe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($quotation['items'] as $idx => $item): ?>
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
                        <td class="py-3.5 px-4 text-right font-mono text-rose-600"><?= format_money($item['discount']) ?></td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900"><?= format_money($item['total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals Breakdown -->
        <div class="flex flex-col sm:flex-row justify-between items-start pt-6 border-t border-slate-100 gap-6">
            <div class="text-xs text-slate-500 max-w-sm">
                <span class="font-bold uppercase text-slate-700 block mb-1">Notas Comerciales:</span>
                <p class="italic bg-slate-50 p-3 rounded-xl border border-slate-100"><?= nl2br(e($quotation['notes'] ?? 'Sin observaciones adicionales.')) ?></p>
            </div>

            <div class="w-full sm:w-64 space-y-2 text-xs font-mono">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Subtotal:</span>
                    <span class="font-bold text-slate-800"><?= format_money($quotation['subtotal']) ?></span>
                </div>
                <?php if ((float)$quotation['discount'] > 0): ?>
                <div class="flex justify-between py-1 border-b border-slate-100 text-rose-600">
                    <span>Descuento:</span>
                    <span class="font-bold">- <?= format_money($quotation['discount']) ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">IGV (<?= e($company['company_igv_rate'] ?? '18') ?>%):</span>
                    <span class="font-bold text-slate-800"><?= format_money($quotation['igv']) ?></span>
                </div>
                <div class="flex justify-between py-2 text-base font-black text-slate-900 font-sans border-t-2 border-slate-900">
                    <span>TOTAL:</span>
                    <span class="font-mono text-blue-600 text-lg"><?= format_money($quotation['total']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Convert to Sale -->
<div id="modal-convert-sale" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Emitir Venta desde Cotización</h3>
            <button type="button" onclick="document.getElementById('modal-convert-sale').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <p class="text-xs text-slate-500 mb-6">
            Esta acción generará la orden de venta definitiva, descontará el stock de almacén y emitirá el comprobante correlativo sin redigitar información.
        </p>

        <form action="<?= url("/quotations/convert/{$quotation['id']}") ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="voucher_type" class="block text-xs font-bold uppercase text-slate-700 mb-1">Tipo de Comprobante *</label>
                <select name="voucher_type" id="voucher_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="FACTURA" <?= $quotation['client_doc_type'] === 'RUC' ? 'selected' : '' ?>>Factura de Venta</option>
                    <option value="BOLETA" <?= $quotation['client_doc_type'] === 'DNI' ? 'selected' : '' ?>>Boleta de Venta</option>
                    <option value="NOTA_VENTA">Nota de Venta Interna</option>
                </select>
            </div>

            <div>
                <label for="payment_condition" class="block text-xs font-bold uppercase text-slate-700 mb-1">Condición de Pago *</label>
                <select name="payment_condition" id="payment_condition" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TRANSFERENCIA" selected>Transferencia Bancaria</option>
                    <option value="CREDITO">Crédito Comercial</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modal-convert-sale').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                    Confirmar y Emitir Venta
                </button>
            </div>
        </form>
    </div>
</div>

