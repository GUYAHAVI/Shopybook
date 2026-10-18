@extends('layouts.public')
@section('title', 'Shopybook — POS, Inventory, Rental Management & M-Pesa Software for Kenyan Businesses')
@section('meta_description', 'Run your shop, salon, hardware store or rental property on one platform. POS, inventory, M-Pesa STK payments, staff, tenants & rent collection, AI websites and a free business listing on Google. Start free in Kenya.')
@section('meta_keywords', 'POS system Kenya, inventory management Kenya, rental management software Kenya, landlord software Kenya, M-Pesa STK push, business management software Kenya, salon software Kenya, houses for rent Kenya, free business website Kenya')
@section('og_image', asset('img/heroimage.png'))

@php
    $faqs = [
        ['q' => 'What is Shopybook?', 'a' => 'Shopybook is an all-in-one business management platform for Kenyan small businesses. It combines POS, inventory, customers, staff, M-Pesa payments, rental property management, reports and an AI website builder in one system.'],
        ['q' => 'Can I accept M-Pesa payments with Shopybook?', 'a' => 'Yes. Shopybook sends an M-Pesa STK push straight to your customer\'s or tenant\'s phone and records the payment automatically once they enter their PIN. Funds go to your own business bank account paybill.'],
        ['q' => 'Can landlords use Shopybook to manage rentals?', 'a' => 'Yes. Landlords register properties and units (even 15 identical houses at once), assign tenants, collect rent via M-Pesa, track arrears and part-payments, print rent receipts and list vacant houses publicly.'],
        ['q' => 'Can I manage more than one business with one account?', 'a' => 'Yes. One login can own several businesses and switch between them instantly. Each business has its own staff, permissions, products and reports.'],
        ['q' => 'Will my business appear on Google?', 'a' => 'Every active business gets a public profile page and can publish a free website on a shopybook.com subdomain. These pages include search-engine structured data and are listed in our sitemap so Google can find them.'],
        ['q' => 'How much does Shopybook cost?', 'a' => 'You can start free. Paid plans start at KSh 500 per month, and the Enterprise plan includes two businesses with extra businesses at half price.'],
    ];
