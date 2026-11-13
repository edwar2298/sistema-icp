<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Nueva Cotización Comercial</h2>
            <p class="text-xs text-slate-500">Elaboración de presupuesto con cálculo en tiempo real de IGV y descuentos</p>
        </div>
        <a href="<?= url('/quotations') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            &larr; Volver
        </a>
    </div>

    <form id="quote-form" action="<?= url('/quotations/store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Client & Header Card -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Datos Principales</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="client_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Cliente *</label>
                    <select name="client_id" id="client_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Seleccione un Cliente --</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>">
                                <?= e($client['business_name']) ?> (<?= e($client['document_type']) ?>: <?= e($client['document_number']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="valid_until" class="block text-xs font-bold uppercase text-slate-700 mb-1">Válido Hasta</label>
                    <input 
                        type="date" 
                        name="valid_until" 
                        id="valid_until" 
                        value="<?= date('Y-m-d', strtotime('+15 days')) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    >
                </div>
            </div>
        </div>

        <!-- Products / Items Table Card -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ítems de la Cotización</h3>
                <button type="button" id="btn-add-item" class="px-3.5 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Agregar Producto</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="items-table">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Producto / Repuesto</th>
                            <th class="py-3 px-3 w-28 text-center">Cant.</th>
                            <th class="py-3 px-3 w-32 text-right">Precio Unit. (S/.)</th>
                            <th class="py-3 px-3 w-28 text-right">Dscto (S/.)</th>
                            <th class="py-3 px-3 w-32 text-right">Subtotal</th>
                            <th class="py-3 px-2 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="items-tbody" class="divide-y divide-slate-100">
                        <!-- Dynamic Rows will appear here -->
                    </tbody>
                </table>
            </div>

            <!-- Totals & Notes Section -->
            <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">Condiciones Comerciales / Notas</label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="4" 
                        placeholder="Ej. Tiempo de entrega: 48 horas. Garantía de 12 meses. Precios incluyen IGV..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </div>

                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-3 font-mono">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Suma de Ítems:</span>
                        <span id="display-sum" class="font-bold">S/. 0.00</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-600">
                        <span>Descuento Global (S/.):</span>
                        <input 
                            type="number" 
                            name="discount" 
                            id="global-discount" 
                            min="0" 
                            step="0.01" 
                            value="0.00" 
                            class="w-24 text-right px-2 py-1 bg-white border border-slate-300 rounded text-xs font-bold focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Base Imponible:</span>
                        <span id="display-subtotal" class="font-bold">S/. 0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>IGV (<?= e($igvRate) ?>%):</span>
                        <span id="display-igv" class="font-bold">S/. 0.00</span>
                    </div>
                    <div class="pt-3 border-t border-slate-200 flex justify-between text-base font-black text-slate-900 font-sans">
                        <span>TOTAL GENERAL:</span>
                        <span id="display-total" class="font-mono text-xl text-blue-600">S/. 0.00</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3 mt-6">
                <a href="<?= url('/quotations') ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                    Guardar Cotización
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Product catalogue dataset in JSON for fast client-side binding -->
<script>
    const availableProducts = <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>;
    const igvRatePercent = <?= (float)$igvRate ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('items-tbody');
    const btnAdd = document.getElementById('btn-add-item');
    const globalDiscountInput = document.getElementById('global-discount');

    let rowIndex = 0;

    function addRow(productId = '', quantity = 1, unitPrice = 0, discount = 0) {
        rowIndex++;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50 transition-colors item-row';
        tr.dataset.index = rowIndex;

        let options = '<option value="">-- Seleccionar --</option>';
        availableProducts.forEach(p => {
            const selected = (p.id == productId) ? 'selected' : '';
            options += `<option value="${p.id}" data-price="${p.sale_price}" data-stock="${p.stock}" ${selected}>[${p.code}] ${p.name} (Stock: ${p.stock})</option>`;
        });

        tr.innerHTML = `
            <td class="py-2.5 px-3">
                <select name="items[${rowIndex}][product_id]" required class="product-select w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                    ${options}
                </select>
            </td>
            <td class="py-2.5 px-3">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" value="${quantity}" required class="qty-input w-full text-center px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-blue-500">
            </td>
            <td class="py-2.5 px-3">
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" value="${Number(unitPrice).toFixed(2)}" required class="price-input w-full text-right px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-blue-500">
            </td>
            <td class="py-2.5 px-3">
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][discount]" value="${Number(discount).toFixed(2)}" class="discount-input w-full text-right px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-blue-500">
            </td>
            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800 row-subtotal">
                S/. 0.00
            </td>
            <td class="py-2.5 px-2 text-center">
                <button type="button" class="btn-remove-row text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Bind events
        const select = tr.querySelector('.product-select');
        const qty = tr.querySelector('.qty-input');
        const price = tr.querySelector('.price-input');
        const disc = tr.querySelector('.discount-input');
        const btnRemove = tr.querySelector('.btn-remove-row');

        select.addEventListener('change', () => {
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.price) {
                price.value = Number(selectedOpt.dataset.price).toFixed(2);
            }
            calculateTotals();
        });

        qty.addEventListener('input', calculateTotals);
        price.addEventListener('input', calculateTotals);
        disc.addEventListener('input', calculateTotals);

        btnRemove.addEventListener('click', () => {
            tr.remove();
            calculateTotals();
        });

        calculateTotals();
    }

    function calculateTotals() {
        let sum = 0;
        const rows = tbody.querySelectorAll('.item-row');

        rows.forEach(tr => {
            const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
            const price = parseFloat(tr.querySelector('.price-input').value) || 0;
            const disc = parseFloat(tr.querySelector('.discount-input').value) || 0;

            const rowTotal = Math.max(0, (qty * price) - disc);
            tr.querySelector('.row-subtotal').textContent = `S/. ${rowTotal.toFixed(2)}`;
            sum += rowTotal;
        });

        const globalDiscount = parseFloat(globalDiscountInput.value) || 0;
        const taxable = Math.max(0, sum - globalDiscount);
        const igv = taxable * (igvRatePercent / 100);
        const total = taxable + igv;

        document.getElementById('display-sum').textContent = `S/. ${sum.toFixed(2)}`;
        document.getElementById('display-subtotal').textContent = `S/. ${taxable.toFixed(2)}`;
        document.getElementById('display-igv').textContent = `S/. ${igv.toFixed(2)}`;
        document.getElementById('display-total').textContent = `S/. ${total.toFixed(2)}`;
    }

    btnAdd.addEventListener('click', () => addRow());
    globalDiscountInput.addEventListener('input', calculateTotals);

    // Initial default row
    addRow();
});
</script>

