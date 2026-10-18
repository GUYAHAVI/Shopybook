@extends('layouts.dash')

@section('title', 'Properties & Units')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Properties & Units</h4>
            <p class="text-muted mb-0">Manage your rental units and public vacancy listings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rentals.properties.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-city me-1"></i> Properties
            </a>
            <a href="{{ route('rentals.units.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Units
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($orphanCount > 0)
        <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <i class="fas fa-map-marker-alt me-1"></i>
                <strong>{{ $orphanCount }} unit{{ $orphanCount === 1 ? '' : 's' }}</strong> aren't linked to a property —
                they have no town/estate so they <strong>won't appear in location search</strong>.
            </div>
            @if($properties->isNotEmpty())
                <form method="POST" action="{{ route('rentals.units.assign-property') }}" class="d-flex gap-2 align-items-center">
                    @csrf
                    <select name="rental_property_id" class="form-select form-select-sm" required>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->name }}{{ $prop->location ? ' · '.$prop->location : '' }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark text-nowrap">Assign all</button>
                </form>
            @else
                <a href="{{ route('rentals.properties.index') }}" class="btn btn-sm btn-dark">Create a property first</a>
            @endif
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <select name="status" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    <option value="vacant" {{ request('status')==='vacant'?'selected':'' }}>Vacant</option>
                    <option value="occupied" {{ request('status')==='occupied'?'selected':'' }}>Occupied</option>
                    <option value="maintenance" {{ request('status')==='maintenance'?'selected':'' }}>Maintenance</option>
                </select>
                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" name="listed" value="1" id="listedOnly"
                           {{ request('listed') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label" for="listedOnly">Publicly listed only</label>
                </div>
            </form>
        </div>
    </div>

    @if($units->isEmpty())
        <div class="card"><div class="card-body text-center py-5 text-muted">
            <i class="fas fa-building fa-3x mb-3 d-block"></i>
            No units yet. <a href="{{ route('rentals.units.create') }}">Add your first units</a> — you can create many at once.
        </div></div>
    @else
    <div class="row g-3">
        @foreach($units as $unit)
        <div class="col-md-4 col-lg-3">
            <div class="card h-100">
                @if($unit->cover_image_url)
                    <img src="{{ $unit->cover_image_url }}" class="card-img-top" style="height:140px;object-fit:cover;" alt="{{ $unit->label }}">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:140px;">
                        <i class="fas fa-home fa-2x text-muted"></i>
                    </div>
                @endif
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $unit->label }}</h6>
                            <small class="text-muted">
                                {{ $unit->location_text ?: '—' }}
                                @if($unit->bedrooms !== null) · {{ $unit->bedrooms === 0 ? 'Bedsitter' : $unit->bedrooms.' bed' }}@endif
                            </small>
                        </div>
                        <span class="badge {{ $unit->status === 'occupied' ? 'bg-success' : ($unit->status === 'maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst($unit->status) }}
                        </span>
                    </div>
                    <div class="mt-2 fw-bold text-primary">KES {{ number_format($unit->rent_amount) }}<small class="text-muted fw-normal">/mo</small></div>
                    @if($unit->tenant)
                        <small class="text-muted d-block mt-1"><i class="fas fa-user me-1"></i>{{ $unit->tenant->name }}</small>
                    @endif
                </div>
                <div class="card-footer bg-transparent p-2 d-flex gap-1">
                    <a href="{{ route('rentals.units.edit', $unit) }}" class="btn btn-sm btn-outline-primary flex-fill">Edit</a>
                    @if($unit->status === 'vacant' || $unit->is_listed)
                    <form method="POST" action="{{ route('rentals.units.toggle-listing', $unit) }}" class="flex-fill">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $unit->is_listed ? 'btn-warning' : 'btn-outline-success' }} w-100" title="Toggle public listing">
                            <i class="fas {{ $unit->is_listed ? 'fa-eye-slash' : 'fa-bullhorn' }}"></i>
                            {{ $unit->is_listed ? 'Unlist' : 'List' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $units->links() }}</div>
    @endif
</div>
@endsection
