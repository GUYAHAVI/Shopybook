@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Product Conversion</h1>
            <p class="text-muted">Update conversion details and calculations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product-conversions.show', $conversion) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>View Details
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
                    <h6 class="m-0 font-weight-bold text-primary">Edit Conversion</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('product-conversions.update', $conversion) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product *</label>
                                    <select name="product_id" id="product_id" class="form-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ $conversion->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->sku ?? 'No SKU' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="conversion_type" class="form-label">Conversion Type *</label>
                                    <select name="conversion_type" id="conversion_type" class="form-select" required>
                                        @foreach($conversionTypes as $key => $label)
                                            <option value="{{ $key }}" {{ $conversion->conversion_type == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_unit" class="form-label">Purchase Unit *</label>
                                    <select name="purchase_unit" id="purchase_unit" class="form-select" required>
                                        @foreach($units as $key => $label)
                                            <option value="{{ $key }}" {{ $conversion->purchase_unit == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="sale_unit" class="form-label">Sale Unit *</label>
                                    <select name="sale_unit" id="sale_unit" class="form-select" required>
                                        @foreach($units as $key => $label)
                                            <option value="{{ $key }}" {{ $conversion->sale_unit == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="conversion_factor" class="form-label">Conversion Factor (Microns) *</label>
                                    <select name="conversion_factor" id="conversion_factor" class="form-select" required>
                                        @foreach($microns as $key => $label)
                                            <option value="{{ $key }}" {{ $conversion->conversion_factor == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select the micron thickness for accurate conversion</div>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_quantity" class="form-label">Purchase Quantity *</label>
                                    <input type="number" step="0.01" min="0.01" name="purchase_quantity" id="purchase_quantity" 
                                           class="form-control" value="{{ $conversion->purchase_quantity }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_cost" class="form-label">Purchase Cost per Unit (KSh) *</label>
                                    <input type="number" step="0.01" min="0" name="purchase_cost" id="purchase_cost" 
                                           class="form-control" value="{{ $conversion->purchase_cost }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Sale Price per Unit (KSh) *</label>
                                    <input type="number" step="0.01" min="0" name="sale_price" id="sale_price" 
                                           class="form-control" value="{{ $conversion->sale_price }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ $conversion->notes }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Conversion
                            </button>
                            <a href="{{ route('product-conversions.show', $conversion) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Live Calculation</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Converted Quantity:</label>
                        <div class="h5 text-primary" id="converted_quantity">
                            {{ number_format($conversion->converted_quantity, 2) }} {{ $conversion->sale_unit }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conversion Formula:</label>
                        <div class="text-muted small" id="conversion_formula">
                            {{ $conversion->formatted_conversion }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profit Margin:</label>
                        <div class="h5">
                            <span class="badge bg-{{ $conversion->calculateProfitMargin() > 0 ? 'success' : 'danger' }}" id="profit_margin">
                                {{ $conversion->formatted_profit_margin }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Examples</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Greenhouse Film (0.2 microns)</h6>
                        <p class="small text-muted">100 kg ÷ 0.2 = 500 sqm</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">Dam Liner (0.5 microns)</h6>
                        <p class="small text-muted">50 kg ÷ 0.5 = 100 sqm</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">Dam Liner (1.0 microns)</h6>
                        <p class="small text-muted">200 kg ÷ 1.0 = 200 sqm</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conversionType = document.getElementById('conversion_type');
    const purchaseQuantity = document.getElementById('purchase_quantity');
    const conversionFactor = document.getElementById('conversion_factor');
    const purchaseUnit = document.getElementById('purchase_unit');
    const saleUnit = document.getElementById('sale_unit');
    const convertedQuantity = document.getElementById('converted_quantity');
    const conversionFormula = document.getElementById('conversion_formula');
    const profitMargin = document.getElementById('profit_margin');

    function calculateConversion() {
        const quantity = parseFloat(purchaseQuantity.value) || 0;
        const factor = parseFloat(conversionFactor.value) || 0;
        const type = conversionType.value;
        const pUnit = purchaseUnit.value;
        const sUnit = saleUnit.value;

        if (quantity > 0 && factor > 0) {
            let result;
            let formula;

            switch(type) {
                case 'weight_to_area':
                    result = quantity / factor;
                    formula = `${quantity} ${pUnit} ÷ ${factor} = ${result.toFixed(2)} ${sUnit}`;
                    break;
                case 'area_to_weight':
                    result = quantity * factor;
                    formula = `${quantity} ${pUnit} × ${factor} = ${result.toFixed(2)} ${sUnit}`;
                    break;
                case 'custom':
                    result = quantity * factor;
                    formula = `${quantity} ${pUnit} × ${factor} = ${result.toFixed(2)} ${sUnit}`;
                    break;
                default:
                    result = quantity;
                    formula = `${quantity} ${pUnit} = ${result.toFixed(2)} ${sUnit}`;
            }

            convertedQuantity.textContent = `${result.toFixed(2)} ${sUnit}`;
            conversionFormula.textContent = formula;
        }
    }

    // Add event listeners
    [conversionType, purchaseQuantity, conversionFactor, purchaseUnit, saleUnit].forEach(element => {
        element.addEventListener('change', calculateConversion);
        element.addEventListener('input', calculateConversion);
    });

    // Initial calculation
    calculateConversion();
});
</script>
@endsection






