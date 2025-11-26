@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Product Conversions</h1>
            <p class="text-muted">Conversion history for {{ $product->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product-conversions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Conversion
            </a>
            <a href="{{ route('product-conversions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>All Conversions
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Product Name:</label>
                        <p>{{ $product->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">SKU:</label>
                        <p>{{ $product->sku ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Category:</label>
                        <p>{{ $product->category ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Current Stock:</label>
                        <p class="h5">{{ number_format($product->stock_quantity ?? 0, 2) }} {{ $product->unit ?? 'units' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Total Conversions:</label>
                        <p class="h5 text-primary">{{ $conversions->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Conversion History</h6>
                </div>
                <div class="card-body">
                    @if($conversions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Purchase</th>
                                        <th>Sale</th>
                                        <th>Factor</th>
                                        <th>Profit Margin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($conversions as $conversion)
                                        <tr>
                                            <td>{{ $conversion->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst(str_replace('_', ' ', $conversion->conversion_type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    {{ number_format($conversion->purchase_quantity, 2) }} {{ $conversion->purchase_unit }}
                                                </div>
                                                <div class="text-muted">
                                                    KSh {{ number_format($conversion->purchase_cost, 2) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    {{ number_format($conversion->converted_quantity, 2) }} {{ $conversion->sale_unit }}
                                                </div>
                                                <div class="text-muted">
                                                    KSh {{ number_format($conversion->sale_price, 2) }}
                                                </div>
                                            </td>
                                            <td>{{ $conversion->conversion_factor }}</td>
                                            <td>
                                                <span class="badge bg-{{ $conversion->calculateProfitMargin() > 0 ? 'success' : 'danger' }}">
                                                    {{ $conversion->formatted_profit_margin }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('product-conversions.show', $conversion) }}" 
                                                       class="btn btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('product-conversions.edit', $conversion) }}" 
                                                       class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No conversions found</h5>
                            <p class="text-muted">This product doesn't have any conversion records yet.</p>
                            <a href="{{ route('product-conversions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Conversion
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($conversions->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Conversion Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ $conversions->count() }}</h4>
                                    <p class="text-muted">Total Conversions</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-success">
                                        {{ $conversions->where('conversion_type', 'weight_to_area')->count() }}
                                    </h4>
                                    <p class="text-muted">Weight to Area</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-info">
                                        {{ $conversions->where('conversion_type', 'area_to_weight')->count() }}
                                    </h4>
                                    <p class="text-muted">Area to Weight</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-warning">
                                        {{ $conversions->where('conversion_type', 'custom')->count() }}
                                    </h4>
                                    <p class="text-muted">Custom</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection






