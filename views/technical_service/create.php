<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Nueva Orden de Servicio Técnico (OST)</h2>
            <p class="text-xs text-slate-500">Recepción de equipo, registro de problema reportado y asignación técnica</p>
        </div>
        <a href="<?= url('/technical-service') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            &larr; Volver
        </a>
    </div>

    <form id="ost-form" action="<?= url('/technical-service/store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Client & Technician Assignment -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Recepción y Asignación</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="client_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Cliente Solicitante *</label>
                    <select name="client_id" id="client_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Seleccione Cliente --</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= e($c['business_name']) ?> (<?= e($c['document_type'] . ': ' . $c['document_number']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="technician_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Técnico Asignado *</label>
                    <select name="technician_id" id="technician_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($technicians as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= \App\Core\Auth::id() === (int)$t['id'] ? 'selected' : '' ?>>
                                <?= e($t['name']) ?> (<?= e($t['role']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Equipment Data & Priority -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Datos del Equipo en Taller</h3>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-700 mr-2">Nivel de Prioridad:</span>
                    <select name="priority" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500">
                        <option value="BAJA">Baja</option>
                        <option value="MEDIA" selected>Media (Normal)</option>
                        <option value="ALTA">Alta</option>
                        <option value="URGENTE">Urgente (Crítico)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label for="equipment_type" class="block text-xs font-bold uppercase text-slate-700 mb-1">Tipo de Equipo *</label>
                    <select name="equipment_type" id="equipment_type" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="Laptop">Laptop / Notebook</option>
                        <option value="PC Escritorio">PC de Escritorio / Workstation</option>
                        <option value="Servidor">Servidor Rack / Torre</option>
                        <option value="Impresora">Impresora / Multifuncional</option>
                        <option value="Switch / Router">Switch / Router de Red</option>
                        <option value="Otro">Otro Dispositivo</option>
                    </select>
                </div>

                <div>
                    <label for="brand" class="block text-xs font-bold uppercase text-slate-700 mb-1">Marca *</label>
                    <input type="text" name="brand" id="brand" required placeholder="Ej. Dell, Lenovo, HP" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="model" class="block text-xs font-bold uppercase text-slate-700 mb-1">Modelo *</label>
                    <input type="text" name="model" id="model" required placeholder="Ej. Latitude 5430" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="serial_number" class="block text-xs font-bold uppercase text-slate-700 mb-1">Número de Serie (S/N)</label>
                    <input type="text" name="serial_number" id="serial_number" placeholder="Ej. CN-0884920" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="problem_description" class="block text-xs font-bold uppercase text-slate-700 mb-1">Descripción Detallada del Problema / Falla Reportada *</label>
                <textarea 
                    name="problem_description" 
                    id="problem_description" 
                    rows="3" 
                    required 
                    placeholder="Detalle exactamente lo que indica el cliente (ej. El equipo enciende pero la pantalla queda en negro tras 5 minutos de uso...)"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>
        </div>

        <!-- Initial Labor & Diagnostic (Optional at intake) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Estimación de Mano de Obra (Opcional)</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="hours_worked" class="block text-xs font-bold uppercase text-slate-700 mb-1">Horas de Trabajo Estimadas</label>
                    <input type="number" step="0.5" min="0" name="hours_worked" id="hours_worked" value="0.0" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="hourly_rate" class="block text-xs font-bold uppercase text-slate-700 mb-1">Tarifa por Hora (S/.)</label>
                    <input type="number" step="0.01" min="0" name="hourly_rate" id="hourly_rate" value="45.00" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3">
            <a href="<?= url('/technical-service') ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                Registrar Orden OST
            </button>
        </div>
    </form>
</div>

