@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Promotion</h1>
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
                    <form action="{{ route('marketing.promotions.update', $promotion) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Promotion Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $promotion->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Promotion Code</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ $promotion->code }}" readonly>
                                    <small class="form-text text-muted">Code cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $promotion->description) }}</textarea>
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
                                        <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed_amount" {{ old('discount_type', $promotion->discount_type) == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
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
                                               id="discount_value" name="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" 
                                               step="0.01" min="0" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="discount_suffix">{{ $promotion->discount_type === 'percentage' ? '%' : '₦' }}</span>
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
                                               id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', $promotion->minimum_amount) }}" 
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
                                           id="start_date" name="start_date" value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}" required>
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
                                           id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" 
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
                                               {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
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
                                               value="{{ $product->id }}" 
                                               {{ in_array($product->id, old('product_ids', $promotion->products->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                                               value="{{ $customer->id }}" 
                                               {{ in_array($customer->id, old('customer_ids', $promotion->customers->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                                <i class="fas fa-save"></i> Update Promotion
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
                    <h6 class="m-0 font-weight-bold text-primary">Promotion Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <div class="h4 text-primary">{{ $promotion->used_count }}</div>
                                <small class="text-muted">Times Used</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success">
                                @if($promotion->usage_limit)
                                    {{ round(($promotion->used_count / $promotion->usage_limit) * 100, 1) }}%
                                @else
                                    Unlimited
                                @endif
                            </div>
                            <small class="text-muted">Usage Rate</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status:</span>
                        <span class="badge badge-{{ $promotion->isActive() ? 'success' : 'secondary' }}">
                            {{ $promotion->isActive() ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Valid Until:</span>
                        <span>{{ $promotion->end_date->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Days Left:</span>
                        <span>{{ max(0, now()->diffInDays($promotion->end_date, false)) }} days</span>
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

    discountType.addEventListener('change', function() {
        if (this.value === 'percentage') {
            discountSuffix.textContent = '%';
        } else {
            discountSuffix.textContent = '₦';
        }
    });
});
</script>
@endpush 