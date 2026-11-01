@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quotations</h1>
            <p class="text-muted">Quote prices to customers and convert accepted quotes to sales</p>
        </div>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Quotation
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
                <div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Open Quotes</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['open'] }}</div></div>
                <div class="col-auto"><i class="fas fa-paper-plane fa-2x text-gray-300"></i></div>
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
                <div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Open Quote Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ number_format($stats['open_value'], 2) }}</div></div>
                <div class="col-auto"><i class="fas fa-coins fa-2x text-gray-300"></i></div>
            </div></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center">
                <div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Converted</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['converted'] }}</div></div>
                <div class="col-auto"><i class="fas fa-exchange-alt fa-2x text-gray-300"></i></div>
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
                        @foreach(\App\Models\Quotation::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select">
                        <option value="">All customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Quote number or customer">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quote list -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Quote Number</th><th>Customer</th><th>Date</th><th>Valid Until</th>
                            <th>Items</th><th>Total</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotes as $quote)
                            <tr>
                                <td><a href="{{ route('quotations.show', $quote) }}" class="fw-bold">{{ $quote->quote_number }}</a></td>
                                <td>{{ $quote->customer_display_name }}</td>
                                <td>{{ $quote->quote_date->format('d M Y') }}</td>
                                <td>
                                    {{ $quote->valid_until?->format('d M Y') ?? '—' }}
                                    @if($quote->is_expired)<span class="badge bg-warning text-dark ms-1">Expired</span>@endif
                                </td>
                                <td>{{ $quote->items_count }}</td>
                                <td>KSh {{ number_format($quote->total_amount, 2) }}</td>
                                <td><span class="badge bg-{{ $quote->status_color }}">{{ $quote->status_label }}</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('quotations.show', $quote) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                        @if($quote->editable)
                                            <a href="{{ route('quotations.edit', $quote) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">
                                No quotations yet.
                                <a href="{{ route('quotations.create') }}">Create your first quotation</a>.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $quotes->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
