@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dynamic Conversion Calculator</h1>
            <p class="text-muted">Flexible selling for {{ $product->name }} - convert on-demand based on customer preference</p>
            <div class="mt-2">
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-star me-1"></i>Exclusive to Havi's Greenhouse Materials
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product-conversions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Conversions
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-2"></i>
                        Dynamic Conversion Calculator
                    </h6>
                </div>
                <div class="card-body">
                    <form id="dynamicConversionForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity *</label>
                                    <input type="number" step="0.01" min="0.01" id="quantity" name="quantity" 
                                           class="form-control" placeholder="Enter quantity" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="from_unit" class="form-label">From Unit *</label>
                                    <select id="from_unit" name="from_unit" class="form-select" required>
                                        <option value="">Select unit</option>
                                        @foreach($conversionOptions as $option)
                                            <option value="{{ $option['from_unit'] }}">{{ $option['from_unit'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="to_unit" class="form-label">To Unit *</label>
                                    <select id="to_unit" name="to_unit" class="form-select" required>
                                        <option value="">Select unit</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profit_margin" class="form-label">Desired Profit Margin (%)</label>
                                    <input type="number" step="0.1" min="0" max="100" id="profit_margin" name="profit_margin" 
                                           class="form-control" value="20" placeholder="20">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-calculator me-2"></i>Calculate Conversion
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Results Section -->
                    <div id="conversionResults" class="mt-4" style="display: none;">
                        <hr>
                        <h5 class="text-primary mb-3">Conversion Results</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Conversion Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Formula:</strong> <span id="formula"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Converted Quantity:</strong> <span id="converted_quantity"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Conversion Factor:</strong> <span id="conversion_factor"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Financial Analysis</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Purchase Total:</strong> <span id="purchase_total"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Sale Total:</strong> <span id="sale_total"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Profit:</strong> <span id="profit"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Profit Margin:</strong> <span id="profit_margin_result"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Pricing Suggestions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <strong>Current Sale Price:</strong> <span id="current_sale_price"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <strong>Suggested Sale Price:</strong> <span id="suggested_sale_price"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <strong>Purchase Cost per Unit:</strong> <span id="purchase_cost_per_unit"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Product Information -->
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
                        <label class="fw-bold">Current Stock:</label>
                        <p class="h5">{{ number_format($product->stock_quantity ?? 0, 2) }} {{ $product->unit ?? 'units' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Available Conversions:</label>
                        <p class="h5 text-primary">{{ count($conversionOptions) }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Examples -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Examples</h6>
                </div>
                <div class="card-body">
                    @if(count($quickExamples) > 0)
                        @foreach($quickExamples as $example)
                            <div class="mb-3">
                                <h6 class="text-primary">{{ $example['from_unit'] }} → {{ $example['to_unit'] }}</h6>
                                <p class="small text-muted mb-1">{{ $example['example'] }}</p>
                                <small class="text-muted">Factor: {{ $example['factor'] }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No conversion examples available. Create conversion rules first.</p>
                    @endif
                </div>
            </div>

            <!-- Available Conversions -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Conversions</h6>
                </div>
                <div class="card-body">
                    @if(count($conversionOptions) > 0)
                        @foreach($conversionOptions as $option)
                            <div class="mb-3">
                                <h6 class="text-success">{{ $option['label'] }}</h6>
                                <p class="small text-muted mb-1">{{ $option['description'] }}</p>
                                <small class="text-muted">
                                    Cost: KSh {{ number_format($option['purchase_cost'], 2) }} | 
                                    Price: KSh {{ number_format($option['sale_price'], 2) }}
                                </small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No conversion rules set up. Create conversion rules first.</p>
                        <a href="{{ route('product-conversions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Conversion Rule
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('dynamicConversionForm');
    const fromUnitSelect = document.getElementById('from_unit');
    const toUnitSelect = document.getElementById('to_unit');
    const resultsDiv = document.getElementById('conversionResults');
    
    const conversionOptions = @json($conversionOptions);

    // Update available "to" units when "from" unit changes
    fromUnitSelect.addEventListener('change', function() {
        const fromUnit = this.value;
        toUnitSelect.innerHTML = '<option value="">Select unit</option>';
        
        if (fromUnit) {
            const availableConversions = conversionOptions.filter(option => option.from_unit === fromUnit);
            availableConversions.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.to_unit;
                optionElement.textContent = option.to_unit;
                toUnitSelect.appendChild(optionElement);
            });
        }
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const productId = {{ $product->id }};
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calculating...';
        submitBtn.disabled = true;

        // Calculate conversion
        fetch(`/product-conversions/product/${productId}/calculate-dynamic`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data);
                
                // Get suggested price
                const profitMargin = document.getElementById('profit_margin').value;
                return fetch(`/product-conversions/product/${productId}/suggested-price`, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            } else {
                alert('Error: ' + data.message);
            }
        })
        .then(response => response.json())
        .then(suggestedData => {
            if (suggestedData.success) {
                displaySuggestedPrice(suggestedData);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while calculating the conversion.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function displayResults(data) {
        document.getElementById('formula').textContent = data.formula;
        document.getElementById('converted_quantity').textContent = `${data.to_quantity} ${data.to_unit}`;
        document.getElementById('conversion_factor').textContent = data.conversion_factor;
        document.getElementById('purchase_total').textContent = `KSh ${data.purchase_total.toFixed(2)}`;
        document.getElementById('sale_total').textContent = `KSh ${data.sale_total.toFixed(2)}`;
        document.getElementById('profit').textContent = `KSh ${data.profit.toFixed(2)}`;
        document.getElementById('profit_margin_result').textContent = `${data.profit_margin.toFixed(1)}%`;
        document.getElementById('current_sale_price').textContent = `KSh ${data.sale_price_per_unit.toFixed(2)} per ${data.to_unit}`;
        document.getElementById('purchase_cost_per_unit').textContent = `KSh ${data.purchase_cost_per_unit.toFixed(2)} per ${data.from_unit}`;
        
        resultsDiv.style.display = 'block';
    }

    function displaySuggestedPrice(data) {
        document.getElementById('suggested_sale_price').textContent = `KSh ${data.suggested_sale_price.toFixed(2)} per ${data.to_unit}`;
    }
});
</script>
@endsection
