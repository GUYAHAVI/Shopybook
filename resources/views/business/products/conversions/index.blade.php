@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Product Conversions</h1>
            <p class="text-muted">Manage product unit conversions and profit calculations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product-conversions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Conversion
            </a>

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Conversion List
                    </h6>
                </div>
                <div class="card-body">
                                         @if(session('success'))
                         <div class="alert alert-success alert-dismissible fade show" role="alert">
                             {{ session('success') }}
                             <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                         </div>
                     @endif

                     @if($business->isEligibleForDynamicConversions())
                         <div class="alert alert-info alert-dismissible fade show" role="alert">
                             <i class="fas fa-star me-2"></i>
                             <strong>Special Feature Available!</strong> You have access to the Dynamic Conversion Calculator for flexible selling. 
                             Click the calculator icon next to any product to use this exclusive feature.
                             <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                         </div>
                     @endif

                    @if($conversions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Conversion Type</th>
                                        <th>Purchase</th>
                                        <th>Sale</th>
                                        <th>Conversion Factor</th>
                                        <th>Profit Margin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($conversions as $conversion)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($conversion->product->main_image)
                                                    <img src="{{ Storage::url($conversion->product->main_image) }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-box text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $conversion->product->name }}</div>
                                                    <small class="text-muted">{{ $conversion->product->sku ?? 'No SKU' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst(str_replace('_', ' ', $conversion->conversion_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ number_format($conversion->purchase_quantity, 2) }} {{ $conversion->purchase_unit }}</div>
                                            <small class="text-muted">KSh {{ number_format($conversion->purchase_cost, 2) }} each</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ number_format($conversion->converted_quantity, 2) }} {{ $conversion->sale_unit }}</div>
                                            <small class="text-muted">KSh {{ number_format($conversion->sale_price, 2) }} each</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $conversion->conversion_factor }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $conversion->calculateProfitMargin() > 0 ? 'success' : 'danger' }}">
                                                {{ $conversion->formatted_profit_margin }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('product-conversions.show', $conversion) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('product-conversions.edit', $conversion) }}" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                                                                 @if($business->isEligibleForDynamicConversions())
                                                     <a href="{{ route('product-conversions.dynamic-calculator', $conversion->product) }}" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="Dynamic Calculator (Havi's Greenhouse Materials Only)">
                                                         <i class="fas fa-calculator"></i>
                                                     </a>
                                                 @endif
                                                <form action="{{ route('product-conversions.destroy', $conversion) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this conversion?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $conversions->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Product Conversions Found</h5>
                            <p class="text-muted">Start by adding your first product conversion to track inventory transformations.</p>
                            <a href="{{ route('product-conversions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Add Your First Conversion
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
