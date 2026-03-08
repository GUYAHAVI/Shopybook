@extends('layouts.master')
@section('title', 'Pricing - Shopybook Business Management Plans for Small Businesses in Kenya')
@section('meta_description', 'Choose the right Shopybook plan for your business. Free Starter, Business Pro at KSh 500/month, and Enterprise at KSh 1,000/month. M-Pesa integration, AI tools, and more.')
@section('meta_keywords', 'shopybook pricing, business management software pricing Kenya, affordable POS Kenya, small business software plans, M-Pesa billing')

@section('content')

<style>
body, html {
    background: #020258 !important;
    color: #fff !important;
}

/* ── Typography ── */
h1, h2, h3, h4, h5, h6,
.display-4, .text-primary {
    color: #13e8e9 !important;
}

.lead, p {
    color: #fff !important;
}

/* ── Buttons ── */
.btn-primary,
.btn-primary:active,
.btn-primary:focus {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
    font-weight: 600;
}
.btn-primary:hover {
    background: #020258 !important;
    color: #13e8e9 !important;
    border: 2px solid #13e8e9 !important;
}

.btn-outline-light,
.btn-outline-light:active,
.btn-outline-light:focus {
    background: transparent !important;
    color: #13e8e9 !important;
    border: 2px solid #13e8e9 !important;
    font-weight: 600;
}
.btn-outline-light:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
}

/* ── Hero ── */
.pricing-hero {
    background: #020258;
    padding: 80px 0 60px;
    position: relative;
    overflow: hidden;
}

.pricing-hero::before {
    content: '';
    position: absolute;
    top: -150px;
    right: -150px;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(19,232,233,0.12) 0%, transparent 70%);
    pointer-events: none;
}

.pricing-hero::after {
    content: '';
    position: absolute;
    bottom: -100px;
    left: -100px;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(19,232,233,0.08) 0%, transparent 70%);
    pointer-events: none;
}

/* ── Pricing Cards ── */
.pricing-card {
    background: #fff !important;
    color: #333 !important;
    border: 2px solid #13e8e9 !important;
    border-radius: 16px !important;
    transition: all 0.35s ease;
    position: relative;
    overflow: visible;
}

.pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(19, 232, 233, 0.35) !important;
}

.pricing-card.featured {
    border: 3px solid #13e8e9 !important;
    transform: scale(1.04);
    box-shadow: 0 15px 45px rgba(19, 232, 233, 0.3) !important;
    z-index: 2;
}

.pricing-card.featured:hover {
    transform: scale(1.04) translateY(-8px);
}

.pricing-card,
.pricing-card * {
    color: #333 !important;
}

.pricing-card .card-title {
    color: #13e8e9 !important;
    font-size: 1.4rem;
    font-weight: 700;
}

.popular-badge {
    background: #13e8e9 !important;
    color: #020258 !important;
    border-radius: 20px;
    padding: 4px 14px;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: inline-block;
    margin-bottom: 14px;
}

.price {
    line-height: 1;
    margin-bottom: 0.5rem;
}

.price .currency {
    font-size: 1.3rem;
    font-weight: 700;
    vertical-align: super;
    color: #13e8e9 !important;
}

.price .amount {
    font-size: 3rem;
    font-weight: 800;
    color: #13e8e9 !important;
}

.price .period {
    font-size: 1rem;
    color: #666 !important;
    font-weight: 400;
}

.pricing-list li {
    padding: 6px 0;
    color: #333 !important;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.pricing-list li:last-child {
    border-bottom: none;
}

.pricing-list .fa-check {
    color: #13e8e9 !important;
    margin-top: 3px;
    flex-shrink: 0;
}

.pricing-list .fa-times {
    color: #ccc !important;
    margin-top: 3px;
    flex-shrink: 0;
}

.pricing-list .unavailable {
    color: #aaa !important;
}

/* ── Feature comparison table ── */
.comparison-table th {
    background: #020258 !important;
    color: #13e8e9 !important;
    border-color: rgba(19,232,233,0.3) !important;
    font-weight: 600;
}

