@extends('layouts.public')

@php
    $bedLabel = $unit->bedrooms === null ? null : ($unit->bedrooms === 0 ? 'Bedsitter' : $unit->bedrooms . ' Bedroom');
    $seoType  = trim(($bedLabel ?? '') . ' ' . ($unit->unit_type && $unit->bedrooms !== 0 ? ucfirst($unit->unit_type) : '')) ?: 'House';
    $town     = $unit->property?->town ? \Illuminate\Support\Str::title(mb_strtolower($unit->property->town)) : null;
    $estate   = $unit->property?->estate ? \Illuminate\Support\Str::title(mb_strtolower($unit->property->estate)) : null;
    $place    = collect([$estate, $town])->filter()->unique()->implode(', ') ?: 'Kenya';
    $images   = collect($unit->images ?? [])->map(fn($p) => asset('storage/' . $p));
@endphp
@section('title', "{$seoType} for Rent in {$place} — KES " . number_format($unit->rent_amount) . '/month | Shopybook')
@section('meta_description', "{$seoType} for rent in {$place} at KES " . number_format($unit->rent_amount) . '/month' . ($unit->property ? ' — ' . $unit->property->name : '') . '. ' . \Illuminate\Support\Str::limit($unit->description ?? 'View photos and book a viewing with the landlord on Shopybook.', 110))
@section('og_image', $images->first() ?? asset('img/heroimage.png'))
@section('canonical', route('houses.show', $unit))

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => array_values(array_filter([
        array_filter([
            '@type' => $unit->unit_type === 'house' ? 'House' : 'Apartment',
            'name' => "{$unit->label} — {$seoType} for rent in {$place}",
            'description' => $unit->description,
            'url' => route('houses.show', $unit),
            'image' => $images->all() ?: null,
            'numberOfBedrooms' => $unit->bedrooms,
            'numberOfRooms' => $unit->bedrooms !== null ? max(1, (int) $unit->bedrooms) : null,
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $unit->property?->name,
                'addressLocality' => $estate ?? $town,
                'addressRegion' => $town,
                'addressCountry' => 'KE',
            ]),
            'offers' => [
                '@type' => 'Offer',
                'price' => (float) $unit->rent_amount,
                'priceCurrency' => 'KES',
                'availability' => 'https://schema.org/InStock',
                'businessFunction' => 'http://purl.org/goodrelations/v1#LeaseOut',
                'priceSpecification' => ['@type' => 'UnitPriceSpecification', 'price' => (float) $unit->rent_amount, 'priceCurrency' => 'KES', 'unitText' => 'MONTH'],
                'seller' => ['@type' => 'Organization', 'name' => $unit->business->name],
            ],
        ], fn($v) => $v !== null && $v !== ''),
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Houses for rent', 'item' => route('houses.index')],
                $town ? ['@type' => 'ListItem', 'position' => 2, 'name' => "Houses in {$town}", 'item' => route('houses.town', \Illuminate\Support\Str::slug($town))] : null,
                ['@type' => 'ListItem', 'position' => $town ? 3 : 2, 'name' => $unit->label],
            ])),
        ],
    ])),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<div class="container py-5">
    <nav class="mb-3"><a href="{{ route('houses.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i> All listings</a></nav>

    <div class="row g-4">
        {{-- Gallery --}}
        <div class="col-lg-7">
            @if($unit->images && count($unit->images))
                <div id="unitGallery" class="carousel slide">
                    <div class="carousel-inner rounded overflow-hidden shadow-sm position-relative">
                        @foreach($unit->images as $i => $path)
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/'.$path) }}" class="d-block w-100 gal-main" style="height:420px;object-fit:cover;cursor:zoom-in;"
                                 alt="{{ $unit->label }}" data-idx="{{ $i }}" onclick="openLightbox({{ $i }})">
                        </div>
                        @endforeach
                        <span class="position-absolute bottom-0 end-0 m-2 px-2 py-1 rounded text-white small" style="background:rgba(0,0,0,.55);">
                            <i class="fas fa-images me-1"></i><span id="galCounter">1</span> / {{ count($unit->images) }}
                        </span>
                    </div>
                    @if(count($unit->images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#unitGallery" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#unitGallery" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if(count($unit->images) > 1)
                <div class="d-flex gap-2 mt-2 flex-wrap">
                    @foreach($unit->images as $i => $path)
                    <img src="{{ asset('storage/'.$path) }}" class="gal-thumb rounded {{ $i === 0 ? 'gal-thumb-active' : '' }}"
                         style="width:84px;height:62px;object-fit:cover;cursor:pointer;"
                         data-bs-target="#unitGallery" data-bs-slide-to="{{ $i }}" alt="{{ $unit->label }} photo {{ $i + 1 }}">
                    @endforeach
                </div>
                @endif

                {{-- Lightbox --}}
                <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content bg-dark border-0">
                            <div class="modal-body p-0 position-relative">
                                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="z-index:5;"></button>
                                <div id="lightboxGallery" class="carousel slide">
                                    <div class="carousel-inner">
                                        @foreach($unit->images as $i => $path)
                                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/'.$path) }}" class="d-block w-100" style="max-height:85vh;object-fit:contain;" alt="{{ $unit->label }}">
                                        </div>
                                        @endforeach
                                    </div>
                                    @if(count($unit->images) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#lightboxGallery" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#lightboxGallery" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded d-flex align-items-center justify-content-center bg-light shadow-sm" style="height:420px;">
                    <div class="text-center text-muted">
                        <i class="fas fa-home fa-4x d-block mb-2"></i>
                        <small>No photos yet — ask the landlord to add some.</small>
                    </div>
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="fw-bold">{{ $unit->label }}</h2>
                    <div class="fs-4 fw-bold text-primary mb-2">KES {{ number_format($unit->rent_amount) }}<small class="text-muted fw-normal fs-6">/month</small></div>

                    <ul class="list-unstyled mb-3">
                        @if($unit->location_text)
                        <li class="mb-1"><i class="fas fa-map-marker-alt text-muted me-2"></i>{{ $unit->location_text }}</li>
                        @endif
                        @if($unit->bedrooms !== null)
                        <li class="mb-1"><i class="fas fa-bed text-muted me-2"></i>{{ $unit->bedrooms === 0 ? 'Bedsitter' : $unit->bedrooms.' bedroom'.($unit->bedrooms>1?'s':'') }}</li>
                        @endif
                        @if($unit->unit_type)
                        <li class="mb-1"><i class="fas fa-home text-muted me-2"></i>{{ ucfirst($unit->unit_type) }}</li>
                        @endif
                    </ul>

                    @if($unit->description)
                    <p class="text-muted">{{ $unit->description }}</p>
                    @endif

                    <hr>

                    <h6 class="fw-semibold mb-2">Contact landlord</h6>
                    <p class="mb-1 fw-semibold">{{ $unit->business->name }}</p>
                    @if($unit->business->phone)
                        <p class="mb-3"><i class="fas fa-phone me-2 text-muted"></i>{{ $unit->business->phone }}</p>
                        @php
                            $waPhone = preg_replace('/\D/', '', $unit->business->phone);
                            $waPhone = str_starts_with($waPhone, '0') ? '254'.substr($waPhone, 1) : $waPhone;
                            $waText = urlencode("Hi, I'm interested in {$unit->label}" . ($unit->location_text ? " at {$unit->location_text}" : '') . " (KES " . number_format($unit->rent_amount) . "/mo) listed on Shopybook.");
                        @endphp
                        <div class="d-grid gap-2">
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" class="btn btn-success">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp Landlord
                            </a>
                            <a href="tel:{{ $unit->business->phone }}" class="btn btn-outline-primary">
                                <i class="fas fa-phone me-1"></i> Call {{ $unit->business->phone }}
                            </a>
                        </div>
                    @endif

                    <hr>

                    {{-- Booking / request form --}}
                    <h6 class="fw-semibold mb-2">Book this house</h6>
                    <p class="small text-muted">Send a booking request — the landlord will contact you to arrange a viewing.</p>
                    @if(session('success'))
                        <div class="alert alert-success small">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger small">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('houses.book', $unit) }}">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-6"><input type="text" name="name" class="form-control" placeholder="Your name" required value="{{ old('name') }}"></div>
                            <div class="col-6"><input type="tel" name="phone" class="form-control" placeholder="Phone e.g. 07xx xxx xxx" required value="{{ old('phone') }}"></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><input type="email" name="email" class="form-control" placeholder="Email (optional)" value="{{ old('email') }}"></div>
                            <div class="col-6"><input type="date" name="preferred_move_in" class="form-control" title="Preferred move-in date" min="{{ date('Y-m-d') }}" value="{{ old('preferred_move_in') }}"></div>
                        </div>
                        <textarea name="message" class="form-control mb-2" rows="2" placeholder="Message to the landlord (optional)">{{ old('message') }}</textarea>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-calendar-check me-1"></i> Request Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Safety notice --}}
    <div class="alert alert-warning d-flex align-items-start gap-2 mt-4 mb-0" role="alert">
        <i class="fas fa-shield-alt mt-1"></i>
        <div class="small">
            <strong>Stay safe:</strong> Shopybook connects you with landlords but does <u>not</u> verify listings or handle deposits.
            <strong>Only pay after you have viewed the house in person and confirmed the owner/caretaker.</strong>
            Never send money to someone you haven't met.
        </div>
    </div>

    {{-- Similar --}}
    @if($similar->isNotEmpty())
    <h5 class="fw-bold mt-5 mb-3">More at {{ $unit->property->name ?? 'this property' }}</h5>
    <div class="row g-3">
        @foreach($similar as $s)
        <div class="col-md-3">
            <a href="{{ route('houses.show', $s) }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm">
                @if($s->cover_image_url)
                    <img src="{{ $s->cover_image_url }}" style="height:130px;object-fit:cover;" class="card-img-top">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:130px;"><i class="fas fa-home text-muted"></i></div>
                @endif
                <div class="card-body p-3">
                    <div class="fw-semibold text-dark">{{ $s->label }}</div>
                    <small class="text-primary fw-bold">KES {{ number_format($s->rent_amount) }}/mo</small>
                </div>
            </div>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>

@if($unit->images && count($unit->images))
<style>
    .gal-thumb { opacity: .6; border: 2px solid transparent; transition: all .15s; }
    .gal-thumb:hover { opacity: 1; }
    .gal-thumb-active, .gal-thumb.gal-thumb-active { opacity: 1; border-color: #7b2e2e; }
</style>
<script>
const gallery = document.getElementById('unitGallery');
const thumbs  = document.querySelectorAll('.gal-thumb');
const counter = document.getElementById('galCounter');

gallery?.addEventListener('slid.bs.carousel', e => {
    counter.textContent = e.to + 1;
    thumbs.forEach((t, i) => t.classList.toggle('gal-thumb-active', i === e.to));
});

let lightbox;
function openLightbox(idx) {
    lightbox = lightbox || new bootstrap.Modal(document.getElementById('galleryModal'));
    const lb = document.getElementById('lightboxGallery');
    bootstrap.Carousel.getOrCreateInstance(lb).to(idx);
    lightbox.show();
}
</script>
@endif
@endsection
