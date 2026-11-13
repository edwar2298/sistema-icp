<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Editar Producto</h2>
            <p class="text-xs text-slate-500">Actualizar precios, código y umbral de seguridad</p>
        </div>
        <a href="<?= url('/products') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            &larr; Volver
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="<?= url("/products/update/{$product['id']}") ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-xs font-bold uppercase text-slate-700 mb-1">Código / SKU *</label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code" 
                        required 
                        value="<?= e($product['code']) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono uppercase focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label for="category_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Categoría *</label>
                    <select name="category_id" id="category_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Nombre del Producto *</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    value="<?= e($product['name']) ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase text-slate-700 mb-1">Descripción / Ficha Técnica</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                ><?= e($product['description'] ?? '') ?></textarea>
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
                        value="<?= e($product['purchase_price']) ?>"
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
                        value="<?= e($product['sale_price']) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:ring-2 focus:ring-blue-500"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Stock Actual</label>
                    <div class="px-3.5 py-2.5 bg-slate-200/80 border border-slate-300 rounded-xl text-sm font-mono font-black text-slate-800">
                        <?= $product['stock'] ?> unidades
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Para variar existencias use el módulo Kardex.</p>
                </div>

                <div>
                    <label for="min_stock" class="block text-xs font-bold uppercase text-slate-700 mb-1">Stock Mínimo (Alerta) *</label>
                    <input 
                        type="number" 
                        min="1"
                        name="min_stock" 
                        id="min_stock" 
                        required 
                        value="<?= e($product['min_stock']) ?>"
                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold uppercase text-slate-700 mb-1">Estado</label>
                    <select name="status" id="status" class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="Activo" <?= $product['status'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= $product['status'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="<?= url('/products') ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

