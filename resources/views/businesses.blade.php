@extends('layouts.public')
@php
    $typeLabels = ['product' => 'Shops & Product Businesses', 'service' => 'Service Businesses', 'hybrid' => 'Hybrid Businesses', 'rentals' => 'Rental Properties & Landlords'];
    $typeLabel  = $typeLabels[$type] ?? null;
@endphp
@section('title', $typeLabel ? "{$typeLabel} in Kenya — Shopybook Business Directory" : 'Kenyan Business Directory — Shops, Salons, Rentals & Services | Shopybook')
@section('meta_description', $typeLabel
    ? "Browse {$typeLabel} in Kenya on Shopybook. View business profiles, websites, contacts and locations."
    : 'Discover Kenyan businesses on Shopybook — shops, hardware stores, salons, barbershops, rental properties and more. View profiles, websites and contacts.')
@section('canonical', $typeLabel ? route('businesses', ['type' => $type]) : route('businesses'))
@if(request()->has('sort'))
    @section('robots', 'noindex, follow')
@endif

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <h1>{{ $typeLabel ? $typeLabel . ' in Kenya' : 'Discover Kenyan Businesses' }}</h1>
        <p class="mx-auto" style="max-width:620px;">
            @if($type)
                Browse {{ ucfirst($type) }} businesses in our community
            @else
                Explore our wide range of business solutions tailored to your needs
            @endif
        </p>
        <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
            @if($type)
                <a href="{{ route('businesses') }}" class="btnb">View All Businesses</a>
            @endif
            <a href="#businesses" class="btn1">Explore Now</a>
        </div>
    </div>
</section>

{{-- ═══════════════ BUSINESSES ═══════════════ --}}
<section class="sb-section sb-section-gray" id="businesses">
    <div class="container">

        {{-- Filter pills --}}
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('businesses') }}"
                   class="sb-filter {{ ($type === 'all' || !$type) ? 'active' : '' }}">
                    <i class="fas fa-th"></i> All Businesses
                </a>
                <a href="{{ route('businesses', ['type' => 'product']) }}"
                   class="sb-filter {{ $type === 'product' ? 'active' : '' }}">
                    <i class="fas fa-boxes-stacked"></i> Product Businesses
                </a>
                <a href="{{ route('businesses', ['type' => 'service']) }}"
                   class="sb-filter {{ $type === 'service' ? 'active' : '' }}">
                    <i class="fas fa-hands-helping"></i> Service Businesses
                </a>
                <a href="{{ route('businesses', ['type' => 'hybrid']) }}"
                   class="sb-filter {{ $type === 'hybrid' ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> Hybrid Businesses
                </a>
                <a href="{{ route('businesses', ['type' => 'rentals']) }}"
                   class="sb-filter {{ $type === 'rentals' ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Rentals
                </a>
            </div>
        </div>

        @if($type && $type !== 'all')
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert" style="background:#fff; border:1px solid #e0ded9; border-radius:8px; color:#444;">
                        <i class="fas fa-filter me-2" style="color:#ff511a;"></i>
                        Showing {{ ucfirst($type) }} businesses only.
                        <a href="{{ route('businesses') }}" style="color:#ff511a; font-weight:600;">View all businesses</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center mb-4">
            <span class="sb-eyebrow">Browse</span>
            <h2 class="sb-heading">Need Something Else?</h2>
            <p class="sb-subheading">Explore our wide range of business solutions tailored to your needs.</p>
        </div>

        <div class="row">
            @include('partials.businesses', ['groupedBusinesses' => $groupedBusinesses])
        </div>
    </div>
</section>

@endsection
