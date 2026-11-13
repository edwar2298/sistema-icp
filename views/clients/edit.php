<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Editar Cliente</h2>
            <p class="text-xs text-slate-500">Actualizar datos fiscales y de contacto</p>
        </div>
        <a href="<?= url('/clients') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            &larr; Volver
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="<?= url("/clients/update/{$client['id']}") ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="document_type" class="block text-xs font-bold uppercase text-slate-700 mb-1">Tipo de Documento *</label>
                    <select name="document_type" id="document_type" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="RUC" <?= $client['document_type'] === 'RUC' ? 'selected' : '' ?>>RUC</option>
                        <option value="DNI" <?= $client['document_type'] === 'DNI' ? 'selected' : '' ?>>DNI</option>
                        <option value="CE" <?= $client['document_type'] === 'CE' ? 'selected' : '' ?>>CE</option>
                        <option value="PASAPORTE" <?= $client['document_type'] === 'PASAPORTE' ? 'selected' : '' ?>>Pasaporte</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="document_number" class="block text-xs font-bold uppercase text-slate-700 mb-1">Número de Documento *</label>
                    <input 
                        type="text" 
                        name="document_number" 
                        id="document_number" 
                        required 
                        value="<?= e($client['document_number']) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500"
                    >
                </div>
            </div>

            <div>
                <label for="business_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Razón Social o Nombre Completo *</label>
                <input 
                    type="text" 
                    name="business_name" 
                    id="business_name" 
                    required 
                    value="<?= e($client['business_name']) ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label for="address" class="block text-xs font-bold uppercase text-slate-700 mb-1">Dirección Fiscal / Domicilio</label>
                <input 
                    type="text" 
                    name="address" 
                    id="address" 
                    value="<?= e($client['address'] ?? '') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase text-slate-700 mb-1">Teléfono</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        value="<?= e($client['phone'] ?? '') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold uppercase text-slate-700 mb-1">Correo Electrónico</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="<?= e($client['email'] ?? '') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <div>
                    <label for="status" class="block text-xs font-bold uppercase text-slate-700 mb-1">Estado</label>
                    <select name="status" id="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="Activo" <?= $client['status'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= $client['status'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="<?= url('/clients') ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                    Actualizar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