@endphp

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => url('/') . '#org',
            'name' => 'Shopybook',
            'url' => url('/'),
            'logo' => asset('img/logo.png'),
            'email' => 'support@shopybook.com',
            'telephone' => '+254717745891',
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Nairobi', 'addressCountry' => 'KE'],
            'areaServed' => 'KE',
        ],
        [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => 'Shopybook',
            'publisher' => ['@id' => url('/') . '#org'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => ['@type' => 'EntryPoint', 'urlTemplate' => route('houses.index') . '?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ],
        [
            '@type' => 'SoftwareApplication',
            'name' => 'Shopybook',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web, Android, iOS',
            'offers' => [
                ['@type' => 'Offer', 'name' => 'Free Starter', 'price' => '0', 'priceCurrency' => 'KES'],
                ['@type' => 'Offer', 'name' => 'Business Pro', 'price' => '500', 'priceCurrency' => 'KES'],
                ['@type' => 'Offer', 'name' => 'Enterprise', 'price' => '1000', 'priceCurrency' => 'KES'],
            ],
        ],
        [
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn($f) => [
                '@type' => 'Question',
                'name' => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
            ])->all(),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light" id="top">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="starsicons d-flex align-items-center">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <p class="mb-0 ms-2">Trusted by 500+ Kenyan businesses</p>
                </div>

                <h1>All Your Business On One Platform</h1>

                <p>Run Your Shop, Not Your Spreadsheets</p>

                <p class="mt-3">
                    POS, inventory, services, staff, rental &amp; tenant management, M-Pesa STK payments,
                    AI website builder and SMS marketing — for shops, salons, hardware stores and landlords,
                    all in one simple system.
                </p>

                <div class="d-flex flex-wrap gap-3 pt-4">
                    <a href="{{ route('register') }}" class="btn1">Get Started Free</a>
                    <a href="#features" class="btnb">Explore Features</a>
                    <a href="{{ route('houses.index') }}" class="btnb"><i class="fas fa-home me-1"></i> Find a House</a>
                </div>

                <p class="mt-4 mb-0" style="font-size: .9rem;">
                    <i class="fas fa-check me-1" style="color:#43ba7f;"></i> No credit card required
                    <span class="mx-2">&bull;</span>
                    <i class="fas fa-check me-1" style="color:#43ba7f;"></i> 5-minute setup
                    <span class="mx-2">&bull;</span>
                    <i class="fas fa-check me-1" style="color:#43ba7f;"></i> M-Pesa ready
                </p>
            </div>

            <div class="col-md-5 mt-5 mt-md-0">
                <img src="{{ asset('img/heroimage.png') }}" alt="Manage barbershop, salon, hardware store and rentals on Shopybook — all in one place"
                     class="img-fluid" style="filter: drop-shadow(0 1.5rem 2.5rem rgba(0,0,0,.35));">
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ ORANGE STRIP ═══════════════ --}}
<section class="sb-strip">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <span class="strip-item"><i class="fas fa-mobile-alt"></i> M-Pesa Integrated</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="strip-item"><i class="fas fa-bolt"></i> 5-Minute Setup</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="strip-item"><i class="fas fa-lock"></i> Secure &amp; Private</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="strip-item"><i class="fas fa-headset"></i> Local Support</span>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ MODULES ═══════════════ --}}
<section class="services" id="services">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-boxes-stacked"></i> Products</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-cash-register"></i> POS System</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-users"></i> Customers</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-warehouse"></i> Inventory</h5></div>
            </div>
        </div>

        <div class="row pt-4">
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-calendar-check"></i> Services</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-user-tie"></i> Staff</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-bullhorn"></i> Marketing</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-chart-line"></i> Analytics</h5></div>
            </div>
        </div>

        <div class="row pt-4">
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-building"></i> Rentals</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-mobile-screen-button"></i> M-Pesa STK</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-sitemap"></i> Multi-Business</h5></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-box"><h5><i class="fa-solid fa-globe"></i> Websites</h5></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ ABOUT ═══════════════ --}}
<section class="about-us" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4>Get To Know Us</h4>
                <h2>About Shopybook</h2>

                <p>
                    Most small businesses in Kenya juggle spreadsheets, WhatsApp messages and paper notebooks
                    just to keep track of stock and sales. Shopybook replaces all of that with one simple,
                    affordable platform built specifically for the way local businesses actually work.
                </p>
                <p>
                    From your very first sale to running several businesses from one login, Shopybook grows with
                    you — with M-Pesa payments, real-time inventory, staff permissions, rental and tenant
                    management, and AI-powered insights all included in one place.
                </p>

                <div class="row count pt-4">
                    <div class="col-sm-4">
                        <div class="count-box">
                            <h3>500+</h3>
                            <p>Active Businesses</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="count-box">
                            <h3>16</h3>
                            <p>Business Modules</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="count-box">
                            <h3>0</h3>
                            <p>Setup Fees</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-5 mt-md-0">
                <img src="{{ asset('jeriah/images/ai-data-analysis-storytelling.webp') }}"
                     alt="Shopybook business dashboard" class="img-fluid" />
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ WHAT WE OFFER ═══════════════ --}}
<section class="sb-section sb-section-gray" id="features">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <span class="sb-eyebrow">What We Offer</span>
                <h2 class="sb-heading">Everything Your Business Needs</h2>
                <p class="sb-subheading">From stock to sales to staff — manage it all in one place.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Product &amp; Inventory</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Never Run Out Of Stock</h3>
                        <p>Track stock in real time, get low-stock alerts, bulk import via CSV or Excel, and capture products straight from a photo with OCR.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Point Of Sale</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Faster Checkout</h3>
                        <p>Full-featured POS with cart management, M-Pesa and cash payments, printed receipts and complete order history.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-users"></i>
                        <span>Customer CRM</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Know Your Customers</h3>
                        <p>Manage customer profiles, track purchase history and run SMS or email campaigns from a single dashboard.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-globe"></i>
                        <span>AI Website Builder</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Get Online In Minutes</h3>
                        <p>Generate a professional website with AI, choose from eight themes and get a free subdomain included.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-user-tie"></i>
                        <span>Staff &amp; Payroll</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Manage Your Team</h3>
                        <p>Employee records, salaries, commissions, salary advances and detailed staff performance reports.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Analytics &amp; Reports</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Make Better Decisions</h3>
                        <p>Real-time sales, profit &amp; loss, inventory and tax reports, plus AI-powered insights from KENADA.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-building"></i>
                        <span>Rental Management <small class="badge bg-success ms-1">New</small></span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Built For Landlords</h3>
                        <p>Register properties and bulk-create units, assign tenants, track arrears and part-payments, and print rent receipts in one click.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        <span>M-Pesa STK Push</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Get Paid Instantly</h3>
                        <p>Send a payment prompt straight to your customer's or tenant's phone. Payments are confirmed and recorded automatically into your own bank paybill.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-sitemap"></i>
                        <span>Multi-Business &amp; Roles <small class="badge bg-success ms-1">New</small></span>
                    </div>
                    <div class="offercardtxt">
                        <h3>One Login, Many Businesses</h3>
                        <p>Own several businesses and switch instantly. Hire staff per business and lock each one to only the modules they need — POS, orders or rentals.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-ranking-star"></i>
                        <span>Staff Sales Report <small class="badge bg-success ms-1">New</small></span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Know Who Sold What</h3>
                        <p>Every sale records who served the customer. See totals, collections and balances per employee, with "Served by" on every receipt.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                        <span>Customer Debts</span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Never Forget A Balance</h3>
                        <p>Sell on credit, record repayments from the debts page and watch balances and order statuses update automatically.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="offercard my-3">
                    <div class="offercard-head">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>AI House Search <small class="badge bg-success ms-1">New</small></span>
                    </div>
                    <div class="offercardtxt">
                        <h3>Vacancies That Get Found</h3>
                        <p>List vacant houses publicly. Seekers describe what they want — "2 bedroom in Kiritiri under 15k" — and AI matches them to your units and booking requests.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ BUILT FOR EVERY BUSINESS ═══════════════ --}}
