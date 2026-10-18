@extends('layouts.public')

@section('title', 'Vacant Houses — ' . $business->name)
@section('meta_description', 'Vacant houses and rooms for rent from ' . $business->name . '. View listings and request a booking.')

@section('content')
<div class="container py-5" style="min-height:60vh;">
    <nav class="mb-4"><a href="{{ route('public.website.direct', $website->subdomain) }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i> {{ $business->name }}</a></nav>

    <div class="text-center mb-4">
        <h1 class="fw-bold">Vacant Houses — {{ $business->name }}</h1>
        <p class="text-muted">Available rentals listed by {{ $business->name }}. Book a viewing below.</p>
        <p class="small text-warning-emphasis mb-0"><i class="fas fa-shield-alt me-1"></i>Only pay after viewing a house and verifying ownership.</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="q" class="form-control" placeholder="Search — e.g. bedsitter, Kiritiri" value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <input type="number" name="max_rent" class="form-control" placeholder="Max rent (KES)" value="{{ request('max_rent') }}">
                </div>
                <div class="col-md-2">
                    <input type="number" name="bedrooms" class="form-control" placeholder="Beds" min="0" value="{{ request('bedrooms') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="fas fa-search me-1"></i>Search</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    @if($units->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fas fa-home fa-3x mb-3 d-block"></i>
            <h5>No vacant houses right now.</h5>
            <p>Check back soon or contact {{ $business->name }} directly.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($units as $unit)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    @if($unit->cover_image_url)
                        <img src="{{ $unit->cover_image_url }}" style="height:180px;object-fit:cover;" alt="{{ $unit->label }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:180px;"><i class="fas fa-home fa-2x text-muted"></i></div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0"><a href="{{ route('houses.show', $unit) }}" class="text-decoration-none text-dark">{{ $unit->label }}</a></h6>
                            <span class="fw-bold text-primary">KES {{ number_format($unit->rent_amount) }}<small class="text-muted fw-normal">/mo</small></span>
                        </div>
                        <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt me-1"></i>{{ $unit->location_text ?: 'Location on request' }}</p>
                        <p class="small text-muted mb-2">{{ $unit->bedrooms === null ? '' : ($unit->bedrooms === 0 ? 'Bedsitter · ' : $unit->bedrooms.' bed · ') }}{{ $unit->unit_type ? ucfirst($unit->unit_type) : '' }}</p>

                        {{-- Booking --}}
                        <button class="btn btn-sm btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#book{{ $unit->id }}">
                            <i class="fas fa-calendar-check me-1"></i> Book / Request Viewing
                        </button>
                        <div class="collapse mt-2" id="book{{ $unit->id }}">
                            <form method="POST" action="{{ route('public.website.houses.book', [$website->subdomain, $unit]) }}">
                                @csrf
                                <input type="text" name="name" class="form-control form-control-sm mb-1" placeholder="Your name" required>
                                <input type="tel" name="phone" class="form-control form-control-sm mb-1" placeholder="Phone" required>
                                <input type="date" name="preferred_move_in" class="form-control form-control-sm mb-1" title="Preferred move-in" min="{{ date('Y-m-d') }}">
                                <button class="btn btn-sm btn-outline-primary w-100">Send Request</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $units->links() }}</div>
    @endif
</div>
@endsection
