@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="fas fa-plus-circle me-2"></i>Add Inventory Item
                    </h2>
                    <p class="text-muted mb-0">Add a new item to your inventory</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('inventory.store') }}">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="e.g., Shaving Cream, Hair Clipper, Massage Oil">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" 
                                        id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach(\App\Models\InventoryItem::getCategories() as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="2"
                                          placeholder="Brief description of the item">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Inventory Details -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-warehouse me-2"></i>Inventory Details
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="unit_type" class="form-label">Unit Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('unit_type') is-invalid @enderror" 
                                        id="unit_type" name="unit_type" required>
                                    <option value="">Select Unit</option>
                                    @foreach(\App\Models\InventoryItem::getUnitTypes() as $key => $label)
                                        <option value="{{ $key }}" {{ old('unit_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="unit_cost" class="form-label">Unit Cost (KSh) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('unit_cost') is-invalid @enderror" 
                                       id="unit_cost" name="unit_cost" value="{{ old('unit_cost') }}" 
                                       step="0.01" min="0" required
                                       placeholder="0.00">
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="current_quantity" class="form-label">Initial Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('current_quantity') is-invalid @enderror" 
                                       id="current_quantity" name="current_quantity" value="{{ old('current_quantity', 0) }}" 
                                       min="0" required>
                                @error('current_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="minimum_quantity" class="form-label">Minimum Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('minimum_quantity') is-invalid @enderror" 
                                       id="minimum_quantity" name="minimum_quantity" value="{{ old('minimum_quantity', 0) }}" 
                                       min="0" required>
                                <small class="text-muted">Reorder level</small>
                                @error('minimum_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="maximum_quantity" class="form-label">Maximum Quantity</label>
                                <input type="number" class="form-control @error('maximum_quantity') is-invalid @enderror" 
                                       id="maximum_quantity" name="maximum_quantity" value="{{ old('maximum_quantity') }}" 
                                       min="0" placeholder="Optional">
                                <small class="text-muted">Storage capacity</small>
                                @error('maximum_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="storage_location" class="form-label">Storage Location</label>
                                <input type="text" class="form-control @error('storage_location') is-invalid @enderror" 
                                       id="storage_location" name="storage_location" value="{{ old('storage_location') }}"
                                       placeholder="e.g., Main Storage, Shelf A, Refrigerator">
                                @error('storage_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Supplier Information -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-truck me-2"></i>Supplier Information
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="supplier" class="form-label">Supplier</label>
                                <input type="text" class="form-control @error('supplier') is-invalid @enderror" 
                                       id="supplier" name="supplier" value="{{ old('supplier') }}"
                                       placeholder="Supplier name">
                                @error('supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand') }}"
                                       placeholder="Brand name">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model') }}"
                                       placeholder="Model number">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="purchase_date" class="form-label">Purchase Date</label>
                                <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                       id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                       id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                <small class="text-muted">For consumable items</small>
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Additional Information
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3"
                                          placeholder="Any additional notes about this item...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Add Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate total value
    const unitCost = document.getElementById('unit_cost');
    const quantity = document.getElementById('current_quantity');
    
    function updateCalculations() {
        // You can add real-time calculations here if needed
    }
    
    if (unitCost && quantity) {
        unitCost.addEventListener('input', updateCalculations);
        quantity.addEventListener('input', updateCalculations);
    }
});
</script>
@endpush
