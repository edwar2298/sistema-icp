<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Nueva Venta / Emisión de Comprobante</h2>
            <p class="text-xs text-slate-500">Facturación directa con descuento automático de existencias en tiempo real</p>
        </div>
        <a href="<?= url('/sales') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            &larr; Volver
        </a>
    </div>

    <form id="sale-form" action="<?= url('/sales/store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Voucher and Client Details -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Datos del Comprobante y Cliente</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="voucher_type" class="block text-xs font-bold uppercase text-slate-700 mb-1">Tipo de Comprobante *</label>
                    <select name="voucher_type" id="voucher_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-blue-500">
                        <option value="BOLETA" data-corr="<?= e($correlatives['BOLETA']['formatted']) ?>">Boleta de Venta (<?= e($correlatives['BOLETA']['formatted']) ?>)</option>
                        <option value="FACTURA" data-corr="<?= e($correlatives['FACTURA']['formatted']) ?>">Factura de Venta (<?= e($correlatives['FACTURA']['formatted']) ?>)</option>
                        <option value="NOTA_VENTA" data-corr="<?= e($correlatives['NOTA_VENTA']['formatted']) ?>">Nota de Venta (<?= e($correlatives['NOTA_VENTA']['formatted']) ?>)</option>
                    </select>
                </div>

                <div>
                    <label for="payment_condition" class="block text-xs font-bold uppercase text-slate-700 mb-1">Condición de Pago *</label>
                    <select name="payment_condition" id="payment_condition" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="EFECTIVO">Efectivo al Contado</option>
                        <option value="TRANSFERENCIA">Transferencia Bancaria (BCP / BBVA / Interbank)</option>
                        <option value="CREDITO">Crédito Comercial (Pendiente de cobro)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Correlativo Asignado</label>
                    <div id="display-correlative" class="px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-mono font-black text-slate-800">
                        <?= e($correlatives['BOLETA']['formatted']) ?>
                    </div>
                </div>
            </div>

            <div>
                <label for="client_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Cliente Receptor *</label>
                <select name="client_id" id="client_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Seleccione Cliente --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" data-doctype="<?= e($c['document_type']) ?>">
                            <?= e($c['business_name']) ?> (<?= e($c['document_type']) ?>: <?= e($c['document_number']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Line Items Card -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Detalle de Productos a Vender</h3>
                <button type="button" id="btn-add-item" class="px-3.5 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Agregar Ítem</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="items-table">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Producto / SKU</th>
                            <th class="py-3 px-3 w-28 text-center">Stock Disp.</th>
                            <th class="py-3 px-3 w-28 text-center">Cantidad</th>
                            <th class="py-3 px-3 w-36 text-right">Precio Unit. (S/.)</th>
                            <th class="py-3 px-3 w-36 text-right">Total Ítem</th>
                            <th class="py-3 px-2 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="items-tbody" class="divide-y divide-slate-100">
                        <!-- Dynamic Rows will appear here -->
                    </tbody>
                </table>
            </div>

            <!-- Notes & Total Calculations -->
            <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">Observaciones / Guía de Remisión Referencial</label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        placeholder="Nro de operación bancaria, orden de compra del cliente, etc..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </div>

                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-3 font-mono">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Subtotal (Base Imponible):</span>
                        <span id="display-subtotal" class="font-bold">S/. 0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>I.G.V. (<?= e($igvRate) ?>%):</span>
                        <span id="display-igv" class="font-bold">S/. 0.00</span>
                    </div>
                    <div class="pt-3 border-t border-slate-200 flex justify-between text-base font-black text-slate-900 font-sans">
                        <span>TOTAL COMPROBANTE:</span>
                        <span id="display-total" class="font-mono text-xl text-blue-600 font-black">S/. 0.00</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3 mt-6">
                <a href="<?= url('/sales') ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="btn-submit-sale" class="px-7 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Emitir y Confirmar Venta</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Product & System Variables -->
<script>
    const availableProducts = <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>;
    const igvRatePercent = <?= (float)$igvRate ?>;
    const correlativeMap = <?= json_encode($correlatives, JSON_UNESCAPED_UNICODE) ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('items-tbody');
    const btnAdd = document.getElementById('btn-add-item');
    const voucherSelect = document.getElementById('voucher_type');
    const correlativeDisplay = document.getElementById('display-correlative');

    voucherSelect.addEventListener('change', () => {
        const type = voucherSelect.value;
        if (correlativeMap[type]) {
            correlativeDisplay.textContent = correlativeMap[type].formatted;
        }
    });

    let rowIndex = 0;

    function addRow(productId = '', quantity = 1, unitPrice = 0) {
        rowIndex++;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50 transition-colors item-row';
        tr.dataset.index = rowIndex;

        let options = '<option value="">-- Seleccionar Producto --</option>';
        availableProducts.forEach(p => {
            const selected = (p.id == productId) ? 'selected' : '';
            options += `<option value="${p.id}" data-price="${p.sale_price}" data-stock="${p.stock}" ${selected}>[${p.code}] ${p.name}</option>`;
        });

        tr.innerHTML = `
            <td class="py-2.5 px-3">
                <select name="items[${rowIndex}][product_id]" required class="product-select w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                    ${options}
                </select>
            </td>
            <td class="py-2.5 px-3 text-center">
                <span class="stock-badge font-mono text-xs font-bold text-slate-600">-</span>
            </td>
            <td class="py-2.5 px-3">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" value="${quantity}" required class="qty-input w-full text-center px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-blue-500">
            </td>
            <td class="py-2.5 px-3">
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" value="${Number(unitPrice).toFixed(2)}" required class="price-input w-full text-right px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-blue-500">
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

        const select = tr.querySelector('.product-select');
        const qty = tr.querySelector('.qty-input');
        const price = tr.querySelector('.price-input');
        const stockBadge = tr.querySelector('.stock-badge');
        const btnRemove = tr.querySelector('.btn-remove-row');

        select.addEventListener('change', () => {
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.price) {
                price.value = Number(selectedOpt.dataset.price).toFixed(2);
                const stock = parseInt(selectedOpt.dataset.stock) || 0;
                stockBadge.textContent = stock;
                stockBadge.className = stock <= 0 
                    ? 'stock-badge font-mono text-xs font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded' 
                    : 'stock-badge font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded';
                qty.max = stock > 0 ? stock : 1;
            } else {
                stockBadge.textContent = '-';
            }
            calculateTotals();
        });

        qty.addEventListener('input', calculateTotals);
        price.addEventListener('input', calculateTotals);

        btnRemove.addEventListener('click', () => {
            tr.remove();
            calculateTotals();
        });

        calculateTotals();
    }

    function calculateTotals() {
        let totalGeneral = 0;
        const rows = tbody.querySelectorAll('.item-row');

        rows.forEach(tr => {
            const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
            const price = parseFloat(tr.querySelector('.price-input').value) || 0;

            const lineTotal = qty * price;
            tr.querySelector('.row-subtotal').textContent = `S/. ${lineTotal.toFixed(2)}`;
            totalGeneral += lineTotal;
        });

        // Subtotal + IGV Calculation (Peru Standard: Total contains IGV 18%)
        const igvFraction = 1 + (igvRatePercent / 100);
        const subtotal = totalGeneral / igvFraction;
        const igv = totalGeneral - subtotal;

        document.getElementById('display-subtotal').textContent = `S/. ${subtotal.toFixed(2)}`;
        document.getElementById('display-igv').textContent = `S/. ${igv.toFixed(2)}`;
        document.getElementById('display-total').textContent = `S/. ${totalGeneral.toFixed(2)}`;
    }

    btnAdd.addEventListener('click', () => addRow());

    // Add initial row
    addRow();
});
</script>

