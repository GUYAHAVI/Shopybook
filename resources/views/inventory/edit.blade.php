@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="fas fa-edit me-2"></i>Edit {{ $inventory->name }}
                    </h2>
                    <p class="text-muted mb-0">Update inventory item information</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                        @csrf
                        @method('PUT')

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
                                       id="name" name="name" value="{{ old('name', $inventory->name) }}" required
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
                                        <option value="{{ $key }}" {{ old('category', $inventory->category) == $key ? 'selected' : '' }}>
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
                                          placeholder="Brief description of the item">{{ old('description', $inventory->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Stock Display -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-warehouse me-2"></i>Inventory Details
                                </h5>
                                <div class="alert alert-info">
                                    <strong>Current Stock:</strong> {{ $inventory->current_quantity }} {{ $inventory->unit_type }}
                                    <br><small>Use the "Adjust Stock" feature to change quantities</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="unit_type" class="form-label">Unit Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('unit_type') is-invalid @enderror" 
                                        id="unit_type" name="unit_type" required>
                                    <option value="">Select Unit</option>
                                    @foreach(\App\Models\InventoryItem::getUnitTypes() as $key => $label)
                                        <option value="{{ $key }}" {{ old('unit_type', $inventory->unit_type) == $key ? 'selected' : '' }}>
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
                                       id="unit_cost" name="unit_cost" value="{{ old('unit_cost', $inventory->unit_cost) }}" 
                                       step="0.01" min="0" required
                                       placeholder="0.00">
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="minimum_quantity" class="form-label">Minimum Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('minimum_quantity') is-invalid @enderror" 
                                       id="minimum_quantity" name="minimum_quantity" value="{{ old('minimum_quantity', $inventory->minimum_quantity) }}" 
                                       min="0" required>
                                <small class="text-muted">Reorder level</small>
                                @error('minimum_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="maximum_quantity" class="form-label">Maximum Quantity</label>
                                <input type="number" class="form-control @error('maximum_quantity') is-invalid @enderror" 
                                       id="maximum_quantity" name="maximum_quantity" value="{{ old('maximum_quantity', $inventory->maximum_quantity) }}" 
                                       min="0" placeholder="Optional">
                                <small class="text-muted">Storage capacity</small>
                                @error('maximum_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="storage_location" class="form-label">Storage Location</label>
                                <input type="text" class="form-control @error('storage_location') is-invalid @enderror" 
                                       id="storage_location" name="storage_location" value="{{ old('storage_location', $inventory->storage_location) }}"
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
                                       id="supplier" name="supplier" value="{{ old('supplier', $inventory->supplier) }}"
                                       placeholder="Supplier name">
                                @error('supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand', $inventory->brand) }}"
                                       placeholder="Brand name">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model', $inventory->model) }}"
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
                                       id="purchase_date" name="purchase_date" 
                                       value="{{ old('purchase_date', $inventory->purchase_date?->format('Y-m-d')) }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                       id="expiry_date" name="expiry_date" 
                                       value="{{ old('expiry_date', $inventory->expiry_date?->format('Y-m-d')) }}"
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
                                          placeholder="Any additional notes about this item...">{{ old('notes', $inventory->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-1"></i> Delete Item
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Item
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Inventory Item
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $inventory->name }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This will also delete all transaction history for this item. This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('inventory.destroy', $inventory) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete Item
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
