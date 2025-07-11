@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Promotion</h1>
        <a href="{{ route('marketing.promotions') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Promotions
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Promotion Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('marketing.promotions.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Promotion Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Promotion Code</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ old('code', strtoupper(uniqid())) }}" readonly>
                                    <small class="form-text text-muted">Auto-generated unique code</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_type">Discount Type *</label>
                                    <select class="form-control @error('discount_type') is-invalid @enderror" 
                                            id="discount_type" name="discount_type" required>
                                        <option value="">Select Type</option>
                                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed_amount" {{ old('discount_type') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                    @error('discount_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_value">Discount Value *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('discount_value') is-invalid @enderror" 
                                               id="discount_value" name="discount_value" value="{{ old('discount_value') }}" 
                                               step="0.01" min="0" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="discount_suffix">₦</span>
                                        </div>
                                    </div>
                                    @error('discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minimum_amount">Minimum Order Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₦</span>
                                        </div>
                                        <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                               id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount') }}" 
                                               step="0.01" min="0">
                                    </div>
                                    @error('minimum_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usage_limit">Usage Limit</label>
                                    <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                           id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" 
                                           min="1" placeholder="Unlimited if empty">
                                    @error('usage_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch mt-4">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Applicable Products</label>
                            <div class="row">
                                @foreach($products as $product)
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="product_{{ $product->id }}" name="product_ids[]" 
                                               value="{{ $product->id }}" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="product_{{ $product->id }}">
                                            {{ $product->name }} - ₦{{ number_format($product->price) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">Leave unchecked to apply to all products</small>
                        </div>

                        <div class="form-group">
                            <label>Target Customers</label>
                            <div class="row">
                                @foreach($customers as $customer)
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="customer_{{ $customer->id }}" name="customer_ids[]" 
                                               value="{{ $customer->id }}" {{ in_array($customer->id, old('customer_ids', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customer_{{ $customer->id }}">
                                            {{ $customer->name }} ({{ $customer->email }})
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">Leave unchecked to apply to all customers</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Promotion
                            </button>
                            <a href="{{ route('marketing.promotions') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Promotion Preview</h6>
                </div>
                <div class="card-body">
                    <div id="preview-card" class="border rounded p-3 bg-light">
                        <h6 id="preview-name">Promotion Name</h6>
                        <p id="preview-description" class="text-muted">Description will appear here</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-primary" id="preview-code">CODE123</span>
                            <span class="text-success font-weight-bold" id="preview-discount">10% OFF</span>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted" id="preview-dates">Valid: Jan 1 - Jan 31, 2025</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.getElementById('discount_type');
    const discountValue = document.getElementById('discount_value');
    const discountSuffix = document.getElementById('discount_suffix');
    const name = document.getElementById('name');
    const description = document.getElementById('description');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    function updatePreview() {
        // Update discount suffix
        if (discountType.value === 'percentage') {
            discountSuffix.textContent = '%';
        } else {
            discountSuffix.textContent = '₦';
        }

        // Update preview card
        document.getElementById('preview-name').textContent = name.value || 'Promotion Name';
        document.getElementById('preview-description').textContent = description.value || 'Description will appear here';
        document.getElementById('preview-code').textContent = document.getElementById('code').value;
        
        if (discountType.value && discountValue.value) {
            const suffix = discountType.value === 'percentage' ? '%' : '₦';
            document.getElementById('preview-discount').textContent = `${discountValue.value}${suffix} OFF`;
        }

        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            const end = new Date(endDate.value).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('preview-dates').textContent = `Valid: ${start} - ${end}`;
        }
    }

    // Add event listeners
    [discountType, discountValue, name, description, startDate, endDate].forEach(element => {
        element.addEventListener('input', updatePreview);
    });

    // Set default dates
    if (!startDate.value) {
        startDate.value = new Date().toISOString().split('T')[0];
    }
    if (!endDate.value) {
        const endDateDefault = new Date();
        endDateDefault.setMonth(endDateDefault.getMonth() + 1);
        endDate.value = endDateDefault.toISOString().split('T')[0];
    }

    updatePreview();
});
</script>
@endpush 