<section class="sb-section sb-section-light" id="business-types">
    <div class="container">
        <div class="text-center mb-5">
            <span class="sb-eyebrow">Who It's For</span>
            <h2 class="sb-heading">A Dashboard Built For Your Kind Of Business</h2>
            <p class="sb-subheading">Pick your business type and Shopybook shows only the tools you need.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['fa-store', 'Shops & Retail', 'Hardware stores, boutiques, mini-marts, agrovets and pharmacies.', ['POS & receipts', 'Stock alerts & suppliers', 'Customer debts']],
                ['fa-scissors', 'Service Businesses', 'Salons, barbershops, spas, garages and clinics.', ['Appointments & bookings', 'Staff commissions', 'Customer history']],
                ['fa-layer-group', 'Hybrid Businesses', 'Sell products and offer services under one roof.', ['Products + services', 'One checkout', 'Unified reports']],
                ['fa-building', 'Rentals & Landlords', 'Apartments, bedsitters, hostels, commercial spaces and short-stays.', ['Units & tenants', 'Rent via M-Pesa + receipts', 'Public vacancy listings']],
            ] as [$icon, $title, $desc, $points])
            <div class="col-md-6 col-lg-3">
                <div class="offercard h-100">
                    <div class="offercard-head"><i class="fa-solid {{ $icon }}"></i><span>{{ $title }}</span></div>
                    <div class="offercardtxt">
                        <p>{{ $desc }}</p>
                        <ul class="list-unstyled small mb-0">
                            @foreach($points as $p)
                                <li class="mb-1"><i class="fas fa-check me-1" style="color:#43ba7f;"></i>{{ $p }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ HOUSE SEARCH BAND ═══════════════ --}}
<section class="hero-section text-light py-5" id="find-a-house">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="sb-eyebrow" style="color:#ffb199;">Looking For A House?</span>
                <h2 class="mt-2">Find Vacant Houses Across Kenya</h2>
                <p class="mb-0">Describe the house you want in your own words. We match you with vacant rentals listed directly by landlords on Shopybook — then you book a viewing.</p>
            </div>
            <div class="col-lg-6">
                <form action="{{ route('houses.index') }}" method="GET" class="bg-white rounded-3 p-2 d-flex gap-2 shadow">
                    <input type="text" name="q" class="form-control border-0 shadow-none" placeholder='e.g. "bedsitter in Kiritiri under 5,000"' aria-label="Describe the house you want">
                    <button class="btn1 text-nowrap" style="border:none;"><i class="fas fa-search me-1"></i> Search</button>
                </form>
                <small class="d-block mt-2 opacity-75"><i class="fas fa-shield-alt me-1"></i> Only pay after viewing a house and verifying the owner.</small>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ WHY CHOOSE (SPLIT) ═══════════════ --}}
<section class="seo py-5">
    <div class="container">
        <div class="row g-0">
            <div class="col-md-5">
                <div class="seo-panel">
                    <h2>Ready In 5 Minutes</h2>
                    <p>
                        Create your account, add your first products and start selling the same day.
                        No installation, no technical setup, no consultants required.
                    </p>
                    <a href="{{ route('register') }}" class="btn1 mt-3" style="width: 100%;">Create Free Account</a>
                </div>
            </div>

            <div class="col-md-7 seotxt">
                <h2>Why Businesses Choose Shopybook</h2>
                <p>
                    We built Shopybook after watching too many good businesses lose money to bad record keeping.
                    Here is what makes it different:
                </p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check"></i> <span><strong>All-in-one</strong> — no need to pay for five different tools</span></li>
                    <li><i class="fas fa-check"></i> <span><strong>M-Pesa ready</strong> — STK push payments into your own bank paybill</span></li>
                    <li><i class="fas fa-check"></i> <span><strong>Found on Google</strong> — a free business profile and website, built for search</span></li>
                    <li><i class="fas fa-check"></i> <span><strong>Secure staff access</strong> — every employee sees only what you allow</span></li>
                    <li><i class="fas fa-check"></i> <span><strong>Mobile first</strong> — run your business from your phone</span></li>
                    <li><i class="fas fa-check"></i> <span><strong>Affordable</strong> — start free and upgrade only as you grow</span></li>
                    <li><i class="fas fa-check"></i> <span><strong>Local support</strong> — WhatsApp and email help right here in Kenya</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ TESTIMONIALS ═══════════════ --}}
