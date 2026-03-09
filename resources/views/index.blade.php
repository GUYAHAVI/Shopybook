@extends('layouts.master')
@section('title', 'Shopybook - Complete Business Management Platform for Small Businesses in Kenya')
@section('meta_description', 'Transform your small business with Shopybook\'s all-in-one platform. Manage products, services, customers, inventory, staff, and sales with M-Pesa integration. Perfect for Kenyan businesses.')
@section('meta_keywords', 'business management software Kenya, small business platform, inventory management, M-Pesa integration, POS system Kenya, customer management, staff management, service booking')
@section('content')

<style>
body, html {
    background: #020258 !important;
    color: #fff !important;
}
.hero-section,
.hero-section *,
.hero-section .container-fluid,
.hero-section .brandtext,
.hero-section .morphing-bubbles-hero,
.hero-section .morphing-bubble,
.hero-section .main-bubble,
.hero-section .secondary-bubble,
.hero-section .accent-bubble {
    background: #020258 !important;
    background-color: #020258 !important;
    color: #fff !important;
}

h1, h2, h3, h4, h5, h6, .display-4, .text-primary {
    color: #13e8e9 !important;
}

.lead, p:not(.pricing-card p):not(.testimonial-card p), .card-text:not(.testimonial-card .card-text), .card-title:not(.pricing-card .card-title):not(.testimonial-card .card-title), .list-unstyled:not(.pricing-list), .contact-info, .social-bubbles, .uscontent, .uscard, .glass-card {
    color: #fff !important;
}

.btn-primary, .btn-primary:active, .btn-primary:focus {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}
.btn-primary:hover {
    background: #020258 !important;
    color: #13e8e9 !important;
    border: 2px solid #13e8e9 !important;
}

.btn-outline-light, .btn-outline-light:active, .btn-outline-light:focus {
    background: transparent !important;
    color: #13e8e9 !important;
    border: 2px solid #13e8e9 !important;
}
.btn-outline-light:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
}

input.form-control, textarea.form-control, select.form-select {
    background: #fff !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}

.glass-card input.form-control, 
.glass-card textarea.form-control, 
.glass-card select.form-select {
    background: #fff !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}
input.form-control:focus, textarea.form-control:focus, select.form-select:focus {
    border-color: #13e8e9 !important;
    box-shadow: 0 0 0 2px #13e8e9 !important;
    color: #020258 !important;
}
input.form-control::placeholder, textarea.form-control::placeholder {
    color: #888 !important;
    opacity: 1;
}

.card, .pricing-card {
    background: #fff !important;
    color: #333 !important;
    border: 2px solid #13e8e9 !important;
}

.glass-card, .uscard {
    background: #020258 !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
}
.card-title, .pricing-card .card-title, .glass-card .card-title, .uscard .card-title {
    color: #13e8e9 !important;
}

.popular-badge {
    background: #13e8e9 !important;
    color: #020258 !important;
    border-radius: 8px;
    padding: 2px 10px;
    font-weight: bold;
    font-size: 0.9rem;
    margin-bottom: 10px;
    display: inline-block;
}

/* SEO-optimized styles */
.stat-card {
    padding: 1rem;
    margin-bottom: 1rem;
}

.stat-card h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-card p {
    font-size: 1rem;
    margin: 0;
    opacity: 0.9;
}

/* Enhanced cards */
.uscard {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
    background: #020258 !important;
    color: #fff !important;
}

.uscard:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(19, 232, 233, 0.3);
}

.usicon {
    background: rgba(19, 232, 233, 0.1);
    padding: 2rem;
    margin-bottom: 1.5rem;
}

.usicon i {
    font-size: 3rem;
    color: #13e8e9;
}

.uscontent h3 {
    color: #13e8e9 !important;
}

.uscontent p {
    color: #fff !important;
}

/* Pricing enhancements */


.pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(19, 232, 233, 0.3);
}

.pricing-card.featured {
    transform: scale(1.05);
    border: 3px solid #13e8e9;
    position: relative;
    z-index: 2;
}

.pricing-card .card-title {
    color: #13e8e9 !important;
    font-weight: 600;
    margin-bottom: 1rem;
}

.pricing-card .price {
    font-size: 2.5rem;
    font-weight: 700;
}

.pricing-card .price .currency,
.pricing-card .price .amount {
    color: #13e8e9 !important;
}

.pricing-card .price .period {
    color: #666 !important;
    opacity: 0.8;
    font-size: 1rem;
}

/* More specific selectors to override global styles */
.pricing-card .pricing-list,
.pricing-card .pricing-list li,
.pricing-card .list-unstyled.pricing-list,
.pricing-card .list-unstyled.pricing-list li {
    color: #333 !important;
    margin-bottom: 0.75rem;
    padding-left: 0.5rem;
}

.pricing-card .btn-outline-light {
    border-color: #13e8e9 !important;
    color: #13e8e9 !important;
    background: transparent !important;
    font-weight: 600;
}

.pricing-card .btn-outline-light:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
    border-color: #13e8e9 !important;
}

