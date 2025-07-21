@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Add Service</h2>
                
                @if(config('app.debug'))
                    <div class="alert alert-info">
                        <strong>Debug Info:</strong> 
                        Available services for bundling: {{ isset($services) ? $services->count() : 'No services variable' }}
                        @if(isset($services) && $services->count() > 0)
                            <br>Services: {{ $services->pluck('name')->join(', ') }}
                        @endif
                    </div>
                @endif
                
                <form method="POST" action="{{ route('services.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Service Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
                        @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration') }}">
                        @error('duration')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="commission_rate" class="form-label">Commission Rate (%)</label>
                        <input type="number" step="0.01" class="form-control" id="commission_rate" name="commission_rate" value="{{ old('commission_rate') }}">
                        @error('commission_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <!-- Service Bundling Section -->
                    <div class="card bg-light mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Service Bundling</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_bundle_trigger" name="is_bundle_trigger" value="1" 
                                           {{ old('is_bundle_trigger') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_bundle_trigger">
                                        This service automatically includes other services
                                    </label>
                                </div>
                                <small class="text-muted">Example: Selecting "Shave" automatically includes "Aftershave"</small>
                            </div>

                            <div id="bundled-services-section" style="display: none;">
                                <div class="mb-3">
                                    <label for="bundled_services" class="form-label">Bundled Services</label>
                                    <select class="form-control" id="bundled_services" name="bundled_services[]" multiple>
                                        @if(isset($services) && $services->count() > 0)
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }} (KSh {{ number_format($service->price, 2) }})</option>
                                            @endforeach
                                        @else
                                            <option disabled>No services available yet. Create some services first.</option>
                                        @endif
                                    </select>
                                    <small class="text-muted">Hold Ctrl to select multiple services that will be automatically included</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_complimentary" name="is_complimentary" value="1"
                                           {{ old('is_complimentary') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_complimentary">
                                        This service is complimentary (free with another service)
                                    </label>
                                </div>
                                <small class="text-muted">Commission will be calculated from the parent service cost</small>
                            </div>

                            <div id="parent-service-section" style="display: none;">
                                <div class="mb-3">
                                    <label for="parent_service_id" class="form-label">Parent Service</label>
                                    <select class="form-control" id="parent_service_id" name="parent_service_id">
                                        <option value="">Select Parent Service</option>
                                        @if(isset($services) && $services->count() > 0)
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" {{ old('parent_service_id') == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }} (KSh {{ number_format($service->price, 2) }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option disabled>No services available yet. Create some services first.</option>
                                        @endif
                                    </select>
                                    <small class="text-muted">The service that this complimentary service is bundled with</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bundleTriggerCheckbox = document.getElementById('is_bundle_trigger');
    const bundledServicesSection = document.getElementById('bundled-services-section');
    const complimentaryCheckbox = document.getElementById('is_complimentary');
    const parentServiceSection = document.getElementById('parent-service-section');

    // Show/hide bundled services section
    bundleTriggerCheckbox.addEventListener('change', function() {
        bundledServicesSection.style.display = this.checked ? 'block' : 'none';
    });

    // Show/hide parent service section
    complimentaryCheckbox.addEventListener('change', function() {
        parentServiceSection.style.display = this.checked ? 'block' : 'none';
        
        // If complimentary is checked, set price to 0
        if (this.checked) {
            document.getElementById('price').value = '0.00';
            document.getElementById('price').readOnly = true;
        } else {
            document.getElementById('price').readOnly = false;
        }
    });

    // Initialize on page load
    if (bundleTriggerCheckbox.checked) {
        bundledServicesSection.style.display = 'block';
    }
    if (complimentaryCheckbox.checked) {
        parentServiceSection.style.display = 'block';
        document.getElementById('price').value = '0.00';
        document.getElementById('price').readOnly = true;
    }
});
</script>
@endsection 
 