<section class="sb-section sb-section-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <span class="sb-eyebrow">Testimonials</span>
                <h2 class="sb-heading">Loved By Small Businesses</h2>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-6">
                <div class="sb-testimonial">
                    <div class="stars mb-3">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="mb-4">
                        "I used to lose track of stock every week. With Shopybook I can see exactly what I have,
                        what sold and what to reorder — all from my phone."
                    </p>
                    <p class="name">Grace Mwangi</p>
                    <p class="role">Boutique Owner, Nairobi</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sb-testimonial">
                    <div class="stars mb-3">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="mb-4">
                        "The POS and M-Pesa integration made checkout so much faster. My customers love the
                        printed receipts and I love the daily sales report."
                    </p>
                    <p class="name">James Otieno</p>
                    <p class="role">Hardware Store Manager, Kisumu</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ PRICING ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <span class="sb-eyebrow">Pricing</span>
                <h2 class="sb-heading">Simple &amp; Transparent</h2>
                <p class="sb-subheading">Start free. Upgrade only when your business needs more power.</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="sb-pricing-card">
                    <h3>Free Starter</h3>
                    <p class="plan-desc">Perfect for getting started</p>
                    <div class="mb-4">
                        <span class="price-currency">KSh</span>
                        <span class="price">0</span>
                        <span class="price-period">/month</span>
                    </div>
                    <ul class="list-unstyled flex-grow-1 mb-4">
                        <li><i class="fas fa-check"></i> Up to 50 products</li>
                        <li><i class="fas fa-check"></i> Basic POS &amp; inventory</li>
                        <li><i class="fas fa-check"></i> 1 staff account</li>
                        <li><i class="fas fa-check"></i> M-Pesa integration</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btnb btn-block-j">Get Started Free</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="sb-pricing-card featured">
                    <span class="badge-popular">Most Popular</span>
                    <h3>Business Pro</h3>
                    <p class="plan-desc">For growing businesses</p>
                    <div class="mb-4">
                        <span class="price-currency">KSh</span>
                        <span class="price">500</span>
                        <span class="price-period">/month</span>
                    </div>
                    <ul class="list-unstyled flex-grow-1 mb-4">
                        <li><i class="fas fa-check"></i> Unlimited products</li>
                        <li><i class="fas fa-check"></i> Full POS &amp; CRM</li>
                        <li><i class="fas fa-check"></i> AI website builder</li>
                        <li><i class="fas fa-check"></i> Marketing automation</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn1 btn-block-j">Start Free Trial</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="sb-pricing-card">
                    <h3>Enterprise</h3>
                    <p class="plan-desc">For large &amp; multi-location</p>
                    <div class="mb-4">
                        <span class="price-currency">KSh</span>
                        <span class="price">1,000</span>
                        <span class="price-period">/month</span>
                    </div>
                    <ul class="list-unstyled flex-grow-1 mb-4">
                        <li><i class="fas fa-check"></i> Everything in Pro</li>
                        <li><i class="fas fa-check"></i> 2 businesses included (+50% off extras)</li>
                        <li><i class="fas fa-check"></i> Unlimited staff &amp; roles</li>
                        <li><i class="fas fa-check"></i> Dedicated support</li>
                    </ul>
                    <a href="{{ route('pricing') }}" class="btnb btn-block-j">Compare Plans</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ FAQ ═══════════════ --}}
<section class="sb-section sb-section-light" id="faq">
    <div class="container" style="max-width:860px;">
        <div class="text-center mb-5">
            <span class="sb-eyebrow">FAQ</span>
            <h2 class="sb-heading">Frequently Asked Questions</h2>
        </div>
        <div class="accordion" id="faqAccordion">
            @foreach($faqs as $i => $f)
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                        {{ $f['q'] }}
                    </button>
                </h3>
                <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">{{ $f['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ CTA ═══════════════ --}}
<section class="sb-section sb-section-light">
    <div class="container">
        <div class="sb-cta">
            <h2>Ready To Grow Your Business?</h2>
            <p>Start free today. No credit card required.</p>
            <a href="{{ route('register') }}" class="btn1">Create Free Account</a>
        </div>
    </div>
</section>

@endsection
