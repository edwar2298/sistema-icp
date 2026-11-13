<!-- Printable Internal Sales Voucher -->
<div class="space-y-6">
    <!-- Header with Company Data & Fiscal Box -->
    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight"><?= e($company['company_name'] ?? 'ICP Informatics Company of Perú') ?></h1>
            <p class="text-xs text-slate-600 mt-1">RUC: <strong class="font-mono"><?= e($company['company_ruc'] ?? '20608976541') ?></strong></p>
            <p class="text-xs text-slate-600"><?= e($company['company_address'] ?? 'San Isidro, Lima, Perú') ?></p>
            <p class="text-xs text-slate-600">Central Telefónica: <?= e($company['company_phone'] ?? '-') ?></p>
            <p class="text-xs text-slate-600">Email: <?= e($company['company_email'] ?? '-') ?></p>
        </div>

        <div class="border-2 border-slate-900 rounded-xl p-4 text-center min-w-[240px] bg-slate-50">
            <p class="text-xs font-black uppercase text-slate-900">
                <?= $sale['voucher_type'] === 'FACTURA' ? 'FACTURA ELECTRÓNICA' : ($sale['voucher_type'] === 'BOLETA' ? 'BOLETA DE VENTA ELECTRÓNICA' : 'NOTA DE VENTA INTERNA') ?>
            </p>
            <p class="text-xl font-black font-mono text-slate-900 my-1">
                <?= e($sale['series']) ?>-<?= str_pad((string)$sale['correlative'], 6, '0', STR_PAD_LEFT) ?>
            </p>
            <p class="text-[11px] text-slate-600 font-mono">RUC: <?= e($company['company_ruc'] ?? '20608976541') ?></p>
        </div>
    </div>

    <!-- Client and Operation Metadata -->
    <div class="p-4 rounded-xl border border-slate-300 bg-slate-50/50 text-xs grid grid-cols-2 gap-4">
        <div>
            <p><span class="text-slate-500 font-semibold">CLIENTE / RAZÓN SOCIAL:</span> <strong class="text-slate-900"><?= e($sale['client_name']) ?></strong></p>
            <p class="mt-1"><span class="text-slate-500 font-semibold"><?= e($sale['client_doc_type']) ?>:</span> <span class="font-mono"><?= e($sale['client_doc_number']) ?></span></p>
            <p class="mt-1"><span class="text-slate-500 font-semibold">DIRECCIÓN:</span> <?= e($sale['client_address'] ?? '-') ?></p>
        </div>
        <div class="text-right space-y-1">
            <p><span class="text-slate-500 font-semibold">FECHA DE EMISIÓN:</span> <?= format_date($sale['created_at'], 'd/m/Y H:i') ?></p>
            <p><span class="text-slate-500 font-semibold">CONDICIÓN DE PAGO:</span> <strong><?= e($sale['payment_condition']) ?></strong></p>
            <p><span class="text-slate-500 font-semibold">MONEDA:</span> Soles (S/.)</p>
            <p><span class="text-slate-500 font-semibold">ESTADO:</span> <strong class="<?= $sale['status'] === 'EMITIDA' ? 'text-emerald-700' : 'text-rose-700' ?>"><?= e($sale['status']) ?></strong></p>
        </div>
    </div>

    <!-- Items Table -->
    <table class="w-full text-left text-xs border border-slate-300">
        <thead class="bg-slate-100 font-bold uppercase text-slate-700 border-b border-slate-300">
            <tr>
                <th class="py-2.5 px-3 border-r border-slate-300 w-12 text-center">Ítem</th>
                <th class="py-2.5 px-3 border-r border-slate-300">Descripción del Producto / Servicio</th>
                <th class="py-2.5 px-3 border-r border-slate-300 w-16 text-center">Cant.</th>
                <th class="py-2.5 px-3 border-r border-slate-300 w-24 text-right">P. Unitario</th>
                <th class="py-2.5 px-3 w-28 text-right">Importe Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            <?php foreach ($sale['items'] as $i => $item): ?>
            <tr>
                <td class="py-2 px-3 border-r border-slate-200 text-center font-mono"><?= $i + 1 ?></td>
                <td class="py-2 px-3 border-r border-slate-200 font-semibold text-slate-800">
                    <?= e($item['product_name']) ?>
                    <?php if (!empty($item['product_code'])): ?>
                        <span class="block text-[10px] text-slate-400 font-mono">SKU: <?= e($item['product_code']) ?></span>
                    <?php endif; ?>
                </td>
                <td class="py-2 px-3 border-r border-slate-200 text-center font-mono"><?= $item['quantity'] ?></td>
                <td class="py-2 px-3 border-r border-slate-200 text-right font-mono"><?= format_money($item['unit_price']) ?></td>
                <td class="py-2 px-3 text-right font-mono font-bold text-slate-900"><?= format_money($item['total']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals and Verification QR Section -->
    <div class="flex justify-between items-start gap-8 pt-4">
        <div class="flex items-center space-x-4">
            <!-- Fiscal Verification QR Box -->
            <div class="w-20 h-20 bg-slate-100 border border-slate-300 rounded-lg flex items-center justify-center p-1 text-center">
                <div class="font-mono text-[9px] text-slate-400 leading-tight">
                    [CÓDIGO QR<br>FISCAL<br>ICP PERÚ]
                </div>
            </div>
            <div class="text-[11px] text-slate-600 max-w-xs space-y-1">
                <p class="font-semibold text-slate-800">Comprobante de Pago Válido Interno</p>
                <p>Representación impresa autorizada para el control comercial y de almacén de ICP Informatics Company of Perú.</p>
                <?php if (!empty($sale['notes'])): ?>
                    <p class="italic text-[10px] text-slate-500 mt-1">Obs: <?= e($sale['notes']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-64 border border-slate-300 rounded-xl overflow-hidden text-xs font-mono">
            <div class="flex justify-between p-2 bg-slate-50 border-b border-slate-200">
                <span class="text-slate-600">Op. Gravada:</span>
                <span class="font-bold"><?= format_money($sale['subtotal']) ?></span>
            </div>
            <div class="flex justify-between p-2 bg-slate-50 border-b border-slate-200">
                <span class="text-slate-600">I.G.V. (18%):</span>
                <span class="font-bold"><?= format_money($sale['igv']) ?></span>
            </div>
            <div class="flex justify-between p-2.5 bg-slate-100 font-bold text-slate-900 text-sm">
                <span>IMPORTE TOTAL:</span>
                <span><?= format_money($sale['total']) ?></span>
            </div>
        </div>
    </div>
</div>

