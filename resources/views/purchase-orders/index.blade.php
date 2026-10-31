@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Purchase Orders</h1>
            <p class="text-muted">Order stock from suppliers and track deliveries</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Purchase Order
        </a>
    </div>

    @foreach(['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $key => $class)
        @if(session($key))
            <div class="alert alert-{{ $class }} alert-dismissible fade show">
                <i class="fas fa-{{ $key === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>{{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
                <div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Open Orders</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['open'] }}</div></div>
                <div class="col-auto"><i class="fas fa-truck-loading fa-2x text-gray-300"></i></div>
            </div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
                <div class="col mr-2"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Drafts</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['draft'] }}</div></div>
                <div class="col-auto"><i class="fas fa-file-alt fa-2x text-gray-300"></i></div>
            </div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
                <div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Open Order Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ number_format($stats['open_value'], 2) }}</div></div>
                <div class="col-auto"><i class="fas fa-coins fa-2x text-gray-300"></i></div>
            </div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
                <div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div></div>
                <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
            </div></div></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all">All</option>
                        @foreach(\App\Models\PurchaseOrder::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">All suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="PO number or supplier">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- PO list -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>PO Number</th><th>Supplier</th><th>Order Date</th><th>Expected</th>
                            <th>Items</th><th>Total</th><th>Received</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pos as $po)
                            <tr>
                                <td><a href="{{ route('purchase-orders.show', $po) }}" class="fw-bold">{{ $po->po_number }}</a></td>
                                <td>{{ $po->supplier->name ?? '—' }}</td>
                                <td>{{ $po->order_date->format('d M Y') }}</td>
                                <td>{{ $po->expected_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $po->items_count }}</td>
                                <td>KSh {{ number_format($po->total_amount, 2) }}</td>
                                <td style="min-width:110px;">
                                    @php $pct = $po->items_sum_quantity_ordered > 0 ? min(100, round($po->items_sum_quantity_received / $po->items_sum_quantity_ordered * 100)) : 0; @endphp
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-{{ $pct >= 100 ? 'success' : 'info' }}" style="width:{{ $pct }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $pct }}%</small>
                                </td>
                                <td><span class="badge bg-{{ $po->status_color }}">{{ $po->status_label }}</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                        @if($po->editable)
                                            <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        @endif
                                        @if($po->receivable)
                                            <a href="{{ route('purchase-orders.receive', $po) }}" class="btn btn-outline-success" title="Receive"><i class="fas fa-truck-loading"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-4 text-muted">
                                No purchase orders yet.
                                <a href="{{ route('purchase-orders.create') }}">Create your first purchase order</a>.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pos->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