.pricing-card .popular-badge {
    background: #13e8e9 !important;
    color: #020258 !important;
    border-radius: 20px;
    padding: 4px 12px;
    font-weight: bold;
    font-size: 0.8rem;
    margin-bottom: 15px;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pricing-card .period {
    color: #666 !important;
    font-size: 1rem;
    opacity: 0.7;
}

/* Additional overrides for text content */
.pricing-card p,
.pricing-card span:not(.currency):not(.amount),
.pricing-card div {
    color: #333 !important;
}

/* Comprehensive pricing card text override */
.pricing-card,
.pricing-card * {
    color: #333 !important;
}

/* Specific overrides for accent colors */
.pricing-card .card-title,
.pricing-card .price .currency,
.pricing-card .price .amount,
.pricing-card .text-primary,
.pricing-card .fas.fa-check {
    color: #13e8e9 !important;
}

/* Period text - slightly lighter */
.pricing-card .period,
.pricing-card .price .period {
    color: #666 !important;
}

/* Contact form enhancements */
.glass-card {
    backdrop-filter: blur(10px);
    background: #020258 !important;
    border: 1px solid rgba(19, 232, 233, 0.3);
    border-radius: 12px;
}

.social-bubble {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(19, 232, 233, 0.1);
    border: 2px solid #13e8e9;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-bubble:hover {
    background: #13e8e9;
    color: #020258 !important;
    transform: scale(1.1);
}

/* Testimonial Carousel Styles */
.testimonial-card {
    background: #fff !important;
    color: #333 !important;
    border: 2px solid #13e8e9 !important;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(19, 232, 233, 0.2);
    transition: all 0.3s ease;
    margin: 0 auto;
    max-width: 600px;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(19, 232, 233, 0.3);
}

.testimonial-text,
.testimonial-text-dark,
.testimonial-card .card-text {
    color: #333 !important;
    font-size: 1.1rem;
    line-height: 1.6;
    font-style: italic;
    margin-bottom: 1.5rem;
}

.testimonial-name {
    color: #13e8e9 !important;
    font-weight: 600;
}

/* Comprehensive testimonial card text override */
.testimonial-card,
.testimonial-card *,
.testimonial-card .card-body,
.testimonial-card .card-body * {
    color: #333 !important;
}

/* Specific overrides for accent colors */
.testimonial-card .testimonial-name,
.testimonial-card h6 {
    color: #13e8e9 !important;
}

.testimonial-card .text-warning {
    color: #020258 !important;
}

.testimonial-card .text-muted {
    color: #666 !important;
}

/* Force dark text for testimonial paragraphs */
.testimonial-card p,
.testimonial-card .card-text,
.testimonial-card .testimonial-text,
.testimonial-card .testimonial-text-dark {
    color: #333 !important;
    font-weight: 400;
}

/* Carousel Controls Styling */
.carousel-control-prev,
.carousel-control-next {
    width: 50px;
    height: 50px;
    background: rgba(19, 232, 233, 0.8);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    border: none;
}

.carousel-control-prev {
    left: -25px;
}

.carousel-control-next {
    right: -25px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: brightness(0) invert(1);
}

/* Carousel Indicators */
.carousel-indicators {
    bottom: -50px;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(19, 232, 233, 0.5);
    border: none;
    margin: 0 5px;
}

.carousel-indicators button.active {
    background-color: #13e8e9;
}

/* Carousel Container */
#testimonialsCarousel {
    padding: 2rem 0;
    position: relative;
}

