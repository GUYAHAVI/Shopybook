@extends('layouts.dash')

@section('title', 'Add Units')

@section('content')
<div class="container-fluid" style="max-width:720px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('rentals.units.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h4 class="mb-1">Add Units</h4>
            <p class="text-muted mb-0">Create one unit or many identical units at once</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('rentals.units.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Property <span class="text-danger">*</span></label>
                    @if($properties->isEmpty())
                        <div class="alert alert-warning small mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            You need a property first — units get their town/estate from it, and unlocated units won't appear in public search.
                            <a href="{{ route('rentals.properties.index') }}" class="alert-link">Create a property</a>
                        </div>
                    @else
                        <select name="rental_property_id" class="form-select" required>
                            @foreach($properties as $prop)
                                <option value="{{ $prop->id }}" {{ $properties->count() === 1 || old('rental_property_id') == $prop->id ? 'selected' : '' }}>
                                    {{ $prop->name }}{{ $prop->location ? ' · '.$prop->location : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">The property's town/estate is what house-seekers search by. <a href="{{ route('rentals.properties.index') }}">Manage properties</a></div>
                    @endif
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label">Label / Prefix <span class="text-danger">*</span></label>
                        <input type="text" name="label_prefix" class="form-control" value="{{ old('label_prefix', 'House') }}"
                               placeholder="e.g. House, Room, Shop" required>
                        <div class="form-text">Creating multiple units auto-numbers them: House 1, House 2…</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">How many? <span class="text-danger">*</span></label>
                        <input type="number" name="count" class="form-control" value="{{ old('count', 1) }}" min="1" max="200" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Rent / month (KES) <span class="text-danger">*</span></label>
                        <input type="number" name="rent_amount" class="form-control" value="{{ old('rent_amount') }}" min="1" step="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bedrooms</label>
                        <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms') }}" min="0" max="20" placeholder="0 = bedsitter">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit type</label>
                        <select name="unit_type" class="form-select">
                            <option value="">—</option>
                            <option value="house">House</option>
                            <option value="bedsitter">Bedsitter</option>
                            <option value="single">Single Room</option>
                            <option value="apartment">Apartment</option>
                            <option value="shop">Shop</option>
                            <option value="office">Office</option>
                            <option value="airbnb">Airbnb / Short stay</option>
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
                    <textarea name="description" id="descField" class="form-control mt-1" rows="3"
                              placeholder="e.g. Self-contained, water included, near Kiritiri market…">{{ old('description') }}</textarea>
                    <div class="form-text">Rough notes are fine — AI turns them into a polished listing line (uses label, type, rent & property location).</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Images (shared across created units, max 6)</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="listed" value="1" id="listed" {{ old('listed', '1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="listed">
                        List on the public "Find a House" page <span class="text-muted">(while vacant — auto-unlisted when occupied)</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-plus me-2"></i>Create Units
                </button>
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
                label:       document.querySelector('[name=label_prefix]')?.value,
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
