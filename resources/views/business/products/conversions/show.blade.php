@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Product Conversion Details</h1>
            <p class="text-muted">View conversion details and calculations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product-conversions.edit', $conversion) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('product-conversions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Conversion Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Product Information</h5>
                            <div class="mb-3">
                                <label class="fw-bold">Product:</label>
                                <p>{{ $conversion->product->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">SKU:</label>
                                <p>{{ $conversion->product->sku ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Category:</label>
                                <p>{{ $conversion->product->category ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Conversion Information</h5>
                            <div class="mb-3">
                                <label class="fw-bold">Conversion Type:</label>
                                <p><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $conversion->conversion_type)) }}</span></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Conversion Factor:</label>
                                <p>{{ $conversion->conversion_factor }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Notes:</label>
                                <p>{{ $conversion->notes ?? 'No notes' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Purchase Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Purchase Quantity:</label>
                        <p class="h5">{{ number_format($conversion->purchase_quantity, 2) }} {{ $conversion->purchase_unit }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Purchase Cost:</label>
                        <p class="h5">KSh {{ number_format($conversion->purchase_cost, 2) }} per {{ $conversion->purchase_unit }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Total Purchase Cost:</label>
                        <p class="h5 text-primary">KSh {{ number_format($conversion->purchase_quantity * $conversion->purchase_cost, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sale Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Converted Quantity:</label>
                        <p class="h5">{{ number_format($conversion->converted_quantity, 2) }} {{ $conversion->sale_unit }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Sale Price:</label>
                        <p class="h5">KSh {{ number_format($conversion->sale_price, 2) }} per {{ $conversion->sale_unit }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Total Sale Value:</label>
                        <p class="h5 text-success">KSh {{ number_format($conversion->converted_quantity * $conversion->sale_price, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profit Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Profit Margin:</label>
                        <p class="h5">
                            <span class="badge bg-{{ $conversion->calculateProfitMargin() > 0 ? 'success' : 'danger' }}">
                                {{ $conversion->formatted_profit_margin }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Gross Profit:</label>
                        <p class="h5 text-{{ $conversion->calculateProfitMargin() > 0 ? 'success' : 'danger' }}">
                            KSh {{ number_format(($conversion->converted_quantity * $conversion->sale_price) - ($conversion->purchase_quantity * $conversion->purchase_cost), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Conversion Formula</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Conversion Calculation:</h6>
                        <p class="mb-0">
                            <strong>{{ number_format($conversion->purchase_quantity, 2) }} {{ $conversion->purchase_unit }}</strong>
                            @if($conversion->conversion_type === 'weight_to_area')
                                ÷ {{ $conversion->conversion_factor }} = 
                            @elseif($conversion->conversion_type === 'area_to_weight')
                                × {{ $conversion->conversion_factor }} = 
                            @else
                                × {{ $conversion->conversion_factor }} = 
                            @endif
                            <strong>{{ number_format($conversion->converted_quantity, 2) }} {{ $conversion->sale_unit }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






