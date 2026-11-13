<!-- Printable Quotation Document -->
<div class="space-y-6">
    <!-- Header with Company Data & Fiscal Box -->
    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight"><?= e($company['company_name'] ?? 'ICP Informatics Company of Perú') ?></h1>
            <p class="text-xs text-slate-600 mt-1">RUC: <strong class="font-mono"><?= e($company['company_ruc'] ?? '20608976541') ?></strong></p>
            <p class="text-xs text-slate-600"><?= e($company['company_address'] ?? 'San Isidro, Lima') ?></p>
            <p class="text-xs text-slate-600">Tel: <?= e($company['company_phone'] ?? '-') ?> &bull; Email: <?= e($company['company_email'] ?? '-') ?></p>
        </div>

        <div class="border-2 border-slate-900 rounded-xl p-4 text-center min-w-[220px] bg-slate-50">
            <p class="text-xs font-bold uppercase text-slate-700">COTIZACIÓN</p>
            <p class="text-lg font-black font-mono text-slate-900 my-1"><?= e($quotation['quotation_number']) ?></p>
            <p class="text-[11px] text-slate-500 font-mono">FECHA: <?= format_date($quotation['created_at'], 'd/m/Y') ?></p>
        </div>
    </div>

    <!-- Client Info -->
    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 text-xs grid grid-cols-2 gap-4">
        <div>
            <p><span class="text-slate-500 font-semibold">SEÑOR(ES):</span> <strong class="text-slate-900"><?= e($quotation['client_name']) ?></strong></p>
            <p class="mt-1"><span class="text-slate-500 font-semibold"><?= e($quotation['client_doc_type']) ?>:</span> <span class="font-mono"><?= e($quotation['client_doc_number']) ?></span></p>
            <p class="mt-1"><span class="text-slate-500 font-semibold">DIRECCIÓN:</span> <?= e($quotation['client_address'] ?? '-') ?></p>
        </div>
        <div class="text-right space-y-1">
            <p><span class="text-slate-500 font-semibold">ASESOR COMERCIAL:</span> <?= e($quotation['user_name']) ?></p>
            <p><span class="text-slate-500 font-semibold">VALIDEZ DE OFERTA:</span> <?= format_date($quotation['valid_until'], 'd/m/Y') ?></p>
            <p><span class="text-slate-500 font-semibold">MONEDA:</span> Soles (S/.)</p>
        </div>
    </div>

    <!-- Items Table -->
    <table class="w-full text-left text-xs border border-slate-300">
        <thead class="bg-slate-100 font-bold uppercase text-slate-700 border-b border-slate-300">
            <tr>
                <th class="py-2.5 px-3 border-r border-slate-300 w-10 text-center">Ítem</th>
                <th class="py-2.5 px-3 border-r border-slate-300">Descripción del Producto / Servicio</th>
                <th class="py-2.5 px-3 border-r border-slate-300 w-16 text-center">Cant.</th>
                <th class="py-2.5 px-3 border-r border-slate-300 w-24 text-right">P. Unitario</th>
                <th class="py-2.5 px-3 border-r border-slate-300 w-20 text-right">Dscto</th>
                <th class="py-2.5 px-3 w-28 text-right">Importe Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            <?php foreach ($quotation['items'] as $i => $item): ?>
            <tr>
                <td class="py-2 px-3 border-r border-slate-200 text-center font-mono"><?= $i + 1 ?></td>
                <td class="py-2 px-3 border-r border-slate-200 font-semibold text-slate-800"><?= e($item['product_name']) ?></td>
                <td class="py-2 px-3 border-r border-slate-200 text-center font-mono"><?= $item['quantity'] ?></td>
                <td class="py-2 px-3 border-r border-slate-200 text-right font-mono"><?= format_money($item['unit_price']) ?></td>
                <td class="py-2 px-3 border-r border-slate-200 text-right font-mono"><?= format_money($item['discount']) ?></td>
                <td class="py-2 px-3 text-right font-mono font-bold"><?= format_money($item['total']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals and Terms -->
    <div class="flex justify-between items-start gap-8 pt-4">
        <div class="text-[11px] text-slate-600 max-w-sm space-y-1">
            <p class="font-bold uppercase text-slate-800">Términos y Condiciones:</p>
            <p><?= nl2br(e($quotation['notes'] ?? 'Los precios incluyen el Impuesto General a las Ventas (IGV). Sujeto a disponibilidad de inventario.')) ?></p>
        </div>

        <div class="w-60 border border-slate-300 rounded-xl overflow-hidden text-xs font-mono">
            <div class="flex justify-between p-2 bg-slate-50 border-b border-slate-200">
                <span class="text-slate-600">Subtotal:</span>
                <span class="font-bold"><?= format_money($quotation['subtotal']) ?></span>
            </div>
            <?php if ((float)$quotation['discount'] > 0): ?>
            <div class="flex justify-between p-2 bg-slate-50 border-b border-slate-200 text-rose-600">
                <span>Descuento:</span>
                <span class="font-bold">- <?= format_money($quotation['discount']) ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between p-2 bg-slate-50 border-b border-slate-200">
                <span class="text-slate-600">IGV (18%):</span>
                <span class="font-bold"><?= format_money($quotation['igv']) ?></span>
            </div>
            <div class="flex justify-between p-2.5 bg-slate-100 font-bold text-slate-900 text-sm">
                <span>TOTAL:</span>
                <span><?= format_money($quotation['total']) ?></span>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="pt-16 grid grid-cols-2 gap-12 text-center text-xs">
        <div>
            <div class="border-t border-slate-400 w-48 mx-auto mb-1"></div>
            <p class="font-bold text-slate-800"><?= e($company['company_name'] ?? 'ICP Informatics') ?></p>
            <p class="text-slate-500 text-[10px]">Asesor Comercial Responsable</p>
        </div>
        <div>
            <div class="border-t border-slate-400 w-48 mx-auto mb-1"></div>
            <p class="font-bold text-slate-800">Aceptación de la Propuesta</p>
            <p class="text-slate-500 text-[10px]">Firma y Sello del Cliente</p>
        </div>
    </div>
</div>

