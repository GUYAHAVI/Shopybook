@extends('layouts.dash')

@section('title', 'Edit Unit')

@section('content')
<div class="container-fluid" style="max-width:720px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('rentals.units.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h4 class="mb-1">Edit {{ $unit->label }}</h4>
            <p class="text-muted mb-0">
                <span class="badge {{ $unit->status === 'occupied' ? 'bg-success' : ($unit->status === 'maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ ucfirst($unit->status) }}</span>
                @if($unit->is_listed)<span class="badge bg-info ms-1"><i class="fas fa-bullhorn"></i> Publicly listed</span>@endif
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('rentals.units.update', $unit) }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Label <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control" value="{{ old('label', $unit->label) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="vacant" {{ $unit->status==='vacant'?'selected':'' }}>Vacant</option>
                            <option value="occupied" {{ $unit->status==='occupied'?'selected':'' }}>Occupied</option>
                            <option value="maintenance" {{ $unit->status==='maintenance'?'selected':'' }}>Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Property <span class="text-danger">*</span></label>
                    <select name="rental_property_id" class="form-select" required>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}" {{ $unit->rental_property_id===$prop->id?'selected':'' }}>
                                {{ $prop->name }}{{ $prop->location ? ' · '.$prop->location : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">The property's town/estate is what house-seekers search by.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Rent / month (KES)</label>
                        <input type="number" name="rent_amount" class="form-control" value="{{ old('rent_amount', $unit->rent_amount) }}" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bedrooms</label>
                        <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms', $unit->bedrooms) }}" min="0" max="20">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit type</label>
                        <select name="unit_type" class="form-select">
                            @foreach(['house','bedsitter','single','apartment','shop','office','airbnb'] as $t)
                                <option value="{{ $t }}" {{ $unit->unit_type===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0">Description</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="aiDescBtn" onclick="enhanceDesc()">
                            <i class="fas fa-wand-magic-sparkles me-1"></i> Enhance with AI
                        </button>
                    </div>
                    <textarea name="description" id="descField" class="form-control mt-1" rows="3">{{ old('description', $unit->description) }}</textarea>
                </div>

                @if($unit->images)
                <div class="mb-3">
                    <label class="form-label">Current images</label>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($unit->images as $path)
                        <div class="position-relative">
                            <img src="{{ asset('storage/'.$path) }}" style="width:90px;height:70px;object-fit:cover;border-radius:6px;">
                            <div class="form-check position-absolute top-0 end-0 bg-white rounded p-1" style="font-size:.7rem;">
                                <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $path }}" title="Remove">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="form-text">Tick an image to remove it.</div>
                </div>
                @endif

                <div class="mb-4">
                    <label class="form-label">Add images (max 6 total)</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Save Changes</button>
                </div>
            </form>

            <form method="POST" action="{{ route('rentals.units.destroy', $unit) }}" class="mt-2"
                  onsubmit="return confirm('Delete {{ addslashes($unit->label) }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100">Delete Unit</button>
            </form>
        </div>
    </div>
</div>

<script>
async function enhanceDesc() {
    const btn  = document.getElementById('aiDescBtn');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Thinking…';
    try {
        const r = await fetch('{{ route('rentals.units.enhance-description') }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({
                description: document.getElementById('descField').value,
                label:       document.querySelector('[name=label]')?.value,
                unit_type:   document.querySelector('[name=unit_type]')?.value,
                bedrooms:    document.querySelector('[name=bedrooms]')?.value,
                rent_amount: document.querySelector('[name=rent_amount]')?.value,
                location:    document.querySelector('[name=rental_property_id]')?.selectedOptions[0]?.textContent?.trim(),
            }),
        });
        const d = await r.json();
        if (d.description) {
            document.getElementById('descField').value = d.description;
        } else {
            alert(d.error || 'Could not enhance — try again.');
        }
    } catch (e) {
        alert('AI request failed — check your connection.');
    }
    btn.disabled = false;
    btn.innerHTML = orig;
}
</script>
@endsection
