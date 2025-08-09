@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Services -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('services.index') }}" class="nav-tab">
                <i class="fas fa-list me-1"></i> All Services
            </a>
            <a href="{{ route('services.create') }}" class="nav-tab">
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
                <h2 class="fw-bold mb-4" style="color: var(--text-primary);">Edit Service</h2>
                <form method="POST" action="{{ route('services.update', $service) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label" style="color: var(--text-primary);">Service Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $service->name) }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label" style="color: var(--text-primary);">Price (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $service->price) }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label" style="color: var(--text-primary);">Duration (minutes)</label>
                        <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration', $service->duration) }}"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('duration')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="commission_rate" class="form-label" style="color: var(--text-primary);">Commission Rate (%)</label>
                        <input type="number" step="0.01" class="form-control" id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $service->commission_rate) }}"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('commission_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label" style="color: var(--text-primary);">Description</label>

                        <x-ai-enhanced-textarea name="description" 
                                               content-type="description" 
                                               tone="professional" 
                                               rows="3" 
                                               placeholder="Enter service description...">
                            {{ old('description', $service->description) }}
                        </x-ai-enhanced-textarea>

                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Services
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Service
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
@endsection 