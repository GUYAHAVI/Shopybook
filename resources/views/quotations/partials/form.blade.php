@php
    $itemsJson = isset($quote)
        ? $quote->items->map(fn ($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity, 'unit_price' => (float) $i->unit_price])->values()
        : collect(old('items', []))->map(fn ($i) => ['product_id' => $i['product_id'] ?? null, 'quantity' => $i['quantity'] ?? '', 'unit_price' => $i['unit_price'] ?? ''])->values();
    $productsJson = $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float) $p->price, 'unit' => $p->measurement_unit, 'stock' => (float) $p->stock_quantity])->values();
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" id="customerSelect">
            <option value="">— New prospect (enter details below) —</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $quote->customer_id ?? '') == $customer->id)>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
        @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label">Quote Date *</label>
        <input type="date" name="quote_date" class="form-control" required
               value="{{ old('quote_date', isset($quote) ? $quote->quote_date->toDateString() : date('Y-m-d')) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Valid Until</label>
        <input type="date" name="valid_until" class="form-control"
               value="{{ old('valid_until', isset($quote) && $quote->valid_until ? $quote->valid_until->toDateString() : '') }}">
    </div>
</div>

<div class="row g-3 mb-4" id="prospectFields">
    <div class="col-md-4">
        <label class="form-label">Prospect Name <span class="text-danger" id="nameReq">*</span></label>
        <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
               value="{{ old('customer_name', $quote->customer_name ?? '') }}" placeholder="Name for a customer not yet saved">
        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Email</label>
        <input type="email" name="customer_email" class="form-control"
               value="{{ old('customer_email', $quote->customer_email ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Phone</label>
        <input type="tel" name="customer_phone" class="form-control"
               value="{{ old('customer_phone', $quote->customer_phone ?? '') }}">
    </div>
</div>

<h6 class="font-weight-bold mb-2">Quote Lines</h6>
@error('items')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
<div class="table-responsive mb-2">
    <table class="table table-bordered" id="quoteItemsTable">
        <thead class="table-light">
            <tr>
                <th style="width:40%">Product</th>
                <th style="width:15%">Quantity</th>
                <th style="width:18%">Unit Price (KSh)</th>
                <th style="width:17%">Line Total</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody id="quoteItemsBody"></tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end font-weight-bold">Total</td>
                <td class="font-weight-bold" id="quoteGrandTotal">KSh 0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addItemBtn">
    <i class="fas fa-plus me-1"></i>Add Line
</button>

<div class="mb-4">
    <label class="form-label">Notes / Terms</label>
    <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Payment terms, delivery notes">{{ old('notes', $quote->notes ?? '') }}</textarea>
</div>

<template id="quoteItemTemplate">
    <tr>
        <td>
            <select class="form-select product-select" data-field="product_id" required>
                <option value="">Select product…</option>
            </select>
        </td>
        <td><input type="number" step="0.001" min="0.001" class="form-control qty-input" data-field="quantity" required></td>
        <td><input type="number" step="0.01" min="0" class="form-control price-input" data-field="unit_price" required></td>
        <td class="line-total align-middle">0.00</td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<script>
(function () {
    const products = @json($productsJson);
    const body = document.getElementById('quoteItemsBody');
    const tpl = document.getElementById('quoteItemTemplate');
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
            opt.dataset.price = p.price;
            select.appendChild(opt);
        });

        if (item) {
            if (item.product_id) select.value = item.product_id;
            tr.querySelector('.qty-input').value = item.quantity ?? '';
            tr.querySelector('.price-input').value = item.unit_price ?? '';
        }

        select.addEventListener('change', function () {
            const price = select.selectedOptions[0]?.dataset.price;
            const priceInput = tr.querySelector('.price-input');
            if (price && !priceInput.value) priceInput.value = price;
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
            const price = parseFloat(tr.querySelector('.price-input').value) || 0;
            const line = qty * price;
            tr.querySelector('.line-total').textContent = line.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            total += line;
        });
        document.getElementById('quoteGrandTotal').textContent = 'KSh ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    document.getElementById('addItemBtn').addEventListener('click', function () { addRow(null); });

    // Show prospect fields only when no saved customer is picked
    const customerSelect = document.getElementById('customerSelect');
    const prospectFields = document.getElementById('prospectFields');
    function toggleProspect() {
        prospectFields.style.display = customerSelect.value ? 'none' : '';
    }
    customerSelect.addEventListener('change', toggleProspect);
    toggleProspect();

    const existing = @json($itemsJson);
    if (existing && existing.length) {
        existing.forEach(addRow);
    } else {
        addRow(null);
    }
})();
</script>
