@extends('layouts.public')

@section('title', 'Choose Your Business Type - Shopybook')
@section('meta_description', 'Tell Shopybook what type of business you run - sell products, provide services, or both.')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <h1><i class="fas fa-rocket me-2"></i>Welcome to Shopybook!</h1>
        <p class="mx-auto" style="max-width:620px;">
            Let's get you started by understanding your business better. What type of business do you operate?
        </p>
    </div>
</section>

{{-- ═══════════════ CHOOSE TYPE ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- Info box --}}
                <div class="sb-tip-box mb-4">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Good to know:</strong> You can have up to 2 businesses on Shopybook. Don't worry — you can always add another business type later or modify your selection.
                    </div>
                </div>

                {{-- Options --}}
                <div class="row g-4">

                    {{-- Products --}}
                    <div class="col-md-4">
                        <a href="{{ route('business.create', ['type' => 'product']) }}" class="sb-type-option sb-type-product">
                            <div class="sb-type-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3>I Sell Products</h3>
                            <p>Perfect for retail stores, online shops, wholesalers, and businesses that sell physical or digital products.</p>
                            <span class="sb-type-cta">Get Started <i class="fas fa-arrow-right ms-1"></i></span>
                        </a>
                    </div>

                    {{-- Services --}}
                    <div class="col-md-4">
                        <a href="{{ route('business.create', ['type' => 'service']) }}" class="sb-type-option sb-type-service">
                            <div class="sb-type-icon">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                            <h3>I Provide Services</h3>
                            <p>Ideal for consultants, salons, repair services, freelancers, and businesses that offer services to clients.</p>
                            <span class="sb-type-cta">Get Started <i class="fas fa-arrow-right ms-1"></i></span>
                        </a>
                    </div>

                    {{-- Both --}}
                    <div class="col-md-4">
                        <a href="{{ route('business.create', ['type' => 'hybrid']) }}" class="sb-type-option sb-type-both">
                            <div class="sb-type-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <h3>I Do Both</h3>
                            <p>Great for businesses that sell products AND provide services, like a salon that also sells beauty products.</p>
                            <span class="sb-type-cta">Get Started <i class="fas fa-arrow-right ms-1"></i></span>
                        </a>
                    </div>

                </div>

                {{-- Skip --}}
                <div class="text-center mt-5 pt-4" style="border-top: 1px solid #e0ded9;">
                    <a href="{{ route('dashboard') }}" class="sb-skip-link">
                        <i class="fas fa-arrow-right me-1"></i>
                        Skip for now — I'll set this up later
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
