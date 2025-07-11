@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
            <p class="text-muted">Update product information</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                           id="category" name="category" value="{{ old('category', $product->category) }}">
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Brand</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                           id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Selling Price (KSh) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">KSh</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost_price" class="form-label">Cost Price (KSh)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">KSh</span>
                                        <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                               id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" 
                                               step="0.01" min="0">
                                    </div>
                                    @error('cost_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                           min="0" required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                    <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                           id="low_stock_threshold" name="low_stock_threshold" 
                                           value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0">
                                    @error('low_stock_threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="measurement_unit" class="form-label">Measurement Unit *</label>
                                    <select class="form-control @error('measurement_unit') is-invalid @enderror" id="measurement_unit" name="measurement_unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="piece" @if(old('measurement_unit', $product->measurement_unit)=='piece') selected @endif>Piece (pcs)</option>
                                        <option value="kg" @if(old('measurement_unit', $product->measurement_unit)=='kg') selected @endif>Kilogram (kg)</option>
                                        <option value="g" @if(old('measurement_unit', $product->measurement_unit)=='g') selected @endif>Gram (g)</option>
                                        <option value="lb" @if(old('measurement_unit', $product->measurement_unit)=='lb') selected @endif>Pound (lb)</option>
                                        <option value="l" @if(old('measurement_unit', $product->measurement_unit)=='l') selected @endif>Litre (l)</option>
                                        <option value="ml" @if(old('measurement_unit', $product->measurement_unit)=='ml') selected @endif>Millilitre (ml)</option>
                                        <option value="m" @if(old('measurement_unit', $product->measurement_unit)=='m') selected @endif>Metre (m)</option>
                                        <option value="cm" @if(old('measurement_unit', $product->measurement_unit)=='cm') selected @endif>Centimetre (cm)</option>
                                        <option value="mm" @if(old('measurement_unit', $product->measurement_unit)=='mm') selected @endif>Millimetre (mm)</option>
                                        <option value="ft" @if(old('measurement_unit', $product->measurement_unit)=='ft') selected @endif>Foot (ft)</option>
                                        <option value="in" @if(old('measurement_unit', $product->measurement_unit)=='in') selected @endif>Inch (in)</option>
                                        <option value="other" @if(old('measurement_unit', $product->measurement_unit)=='other') selected @endif>Other</option>
                                    </select>
                                    @error('measurement_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Images -->
                        @if($product->images && count($product->images) > 0)
                        <div class="mb-3">
                            <label class="form-label">Current Images</label>
                            <div class="row">
                                @foreach($product->images as $index => $image)
                                <div class="col-md-3 mb-2">
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($image) }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                                onclick="removeImage({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="images" class="form-label">Add New Images</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">Select new images to add to the existing ones.</div>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" 
                                               name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Product
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured" 
                                               name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Product Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Created:</strong> {{ $product->created_at->format('M d, Y') }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated:</strong> {{ $product->updated_at->format('M d, Y') }}
                    </div>
                    <div class="mb-3">
                        <strong>Stock Status:</strong> 
                        <span class="badge bg-{{ $product->stock_status_color }}">
                            {{ $product->stock_status_text }}
                        </span>
                    </div>
                    @if($product->profit_margin)
                    <div class="mb-3">
                        <strong>Profit Margin:</strong> {{ $product->profit_margin_formatted }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function removeImage(index) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Add hidden input to track removed images
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'removed_images[]';
        input.value = index;
        document.querySelector('form').appendChild(input);
        
        // Hide the image container
        event.target.closest('.col-md-3').style.display = 'none';
    }
}
</script>
@endsection 