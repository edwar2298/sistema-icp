<?php
// State machine transitions map
$currentStatus = $order['status'];
$allowedTransitions = match($currentStatus) {
    'REGISTRADA' => ['EN_PROCESO'],
    'EN_PROCESO' => ['EN_ESPERA_REPUESTO', 'ATENDIDA'],
    'EN_ESPERA_REPUESTO' => ['EN_PROCESO', 'ATENDIDA'],
    'ATENDIDA' => ['ENTREGADA', 'EN_PROCESO'],
    default => [],
};

$statusBadge = match($currentStatus) {
    'REGISTRADA' => 'bg-blue-100 text-blue-800 border-blue-200',
    'EN_PROCESO' => 'bg-amber-100 text-amber-800 border-amber-200',
    'EN_ESPERA_REPUESTO' => 'bg-purple-100 text-purple-800 border-purple-200',
    'ATENDIDA' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'ENTREGADA' => 'bg-slate-200 text-slate-800 border-slate-300',
    default => 'bg-slate-100 text-slate-700',
};
?>
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Orden de Servicio</span>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight font-mono"><?= e($order['order_number']) ?></h2>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border <?= $statusBadge ?>">
                    <?= str_replace('_', ' ', e($currentStatus)) ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Ingresado el <?= format_date($order['created_at']) ?> &bull; Técnico: <strong><?= e($order['technician_name']) ?></strong></p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="<?= url('/technical-service') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                &larr; Volver
            </a>
            <a href="<?= url("/technical-service/print/{$order['id']}") ?>" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-md transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Imprimir Hoja de Conformidad</span>
            </a>
        </div>
    </div>

    <!-- State Machine & Status Advancement Card -->
    <?php if (!empty($allowedTransitions)): ?>
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 p-6 rounded-3xl shadow-sm">
        <h3 class="text-xs font-black uppercase tracking-wider text-blue-900 mb-2 flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
            Máquina de Estados de la Orden
        </h3>
        <p class="text-xs text-blue-700 mb-4">
            Avance el ciclo de vida del equipo en taller. Los cambios quedan registrados en la bitácora histórica.
        </p>

        <form action="<?= url("/technical-service/status/{$order['id']}") ?>" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
            <?= csrf_field() ?>
            <div class="w-full sm:w-64">
                <select name="status" required class="w-full px-3.5 py-2.5 bg-white border border-blue-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    <?php foreach ($allowedTransitions as $next): ?>
                        <option value="<?= $next ?>">&rarr; Pasar a: <?= str_replace('_', ' ', $next) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex-1 w-full">
                <input type="text" name="comments" placeholder="Nota u observación del cambio de estado..." class="w-full px-3.5 py-2.5 bg-white border border-blue-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all whitespace-nowrap">
                Actualizar Estado
            </button>
        </form>
    </div>
    <?php else: ?>
    <div class="bg-slate-100 border border-slate-200 p-4 rounded-2xl text-xs text-slate-600 flex items-center">
        <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>Esta orden ha sido <strong>ENTREGADA</strong> al cliente con firma de conformidad. El ciclo se encuentra cerrado.</span>
    </div>
    <?php endif; ?>

    <!-- Main Order Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Technical Details & Work -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Equipment & Client Summary -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cliente</span>
                        <p class="text-sm font-bold text-slate-900"><?= e($order['client_name']) ?></p>
                        <p class="text-xs text-slate-500 font-mono"><?= e($order['client_doc_type']) ?>: <?= e($order['client_doc_number']) ?></p>
                        <p class="text-xs text-slate-500">Tel: <?= e($order['client_phone'] ?? '-') ?></p>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Equipo en Taller</span>
                        <p class="text-sm font-bold text-slate-900"><?= e($order['equipment_type']) ?> <?= e($order['brand']) ?> <?= e($order['model']) ?></p>
                        <p class="text-xs text-slate-500 font-mono">S/N: <?= e($order['serial_number'] ?? 'N/A') ?></p>
                        <p class="text-xs font-semibold text-slate-600">Prioridad: <span class="uppercase text-blue-600"><?= e($order['priority']) ?></span></p>
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Falla Reportada por el Cliente</span>
                    <p class="text-xs bg-slate-50 p-3 rounded-xl border border-slate-100 text-slate-700"><?= nl2br(e($order['problem_description'])) ?></p>
                </div>
            </div>

            <!-- Technical Form: Diagnostic & Work Done -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Informe de Trabajo y Liquidación de Horas</h3>
                
                <form action="<?= url("/technical-service/update/{$order['id']}") ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="client_id" value="<?= $order['client_id'] ?>">
                    <input type="hidden" name="technician_id" value="<?= $order['technician_id'] ?>">
                    <input type="hidden" name="equipment_type" value="<?= e($order['equipment_type']) ?>">
                    <input type="hidden" name="brand" value="<?= e($order['brand']) ?>">
                    <input type="hidden" name="model" value="<?= e($order['model']) ?>">
                    <input type="hidden" name="serial_number" value="<?= e($order['serial_number']) ?>">
                    <input type="hidden" name="problem_description" value="<?= e($order['problem_description']) ?>">
                    <input type="hidden" name="priority" value="<?= e($order['priority']) ?>">
                    <input type="hidden" name="customer_notes" value="<?= e($order['customer_notes'] ?? '') ?>">

                    <div>
                        <label for="diagnostic" class="block text-xs font-bold uppercase text-slate-700 mb-1">Diagnóstico Técnico Especializado</label>
                        <textarea name="diagnostic" id="diagnostic" rows="3" placeholder="Detalle técnico de las pruebas efectuadas y causa raíz de la avería..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500"><?= e($order['diagnostic'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label for="work_performed" class="block text-xs font-bold uppercase text-slate-700 mb-1">Trabajo Realizado / Procedimientos Ejecutados</label>
                        <textarea name="work_performed" id="work_performed" rows="3" placeholder="Mantenimiento realizado, reparaciones a nivel de componentes o reinstalación..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500"><?= e($order['work_performed'] ?? '') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <label for="hours_worked" class="block text-xs font-bold uppercase text-slate-700 mb-1">Horas de Trabajo Invertidas</label>
                            <input type="number" step="0.5" min="0" name="hours_worked" id="hours_worked" value="<?= e($order['hours_worked']) ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="hourly_rate" class="block text-xs font-bold uppercase text-slate-700 mb-1">Tarifa Horaria (S/.)</label>
                            <input type="number" step="0.01" min="0" name="hourly_rate" id="hourly_rate" value="<?= e($order['hourly_rate']) ?>" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow transition-all">
                            Guardar Informe & Horas
                        </button>
                    </div>
                </form>
            </div>

            <!-- Spare Parts Table -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Repuestos Utilizados en el Servicio</h3>
                    <span class="text-xs font-mono font-bold text-slate-700">Total Repuestos: <?= format_money($order['parts_cost']) ?></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 font-bold uppercase text-slate-400 border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Repuesto / Componente</th>
                                <th class="py-2.5 px-3 text-center">Cant.</th>
                                <th class="py-2.5 px-3 text-right">Precio Unit.</th>
                                <th class="py-2.5 px-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($order['parts'])): ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">No se requirieron repuestos físicos en este servicio.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($order['parts'] as $part): ?>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800">
                                        <?= e($part['part_name']) ?>
                                        <?php if (!empty($part['product_code'])): ?>
                                            <span class="block text-[10px] text-slate-400 font-mono">SKU: <?= e($part['product_code']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold"><?= $part['quantity'] ?></td>
                                    <td class="py-2.5 px-3 text-right font-mono"><?= format_money($part['unit_price']) ?></td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900"><?= format_money($part['subtotal']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Cost Summary & Status History -->
        <div class="space-y-6">
            <!-- Cost Summary Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3 font-mono">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 font-sans mb-3">Liquidación de Costos</h4>
                <div class="flex justify-between text-xs text-slate-600">
                    <span>Mano de Obra (<?= $order['hours_worked'] ?>h):</span>
                    <span class="font-bold text-slate-800"><?= format_money($order['labor_cost']) ?></span>
                </div>
                <div class="flex justify-between text-xs text-slate-600">
                    <span>Repuestos Utilizados:</span>
                    <span class="font-bold text-slate-800"><?= format_money($order['parts_cost']) ?></span>
                </div>
                <div class="pt-3 border-t-2 border-slate-900 flex justify-between text-base font-black text-slate-900 font-sans">
                    <span>TOTAL SERVICIO:</span>
                    <span class="font-mono text-xl text-blue-600"><?= format_money($order['total_cost']) ?></span>
                </div>
            </div>

            <!-- Status Timeline History -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Historial de Trazabilidad</h4>
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    <?php foreach ($order['history'] as $h): ?>
                    <div class="relative">
                        <div class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-blue-600 ring-4 ring-white"></div>
                        <p class="text-xs font-bold text-slate-800"><?= str_replace('_', ' ', e($h['to_status'])) ?></p>
                        <p class="text-[10px] text-slate-400 font-mono"><?= format_date($h['created_at']) ?> por <?= e($h['user_name']) ?></p>
                        <?php if (!empty($h['comments'])): ?>
                            <p class="text-[11px] text-slate-600 bg-slate-50 p-2 rounded-lg mt-1 border border-slate-100"><?= e($h['comments']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

