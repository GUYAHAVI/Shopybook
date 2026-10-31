@php
    $itemsJson = isset($po)
        ? $po->items->map(fn ($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity_ordered, 'unit_cost' => (float) $i->unit_cost])->values()
        : collect(old('items', []))->map(fn ($i) => ['product_id' => $i['product_id'] ?? null, 'quantity' => $i['quantity'] ?? '', 'unit_cost' => $i['unit_cost'] ?? ''])->values();
    $productsJson = $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'cost' => (float) $p->cost_price, 'unit' => $p->measurement_unit])->values();
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Supplier *</label>
        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">Select supplier…</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $po->supplier_id ?? '') == $supplier->id)>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Order Date *</label>
        <input type="date" name="order_date" class="form-control" required
               value="{{ old('order_date', isset($po) ? $po->order_date->toDateString() : date('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Expected Delivery</label>
        <input type="date" name="expected_date" class="form-control"
               value="{{ old('expected_date', isset($po) && $po->expected_date ? $po->expected_date->toDateString() : '') }}">
    </div>
</div>

<h6 class="font-weight-bold mb-2">Order Lines</h6>
@error('items')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
<div class="table-responsive mb-2">
    <table class="table table-bordered" id="poItemsTable">
        <thead class="table-light">
            <tr>
                <th style="width:40%">Product</th>
                <th style="width:15%">Quantity</th>
                <th style="width:18%">Unit Cost (KSh)</th>
                <th style="width:17%">Line Total</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody id="poItemsBody"></tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end font-weight-bold">Total</td>
                <td class="font-weight-bold" id="poGrandTotal">KSh 0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addItemBtn">
    <i class="fas fa-plus me-1"></i>Add Line
</button>

<div class="mb-4">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $po->notes ?? '') }}</textarea>
</div>

<template id="poItemTemplate">
    <tr>
        <td>
            <select class="form-select product-select" data-field="product_id" required>
                <option value="">Select product…</option>
            </select>
        </td>
        <td><input type="number" step="0.001" min="0.001" class="form-control qty-input" data-field="quantity" required></td>
        <td><input type="number" step="0.01" min="0" class="form-control cost-input" data-field="unit_cost" required></td>
        <td class="line-total align-middle">0.00</td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<script>
(function () {
    const products = @json($productsJson);
    const body = document.getElementById('poItemsBody');
    const tpl = document.getElementById('poItemTemplate');
    let rowIndex = 0;

    function addRow(item) {
        const tr = tpl.content.firstElementChild.cloneNode(true);
        const idx = rowIndex++;

        tr.querySelectorAll('[data-field]').forEach(function (el) {
            el.name = 'items[' + idx + '][' + el.dataset.field + ']';
        });

        const select = tr.querySelector('.product-select');
        products.forEach(function (p) {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name + (p.unit ? ' (' + p.unit + ')' : '');
            opt.dataset.cost = p.cost;
            select.appendChild(opt);
        });

        if (item) {
            if (item.product_id) select.value = item.product_id;
            tr.querySelector('.qty-input').value = item.quantity ?? '';
            tr.querySelector('.cost-input').value = item.unit_cost ?? '';
        }

        select.addEventListener('change', function () {
            const cost = select.selectedOptions[0]?.dataset.cost;
            const costInput = tr.querySelector('.cost-input');
            if (cost && !costInput.value) costInput.value = cost;
            recalc();
        });
        tr.querySelectorAll('input').forEach(function (el) { el.addEventListener('input', recalc); });
        tr.querySelector('.remove-row').addEventListener('click', function () {
            if (body.children.length > 1) { tr.remove(); recalc(); }
        });

        body.appendChild(tr);
        recalc();
    }

    function recalc() {
        let total = 0;
        body.querySelectorAll('tr').forEach(function (tr) {
            const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(tr.querySelector('.cost-input').value) || 0;
            const line = qty * cost;
            tr.querySelector('.line-total').textContent = line.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            total += line;
        });
        document.getElementById('poGrandTotal').textContent = 'KSh ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    document.getElementById('addItemBtn').addEventListener('click', function () { addRow(null); });

    const existing = @json($itemsJson);
    if (existing && existing.length) {
        existing.forEach(addRow);
    } else {
        addRow(null);
    }
})();
</script>