.comparison-table td {
    border-color: rgba(19,232,233,0.15) !important;
    color: #fff !important;
    background: transparent !important;
    vertical-align: middle;
}

.comparison-table tr:nth-child(even) td {
    background: rgba(19,232,233,0.04) !important;
}

.comparison-table .feature-label {
    font-weight: 600;
    color: #fff !important;
}

.check-icon { color: #13e8e9 !important; font-size: 1.1rem; }
.cross-icon { color: #555 !important; font-size: 1.1rem; }

/* ── FAQ ── */
.faq-item {
    border: 1px solid rgba(19,232,233,0.25);
    border-radius: 10px;
    margin-bottom: 12px;
    overflow: hidden;
    transition: border-color 0.3s;
}

.faq-item:hover {
    border-color: #13e8e9;
}

.faq-question {
    background: rgba(19,232,233,0.06);
    color: #fff !important;
    padding: 16px 20px;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: none;
    width: 100%;
    text-align: left;
    font-size: 1rem;
}

.faq-question .faq-icon {
    color: #13e8e9;
    transition: transform 0.3s;
    flex-shrink: 0;
}

.faq-question.open .faq-icon {
    transform: rotate(45deg);
}

.faq-answer {
    display: none;
    padding: 14px 20px 18px;
    color: rgba(255,255,255,0.85) !important;
    font-size: 0.95rem;
    line-height: 1.7;
}

.faq-answer.open {
    display: block;
}

/* ── Trust badges ── */
.trust-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    background: rgba(19,232,233,0.07);
    border: 1px solid rgba(19,232,233,0.25);
    border-radius: 10px;
    color: #fff !important;
    font-size: 0.95rem;
}

.trust-badge i {
    font-size: 1.5rem;
    color: #13e8e9;
    flex-shrink: 0;
}

/* ── Billing toggle ── */
.billing-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
    justify-content: center;
    margin-bottom: 2rem;
}

.billing-toggle-wrap .form-check-input {
    width: 3rem !important;
    height: 1.6rem !important;
    cursor: pointer;
    background-color: rgba(19,232,233,0.3) !important;
    border-color: #13e8e9 !important;
}

.billing-toggle-wrap .form-check-input:checked {
    background-color: #13e8e9 !important;
    border-color: #13e8e9 !important;
}

.billing-label {
    color: #fff !important;
    font-weight: 600;
    font-size: 1rem;
}

.save-badge {
    background: #13e8e9;
    color: #020258;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
    vertical-align: middle;
    margin-left: 4px;
}

/* ── CTA section ── */
.cta-section {
    background: linear-gradient(135deg, rgba(19,232,233,0.12) 0%, rgba(2,2,88,0.9) 100%);
    border: 1px solid rgba(19,232,233,0.3);
    border-radius: 20px;
}

/* ── Mobile ── */
@media (max-width: 768px) {
    .pricing-card.featured {
        transform: none;
        margin-bottom: 1.5rem;
    }
    .pricing-card.featured:hover {
        transform: translateY(-6px);
    }
    .price .amount { font-size: 2.4rem; }
    .pricing-hero { padding: 60px 0 40px; }
}
</style>

{{-- ══════════════════════ HERO ══════════════════════ --}}
<section class="pricing-hero text-center">
    <div class="container position-relative" style="z-index: 2;">
        <span class="popular-badge mb-3">Simple &amp; Transparent</span>
        <h1 class="display-4 fw-bold mb-3">
            Choose Your <span class="text-primary">Perfect Plan</span>
        </h1>
        <p class="lead mx-auto" style="max-width:620px;">
            Affordable plans built for Kenyan small businesses. No hidden fees, no lock-in.
            Start free and upgrade as you grow.
        </p>

        {{-- Billing toggle --}}
        <div class="billing-toggle-wrap mt-4" id="billingToggleWrap">
            <span class="billing-label">Monthly</span>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="billingToggle" role="switch">
            </div>
            <span class="billing-label">Annual <span class="save-badge">Save 20%</span></span>
        </div>
    </div>
</section>

