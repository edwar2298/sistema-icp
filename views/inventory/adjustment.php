<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Ajuste Manual de Existencias</h2>
            <p class="text-xs text-slate-500">Regularización de inventario físico, mermas o ingresos por compra</p>
        </div>
        <a href="<?= url('/kardex') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            &larr; Volver al Kardex
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="<?= url('/kardex/adjustment') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label for="product_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Producto a Regularizar *</label>
                <select name="product_id" id="product_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Seleccione un Producto --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($selectedProductId === (int)$p['id']) ? 'selected' : '' ?>>
                            [<?= e($p['code']) ?>] <?= e($p['name']) ?> (Stock actual: <?= $p['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="movement_type" class="block text-xs font-bold uppercase text-slate-700 mb-1">Tipo de Ajuste *</label>
                    <select name="movement_type" id="movement_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="AJUSTE_POSITIVO">Ajuste Positivo (+) Suma existencias</option>
                        <option value="INGRESO">Ingreso de Mercadería (+) Suma existencias</option>
                        <option value="AJUSTE_NEGATIVO">Ajuste Negativo (-) Descuenta existencias</option>
                        <option value="SALIDA">Salida por Merma / Daño (-) Descuenta</option>
                    </select>
                </div>

                <div>
                    <label for="quantity" class="block text-xs font-bold uppercase text-slate-700 mb-1">Cantidad a Ajustar *</label>
                    <input 
                        type="number" 
                        min="1" 
                        name="quantity" 
                        id="quantity" 
                        required 
                        placeholder="1"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500"
                    >
                </div>
            </div>

            <div>
                <label for="reason" class="block text-xs font-bold uppercase text-slate-700 mb-1">Motivo Justificativo del Ajuste *</label>
                <textarea 
                    name="reason" 
                    id="reason" 
                    rows="3" 
                    required 
                    placeholder="Ej. Conteo físico de inventario de fin de mes, regularización de diferencia de 1 unidad..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                ></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Este motivo quedará inmutablemente registrado en la bitácora y en el Kardex.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="<?= url('/kardex') ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                    Aplicar Ajuste en Almacén
                </button>
            </div>
        </form>
    </div>
</div>

