@extends('layouts.public')
@section('title', 'Shopybook - Complete Business Management Platform for Small Businesses in Kenya')
@section('meta_description', 'Transform your small business with Shopybook\'s all-in-one platform. Manage products, services, customers, inventory, staff and sales with M-Pesa integration. Built for Kenyan businesses.')
@section('meta_keywords', 'business management software Kenya, small business platform, inventory management, M-Pesa integration, POS system Kenya, customer management, staff management, service booking')

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
                    POS, inventory, services, staff, M-Pesa payments, AI website builder and SMS marketing —
                    everything a Kenyan small business needs, in one simple system.
                </p>

                <div class="d-flex flex-wrap gap-3 pt-4">
                    <a href="{{ route('register') }}" class="btn1">Get Started Free</a>
                    <a href="#features" class="btnb">Explore Features</a>
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
                <div class="hero-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="hero-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div>
                            <h5>M-Pesa Payment Received</h5>
                            <small>KSh 3,500 from Jane Doe</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="hero-stat">KSh 24k</div>
                            <small>Today's Sales</small>
                        </div>
                        <div class="col-4">
                            <div class="hero-stat">142</div>
                            <small>Products</small>
                        </div>
                        <div class="col-4">
                            <div class="hero-stat">18</div>
                            <small>Customers</small>
                        </div>
                    </div>
                </div>
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
                    From your very first sale to managing multiple branches, Shopybook grows with you — with
                    M-Pesa payments, real-time inventory, staff management and AI-powered insights all
                    included in one place.
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
                            <h3>12</h3>
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
                    <li><i class="fas fa-check"></i> <span><strong>M-Pesa ready</strong> — accept mobile money from day one</span></li>
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
                        <li><i class="fas fa-check"></i> Multi-location support</li>
                        <li><i class="fas fa-check"></i> Unlimited staff</li>
                        <li><i class="fas fa-check"></i> Dedicated support</li>
                    </ul>
                    <a href="{{ route('pricing') }}" class="btnb btn-block-j">Compare Plans</a>
                </div>
            </div>
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
