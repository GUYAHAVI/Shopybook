@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $po->po_number }}
                <span class="badge bg-{{ $po->status_color }} align-middle">{{ $po->status_label }}</span>
            </h1>
            <p class="text-muted mb-0">
                {{ $po->supplier->name ?? '—' }} · Ordered {{ $po->order_date->format('d M Y') }}
                @if($po->expected_date) · Expected {{ $po->expected_date->format('d M Y') }} @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            @if($po->editable)
                <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <form action="{{ route('purchase-orders.send', $po) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info"
                            title="{{ $po->supplier?->email ? 'Emails the PO to ' . $po->supplier->email : 'Supplier has no email on record — only marks the PO as sent' }}">
                        <i class="fas fa-paper-plane me-2"></i>Send to Supplier{{ $po->supplier?->email ? ' (Email)' : '' }}
                    </button>
                </form>
            @endif
            @if(in_array($po->status, ['sent', 'partially_received'], true) && $po->supplier?->email)
                <form action="{{ route('purchase-orders.resend-email', $po) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-info" title="Email the PO to {{ $po->supplier->email }} again">
                        <i class="fas fa-envelope me-2"></i>Resend Email
                    </button>
                </form>
            @endif
            @if($po->receivable)
                <a href="{{ route('purchase-orders.receive', $po) }}" class="btn btn-success">
                    <i class="fas fa-truck-loading me-2"></i>Receive Goods
                </a>
            @endif
            @if(!$po->receipts->count() && $po->status !== 'cancelled')
                <form action="{{ route('purchase-orders.cancel', $po) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Cancel this purchase order?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger"><i class="fas fa-times me-2"></i>Cancel</button>
                </form>
            @endif
        </div>
    </div>

    @foreach(['success' => 'success', 'error' => 'danger'] as $key => $class)
        @if(session($key))
            <div class="alert alert-{{ $class }} alert-dismissible fade show">{{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
    @endforeach

    <div class="row">
        <div class="col-lg-8">
            <!-- Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Order Lines</h6>
                    <span class="text-muted small">{{ $po->received_percent }}% received</span>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height:10px;">
                        <div class="progress-bar bg-{{ $po->received_percent >= 100 ? 'success' : 'info' }}" style="width:{{ $po->received_percent }}%"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>Product</th><th>Ordered</th><th>Received</th><th>Remaining</th><th>Unit Cost</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ rtrim(rtrim(number_format($item->quantity_ordered, 3), '0'), '.') }}</td>
                                        <td>{{ rtrim(rtrim(number_format($item->quantity_received, 3), '0'), '.') }}</td>
                                        <td>
                                            @if($item->remaining > 0)
                                                <span class="text-warning fw-bold">{{ rtrim(rtrim(number_format($item->remaining, 3), '0'), '.') }}</span>
                                            @else
                                                <span class="text-success"><i class="fas fa-check"></i> 0</span>
                                            @endif
                                        </td>
                                        <td>KSh {{ number_format($item->unit_cost, 2) }}</td>
                                        <td>KSh {{ number_format($item->total_cost, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end font-weight-bold">Total</td>
                                    <td class="font-weight-bold">KSh {{ number_format($po->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @if($po->notes)
                        <p class="text-muted mb-0"><strong>Notes:</strong> {{ $po->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Receipts -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Goods Received ({{ $po->receipts->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($po->receipts->isEmpty())
                        <p class="text-muted mb-0">Nothing received yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th>Receipt #</th><th>Date</th><th>Product</th><th>Qty</th><th>Value</th><th>Payment</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($po->receipts as $receipt)
                                        <tr>
                                            <td><a href="{{ route('products.receive.show', $receipt) }}">{{ $receipt->receipt_number }}</a></td>
                                            <td>{{ $receipt->receipt_date->format('d M Y') }}</td>
                                            <td>{{ $receipt->product_name }}</td>
                                            <td>{{ rtrim(rtrim(number_format($receipt->quantity_received, 3), '0'), '.') }}</td>
                                            <td>KSh {{ number_format($receipt->total_cost, 2) }}</td>
                                            <td><span class="badge bg-{{ $receipt->payment_mode === 'credit' ? 'warning' : 'success' }}">{{ ucfirst($receipt->payment_mode ?? 'paid') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Supplier -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supplier</h6>
                </div>
                <div class="card-body">
                    @if($po->supplier)
                        <p class="mb-1 fw-bold">{{ $po->supplier->name }}</p>
                        @if($po->supplier->contact_person)<p class="mb-1 text-muted">{{ $po->supplier->contact_person }}</p>@endif
                        @if($po->supplier->phone)<p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $po->supplier->phone }}</p>@endif
                        @if($po->supplier->email)<p class="mb-0"><i class="fas fa-envelope me-2"></i>{{ $po->supplier->email }}</p>@endif
                    @else
                        <p class="text-muted mb-0">Supplier deleted.</p>
                    @endif
                </div>
            </div>

            <!-- Bills -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supplier Bills ({{ $po->debts->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($po->debts->isEmpty())
                        <p class="text-muted mb-0">No unpaid bills linked to this order.</p>
                    @else
                        @foreach($po->debts as $debt)
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">{{ $debt->reference_number }}</span>
                                    <span class="badge bg-{{ $debt->status === 'paid' ? 'success' : ($debt->status === 'partial' ? 'warning' : 'danger') }}">{{ ucfirst($debt->status) }}</span>
                                </div>
                                <small class="text-muted">
                                    KSh {{ number_format($debt->total_amount, 2) }} · balance KSh {{ number_format($debt->balance, 2) }}
                                    @if($debt->due_date) · due {{ $debt->due_date->format('d M Y') }} @endif
                                </small>
                            </div>
                        @endforeach
                        <a href="{{ route('sales.supplier-debts') }}" class="small">Manage supplier bills →</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