.carousel-inner {
    padding: 0 50px;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .stat-card h3 {
        font-size: 2rem;
    }
    
    .pricing-card.featured {
        transform: none;
        margin-top: 1rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .testimonial-card {
        margin: 0 10px;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 40px;
        height: 40px;
    }
    
    .carousel-control-prev {
        left: -20px;
    }
    
    .carousel-control-next {
        right: -20px;
    }
    
    .carousel-inner {
        padding: 0 30px;
    }
}
</style>

    <div id="home" class="container-fluid px-0 hero-section">
        <div class="morphing-bubbles-hero">
            <div class="morphing-bubble main-bubble"></div>
            <div class="morphing-bubble secondary-bubble"></div>
            <div class="morphing-bubble accent-bubble"></div>
        </div>
        
        <!-- Mobile-specific floating elements -->
        <div class="mobile-floating-elements d-block d-lg-none">
            <div class="mobile-bubble-1"></div>
            <div class="mobile-bubble-2"></div>
            <div class="mobile-bubble-3"></div>
        </div>
        
        <div class="brandtext">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-7 col-md-8 col-sm-12">
                    <!-- Mobile badge -->
                    <div class="mobile-badge d-block d-lg-none mb-3">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="fas fa-mobile-alt me-2"></i>Mobile Optimized
                        </span>
                    </div>
                    
                    <h1 class="text-center text-md-start">Transform Your Business with Shopybook</h1>
                    <p class="lead text-center text-md-start">Complete Business Management Platform with AI-Powered Features</p>
                    <p class="mb-4 text-center text-md-start">
                        All-in-one platform: POS, Inventory, Website Builder, AI Advisor, Marketing Automation, 
                        Staff Management & M-Pesa Integration. Designed for Kenyan businesses.
                    </p>
                    
                    <!-- Mobile feature highlights -->
                    <div class="mobile-features d-block d-lg-none mb-4">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="feature-highlight">
                                    <i class="fas fa-robot text-primary"></i>
                                    <small>AI Powered</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-highlight">
                                    <i class="fas fa-globe text-primary"></i>
                                    <small>Website Builder</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-highlight">
                                    <i class="fas fa-mobile-alt text-primary"></i>
                                    <small>M-Pesa Ready</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-highlight">
                                    <i class="fas fa-chart-line text-primary"></i>
                                    <small>Real Analytics</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-md-start">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket me-2"></i>Start Free Account
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-arrow-down me-2"></i>Explore Features
                        </a>
                    </div>
                    
                    <!-- Mobile trust indicators -->
                    <div class="mobile-trust-indicators d-block d-lg-none mt-4">
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i>Trusted by 500+ businesses
                                <span class="mx-2">•</span>
                                <i class="fas fa-clock me-1"></i>5-minute setup
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-4 d-none d-lg-block position-relative">
                    <div class="brandimgs-container">
                        <div class="image-bubble brand-bubble-1">
                            <img src="{{ asset('img/img-1.jpeg') }}" alt="Product 1">
                        </div>
                        <div class="image-bubble brand-bubble-2">
                            <img src="{{ asset('img/img-2.jpeg') }}" alt="Product 2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <section id="features" class="py-5 text-light position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 position-relative d-none d-lg-block">
                    <div class="feature-bubbles-container">
                        <div class="image-bubble feature-bubble-1">
                            <img src="{{ asset('img/img-3.jpeg') }}" alt="Product 3">
                        </div>
                        <div class="image-bubble feature-bubble-2">
                            <img src="{{ asset('img/img-4.jpeg') }}" alt="Product 4">
                        </div>
                    </div>
                    <div class="morphing-bubbles">
                        <div class="morphing-bubble main-bubble"></div>
                        <div class="morphing-bubble secondary-bubble"></div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <!-- Mobile feature cards -->
                    <div class="mobile-feature-cards d-block d-lg-none mb-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="mobile-feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h6>Multi-tenant</h6>
                                    <small>Unlimited growth</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mobile-feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <h6>M-Pesa Ready</h6>
                                    <small>Local payments</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mobile-feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <h6>Multi-language</h6>
                                    <small>English, Swahili</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mobile-feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <h6>Mobile First</h6>
                                    <small>Responsive design</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="display-4 mb-4 text-center text-lg-start">
                        Why Choose <span class="text-primary">Shopybook?</span>
                    </h2>
                    <p class="lead text-center text-lg-start">The complete business management solution built for small businesses in Kenya.</p>
                    <p class="text-center text-lg-start">
                        Shopybook is a comprehensive multi-tenant platform designed specifically for small businesses. 
                        From product management with OCR scanning and service bookings to customer CRM, AI-powered website builder, 
                        and M-Pesa payments, we provide everything you need to grow your business efficiently.
                    </p>
                    <ul class="list-unstyled mt-4 d-none d-lg-block">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Complete product & inventory management with OCR</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> AI-powered website builder with 8 themes</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> KENADA AI Business Advisor for growth insights</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Multi-language support (English, Swahili, Sheng)</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Full-featured POS with M-Pesa integration</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Marketing automation with social media & SMS</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="services" class="py-5 bg-dark text-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4">
                    Comprehensive <span class="text-primary">Business Features</span>
                </h2>
                <p class="lead">
                    Everything you need to manage and grow your business
                </p>
            </div>

            <!-- Mobile feature grid -->
            <div class="mobile-features-grid d-block d-lg-none mb-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <h6>Products</h6>
                            <small>Full inventory + OCR</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <h6>Services</h6>
                            <small>Complete bookings</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-cash-register"></i>
                            </div>
                            <h6>POS System</h6>
                            <small>Full featured</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <h6>Customers</h6>
                            <small>CRM + history</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                            <h6>AI Website</h6>
                            <small>Auto-build sites</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <h6>AI Advisor</h6>
                            <small>KENADA insights</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <h6>Staff</h6>
                            <small>HR + payroll</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <h6>Marketing</h6>
                            <small>SMS + Social</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <h6>Analytics</h6>
                            <small>Live insights</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-mobile-alt"></i>
                            </div>
                            <h6>Payments</h6>
                            <small>M-Pesa + more</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                            <h6>Inventory</h6>
                            <small>Real-time stock</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mobile-feature-item">
                            <div class="feature-icon-mobile">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <h6>Notifications</h6>
                            <small>Smart alerts</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 d-none d-lg-flex">
                <!-- Product Management -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Product Management</h3>
                            <p>
                                Complete inventory control with categories, brands, stock tracking, OCR image processing, 
                                bulk import (CSV/Excel), and automated low-stock alerts. Stock receiving with receipt history tracking.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Service Booking -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Service Booking System</h3>
                            <p>
                                Complete service management with bookings, staff assignments, commission tracking, 
                                bundled services, bulk entry, PDF/Excel reports, and automated email notifications.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Point of Sale -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Point of Sale System</h3>
                            <p>
                                Full-featured POS with cart management, multiple payment methods 
                                (Cash & M-Pesa), dynamic product conversions, receipt printing & order history.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Customer Management -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Customer CRM</h3>
                            <p>
                                Manage individual & organization customers with complete purchase history, 
                                walk-in customer support, and detailed customer profiles.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Staff Management -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Staff Management</h3>
                            <p>
                                Complete HR system with employee records, salary management, commission calculations, 
                                salary advances tracking, and detailed staff performance reports.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Inventory Tracking -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-warehouse"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Inventory Management</h3>
                            <p>
                                Real-time stock levels, stock receiving with receipts, inventory transactions, 
                                automated low stock alerts, equipment tracking, and comprehensive inventory reports.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AI-Powered Website Builder -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-globe"></i>
                        </div>
                        <div class="uscontent">
                            <h3>AI Website Builder</h3>
                            <p>
                                Create professional websites with AI-powered theme recommendations, auto-generated content, 
                                8 beautiful themes, drag & drop sections, SEO optimization, and free subdomain included.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Analytics & Reports -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Business Analytics</h3>
                            <p>
                                Real-time dashboard with sales analytics, service performance, staff metrics, 
                                financial reports, profit margin calculations, and AI-powered business insights (KENADA).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Marketing & Social Media -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Marketing Automation</h3>
                            <p>
                                Bulk SMS & email marketing, social media integration (Facebook, Instagram, Twitter, LinkedIn), 
                                post scheduling, AI video generation, and promotions management.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- M-Pesa & Payments -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-mobile-alt"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Payment Integration</h3>
                            <p>
                                Accept payments via M-Pesa and Cash. Track all transactions, 
                                generate receipts automatically, and manage payment history.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AI Business Advisor -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div class="uscontent">
                            <h3>AI Business Advisor</h3>
                            <p>
                                KENADA - Your AI business consultant with continuous learning, business insights, 
                                personalized recommendations, AI chat assistant, and data-driven growth strategies.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notifications System -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Smart Notifications</h3>
                            <p>
                                Dashboard notification center with email notifications for orders, bookings, and low stock. 
                                Track unread count, mark as read/unread, and never miss important updates.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registered business -->
    <section class="py-5 text-light">
        <div class="container px-5">
            <div class="text-center mb-5">
                <h2 class="display-4">
                    Trusted by <span class="text-primary">Growing Businesses</span>
                </h2>
                <p class="lead">Join the community of successful businesses using Shopybook across Kenya</p>
                <div class="row mt-4">
                    <div class="col-md-3 col-6 text-center">
                        <div class="stat-card">
                            <h3 class="text-primary">500+</h3>
                            <p>Active Businesses</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <div class="stat-card">
                            <h3 class="text-primary">10,000+</h3>
                            <p>Products Managed</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <div class="stat-card">
                            <h3 class="text-primary">25,000+</h3>
                            <p>Orders Processed</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <div class="stat-card">
                            <h3 class="text-primary">KSh 5M+</h3>
                            <p>Revenue Managed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
           
             @include('partials.businesses', ['groupedBusinesses' => $groupedBusinesses])
            
           

           
        </div>
    
    </section>


    <!-- Testimonials Section -->
    <section class="py-5 bg-dark text-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4">
                    What <span class="text-primary">Business Owners Say</span>
                </h2>
                <p class="lead">Real success stories from Shopybook users across Kenya</p>
            </div>

            @php
            $hardcodedTestimonials = [
                ['name' => 'James Mwangi', 'role' => 'Electronics Store, Nairobi', 'quote' => 'Shopybook transformed my retail business. The M-Pesa integration and inventory management have increased my efficiency by 300%. I can now focus on growing instead of tracking stock manually.', 'rating' => 5, 'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg'],
                ['name' => 'Wanjiku Kamau', 'role' => 'Beauty Salon, Mombasa', 'quote' => 'Managing my salon services and staff commissions was a nightmare before Shopybook. Now everything is automated and my staff love the transparent commission tracking.', 'rating' => 5, 'avatar' => 'https://randomuser.me/api/portraits/women/45.jpg'],
                ['name' => 'Peter Ochieng',  'role' => 'Hardware Store, Kisumu',  'quote' => 'The local payment methods make Shopybook perfect for our market. Our customers love paying with M-Pesa, and we get instant confirmations.', 'rating' => 5, 'avatar' => 'https://randomuser.me/api/portraits/men/28.jpg'],
            ];
            $showTestimonials = ($platformTestimonials->count() >= 3) ? $platformTestimonials : collect($hardcodedTestimonials);
            @endphp

            <div class="row g-4">
            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner">
                    @foreach($showTestimonials as $idx => $t)
                    @php
                        $isModel = $t instanceof \App\Models\Testimonial;
                        $tName   = $isModel ? $t->name   : $t['name'];
                        $tRole   = $isModel ? $t->role   : $t['role'];
                        $tQuote  = $isModel ? $t->quote  : $t['quote'];
                        $tRating = $isModel ? $t->rating : $t['rating'];
                        $tAvatar = $isModel ? null : ($t['avatar'] ?? null);
                    @endphp
                    <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card testimonial-card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3 text-warning">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i class="fas fa-star{{ $s > $tRating ? '-o' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="card-text testimonial-text">
                                            "{{ $tQuote }}"
                                        </p>
                                        <div class="d-flex align-items-center justify-content-center mt-3">
                                            @if($tAvatar)
                                            <img src="{{ $tAvatar }}" class="rounded-circle me-3" width="50" alt="{{ $tName }}" />
                                            @else
                                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white" style="width:50px;height:50px;flex-shrink:0;font-weight:700;font-size:1.1rem;">
                                                {{ strtoupper(substr($tName, 0, 1)) }}
                                            </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 testimonial-name">{{ $tName }}</h6>
                                                <small class="text-muted">{{ $tRole }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>

                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    @foreach($showTestimonials as $idx => $t)
                    <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="{{ $idx }}"
                        {{ $idx === 0 ? 'class="active" aria-current="true"' : '' }} aria-label="Slide {{ $idx + 1 }}"></button>
                    @endforeach
                </div>
            </div>
            </div>

            <!-- Leave a Review Button -->
            <div class="text-center mt-4">
                <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#platformReviewModal">
                    <i class="fas fa-pen me-1"></i> Share Your Experience
                </button>
            </div>
        </div>
    </section>

    <!-- Platform Review Submission Modal -->
    <div class="modal fade" id="platformReviewModal" tabindex="-1" aria-labelledby="platformReviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="platformReviewLabel"><i class="fas fa-star text-warning me-2"></i>Share Your Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Success panel (shown after AJAX submit) -->
                <div id="platformReviewSuccess" class="modal-body text-center py-5" style="display:none;">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h5 class="mb-2">Thank you!</h5>
                    <p class="text-muted mb-4">Your review has been submitted and will appear after approval.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
                <!-- Form panel -->
                <form id="platformReviewForm" action="{{ route('testimonials.submit') }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div id="platformReviewError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Mary Njoroge" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Business / Role <span class="text-muted">(optional)</span></label>
                            <input type="text" name="role" class="form-control" placeholder="e.g. Boutique Owner, Nairobi" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Your Review <span class="text-danger">*</span></label>
                            <textarea name="quote" class="form-control" rows="4" minlength="20" maxlength="1000" placeholder="Tell us how Shopybook helped your business..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rating</label>
                            <div id="starRating" class="d-flex gap-2 fs-4">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-warning star-pick" data-val="{{ $i }}" style="cursor:pointer;" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="5">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="platformReviewBtn" class="btn btn-primary">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <section id="signup" class="py-5 position-relative">
        <div class="container position-relative" style="z-index: 2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-4 mb-4 text-light">
                        Simple <span class="text-primary">Pricing</span>
                    </h2>
                    <p class="lead mb-5 text-light">
                        Affordable plans designed for Kenyan small businesses
                    </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <!-- Free Plan -->
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h3 class="card-title">Free Starter</h3>
                            <div class="price mb-4">
                                <span class="currency">KSh</span>
                                <span class="amount">0</span>
                                <span class="period">/month</span>
                            </div>
                            <ul class="list-unstyled pricing-list mb-4 text-start">
                                <li><i class="fas fa-check text-primary me-2"></i>Up to 50 products</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Basic inventory tracking</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Customer management</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Basic reporting</li>
                                <li><i class="fas fa-check text-primary me-2"></i>M-Pesa integration</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-outline-light">Get Started Free</a>
                        </div>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card featured h-100">
                        <div class="card-body text-center">
                            <div class="popular-badge">MOST POPULAR</div>
                            <h3 class="card-title">Business Pro</h3>
                            <div class="price mb-4">
                                <span class="currency">KSh</span>
                                <span class="amount">500</span>
                                <span class="period">/month</span>
                            </div>
                            <ul class="list-unstyled pricing-list mb-4 text-start">
                                <li><i class="fas fa-check text-primary me-2"></i>Unlimited products & services</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Advanced inventory management</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Staff & commission management</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Service booking system</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Advanced analytics</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Marketing tools</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Priority support</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-primary">Start 30-Day Trial</a>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h3 class="card-title">Enterprise</h3>
                            <div class="price mb-4">
                                <span class="currency">KSh</span>
                                <span class="amount">1,000</span>
                                <span class="period">/month</span>
                            </div>
                            <ul class="list-unstyled pricing-list mb-4 text-start">
                                <li><i class="fas fa-check text-primary me-2"></i>Everything in Pro</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Multi-location support</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Advanced user permissions</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Custom integrations</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Dedicated account manager</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Custom training</li>
                            </ul>
                            <a href="#contact" class="btn btn-outline-light">Contact Sales</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-light" style="opacity: 0.8;">
                    <i class="fas fa-shield-alt me-2"></i>30-day money-back guarantee • No setup fees • Cancel anytime
                </p>
            </div>
        </div>
    </section>
           
    

    <!-- Contact Section -->
    <section id="contact" class="position-relative py-5 text-light">
        <div class="container position-relative" style="z-index: 2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-4 mb-4">
                        Ready to <span class="text-primary">Transform</span> Your Business?
                    </h2>
                    <p class="lead mb-5">
                        Join hundreds of successful businesses already using Shopybook
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <form class="glass-card p-4" action="{{ route('contact.submit') }}" method="POST" id="contactForm">
                        @csrf
                        <h3 class="mb-4 text-primary">Get Started Today</h3>
                        
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control bg-transparent text-light" placeholder="Business Owner Name" value="{{ old('name') }}" required />
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control bg-transparent text-light" placeholder="Business Email" value="{{ old('email') }}" required />
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control bg-transparent text-light" placeholder="Phone Number (e.g., +254717745891)" value="{{ old('phone') }}" required />
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <select name="business_type" class="form-select bg-dark text-light" required>
                                <option value="">Select Your Business Type...</option>
                                <option value="retail" {{ old('business_type') == 'retail' ? 'selected' : '' }}>Retail Store</option>
                                <option value="service" {{ old('business_type') == 'service' ? 'selected' : '' }}>Service Business</option>
                                <option value="restaurant" {{ old('business_type') == 'restaurant' ? 'selected' : '' }}>Restaurant/Food</option>
                                <option value="salon" {{ old('business_type') == 'salon' ? 'selected' : '' }}>Salon/Beauty</option>
                                <option value="electronics" {{ old('business_type') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="hardware" {{ old('business_type') == 'hardware' ? 'selected' : '' }}>Hardware Store</option>
                                <option value="clothing" {{ old('business_type') == 'clothing' ? 'selected' : '' }}>Clothing/Fashion</option>
                                <option value="other" {{ old('business_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('business_type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <textarea name="message" class="form-control bg-transparent text-light" rows="4"
                                placeholder="Tell us about your business challenges or questions" required>{{ old('message') }}</textarea>
                            @error('message')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-send-fill me-2"></i>Send Your Message
                        </button>
                        <p class="small text-center mt-3 text-muted">
                            We'll respond within 24 hours • Free consultation available
                        </p>
                    </form>
                </div>

                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="glass-card h-100 p-4">
                        <h3 class="card-title mb-4 text-primary">Get in Touch</h3>
                        <ul class="list-unstyled contact-info">
                            <li class="mb-3">
                                <i class="bi bi-envelope-fill text-primary me-3 fs-4"></i>
                                <div>
                                    <strong>Email Support</strong><br>
                                    <span>info@shopybook.com</span>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-telephone-fill text-primary me-3 fs-4"></i>
                                <div>
                                    <strong>Phone Support</strong><br>
                                    <span>+254 717745891</span><br>
                                    <small class="brandtext">Mon-Fri, 8AM-6PM EAT</small>
                                </div>
                            </li>
                            <li class="mb-3">
                            <i class="bi bi-whatsapp text-primary me-3 fs-4"></i>	
                                <div>
                                    <strong>WhatsApp Support</strong><br>
                                    <span>+254 717745891</span><br>
                                    <small class="brandtext">Quick responses</small>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-geo-alt-fill text-primary me-3 fs-4"></i>
                                <div>
                                    <strong>Location</strong><br>
                                    <span>Nairobi, Kenya</span><br>
                                    <small class="brandtext">Serving all of East Africa</small>
                                </div>
                            </li>
                        </ul>

                        <div class="mt-4">
                            <h5 class="mb-3 text-primary">Connect With Us</h5>
                            <div class="social-bubbles d-flex gap-3 flex-wrap">
                                <a href="https://wa.me/254717745891" target="_blank" class="social-bubble text-primary" title="WhatsApp">
                                <i class="bi bi-whatsapp fs-4"></i>
                                </a>
                                <a href="https://twitter.com/shopybook" target="_blank" class="social-bubble text-primary" title="Twitter">
                                <i class="bi bi-twitter-x fs-4"></i>
                                </a>
                                <a href="https://www.linkedin.com/company/108257756/admin/dashboard/" target="_blank" class="social-bubble text-primary" title="LinkedIn">
                                <i class="bi bi-linkedin fs-4"></i>
                                </a>
                                <a href="https://www.facebook.com/profile.php?id=61578210136925" target="_blank" class="social-bubble text-primary" title="Facebook">
                                <i class="bi bi-facebook fs-4"></i>
                                </a>
                                <a href="https://www.instagram.com/shopybook254?igsh=MWMzaXI5Z3I3eXR4ZQ==" target="_blank" class="social-bubble text-primary" title="Instagram">
                                <i class="bi bi-instagram fs-4"></i>
                                </a>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-dark rounded">
                            <h6 class="text-primary mb-2">
                                <i class="bi bi-headset me-2"></i>Need Immediate Help?
                            </h6>
                            <p class="mb-2 small">Our support team is ready to help you get started</p>
                            <a href="tel:+254f717745891" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-telephone me-1"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #020258; border: 3px solid #13e8e9;">
                <div class="modal-header" style="background: #13e8e9; border: none;">
                    <h5 class="modal-title" id="successModalLabel" style="color: #020258;">
                        <i class="bi bi-check-circle-fill me-2"></i>Message Sent Successfully!
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4" style="color: #fff;">
                    <div class="mb-3">
                        <i class="bi bi-envelope-check" style="font-size: 4rem; color: #13e8e9;"></i>
                    </div>
                    <h4 style="color: #13e8e9; margin-bottom: 1rem;">Thank You for Contacting Us!</h4>
                    <p class="mb-2" style="color: #fff;">We've received your message and will get back to you shortly.</p>
                    <p class="small" style="color: rgba(255, 255, 255, 0.7);">Our team typically responds within 24 hours during business days.</p>
                </div>
                <div class="modal-footer" style="border: none; justify-content: center;">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-2"></i>Got It
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #020258; border: 3px solid #dc3545;">
                <div class="modal-header" style="background: #dc3545; border: none;">
                    <h5 class="modal-title text-white" id="errorModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Oops! Something Went Wrong
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4" style="color: #fff;">
                    <div class="mb-3">
                        <i class="bi bi-x-circle" style="font-size: 4rem; color: #dc3545;"></i>
                    </div>
                    <h4 style="color: #13e8e9; margin-bottom: 1rem;">Message Not Sent</h4>
                    <p class="mb-3" style="color: #fff;">Sorry, there was an error sending your message. Please try again or contact us directly.</p>
                    <div class="alert" style="background: rgba(19, 232, 233, 0.1); border: 1px solid #13e8e9; color: #fff;">
                        <strong style="color: #13e8e9;">Alternative Contact:</strong><br>
                        <i class="bi bi-envelope me-2"></i><a href="mailto:info@shopybook.com" style="color: #13e8e9; text-decoration: none;">info@shopybook.com</a><br>
                        <i class="bi bi-telephone me-2"></i><a href="tel:+254717745891" style="color: #13e8e9; text-decoration: none;">+254 717745891</a><br>
                        <i class="bi bi-whatsapp me-2"></i><a href="https://wa.me/254717745891" style="color: #13e8e9; text-decoration: none;" target="_blank">WhatsApp</a>
                    </div>
                </div>
                <div class="modal-footer" style="border: none; justify-content: center;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="document.getElementById('contactForm').scrollIntoView({behavior: 'smooth'});">
                        <i class="bi bi-arrow-repeat me-2"></i>Try Again
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show success modal if success message exists
            @if(session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                // Clear form after showing success
                setTimeout(function() {
                    document.getElementById('contactForm').reset();
                }, 500);
            @endif

            // Show error modal if error message exists
            @if(session('error'))
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            @endif

            // Add loaded class to trigger animations
            setTimeout(function () {
                document.querySelector('.brandimgs-container').classList.add('loaded');
                document.querySelector('.feature-bubbles-container').classList.add('loaded');
            }, 500);

            // Mobile-specific animations
            setTimeout(function () {
                const mobileElements = document.querySelectorAll('.mobile-floating-elements > div, .mobile-feature-card, .feature-highlight');
                mobileElements.forEach((el, index) => {
                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, index * 200);
                });
            }, 1000);

            // Hover effects
            const images = document.querySelectorAll('.image-bubble');
            images.forEach(img => {
                img.addEventListener('mouseenter', function () {
                    this.style.transform = 'scale(1.05)';
                    this.style.zIndex = '20';
                    this.style.boxShadow = '0 15px 40px rgba(0,0,0,0.4)';
                });
                img.addEventListener('mouseleave', function () {
                    this.style.transform = 'scale(1)';
                    this.style.zIndex = '10';
                    this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
                });
            });

            // Mobile touch effects
            const mobileCards = document.querySelectorAll('.mobile-feature-card, .feature-highlight');
            mobileCards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                });
                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Mobile feature items touch effects
            const mobileFeatureItems = document.querySelectorAll('.mobile-feature-item');
            mobileFeatureItems.forEach(item => {
                item.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                });
                item.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Stat cards touch effects
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Testimonial cards touch effects
            const testimonialCards = document.querySelectorAll('.testimonial-card');
            testimonialCards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Pricing cards touch effects
            const pricingCards = document.querySelectorAll('.pricing-card');
            pricingCards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Contact form elements touch effects
            const contactElements = document.querySelectorAll('.glass-card, .contact-info li, .social-bubble');
            contactElements.forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                element.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
   
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for images to load and initial draw
    window.addEventListener('load', function() {
        console.log('Window loaded - attempting to draw connectors');
        connectImagesWithLines();
    });

    // Handle window resize with debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            console.log('Window resized - redrawing connectors');
            connectImagesWithLines();
        }, 250);
    });
});

function connectImagesWithLines() {
    console.log('Starting connectImagesWithLines function');
    
    try {
        // Get all the elements
        const img1 = document.querySelector('.brand-bubble-1');
        const img2 = document.querySelector('.brand-bubble-2');
        const img3 = document.querySelector('.feature-bubble-1');
        const img4 = document.querySelector('.feature-bubble-2');
        
        // Debug: Check if elements exist
        console.log('Elements found:', {
            img1: !!img1,
            img2: !!img2,
            img3: !!img3,
            img4: !!img4
        });
        
        if (!img1 || !img2 || !img3 || !img4) {
            console.error('One or more elements not found!');
            return;
        }
        
        // Remove existing connectors if they exist
        document.querySelectorAll('.custom-connector, .custom-connector-container').forEach(el => {
            console.log('Removing existing connector:', el);
            el.remove();
        });
        
        // Create a new SVG container
        const svgContainer = document.createElement('div');
        svgContainer.className = 'custom-connector-container';
        Object.assign(svgContainer.style, {
            position: 'absolute',
            top: '0',
            left: '0',
            width: '100%',
            height: '100%',
            pointerEvents: 'none',
            overflow: 'visible',
            zIndex: '1'
        });
        
        // Insert the SVG container
        document.body.appendChild(svgContainer); // Changed from #home to body for better compatibility
        
        // Create SVG element
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute('class', 'custom-connector');
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', '100%');
        Object.assign(svg.style, {
            position: 'absolute',
            top: '0',
            left: '0',
            overflow: 'visible'
        });
        
        svgContainer.appendChild(svg);
        
        // Calculate positions relative to document
        function getAbsolutePosition(el) {
            const rect = el.getBoundingClientRect();
            const position = {
                x: rect.left + window.scrollX,
                y: rect.top + window.scrollY,
                width: rect.width,
                height: rect.height
            };
            console.log('Position for', el.className, position);
            return position;
        }
        
        const img1Pos = getAbsolutePosition(img1);
        const img2Pos = getAbsolutePosition(img2);
        const img3Pos = getAbsolutePosition(img3);
        const img4Pos = getAbsolutePosition(img4);
        
        // Calculate connection points
        const img1BottomLeft = {
            x: img1Pos.x,
            y: img1Pos.y + img1Pos.height
        };
        
        const img2BottomRight = {
            x: img2Pos.x + img2Pos.width,
            y: img2Pos.y + img2Pos.height
        };
        
        const img3TopRight = {
            x: img3Pos.x + img3Pos.width,
            y: img3Pos.y
        };
        
        const img4TopLeft = {
            x: img4Pos.x,
            y: img4Pos.y
        };
        
        // Create first path (img1 bottom-left to img3 top-right)
        const path1 = document.createElementNS("http://www.w3.org/2000/svg", "path");
        const path1MiddleX = (img1BottomLeft.x + img3TopRight.x) / 2;
        const path1Data = `M${img1BottomLeft.x},${img1BottomLeft.y} 
                          C${path1MiddleX},${img1BottomLeft.y} 
                          ${path1MiddleX},${img3TopRight.y} 
                          ${img3TopRight.x},${img3TopRight.y}`;
        path1.setAttribute('d', path1Data);
        Object.assign(path1.style, {
            stroke: 'rgba(255, 255, 255, 0.7)',
            strokeWidth: '2px',
            strokeDasharray: '5,3',
            fill: 'none'
        });
        svg.appendChild(path1);
        
        // Create second path (img2 bottom-right to img4 top-left)
        const path2 = document.createElementNS("http://www.w3.org/2000/svg", "path");
        const path2MiddleX = (img2BottomRight.x + img4TopLeft.x) / 2;
        const path2Data = `M${img2BottomRight.x},${img2BottomRight.y} 
                          C${path2MiddleX},${img2BottomRight.y} 
                          ${path2MiddleX},${img4TopLeft.y} 
                          ${img4TopLeft.x},${img4TopLeft.y}`;
        path2.setAttribute('d', path2Data);
        Object.assign(path2.style, {
            stroke: 'rgba(255, 255, 255, 0.7)',
            strokeWidth: '2px',
            strokeDasharray: '5,3',
            fill: 'none'
        });
        svg.appendChild(path2);
        
        console.log('Connectors created successfully');
        console.log('Path1 data:', path1Data);
        console.log('Path2 data:', path2Data);
        
    } catch (error) {
        console.error('Error in connectImagesWithLines:', error);
    }
}
</script>
 
    <!-- Shopybook Customer Support Chatbot -->
    @include('components.chatbot')

<script>
// Star rating picker + AJAX submit for platform review modal
document.addEventListener('DOMContentLoaded', function () {
    var stars  = document.querySelectorAll('#starRating .star-pick');
    var input  = document.getElementById('ratingInput');
    var form   = document.getElementById('platformReviewForm');
    var btn    = document.getElementById('platformReviewBtn');
    var errDiv = document.getElementById('platformReviewError');
    var successPanel = document.getElementById('platformReviewSuccess');
    var modal  = document.getElementById('platformReviewModal');

    // ── Star rating ──────────────────────────────────────────
    function highlight(val) {
        stars.forEach(function(s) {
            s.style.opacity = s.dataset.val <= val ? '1' : '0.3';
        });
    }
    if (stars.length && input) {
        highlight(5);
        stars.forEach(function(s) {
            s.addEventListener('click', function() { input.value = s.dataset.val; highlight(s.dataset.val); });
            s.addEventListener('mouseenter', function() { highlight(s.dataset.val); });
        });
        document.getElementById('starRating').addEventListener('mouseleave', function() { highlight(input.value); });
    }

    // ── AJAX submit ──────────────────────────────────────────
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            errDiv.classList.add('d-none');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Submitting…';

            try {
                var resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(form),
                });

                if (resp.ok) {
                    form.style.display = 'none';
                    successPanel.style.display = 'block';
                } else {
                    var data = await resp.json();
                    var msg = data.message
                        || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Something went wrong.');
                    errDiv.textContent = msg;
                    errDiv.classList.remove('d-none');
                    btn.disabled = false;
                    btn.textContent = 'Submit Review';
                }
            } catch (err) {
                errDiv.textContent = 'Network error. Please try again.';
                errDiv.classList.remove('d-none');
                btn.disabled = false;
                btn.textContent = 'Submit Review';
            }
        });
    }

    // ── Reset on modal close ──────────────────────────────────
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            if (form) { form.style.display = ''; form.reset(); }
            if (successPanel) successPanel.style.display = 'none';
            if (errDiv) errDiv.classList.add('d-none');
            if (btn) { btn.disabled = false; btn.textContent = 'Submit Review'; }
            if (input) { input.value = 5; highlight(5); }
        });
    }
});
</script>

@endsection