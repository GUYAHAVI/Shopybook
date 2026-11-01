@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $quote->quote_number }}
                <span class="badge bg-{{ $quote->status_color }} align-middle">{{ $quote->status_label }}</span>
                @if($quote->is_expired)<span class="badge bg-warning text-dark align-middle">Expired</span>@endif
            </h1>
            <p class="text-muted mb-0">
                {{ $quote->customer_display_name }} · Quoted {{ $quote->quote_date->format('d M Y') }}
                @if($quote->valid_until) · Valid until {{ $quote->valid_until->format('d M Y') }} @endif
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            @if($quote->editable)
                <a href="{{ route('quotations.edit', $quote) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <form action="{{ route('quotations.send', $quote) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info"
                            title="{{ $quote->contact_email ? 'Emails the quote to ' . $quote->contact_email : 'No customer email on record — only marks the quote as sent' }}">
                        <i class="fas fa-paper-plane me-2"></i>Send to Customer{{ $quote->contact_email ? ' (Email)' : '' }}
                    </button>
                </form>
            @endif
            @if($quote->status === 'sent')
                <form action="{{ route('quotations.accept', $quote) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" title="Customer accepted the quote">
                        <i class="fas fa-check me-2"></i>Accepted
                    </button>
                </form>
                <form action="{{ route('quotations.decline', $quote) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" title="Customer declined the quote">
                        <i class="fas fa-times me-2"></i>Declined
                    </button>
                </form>
                @if($quote->contact_email)
                    <form action="{{ route('quotations.resend-email', $quote) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-info" title="Email the quote to {{ $quote->contact_email }} again">
                            <i class="fas fa-envelope me-2"></i>Resend Email
                        </button>
                    </form>
                @endif
            @endif
            @if($quote->convertible)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#convertModal">
                    <i class="fas fa-exchange-alt me-2"></i>Convert to Sale
                </button>
            @endif
            @if($quote->cancellable)
                <form action="{{ route('quotations.cancel', $quote) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Cancel this quotation?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger"><i class="fas fa-ban me-2"></i>Cancel</button>
                </form>
            @endif
        </div>
    </div>

    @foreach(['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $key => $class)
        @if(session($key))
            <div class="alert alert-{{ $class }} alert-dismissible fade show">{{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
    @endforeach
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quote Lines</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach($quote->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ rtrim(rtrim(number_format($item->quantity, 3), '0'), '.') }}</td>
                                        <td>KSh {{ number_format($item->unit_price, 2) }}</td>
                                        <td>KSh {{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal</td>
                                    <td>KSh {{ number_format($quote->subtotal, 2) }}</td>
                                </tr>
                                @if((float) $quote->tax > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">Tax</td>
                                        <td>KSh {{ number_format($quote->tax, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end font-weight-bold">Total</td>
                                    <td class="font-weight-bold">KSh {{ number_format($quote->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @if($quote->notes)
                        <p class="text-muted mb-0"><strong>Notes:</strong> {{ $quote->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Customer -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-bold">{{ $quote->customer_display_name }}</p>
                    @if(!$quote->customer_id)
                        <p class="mb-1 text-muted small">New prospect — a customer record is created on conversion.</p>
                    @endif
                    @if($quote->customer_phone || $quote->customer?->phone)
                        <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $quote->customer?->phone ?? $quote->customer_phone }}</p>
                    @endif
                    @if($quote->contact_email)
                        <p class="mb-0"><i class="fas fa-envelope me-2"></i>{{ $quote->contact_email }}</p>
                    @endif
                </div>
            </div>

            <!-- Linked sale -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Linked Sale</h6>
                </div>
                <div class="card-body">
                    @if($quote->order)
                        @php
                            $orderDocRoute = $quote->order->payment_status === 'paid' ? 'sales.print-receipt' : 'sales.generate-invoice';
                        @endphp
                        <p class="mb-1">
                            Order <a href="{{ route($orderDocRoute, $quote->order) }}" class="fw-bold">{{ $quote->order->order_number }}</a>
                        </p>
                        <small class="text-muted">
                            KSh {{ number_format($quote->order->total_amount, 2) }}
                            · <span class="badge bg-{{ $quote->order->payment_status === 'paid' ? 'success' : ($quote->order->payment_status === 'partial' ? 'warning' : 'danger') }}">{{ ucfirst($quote->order->payment_status ?? 'unpaid') }}</span>
                        </small>
                        <div class="mt-2">
                            <a href="{{ route($orderDocRoute, $quote->order) }}" class="small">
                                {{ $quote->order->payment_status === 'paid' ? 'View receipt →' : 'View invoice →' }}
                            </a>
                        </div>
                    @else
                        <p class="text-muted mb-0">Not converted to a sale yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Convert to sale modal --}}
@if($quote->convertible)
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('quotations.convert', $quote) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Convert {{ $quote->quote_number }} to Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    Creates an order for <strong>KSh {{ number_format($quote->total_amount, 2) }}</strong>, deducts stock,
                    and records the sale in the books.
                </p>
                <div class="mb-3">
                    <label class="form-label">Payment received</label>
                    <select name="payment_status" class="form-select" id="convStatus">
                        <option value="paid">Paid in full</option>
                        <option value="partial">Partial payment</option>
                        <option value="unpaid">Unpaid (sell on credit)</option>
                    </select>
                </div>
                <div class="mb-3" id="convAmountWrap" style="display:none;">
                    <label class="form-label">Amount received (KSh)</label>
                    <input type="number" name="amount_paid" class="form-control" min="0" step="0.01"
                           max="{{ $quote->total_amount }}" placeholder="0.00">
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment method</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div class="mb-1" id="convDueWrap" style="display:none;">
                    <label class="form-label">Payment due date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ now()->addDays(30)->toDateString() }}">
                    <div class="form-text">A customer debt is created for the unpaid balance.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Sale</button>
            </div>
        </form>
    </div></div>
</div>

<script>
document.getElementById('convStatus').addEventListener('change', function () {
    document.getElementById('convAmountWrap').style.display = this.value === 'partial' ? '' : 'none';
    document.getElementById('convDueWrap').style.display = this.value === 'paid' ? 'none' : '';
});

// Prevent double-submits creating two orders
document.querySelector('#convertModal form').addEventListener('submit', function () {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Creating sale…';
});
</script>
@endif
@endsection