{{-- ══════════════════════ PRICING CARDS ══════════════════════ --}}
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center g-4 align-items-stretch">

            {{-- ── Free Plan ── --}}
            <div class="col-lg-4 col-md-6">
                <div class="card pricing-card h-100">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3">
                            <h3 class="card-title">Free Starter</h3>
                            <p class="mb-0" style="color:#666 !important; font-size:.9rem;">
                                Perfect for getting started
                            </p>
                        </div>
                        <div class="price mb-1">
                            <span class="currency">KSh</span>
                            <span class="amount" data-monthly="0" data-annual="0">0</span>
                        </div>
                        <p style="color:#888 !important; font-size:.85rem;" class="mb-4">
                            <span class="period">/month</span> &mdash; forever free
                        </p>

                        <ul class="list-unstyled pricing-list mb-4 flex-grow-1">
                            <li><i class="fas fa-check"></i> Up to 50 products</li>
                            <li><i class="fas fa-check"></i> Basic inventory tracking</li>
                            <li><i class="fas fa-check"></i> Customer management (up to 100)</li>
                            <li><i class="fas fa-check"></i> Basic POS system</li>
                            <li><i class="fas fa-check"></i> M-Pesa integration</li>
                            <li><i class="fas fa-check"></i> Basic reporting &amp; analytics</li>
                            <li><i class="fas fa-check"></i> 1 staff account</li>
                            <li><i class="fas fa-times unavailable"></i> <span class="unavailable">AI website builder</span></li>
                            <li><i class="fas fa-times unavailable"></i> <span class="unavailable">Marketing automation</span></li>
                            <li><i class="fas fa-times unavailable"></i> <span class="unavailable">KENADA AI advisor</span></li>
                        </ul>

                        <a href="{{ route('register') }}" class="btn btn-outline-light w-100 mt-auto">
                            <i class="fas fa-rocket me-2"></i>Get Started Free
                        </a>
                        <p class="text-center mt-2" style="font-size:.8rem; color:#999 !important;">
                            No credit card required
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Pro Plan ── --}}
            <div class="col-lg-4 col-md-6">
                <div class="card pricing-card featured h-100">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="text-center mb-2">
                            <span class="popular-badge">Most Popular</span>
                        </div>
                        <div class="mb-3">
                            <h3 class="card-title">Business Pro</h3>
                            <p class="mb-0" style="color:#666 !important; font-size:.9rem;">
                                For growing businesses
                            </p>
                        </div>
                        <div class="price mb-1">
                            <span class="currency">KSh</span>
                            <span class="amount" data-monthly="500" data-annual="400">500</span>
                        </div>
                        <p style="color:#888 !important; font-size:.85rem;" class="mb-4">
                            <span class="period">/month</span>
                            <span class="annual-note d-none" style="color:#13e8e9 !important;">&nbsp;&bull;&nbsp;billed annually</span>
                        </p>

                        <ul class="list-unstyled pricing-list mb-4 flex-grow-1">
                            <li><i class="fas fa-check"></i> <strong>Unlimited</strong> products &amp; services</li>
                            <li><i class="fas fa-check"></i> Advanced inventory management</li>
                            <li><i class="fas fa-check"></i> Unlimited customers &amp; CRM</li>
                            <li><i class="fas fa-check"></i> Full-featured POS + barcode scanner</li>
                            <li><i class="fas fa-check"></i> Staff management (up to 10) + commissions</li>
                            <li><i class="fas fa-check"></i> Service booking system</li>
                            <li><i class="fas fa-check"></i> AI website builder (8 themes)</li>
                            <li><i class="fas fa-check"></i> Advanced analytics &amp; reports</li>
                            <li><i class="fas fa-check"></i> Bulk SMS &amp; email marketing</li>
                            <li><i class="fas fa-check"></i> KENADA AI business advisor</li>
                            <li><i class="fas fa-check"></i> Priority email support</li>
                        </ul>

                        <a href="{{ route('register') }}" class="btn btn-primary w-100 mt-auto">
                            <i class="fas fa-play me-2"></i>Start 30-Day Free Trial
                        </a>
                        <p class="text-center mt-2" style="font-size:.8rem; color:#999 !important;">
                            No credit card needed for trial
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Enterprise Plan ── --}}
            <div class="col-lg-4 col-md-6">
                <div class="card pricing-card h-100">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3">
                            <h3 class="card-title">Enterprise</h3>
                            <p class="mb-0" style="color:#666 !important; font-size:.9rem;">
                                For large &amp; multi-location businesses
                            </p>
                        </div>
                        <div class="price mb-1">
                            <span class="currency">KSh</span>
                            <span class="amount" data-monthly="1000" data-annual="800">1,000</span>
                        </div>
                        <p style="color:#888 !important; font-size:.85rem;" class="mb-4">
                            <span class="period">/month</span>
                            <span class="annual-note d-none" style="color:#13e8e9 !important;">&nbsp;&bull;&nbsp;billed annually</span>
                        </p>

                        <ul class="list-unstyled pricing-list mb-4 flex-grow-1">
                            <li><i class="fas fa-check"></i> Everything in Business Pro</li>
                            <li><i class="fas fa-check"></i> Multi-location support</li>
                            <li><i class="fas fa-check"></i> Unlimited staff accounts</li>
                            <li><i class="fas fa-check"></i> Advanced user permissions &amp; roles</li>
                            <li><i class="fas fa-check"></i> Custom integrations &amp; API access</li>
                            <li><i class="fas fa-check"></i> Dedicated account manager</li>
                            <li><i class="fas fa-check"></i> Custom onboarding &amp; training</li>
                            <li><i class="fas fa-check"></i> White-label website option</li>
                            <li><i class="fas fa-check"></i> SLA &amp; uptime guarantee</li>
                            <li><i class="fas fa-check"></i> OCR bulk product import</li>
                        </ul>

                        <a href="#contact-cta" class="btn btn-outline-light w-100 mt-auto">
                            <i class="fas fa-envelope me-2"></i>Contact Sales
                        </a>
                        <p class="text-center mt-2" style="font-size:.8rem; color:#999 !important;">
                            Custom pricing available
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Guarantee strip --}}
        <div class="text-center mt-5">
            <p style="opacity:.85; color:rgba(255,255,255,0.8) !important;">
                <i class="fas fa-shield-alt me-2" style="color:#13e8e9;"></i>30-day money-back guarantee
                &nbsp;&bull;&nbsp;
                <i class="fas fa-lock me-2" style="color:#13e8e9;"></i>No setup fees
                &nbsp;&bull;&nbsp;
                <i class="fas fa-times-circle me-2" style="color:#13e8e9;"></i>Cancel anytime
            </p>
        </div>
    </div>
