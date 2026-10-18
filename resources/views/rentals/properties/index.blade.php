@extends('layouts.dash')

@section('title', 'Properties')

@section('content')
<div class="container-fluid" style="max-width:820px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Properties</h4>
            <p class="text-muted mb-0">Buildings/estates that group your units (optional)</p>
        </div>
        <a href="{{ route('rentals.units.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Units</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="mb-3">Add Property</h6>
            <form method="POST" action="{{ route('rentals.properties.store') }}" class="row g-2">
                @csrf
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Name — e.g. Kiritiri Court" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="town" class="form-control" placeholder="Town — e.g. Embu">
                </div>
                <div class="col-md-3">
                    <input type="text" name="estate" class="form-control" placeholder="Estate — e.g. Kiritiri">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Add</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Location</th><th class="text-center">Units</th><th></th></tr></thead>
                <tbody>
                @forelse($properties as $prop)
                    <tr>
                        <td class="fw-semibold">{{ $prop->name }}</td>
                        <td>{{ $prop->location ?: '—' }}</td>
                        <td class="text-center">{{ $prop->units_count }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('rentals.properties.destroy', $prop) }}" class="d-inline"
                                  onsubmit="return confirm('Remove {{ addslashes($prop->name) }}? Units are kept but unlinked.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No properties yet — add one above, or just create standalone units.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
