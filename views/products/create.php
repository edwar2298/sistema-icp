<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Nuevo Producto</h2>
            <p class="text-xs text-slate-500">Registro de artículo en catálogo y apertura inicial en Kardex</p>
        </div>
        <a href="<?= url('/products') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            &larr; Volver
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="<?= url('/products/store') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-xs font-bold uppercase text-slate-700 mb-1">Código / SKU *</label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code" 
                        required 
                        placeholder="Ej. LAP-DELL-5430"
                        value="<?= e(old('code')) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono uppercase focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label for="category_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Categoría *</label>
                    <select name="category_id" id="category_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Seleccione Categoría --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Nombre del Producto / Repuesto *</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    placeholder="Ej. Laptop Dell Latitude 5430 Core i7 16GB 512GB SSD"
                    value="<?= e(old('name')) ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase text-slate-700 mb-1">Descripción / Ficha Técnica</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3" 
                    placeholder="Especificaciones técnicas, puertos, compatibilidad o garantía..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                ><?= e(old('description')) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="purchase_price" class="block text-xs font-bold uppercase text-slate-700 mb-1">Precio de Compra (S/.) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0"
                        name="purchase_price" 
                        id="purchase_price" 
                        required 
                        placeholder="0.00"
                        value="<?= e(old('purchase_price', '0.00')) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="sale_price" class="block text-xs font-bold uppercase text-slate-700 mb-1">Precio de Venta (S/.) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0"
                        name="sale_price" 
                        id="sale_price" 
                        required 
                        placeholder="0.00"
                        value="<?= e(old('sale_price', '0.00')) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:ring-2 focus:ring-blue-500"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div>
                    <label for="stock" class="block text-xs font-bold uppercase text-slate-700 mb-1">Stock Inicial de Apertura *</label>
                    <input 
                        type="number" 
                        min="0"
                        name="stock" 
                        id="stock" 
                        required 
                        value="<?= e(old('stock', '0')) ?>"
                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Se creará un movimiento inicial en el Kardex.</p>
                </div>

                <div>
                    <label for="min_stock" class="block text-xs font-bold uppercase text-slate-700 mb-1">Umbral de Stock Mínimo (Alerta) *</label>
                    <input 
                        type="number" 
                        min="1"
                        name="min_stock" 
                        id="min_stock" 
                        required 
                        value="<?= e(old('min_stock', '5')) ?>"
                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Generará alerta visual al alcanzar o bajar de esta cifra.</p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="<?= url('/products') ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                    Guardar Producto
                </button>
            </div>
        </form>
    </div>
</div>

