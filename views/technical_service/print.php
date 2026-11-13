<!-- Printable Technical Service Conformity Document -->
<div class="space-y-6">
    <!-- Header with Company Data & OST Box -->
    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight"><?= e($company['company_name'] ?? 'ICP Informatics Company of Perú') ?></h1>
            <p class="text-xs text-slate-600 mt-1">RUC: <strong class="font-mono"><?= e($company['company_ruc'] ?? '20608976541') ?></strong></p>
            <p class="text-xs text-slate-600">Servicio Técnico Especializado en Hardware & Redes</p>
            <p class="text-xs text-slate-600"><?= e($company['company_address'] ?? 'San Isidro, Lima') ?></p>
            <p class="text-xs text-slate-600">Central: <?= e($company['company_phone'] ?? '-') ?> &bull; <?= e($company['company_email'] ?? '-') ?></p>
        </div>

        <div class="border-2 border-slate-900 rounded-xl p-4 text-center min-w-[240px] bg-slate-50">
            <p class="text-xs font-black uppercase text-slate-900">ACTA DE SERVICIO TÉCNICO Y CONFORMIDAD</p>
            <p class="text-xl font-black font-mono text-slate-900 my-1"><?= e($order['order_number']) ?></p>
            <p class="text-[11px] text-slate-600 font-mono">ESTADO: <?= str_replace('_', ' ', e($order['status'])) ?></p>
        </div>
    </div>

    <!-- Client & Equipment Info -->
    <div class="p-4 rounded-xl border border-slate-300 bg-slate-50/50 text-xs grid grid-cols-2 gap-4">
        <div>
            <p><span class="text-slate-500 font-semibold">CLIENTE / TITULAR:</span> <strong class="text-slate-900"><?= e($order['client_name']) ?></strong></p>
            <p class="mt-1"><span class="text-slate-500 font-semibold"><?= e($order['client_doc_type']) ?>:</span> <span class="font-mono"><?= e($order['client_doc_number']) ?></span></p>
            <p class="mt-1"><span class="text-slate-500 font-semibold">TELÉFONO / CONTACTO:</span> <?= e($order['client_phone'] ?? '-') ?></p>
            <p class="mt-1"><span class="text-slate-500 font-semibold">DIRECCIÓN:</span> <?= e($order['client_address'] ?? '-') ?></p>
        </div>
        <div class="space-y-1">
            <p><span class="text-slate-500 font-semibold">TIPO DE EQUIPO:</span> <strong><?= e($order['equipment_type']) ?></strong></p>
            <p><span class="text-slate-500 font-semibold">MARCA / MODELO:</span> <strong><?= e($order['brand']) ?> <?= e($order['model']) ?></strong></p>
            <p><span class="text-slate-500 font-semibold">NÚMERO DE SERIE (S/N):</span> <strong class="font-mono"><?= e($order['serial_number'] ?? 'N/A') ?></strong></p>
            <p><span class="text-slate-500 font-semibold">TÉCNICO ESPECIALISTA:</span> <?= e($order['technician_name']) ?></p>
            <p><span class="text-slate-500 font-semibold">FECHA DE INGRESO:</span> <?= format_date($order['created_at'], 'd/m/Y') ?></p>
        </div>
    </div>

    <!-- Problem & Diagnostic -->
    <div class="space-y-3 text-xs">
        <div>
            <p class="font-bold uppercase text-slate-800 bg-slate-100 p-1.5 rounded border border-slate-200">1. Falla / Síntoma Reportado por el Cliente</p>
            <p class="p-2.5 text-slate-700 bg-white border border-slate-200 rounded mt-1"><?= nl2br(e($order['problem_description'])) ?></p>
        </div>

        <div>
            <p class="font-bold uppercase text-slate-800 bg-slate-100 p-1.5 rounded border border-slate-200">2. Diagnóstico Técnico y Causa Raíz</p>
            <p class="p-2.5 text-slate-700 bg-white border border-slate-200 rounded mt-1"><?= nl2br(e($order['diagnostic'] ?? 'Evaluación de laboratorio concluida satisfactoriamente.')) ?></p>
        </div>

        <div>
            <p class="font-bold uppercase text-slate-800 bg-slate-100 p-1.5 rounded border border-slate-200">3. Solución / Procedimientos Efectuados</p>
            <p class="p-2.5 text-slate-700 bg-white border border-slate-200 rounded mt-1"><?= nl2br(e($order['work_performed'] ?? 'Trabajo ejecutado según estándares de calidad de ICP.')) ?></p>
        </div>
    </div>

    <!-- Spare Parts Table if any -->
    <?php if (!empty($order['parts'])): ?>
    <div>
        <p class="font-bold uppercase text-slate-800 text-xs mb-1">4. Repuestos y Componentes Instalados:</p>
        <table class="w-full text-left text-xs border border-slate-300">
            <thead class="bg-slate-100 font-bold uppercase text-slate-700 border-b border-slate-300">
                <tr>
                    <th class="py-2 px-3 border-r border-slate-300">Descripción del Repuesto</th>
                    <th class="py-2 px-3 border-r border-slate-300 w-16 text-center">Cant.</th>
                    <th class="py-2 px-3 border-r border-slate-300 w-24 text-right">P. Unitario</th>
                    <th class="py-2 px-3 w-28 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php foreach ($order['parts'] as $part): ?>
                <tr>
                    <td class="py-1.5 px-3 border-r border-slate-200 font-semibold text-slate-800"><?= e($part['part_name']) ?></td>
                    <td class="py-1.5 px-3 border-r border-slate-200 text-center font-mono"><?= $part['quantity'] ?></td>
                    <td class="py-1.5 px-3 border-r border-slate-200 text-right font-mono"><?= format_money($part['unit_price']) ?></td>
                    <td class="py-1.5 px-3 text-right font-mono font-bold"><?= format_money($part['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Cost Breakdown -->
    <div class="flex justify-end pt-2">
        <div class="w-64 border border-slate-300 rounded-xl overflow-hidden text-xs font-mono">
            <div class="flex justify-between p-2 bg-slate-50 border-b border-slate-200">
                <span class="text-slate-600">Mano de Obra (<?= $order['hours_worked'] ?>h):</span>
                <span class="font-bold"><?= format_money($order['labor_cost']) ?></span>
            </div>
            <div class="flex justify-between p-2 bg-slate-50 border-b border-slate-200">
                <span class="text-slate-600">Repuestos:</span>
                <span class="font-bold"><?= format_money($order['parts_cost']) ?></span>
            </div>
            <div class="flex justify-between p-2.5 bg-slate-100 font-bold text-slate-900 text-sm">
                <span>TOTAL SERVICIO:</span>
                <span><?= format_money($order['total_cost']) ?></span>
            </div>
        </div>
    </div>

    <!-- Conformity Statement & Signatures -->
    <div class="pt-6 border-t border-slate-200 text-[11px] text-slate-600 space-y-2">
        <p class="font-bold uppercase text-slate-800">Términos de Conformidad y Garantía:</p>
        <p class="leading-relaxed">
            El cliente declara recibir el equipo a entera satisfacción, habiendo verificado el correcto encendido, operatividad del sistema y estado estético en que fue entregado. ICP Informatics Company of Perú otorga una garantía de noventa (90) días calendario sobre la mano de obra y repuestos detallados en este documento, no cubriendo fallas derivadas de fluctuaciones eléctricas, golpes o manipulación de terceros no autorizados.
        </p>
    </div>

    <div class="pt-20 grid grid-cols-2 gap-12 text-center text-xs">
        <div>
            <div class="border-t border-slate-400 w-56 mx-auto mb-1"></div>
            <p class="font-bold text-slate-800"><?= e($order['technician_name']) ?></p>
            <p class="text-slate-500 text-[10px]">Especialista Técnico / Taller ICP</p>
        </div>
        <div>
            <div class="border-t border-slate-400 w-56 mx-auto mb-1"></div>
            <p class="font-bold text-slate-800"><?= e($order['client_name']) ?></p>
            <p class="text-slate-500 text-[10px]">Firma de Conformidad del Cliente (DNI / Sello)</p>
            <p class="text-slate-400 text-[9px] mt-0.5">Fecha de Entrega: _____/_____/2026</p>
        </div>
    </div>
</div>

