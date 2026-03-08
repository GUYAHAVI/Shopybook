@extends('layouts.dash')

@section('title', 'Create Your Website')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1" style="color:var(--text-primary);font-weight:700;">
                    <i class="fas fa-rocket me-2" style="color:var(--primary-color);"></i>Create Your Website
                </h1>
                <p class="text-muted mb-0">
                    Build a stunning AI-powered website for <strong>{{ $business->name }}</strong> in minutes.
                </p>
            </div>
            <a href="{{ route('website.builder.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Hero AI Build Card -->
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card border-0 mb-5" style="background:linear-gradient(135deg,#1a237e 0%,#283593 60%,#1565c0 100%);position:relative;overflow:hidden;border-radius:1.25rem;">
                <!-- Decorative circles -->
                <div style="position:absolute;top:-60px;right:-60px;width:240px;height:240px;background:rgba(255,255,255,.07);border-radius:50%;pointer-events:none;"></div>
                <div style="position:absolute;bottom:-40px;left:-40px;width:180px;height:180px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;"></div>

                <div class="card-body p-5" style="position:relative;z-index:1;">
                    <div class="text-center mb-4">
                        <div style="font-size:4rem;line-height:1;margin-bottom:1rem;">🤖</div>
                        <h2 style="color:#fff;font-weight:800;font-size:2rem;margin-bottom:.75rem;">
                            AI Website Builder
                        </h2>
                        <p style="color:rgba(255,255,255,.88);font-size:1.1rem;max-width:520px;margin:0 auto 2rem;line-height:1.65;">
                            Answer a few questions and Claude AI builds your <strong>complete website</strong> — professional content, SEO, and beautiful design — in about 60 seconds.
                        </p>
                    </div>

                    <!-- Feature pills -->
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-5" style="color:rgba(255,255,255,.9);font-size:.92rem;">
                        <span><i class="fas fa-check-circle me-1" style="color:#10b981;"></i>5–7 Pages Created</span>
                        <span><i class="fas fa-check-circle me-1" style="color:#10b981;"></i>AI-Generated Content</span>
                        <span><i class="fas fa-check-circle me-1" style="color:#10b981;"></i>Custom Colors & Fonts</span>
                        <span><i class="fas fa-check-circle me-1" style="color:#10b981;"></i>SEO Optimized</span>
                        <span><i class="fas fa-check-circle me-1" style="color:#10b981;"></i>Fully Editable</span>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('website-configurator.step1') }}"
                           class="btn btn-light btn-lg px-5 py-3"
                           style="font-weight:700;font-size:1.1rem;border-radius:50px;box-shadow:0 8px 24px rgba(0,0,0,.25);letter-spacing:.3px;">
                            <i class="fas fa-magic me-2" style="color:var(--primary-color,#1a237e);"></i>
                            Start AI Builder
                        </a>
                        <p class="mt-3 mb-0" style="color:rgba(255,255,255,.7);font-size:.875rem;">
                            <i class="fas fa-clock me-1"></i>Takes only 1–2 minutes
                        </p>
                    </div>
                </div>
            </div>

            <!-- What to expect -->
            <h4 class="text-center mb-4" style="color:var(--text-primary);font-weight:600;">How It Works</h4>
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-6 text-center">
                    <div style="font-size:2.5rem;margin-bottom:.75rem;">1️⃣</div>
                    <h6 style="color:var(--text-primary);font-weight:700;">Choose Type</h6>
                    <p class="text-muted mb-0" style="font-size:.875rem;">Tell us what kind of website you need</p>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <div style="font-size:2.5rem;margin-bottom:.75rem;">2️⃣</div>
                    <h6 style="color:var(--text-primary);font-weight:700;">Describe Business</h6>
                    <p class="text-muted mb-0" style="font-size:.875rem;">Share your business name &amp; details</p>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <div style="font-size:2.5rem;margin-bottom:.75rem;">3️⃣</div>
                    <h6 style="color:var(--text-primary);font-weight:700;">Pick Style</h6>
                    <p class="text-muted mb-0" style="font-size:.875rem;">Choose your colors and fonts</p>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <div style="font-size:2.5rem;margin-bottom:.75rem;">🚀</div>
                    <h6 style="color:var(--text-primary);font-weight:700;">AI Builds It</h6>
                    <p class="text-muted mb-0" style="font-size:.875rem;">Claude generates your full website</p>
                </div>
            </div>

            <!-- Bottom CTA -->
            <div class="text-center">
                <a href="{{ route('website-configurator.step1') }}"
                   class="btn btn-primary btn-lg px-5 py-3"
                   style="font-weight:700;border-radius:50px;background:linear-gradient(135deg,var(--primary-color),#1565c0);border:none;box-shadow:0 6px 18px rgba(2,2,88,.3);">
                    <i class="fas fa-rocket me-2"></i>Build My Website Now
                </a>
                <p class="text-muted mt-3 mb-0" style="font-size:.875rem;">
                    Your website will go live at: <strong style="color:var(--primary-color);">{{ $business->slug }}.shopybook.com</strong>
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
