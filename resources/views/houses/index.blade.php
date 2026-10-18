@extends('layouts.public')

@if($townName)
    @section('title', 'Houses for Rent in ' . $townName . ' — Vacant Bedsitters, 1 & 2 Bedrooms | Shopybook')
    @section('meta_description', $units->total() . ' vacant houses for rent in ' . $townName . ', Kenya. Bedsitters, 1 and 2 bedroom houses listed directly by landlords — view photos, rent prices and book a viewing.')
    @section('canonical', route('houses.town', \Illuminate\Support\Str::slug($townName)))
@else
    @section('title', 'Houses for Rent in Kenya — Find Vacant Bedsitters & Apartments | Shopybook')
    @section('meta_description', 'Find vacant houses for rent in Kenya. Describe what you want — bedsitter in Kiritiri, 2-bedroom in Embu — and get matched with rentals listed directly by landlords on Shopybook.')
    @section('canonical', route('houses.index'))
@endif
{{-- Filtered/AI search result pages are near-duplicates: keep them out of the index but let crawlers follow listings --}}
@if(request()->except('page') && !$townName)
    @section('robots', 'noindex, follow')
@endif

@if($units->isNotEmpty())
@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $townName ? "Houses for rent in {$townName}" : 'Houses for rent in Kenya',
    'itemListElement' => $units->values()->map(fn($u, $i) => [
        '@type' => 'ListItem',
        'position' => $i + 1 + ($units->currentPage() - 1) * $units->perPage(),
        'url' => route('houses.show', $u),
        'name' => $u->label . ($u->location_text ? ' — ' . $u->location_text : ''),
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endif

@section('content')
{{-- Hero search --}}
<div style="background: linear-gradient(135deg,#7b2e2e 0%,#4a1a1a 60%,#2d1111 100%); padding: 4rem 0 5rem;">
    <div class="container">
        <div class="text-center text-white mb-4">
            @if($townName)
                <h1 class="fw-bold display-6 mb-2">Houses for Rent in {{ $townName }}</h1>
                <p class="mb-0 opacity-75">{{ $units->total() }} vacant {{ $units->total() === 1 ? 'house' : 'houses' }} in {{ $townName }} — listed directly by landlords.</p>
            @else
                <h1 class="fw-bold display-6 mb-2">Find your next home</h1>
                <p class="mb-0 opacity-75">Describe the house you want — we'll match you with vacant rentals across Kenya.</p>
            @endif
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <form method="GET" class="bg-white rounded-3 shadow-lg p-2 d-flex flex-column flex-md-row gap-2">
                    <div class="flex-grow-1 d-flex align-items-center px-2">
                        <i class="fas fa-search text-muted me-2"></i>
                        <input type="text" name="q" class="form-control border-0 shadow-none"
                               style="font-size:1.05rem;"
                               placeholder='Try: "bedsitter in Kiritiri under 5,000" or "2-bedroom house Embu town"'
                               value="{{ request('q') }}">
                    </div>
                    <button class="btn btn-primary btn-lg px-4" type="submit">
                        <i class="fas fa-wand-magic-sparkles me-1"></i> Find it
                    </button>
                </form>

                {{-- Refinement filters --}}
                <form method="GET" class="d-flex flex-wrap gap-2 mt-3">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <select name="town" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">📍 Any town</option>
                        @foreach($towns as $town)
                            <option value="{{ $town }}" {{ request('town')===$town?'selected':'' }}>{{ $town }}</option>
                        @endforeach
                    </select>
                    <select name="unit_type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">🏠 Any type</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('unit_type')===$type?'selected':'' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="max_rent" class="form-control form-control-sm" style="width:150px;" placeholder="Max rent (KES)" value="{{ request('max_rent') }}">
                    <select name="bedrooms" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">🛏 Beds</option>
                        <option value="0" {{ request('bedrooms')==='0'?'selected':'' }}>Bedsitter</option>
                        @for($i=1;$i<=4;$i++)<option value="{{ $i }}" {{ request('bedrooms')==(string)$i?'selected':'' }}>{{ $i }} bedroom{{ $i>1?'s':'' }}</option>@endfor
                    </select>
                    <button class="btn btn-sm btn-light">Apply</button>
                    @if(request()->anyFilled(['q','town','unit_type','max_rent','bedrooms']))
                        <a href="{{ route('houses.index') }}" class="btn btn-sm btn-outline-light">Clear</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container py-4" style="margin-top:-2rem;">
    {{-- AI interpretation + safety --}}
    @if($aiSummary)
        <div class="alert d-flex align-items-center gap-2 border-0 shadow-sm mb-3" style="background:#eef6ff;">
            <i class="fas fa-wand-magic-sparkles text-primary"></i>
            <div>
                <strong>Understood:</strong> {{ $aiSummary }}
                <small class="text-muted d-block">Showing {{ $units->total() }} match{{ $units->total() === 1 ? '' : 'es' }} — refine with the filters above if needed.</small>
            </div>
        </div>
    @endif

    <div class="d-flex align-items-center gap-2 small text-muted mb-3">
        <i class="fas fa-shield-alt text-warning"></i>
        <span>Only pay after viewing a house and verifying ownership — Shopybook connects you but doesn't verify landlords or handle deposits.</span>
    </div>

    {{-- Results --}}
    @if($units->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-home fa-3x mb-3 d-block opacity-50"></i>
                <h5>No vacant houses match that yet.</h5>
                <p class="mb-3">Try fewer words or remove a filter — new listings appear as landlords publish them.</p>
                <a href="{{ route('houses.index') }}" class="btn btn-outline-primary">See all houses</a>
            </div>
        </div>
    @else
        <p class="text-muted small mb-3">{{ $units->total() }} vacant {{ $units->total() === 1 ? 'house' : 'houses' }} found</p>
        <div class="row g-4">
            @foreach($units as $unit)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('houses.show', $unit) }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm overflow-hidden house-card">
                    <div class="position-relative">
                        @if($unit->cover_image_url)
                            <img src="{{ $unit->cover_image_url }}" style="height:200px;width:100%;object-fit:cover;" alt="{{ $unit->label }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="height:200px;background:linear-gradient(135deg,#f3ece9,#e8ddd9);">
                                <i class="fas fa-home fa-3x" style="color:#c9b8b3;"></i>
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 start-0 m-2 px-2 py-1 rounded fw-bold text-white" style="background:rgba(123,46,46,.92);font-size:.85rem;">
                            KES {{ number_format($unit->rent_amount) }}<small class="fw-normal">/mo</small>
                        </span>
                        @if($unit->unit_type)
                        <span class="position-absolute top-0 end-0 m-2 badge bg-light text-dark">{{ ucfirst($unit->unit_type) }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold text-dark mb-1">{{ $unit->label }}</h6>
                        <p class="text-muted small mb-1">
                            <i class="fas fa-map-marker-alt me-1" style="color:#7b2e2e;"></i>{{ $unit->location_text ?: 'Location on request' }}
                        </p>
                        <p class="small text-muted mb-0">
                            {{ $unit->bedrooms === null ? '' : ($unit->bedrooms === 0 ? '🛏 Bedsitter' : '🛏 '.$unit->bedrooms.' bedroom'.($unit->bedrooms>1?'s':'')) }}
                            @if($unit->property)<span class="ms-1">· {{ $unit->property->name }}</span>@endif
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <span class="btn btn-sm btn-outline-primary w-100">View & Book <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $units->links() }}</div>
    @endif

    @if($townLinks->isNotEmpty())
    <div class="mt-5">
        <h2 class="h6 fw-bold text-muted text-uppercase mb-3" style="letter-spacing:.05em;">Browse houses by town</h2>
        <div class="d-flex flex-wrap gap-2">
            @foreach($townLinks as $t)
                <a href="{{ route('houses.town', \Illuminate\Support\Str::slug($t)) }}"
                   class="btn btn-sm {{ $townName === $t ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-map-marker-alt me-1"></i>Houses for rent in {{ $t }}
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    .house-card { transition: transform .18s ease, box-shadow .18s ease; }
    .house-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.12)!important; }
    .house-card img { transition: transform .3s ease; }
    .house-card:hover img { transform: scale(1.04); }
</style>
@endsection
