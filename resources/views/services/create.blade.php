@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Services -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('services.index') }}" class="nav-tab">
                <i class="fas fa-list me-1"></i> All Services
            </a>
            <a href="{{ route('services.create') }}" class="nav-tab active">
                <i class="fas fa-plus me-1"></i> Add Service
            </a>
            <a href="{{ route('service-bookings.index') }}" class="nav-tab">
                <i class="fas fa-calendar-check me-1"></i> Bookings
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <h2 class="fw-bold mb-4" style="color: var(--text-primary);">Add Service</h2>
                
                @if(config('app.debug'))
                    <div class="alert alert-info" style="background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary);">
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
                        <label for="name" class="form-label" style="color: var(--text-primary);">Service Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label" style="color: var(--text-primary);">Price (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label" style="color: var(--text-primary);">Duration (minutes)</label>
                        <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration') }}"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('duration')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="commission_rate" class="form-label" style="color: var(--text-primary);">Commission Rate (%)</label>
                        <input type="number" step="0.01" class="form-control" id="commission_rate" name="commission_rate" value="{{ old('commission_rate') }}"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('commission_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label" style="color: var(--text-primary);">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">{{ old('description') }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <!-- Service Bundling Section -->
                    <div class="card mb-3" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                        <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                            <h6 class="mb-0" style="color: var(--text-primary);">Service Bundling</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_bundle_trigger" name="is_bundle_trigger" value="1" 
                                           {{ old('is_bundle_trigger') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_bundle_trigger" style="color: var(--text-primary);">
                                        This service automatically includes other services
                                    </label>
                                </div>
                                <small class="text-muted" style="color: var(--text-muted);">Example: Selecting "Shave" automatically includes "Aftershave"</small>
                            </div>

                            <div id="bundled-services-section" style="display: none;">
                                <div class="mb-3">
                                    <label for="bundled_services" class="form-label" style="color: var(--text-primary);">Bundled Services</label>
                                    <select class="form-control" id="bundled_services" name="bundled_services[]" multiple
                                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                        @if(isset($services) && $services->count() > 0)
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }} (KSh {{ number_format($service->price, 2) }})</option>
                                            @endforeach
                                        @else
                                            <option disabled>No services available yet. Create some services first.</option>
                                        @endif
                                    </select>
                                    <small class="text-muted" style="color: var(--text-muted);">Hold Ctrl to select multiple services that will be automatically included</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_complimentary" name="is_complimentary" value="1"
                                           {{ old('is_complimentary') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_complimentary" style="color: var(--text-primary);">
                                        This service is complimentary (free with another service)
                                    </label>
                                </div>
                                <small class="text-muted" style="color: var(--text-muted);">Commission will be calculated from the parent service cost</small>
                            </div>

                            <div id="parent-service-section" style="display: none;">
                                <div class="mb-3">
                                    <label for="parent_service_id" class="form-label" style="color: var(--text-primary);">Parent Service</label>
                                    <select class="form-control" id="parent_service_id" name="parent_service_id"
                                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
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
                                    <small class="text-muted" style="color: var(--text-muted);">Select the service that this complimentary service will be included with</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Services
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Create Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.nav-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.nav-tab {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
    border-color: var(--border-color);
}

.nav-tab.active {
    color: var(--white);
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tab {
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bundleTriggerCheckbox = document.getElementById('is_bundle_trigger');
    const bundledServicesSection = document.getElementById('bundled-services-section');
    const complimentaryCheckbox = document.getElementById('is_complimentary');
    const parentServiceSection = document.getElementById('parent-service-section');

    // Handle bundle trigger checkbox
    bundleTriggerCheckbox.addEventListener('change', function() {
        if (this.checked) {
            bundledServicesSection.style.display = 'block';
            // Uncheck complimentary if bundle trigger is checked
            complimentaryCheckbox.checked = false;
            parentServiceSection.style.display = 'none';
        } else {
            bundledServicesSection.style.display = 'none';
        }
    });

    // Handle complimentary checkbox
    complimentaryCheckbox.addEventListener('change', function() {
        if (this.checked) {
            parentServiceSection.style.display = 'block';
            // Uncheck bundle trigger if complimentary is checked
            bundleTriggerCheckbox.checked = false;
            bundledServicesSection.style.display = 'none';
        } else {
            parentServiceSection.style.display = 'none';
        }
    });

    // Initialize state based on old values
    if (bundleTriggerCheckbox.checked) {
        bundledServicesSection.style.display = 'block';
    }
    if (complimentaryCheckbox.checked) {
        parentServiceSection.style.display = 'block';
    }
});
</script>
@endsection 
 