</section>

{{-- ══════════════════════ TRUST BADGES ══════════════════════ --}}
<section class="py-4 bg-dark">
    <div class="container">
        <div class="row g-3 justify-content-center">
            <div class="col-md-3 col-sm-6">
                <div class="trust-badge">
                    <i class="fas fa-users"></i>
                    <div>
                        <div style="font-weight:700; color:#13e8e9 !important;">500+</div>
                        <div style="font-size:.8rem; opacity:.8;">Active businesses</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="trust-badge">
                    <i class="fas fa-mobile-alt"></i>
                    <div>
                        <div style="font-weight:700; color:#13e8e9 !important;">M-Pesa Ready</div>
                        <div style="font-size:.8rem; opacity:.8;">Instant confirmations</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="trust-badge">
                    <i class="fas fa-headset"></i>
                    <div>
                        <div style="font-weight:700; color:#13e8e9 !important;">24/7 Support</div>
                        <div style="font-size:.8rem; opacity:.8;">Via WhatsApp &amp; email</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="trust-badge">
                    <i class="fas fa-clock"></i>
                    <div>
                        <div style="font-weight:700; color:#13e8e9 !important;">5-Min Setup</div>
                        <div style="font-size:.8rem; opacity:.8;">Start in minutes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════ FEATURE COMPARISON TABLE ══════════════════════ --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">Full <span class="text-primary">Feature Comparison</span></h2>
            <p class="lead">See exactly what's included in each plan</p>
        </div>

        <div class="table-responsive rounded-3 border" style="border-color:rgba(19,232,233,0.25) !important;">
            <table class="table table-borderless comparison-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40%; padding:18px 20px;">Feature</th>
                        <th class="text-center" style="padding:18px 12px;">Free Starter</th>
                        <th class="text-center" style="padding:18px 12px; position:relative;">
                            Business Pro
                            <br><small style="color:#13e8e9 !important; font-size:.7rem;">Most Popular</small>
                        </th>
                        <th class="text-center" style="padding:18px 12px;">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Products & Inventory --}}
                    <tr><td colspan="4" style="background:rgba(19,232,233,0.08) !important; color:#13e8e9 !important; font-weight:700; padding:10px 20px; font-size:.85rem; text-transform:uppercase; letter-spacing:1px;">Products &amp; Inventory</td></tr>
                    <tr>
                        <td class="feature-label ps-4">Products limit</td>
                        <td class="text-center">Up to 50</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Inventory tracking</td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Low stock alerts</td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">OCR product scanning</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Bulk CSV/Excel import</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>

                    {{-- Sales & POS --}}
                    <tr><td colspan="4" style="background:rgba(19,232,233,0.08) !important; color:#13e8e9 !important; font-weight:700; padding:10px 20px; font-size:.85rem; text-transform:uppercase; letter-spacing:1px;">Sales &amp; POS</td></tr>
                    <tr>
                        <td class="feature-label ps-4">Point of Sale (POS)</td>
                        <td class="text-center">Basic</td>
                        <td class="text-center">Full-featured</td>
                        <td class="text-center">Full-featured</td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Barcode scanning</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">M-Pesa STK Push</td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Receipt printing</td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Service booking system</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>

                    {{-- Staff & Customers --}}
                    <tr><td colspan="4" style="background:rgba(19,232,233,0.08) !important; color:#13e8e9 !important; font-weight:700; padding:10px 20px; font-size:.85rem; text-transform:uppercase; letter-spacing:1px;">Staff &amp; Customers</td></tr>
                    <tr>
                        <td class="feature-label ps-4">Customer CRM</td>
                        <td class="text-center">Up to 100</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Staff accounts</td>
                        <td class="text-center">1</td>
                        <td class="text-center">Up to 10</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Commission tracking</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Salary &amp; payroll</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">User roles &amp; permissions</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center">Basic</td>
                        <td class="text-center">Advanced</td>
                    </tr>

                    {{-- AI & Website --}}
                    <tr><td colspan="4" style="background:rgba(19,232,233,0.08) !important; color:#13e8e9 !important; font-weight:700; padding:10px 20px; font-size:.85rem; text-transform:uppercase; letter-spacing:1px;">AI &amp; Website</td></tr>
                    <tr>
                        <td class="feature-label ps-4">AI website builder (8 themes)</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Custom domain support</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">KENADA AI advisor</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">AI chatbot assistant</td>
                        <td class="text-center">Basic</td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">White-label website</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>

                    {{-- Marketing --}}
                    <tr><td colspan="4" style="background:rgba(19,232,233,0.08) !important; color:#13e8e9 !important; font-weight:700; padding:10px 20px; font-size:.85rem; text-transform:uppercase; letter-spacing:1px;">Marketing</td></tr>
                    <tr>
                        <td class="feature-label ps-4">Bulk SMS marketing</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Email marketing</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Social media scheduling</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>

                    {{-- Support --}}
                    <tr><td colspan="4" style="background:rgba(19,232,233,0.08) !important; color:#13e8e9 !important; font-weight:700; padding:10px 20px; font-size:.85rem; text-transform:uppercase; letter-spacing:1px;">Support</td></tr>
                    <tr>
                        <td class="feature-label ps-4">Community support</td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Priority email support</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Dedicated account manager</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>
                    <tr>
                        <td class="feature-label ps-4">Custom training &amp; onboarding</td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                        <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                    </tr>

                    {{-- CTA row --}}
                    <tr style="background:rgba(19,232,233,0.04) !important;">
                        <td></td>
                        <td class="text-center py-4">
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">Get Started</a>
                        </td>
                        <td class="text-center py-4">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Start Trial</a>
                        </td>
                        <td class="text-center py-4">
                            <a href="#contact-cta" class="btn btn-outline-light btn-sm">Contact Us</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ══════════════════════ FAQ ══════════════════════ --}}
