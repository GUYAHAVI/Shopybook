@extends('layouts.public')
@section('title', 'Pricing - Shopybook Business Management Plans for Small Businesses in Kenya')
@section('meta_description', 'Choose the right Shopybook plan for your business. Free Starter, Business Pro at KSh 500/month, and Enterprise at KSh 1,000/month. M-Pesa integration, AI tools, and more.')
@section('meta_keywords', 'shopybook pricing, business management software pricing Kenya, affordable POS Kenya, small business software plans, M-Pesa billing')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <span class="badge-popular">Simple &amp; Transparent</span>
        <h1>Choose Your Perfect Plan</h1>
        <p class="mx-auto" style="max-width:620px;">
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

{{-- ═══════════════ PRICING CARDS ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row justify-content-center g-4 align-items-stretch">

            {{-- ── Free Plan ── --}}
            <div class="col-lg-4 col-md-6">
                <div class="sb-pricing-card">
                    <h3>Free Starter</h3>
                    <p class="plan-desc">Perfect for getting started</p>
                    <div class="mb-1">
                        <span class="price-currency">KSh</span>
                        <span class="price"><span class="amount" data-monthly="0" data-annual="0">0</span></span>
                        <span class="price-period">/month &mdash; forever free</span>
                    </div>

                    <ul class="list-unstyled flex-grow-1 my-4">
                        <li><i class="fas fa-check"></i> Up to 50 products</li>
                        <li><i class="fas fa-check"></i> Basic inventory tracking</li>
                        <li><i class="fas fa-check"></i> Customer management (up to 100)</li>
                        <li><i class="fas fa-check"></i> Basic POS system</li>
                        <li><i class="fas fa-check"></i> M-Pesa integration</li>
                        <li><i class="fas fa-check"></i> Basic reporting &amp; analytics</li>
                        <li><i class="fas fa-check"></i> 1 staff account</li>
                        <li><i class="fas fa-times"></i> <span class="text-muted">AI website builder</span></li>
                        <li><i class="fas fa-times"></i> <span class="text-muted">Marketing automation</span></li>
                        <li><i class="fas fa-times"></i> <span class="text-muted">KENADA AI advisor</span></li>
                    </ul>

                    <a href="{{ route('register') }}" class="btnb btn-block-j">Get Started Free</a>
                    <p class="text-center mt-2 mb-0" style="font-size:.8rem; color:#999;">
                        No credit card required
                    </p>
                </div>
            </div>

            {{-- ── Pro Plan ── --}}
            <div class="col-lg-4 col-md-6">
                <div class="sb-pricing-card featured">
                    <span class="badge-popular">Most Popular</span>
                    <h3>Business Pro</h3>
                    <p class="plan-desc">For growing businesses</p>
                    <div class="mb-1">
                        <span class="price-currency">KSh</span>
                        <span class="price"><span class="amount" data-monthly="500" data-annual="400">500</span></span>
                        <span class="price-period">/month</span>
                        <span class="annual-note d-none" style="color:#ff511a;">&nbsp;&bull;&nbsp;billed annually</span>
                    </div>

                    <ul class="list-unstyled flex-grow-1 my-4">
                        <li><i class="fas fa-check"></i> <strong>Unlimited</strong> products &amp; services</li>
                        <li><i class="fas fa-check"></i> Advanced inventory management</li>
                        <li><i class="fas fa-check"></i> Unlimited customers &amp; CRM</li>
                        <li><i class="fas fa-check"></i> Full-featured POS</li>
                        <li><i class="fas fa-check"></i> Staff management (up to 10) + commissions</li>
                        <li><i class="fas fa-check"></i> Service booking system</li>
                        <li><i class="fas fa-check"></i> AI website builder (8 themes)</li>
                        <li><i class="fas fa-check"></i> Advanced analytics &amp; reports</li>
                        <li><i class="fas fa-check"></i> Bulk SMS &amp; email marketing</li>
                        <li><i class="fas fa-check"></i> KENADA AI business advisor</li>
                        <li><i class="fas fa-check"></i> Priority email support</li>
                    </ul>

                    <a href="{{ route('register') }}" class="btn1 btn-block-j">Start 30-Day Free Trial</a>
                    <p class="text-center mt-2 mb-0" style="font-size:.8rem; color:#999;">
                        No credit card needed for trial
                    </p>
                </div>
            </div>

            {{-- ── Enterprise Plan ── --}}
            <div class="col-lg-4 col-md-6">
                <div class="sb-pricing-card">
                    <h3>Enterprise</h3>
                    <p class="plan-desc">For large &amp; multi-location businesses</p>
                    <div class="mb-1">
                        <span class="price-currency">KSh</span>
                        <span class="price"><span class="amount" data-monthly="1000" data-annual="800">1,000</span></span>
                        <span class="price-period">/month</span>
                        <span class="annual-note d-none" style="color:#ff511a;">&nbsp;&bull;&nbsp;billed annually</span>
                    </div>

                    <ul class="list-unstyled flex-grow-1 my-4">
                        <li><i class="fas fa-check"></i> Everything in Business Pro</li>
                        <li><i class="fas fa-check"></i> Multi-location support</li>
                        <li><i class="fas fa-check"></i> Unlimited staff accounts</li>
                        <li><i class="fas fa-check"></i> Advanced user permissions &amp; roles</li>
                        <li><i class="fas fa-check"></i> Custom integrations</li>
                        <li><i class="fas fa-check"></i> Dedicated account manager</li>
                        <li><i class="fas fa-check"></i> Custom onboarding &amp; training</li>
                        <li><i class="fas fa-check"></i> SLA &amp; uptime guarantee</li>
                        <li><i class="fas fa-check"></i> OCR bulk product import</li>
                    </ul>

                    <a href="#contact-cta" class="btnb btn-block-j">Contact Sales</a>
                    <p class="text-center mt-2 mb-0" style="font-size:.8rem; color:#999;">
                        Custom pricing available
                    </p>
                </div>
            </div>

        </div>

        {{-- Guarantee strip --}}
        <div class="text-center mt-5">
            <p class="mb-0" style="color:#444;">
                <i class="fas fa-shield-alt me-2" style="color:#43ba7f;"></i>30-day money-back guarantee
                &nbsp;&bull;&nbsp;
                <i class="fas fa-lock me-2" style="color:#43ba7f;"></i>No setup fees
                &nbsp;&bull;&nbsp;
                <i class="fas fa-times-circle me-2" style="color:#43ba7f;"></i>Cancel anytime
            </p>
        </div>
    </div>
</section>

{{-- ═══════════════ TRUST STRIP ═══════════════ --}}
<section class="sb-strip">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-md-3 col-sm-6">
                <span class="strip-item"><i class="fas fa-users"></i> <strong>500+</strong>&nbsp;Active businesses</span>
            </div>
            <div class="col-md-3 col-sm-6">
                <span class="strip-item"><i class="fas fa-mobile-alt"></i> M-Pesa Ready</span>
            </div>
            <div class="col-md-3 col-sm-6">
                <span class="strip-item"><i class="fas fa-headset"></i> 24/7 Support</span>
            </div>
            <div class="col-md-3 col-sm-6">
                <span class="strip-item"><i class="fas fa-clock"></i> 5-Min Setup</span>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ FEATURE COMPARISON TABLE ═══════════════ --}}
