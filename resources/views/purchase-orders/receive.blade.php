@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Receive Goods — {{ $po->po_number }}</h1>
            <p class="text-muted">{{ $po->supplier->name ?? '—' }}</p>
        </div>
        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Order
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('purchase-orders.receive.process', $po) }}" method="POST">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quantities Received</h6>
            </div>
            <div class="card-body">
                <p class="text-muted">Enter the quantity delivered for each line. Leave at 0 for lines not delivered.</p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr><th>Product</th><th>Ordered</th><th>Already Received</th><th>Remaining</th><th>Unit Cost</th><th>Receiving Now</th></tr>
                        </thead>
                        <tbody>
                            @foreach($po->items as $item)
                                <tr class="{{ $item->remaining <= 0 ? 'table-success' : '' }}">
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ rtrim(rtrim(number_format($item->quantity_ordered, 3), '0'), '.') }}</td>
                                    <td>{{ rtrim(rtrim(number_format($item->quantity_received, 3), '0'), '.') }}</td>
                                    <td>{{ rtrim(rtrim(number_format($item->remaining, 3), '0'), '.') }}</td>
                                    <td>KSh {{ number_format($item->unit_cost, 2) }}</td>
                                    <td>
                                        @if($item->remaining > 0)
                                            <input type="number" step="0.001" min="0" max="{{ $item->remaining }}"
                                                   class="form-control" style="max-width:140px;"
                                                   name="items[{{ $item->id }}][quantity]"
                                                   value="{{ old('items.' . $item->id . '.quantity', 0) }}">
                                        @else
                                            <span class="text-success"><i class="fas fa-check me-1"></i>Complete</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Receipt & Payment Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Receipt Date *</label>
                        <input type="date" name="receipt_date" class="form-control" required
                               value="{{ old('receipt_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Supplier Invoice #</label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="payment_mode" id="modeCredit" value="credit"
                                   {{ old('payment_mode', 'credit') === 'credit' ? 'checked' : '' }}>
                            <label class="form-check-label" for="modeCredit">On credit (supplier bill)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="payment_mode" id="modePaid" value="paid"
                                   {{ old('payment_mode') === 'paid' ? 'checked' : '' }}>
                            <label class="form-check-label" for="modePaid">Paid now</label>
                        </div>
                    </div>
                    <div class="col-md-4" id="paidAccountWrap" style="display:none;">
                        <label class="form-label">Paid From Account *</label>
                        <select name="paid_from_account_id" class="form-select">
                            <option value="">Select account…</option>
                            @foreach($moneyAccounts as $account)
                                <option value="{{ $account->id }}" @selected(old('paid_from_account_id') == $account->id)>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4" id="dueDateWrap">
                        <label class="form-label">Payment Due Date *</label>
                        <input type="date" name="due_date" class="form-control"
                               value="{{ old('due_date', now()->addDays(30)->toDateString()) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-truck-loading me-2"></i>Record Receipt
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    const paid = document.getElementById('modePaid');
    const credit = document.getElementById('modeCredit');
    const accountWrap = document.getElementById('paidAccountWrap');
    const dueWrap = document.getElementById('dueDateWrap');
    function toggle() {
        accountWrap.style.display = paid.checked ? 'block' : 'none';
        dueWrap.style.display = credit.checked ? 'block' : 'none';
    }
    paid.addEventListener('change', toggle);
    credit.addEventListener('change', toggle);
    toggle();
})();
</script>
@endsection