<section class="py-5 bg-dark">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">Frequently Asked <span class="text-primary">Questions</span></h2>
            <p class="lead">Everything you need to know about Shopybook pricing</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Can I really use the Free plan forever?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        Yes! The Free Starter plan is completely free with no time limit. You get core features to manage up to 50 products, 100 customers, and accept M-Pesa payments. Upgrade only when your business needs more power.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        How does the 30-day free trial work?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        When you register, you get access to all Business Pro features for 30 days at no cost — no credit card required. After the trial, you can choose to subscribe at KSh 500/month or downgrade to the free plan.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Which payment methods can I use to pay for Shopybook?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        You can pay via M-Pesa (our most popular option for Kenyan businesses), credit/debit card, or bank transfer. We make it easy to pay in KSh with zero forex fees.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Can I upgrade or downgrade my plan at any time?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        Absolutely. You can upgrade, downgrade, or cancel your subscription at any time from your dashboard. Upgrades take effect immediately; downgrades apply at the end of your billing cycle. We never charge cancellation fees.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Is M-Pesa integration included in all plans?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        Yes! M-Pesa STK Push integration is available on all plans including the free tier. Your customers can pay directly from their Safaricom line and you get instant payment confirmations.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        What does the AI website builder include?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        On Business Pro and Enterprise you get access to our AI-powered website builder with 8 professionally designed themes, auto-generated content from your business data, SEO optimization, mobile-responsive design, and custom domain support.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Do you offer a money-back guarantee?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        Yes. If you are not satisfied within the first 30 days of a paid subscription, contact us and we will issue a full refund — no questions asked.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        Is my business data secure?
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        Your data is encrypted at rest and in transit using industry-standard TLS/SSL. We perform regular automated backups and our infrastructure is hosted on secure, audited servers. We never sell your data to third parties.
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════ CONTACT CTA ══════════════════════ --}}
<section id="contact-cta" class="py-5">
    <div class="container">
        <div class="cta-section p-5 text-center">
            <h2 class="display-4 mb-3">
                Ready to <span class="text-primary">Get Started?</span>
            </h2>
            <p class="lead mb-4" style="max-width:580px; margin: 0 auto 2rem;">
                Join 500+ Kenyan businesses already growing with Shopybook. Set up in 5 minutes, no credit card required.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket me-2"></i>Start Free Account
                </a>
                <a href="https://wa.me/254717745891" target="_blank" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-whatsapp me-2"></i>Chat on WhatsApp
                </a>
            </div>
            <p class="mt-4" style="font-size:.85rem; opacity:.7; color:rgba(255,255,255,0.7) !important;">
                Questions? Email us at <a href="mailto:info@shopybook.com" style="color:#13e8e9 !important;">info@shopybook.com</a>
                or call <a href="tel:+254717745891" style="color:#13e8e9 !important;">+254 717 745 891</a>
            </p>
        </div>
    </div>
</section>

<script>
function toggleFaq(btn) {
    const answer = btn.nextElementSibling;
    const icon = btn.querySelector('.faq-icon');
    const isOpen = answer.classList.contains('open');

    // Close all
    document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('open'));
    document.querySelectorAll('.faq-question').forEach(b => b.classList.remove('open'));

    if (!isOpen) {
        answer.classList.add('open');
        btn.classList.add('open');
    }
}

// Billing toggle
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('billingToggle');
    if (!toggle) return;

    toggle.addEventListener('change', function () {
        const isAnnual = this.checked;

        document.querySelectorAll('.price .amount').forEach(function (el) {
            const monthly = parseInt(el.dataset.monthly);
            const annual  = parseInt(el.dataset.annual);
            if (isNaN(monthly)) return;

            const val = isAnnual ? annual : monthly;
            el.textContent = val.toLocaleString();
        });

        document.querySelectorAll('.annual-note').forEach(function (el) {
            el.classList.toggle('d-none', !isAnnual);
        });
    });
});
</script>

@endsection
