@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add Product Conversion</h1>
            <p class="text-muted">Create a new product unit conversion</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product-conversions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Conversion Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('product-conversions.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product *</label>
                                    <select name="product_id" id="product_id" class="form-select" required>
                                        <option value="">Select a product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="conversion_type" class="form-label">Conversion Type *</label>
                                    <select name="conversion_type" id="conversion_type" class="form-select" required>
                                        <option value="">Select conversion type</option>
                                        @foreach($conversionTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="purchase_unit" class="form-label">Purchase Unit *</label>
                                            <select name="purchase_unit" id="purchase_unit" class="form-select" required>
                                                <option value="">Select unit</option>
                                                @foreach($units as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sale_unit" class="form-label">Sale Unit *</label>
                                            <select name="sale_unit" id="sale_unit" class="form-select" required>
                                                <option value="">Select unit</option>
                                                @foreach($units as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="conversion_factor" class="form-label">Conversion Factor (Microns) *</label>
                                    <select id="micron_preset" class="form-select mb-2">
                                        <option value="">Select preset</option>
                                        @foreach($microns as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="conversion_factor" id="conversion_factor" 
                                           class="form-control" step="0.0001" min="0.0001" required>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_quantity" class="form-label">Purchase Quantity *</label>
                                    <input type="number" name="purchase_quantity" id="purchase_quantity" 
                                           class="form-control" step="0.01" min="0.01" required>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_cost" class="form-label">Purchase Cost per Unit (KSh) *</label>
                                    <input type="number" name="purchase_cost" id="purchase_cost" 
                                           class="form-control" step="0.01" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Sale Price per Unit (KSh) *</label>
                                    <input type="number" name="sale_price" id="sale_price" 
                                           class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('product-conversions.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Conversion</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const micronPreset = document.getElementById('micron_preset');
    const conversionFactor = document.getElementById('conversion_factor');

    micronPreset.addEventListener('change', function() {
        if (this.value && this.value !== 'custom') {
            conversionFactor.value = this.value;
        }
    });
});
</script>
@endsection