<section class="sb-section sb-section-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="sb-eyebrow">Compare</span>
            <h2 class="sb-heading">Full Feature Comparison</h2>
            <p class="sb-subheading">See exactly what's included in each plan.</p>
        </div>

        <div class="table-responsive">
            <table class="table sb-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40%;">Feature</th>
                        <th class="text-center">Free Starter</th>
                        <th class="text-center">
                            Business Pro
                            <br><small style="color:#ff511a; font-size:.7rem;">Most Popular</small>
                        </th>
                        <th class="text-center">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Products & Inventory --}}
                    <tr><td colspan="4" class="fw-bold" style="background:#7b2e2e; color:#ff511a; text-transform:uppercase; letter-spacing:1px; font-size:.85rem;">Products &amp; Inventory</td></tr>
                    <tr>
                        <td class="ps-4">Products limit</td>
                        <td class="text-center">Up to 50</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="ps-4">Inventory tracking</td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Low stock alerts</td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">OCR product scanning</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Bulk CSV/Excel import</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>

                    {{-- Sales & POS --}}
                    <tr><td colspan="4" class="fw-bold" style="background:#7b2e2e; color:#ff511a; text-transform:uppercase; letter-spacing:1px; font-size:.85rem;">Sales &amp; POS</td></tr>
                    <tr>
                        <td class="ps-4">Point of Sale (POS)</td>
                        <td class="text-center">Basic</td>
                        <td class="text-center">Full-featured</td>
                        <td class="text-center">Full-featured</td>
                    </tr>
                    <tr>
                        <td class="ps-4">M-Pesa STK Push</td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Receipt printing</td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Service booking system</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>

                    {{-- Staff & Customers --}}
                    <tr><td colspan="4" class="fw-bold" style="background:#7b2e2e; color:#ff511a; text-transform:uppercase; letter-spacing:1px; font-size:.85rem;">Staff &amp; Customers</td></tr>
                    <tr>
                        <td class="ps-4">Customer CRM</td>
                        <td class="text-center">Up to 100</td>
                        <td class="text-center">Unlimited</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="ps-4">Staff accounts</td>
                        <td class="text-center">1</td>
                        <td class="text-center">Up to 10</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="ps-4">Commission tracking</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Salary &amp; payroll</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">User roles &amp; permissions</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center">Basic</td>
                        <td class="text-center">Advanced</td>
                    </tr>

                    {{-- AI & Website --}}
                    <tr><td colspan="4" class="fw-bold" style="background:#7b2e2e; color:#ff511a; text-transform:uppercase; letter-spacing:1px; font-size:.85rem;">AI &amp; Website</td></tr>
                    <tr>
                        <td class="ps-4">AI website builder (8 themes)</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Free subdomain (yourname.shopybook.com)</td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">KENADA AI advisor</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">AI chatbot assistant</td>
                        <td class="text-center">Basic</td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>

                    {{-- Marketing --}}
                    <tr><td colspan="4" class="fw-bold" style="background:#7b2e2e; color:#ff511a; text-transform:uppercase; letter-spacing:1px; font-size:.85rem;">Marketing</td></tr>
                    <tr>
                        <td class="ps-4">Bulk SMS marketing</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Email marketing</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Social media scheduling</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>

                    {{-- Support --}}
                    <tr><td colspan="4" class="fw-bold" style="background:#7b2e2e; color:#ff511a; text-transform:uppercase; letter-spacing:1px; font-size:.85rem;">Support</td></tr>
                    <tr>
                        <td class="ps-4">Community support</td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Priority email support</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Dedicated account manager</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Custom training &amp; onboarding</td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-times sb-cross"></i></td>
                        <td class="text-center"><i class="fas fa-check sb-check"></i></td>
                    </tr>

                    {{-- CTA row --}}
                    <tr>
                        <td></td>
                        <td class="text-center py-4">
                            <a href="{{ route('register') }}" class="btnb">Get Started</a>
                        </td>
                        <td class="text-center py-4">
                            <a href="{{ route('register') }}" class="btn1">Start Trial</a>
                        </td>
                        <td class="text-center py-4">
                            <a href="#contact-cta" class="btnb">Contact Us</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ═══════════════ FAQ ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="text-center mb-5">
            <span class="sb-eyebrow">FAQ</span>
            <h2 class="sb-heading">Frequently Asked Questions</h2>
            <p class="sb-subheading">Everything you need to know about Shopybook pricing.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        Can I really use the Free plan forever?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        Yes! The Free Starter plan is completely free with no time limit. You get core features to manage up to 50 products, 100 customers, and accept M-Pesa payments. Upgrade only when your business needs more power.
                    </div>
                </div>

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        How does the 30-day free trial work?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        When you register, you get access to all Business Pro features for 30 days at no cost — no credit card required. After the trial, you can choose to subscribe at KSh 500/month or downgrade to the free plan.
                    </div>
                </div>

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        Which payment methods can I use to pay for Shopybook?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        You can pay via M-Pesa (our most popular option for Kenyan businesses), credit/debit card, or bank transfer. We make it easy to pay in KSh with zero forex fees.
                    </div>
                </div>

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        Can I upgrade or downgrade my plan at any time?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        Absolutely. You can upgrade, downgrade, or cancel your subscription at any time from your dashboard. Upgrades take effect immediately; downgrades apply at the end of your billing cycle. We never charge cancellation fees.
                    </div>
                </div>

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        Is M-Pesa integration included in all plans?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        Yes! M-Pesa STK Push integration is available on all plans including the free tier. Your customers can pay directly from their Safaricom line and you get instant payment confirmations.
                    </div>
                </div>

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        What does the AI website builder include?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        On Business Pro and Enterprise you get access to our AI-powered website builder with 8 professionally designed themes, auto-generated content from your business data, SEO optimization, mobile-responsive design, and a free subdomain (yourbusiness.shopybook.com).
                    </div>
                </div>

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        Do you offer a money-back guarantee?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        Yes. If you are not satisfied within the first 30 days of a paid subscription, contact us and we will issue a full refund — no questions asked.
                    </div>
                </div>

                <div class="sb-faq-item">
                    <button class="sb-faq-q" onclick="toggleFaq(this)">
                        Is my business data secure?
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="sb-faq-a">
                        Your data is encrypted at rest and in transit using industry-standard TLS/SSL. We perform regular automated backups and our infrastructure is hosted on secure, audited servers. We never sell your data to third parties.
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ CONTACT CTA ═══════════════ --}}
<section class="sb-section sb-section-light" id="contact-cta">
    <div class="container">
        <div class="sb-cta">
            <h2>Ready To Get Started?</h2>
            <p>Join 500+ Kenyan businesses already growing with Shopybook. Set up in 5 minutes, no credit card required.</p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="{{ route('register') }}" class="btn1">Start Free Account</a>
                <a href="https://wa.me/254717745891" target="_blank" class="btnb">Chat on WhatsApp</a>
            </div>
            <p class="mt-4 mb-0" style="font-size:.85rem;">
                Questions? Email us at <a href="mailto:support@shopybook.com" style="color:#ff511a;">support@shopybook.com</a>
                or call <a href="tel:+254717745891" style="color:#ff511a;">0717 745 891</a>
            </p>
        </div>
    </div>
</section>

<script>
function toggleFaq(btn) {
    const answer = btn.nextElementSibling;
    const isOpen = answer.classList.contains('open');

    // Close all
    document.querySelectorAll('.sb-faq-a').forEach(a => a.classList.remove('open'));
    document.querySelectorAll('.sb-faq-q').forEach(b => b.classList.remove('open'));

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
