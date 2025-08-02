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

.lead, p, .card-text, .card-title, .list-unstyled, .contact-info, .social-bubbles, .uscontent, .uscard {
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
input.form-control:focus, textarea.form-control:focus, select.form-select:focus {
    border-color: #13e8e9 !important;
    box-shadow: 0 0 0 2px #13e8e9 !important;
    color: #020258 !important;
}
input.form-control::placeholder, textarea.form-control::placeholder {
    color: #888 !important;
    opacity: 1;
}

.card, .pricing-card, .glass-card, .uscard {
    background: #03104e !important;
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

/* Pricing enhancements */
.pricing-card {
    background: #03104e !important;
    border: 2px solid #13e8e9 !important;
    border-radius: 12px;
    transition: all 0.3s ease;
}

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
    color: #fff !important;
    opacity: 0.8;
    font-size: 1rem;
}

.pricing-card .list-unstyled li {
    color: #fff !important;
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
    font-size: 1rem;
    opacity: 0.7;
}

/* Contact form enhancements */
.glass-card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.1);
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
}
</style>

    <div id="home" class="container-fluid px-0 hero-section">
        <div>
            <div class="morphing-bubbles-hero">
                <div class="morphing-bubble main-bubble"></div>
                <div class="morphing-bubble secondary-bubble"></div>
                <div class="morphing-bubble accent-bubble"></div>
            </div>
            <div class="brandtext ">
                <div class="row">
                    <div class="col-md-7">
                        <h1>Transform Your Business with Shopybook</h1>
                        <p class="lead">Complete Business Management Platform for Small Businesses in Kenya</p>
                        <p class="mb-4">
                            Manage products, services, customers, inventory, staff, and sales with M-Pesa integration. 
                            All-in-one solution designed specifically for emerging markets.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-2">Start Free Account</a>
                            <a href="#features" class="btn btn-outline-light btn-lg">Explore Features</a>
                        </div>
                    </div>
                    <div class="col-md-5 position-relative">
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
    </div>
    
    <!-- Features Section -->
    <section id="features" class="py-5 text-light position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 position-relative">
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
                <div class="col-lg-6">
                    <h2 class="display-4 mb-4">
                        Why Choose <span class="text-primary">Shopybook?</span>
                    </h2>
                    <p class="lead">The complete business management solution built for small businesses.</p>
                    <p>
                        Shopybook is a comprehensive multi-tenant platform designed specifically for small businesses 
                        in emerging markets. From product management and service bookings to customer relationships 
                        and M-Pesa payments, we provide everything you need to grow your business efficiently.
                    </p>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Multi-tenant architecture for unlimited growth</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> M-Pesa & PayPal payment integration</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Multi-language support (English, Swahili, Sheng)</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Mobile-first responsive design</li>
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

            <div class="row g-4">
                <!-- Product Management -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Product Management</h3>
                            <p>
                                Complete inventory control with categories, brands, stock tracking, and low-stock alerts. 
                                Manage unlimited products with barcode support.
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
                            <h3>Service Booking</h3>
                            <p>
                                Manage service appointments, staff assignments, commission tracking, and bundled services. 
                                Perfect for salons, repair shops, and service businesses.
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
                            <h3>Customer Management</h3>
                            <p>
                                Build lasting relationships with comprehensive customer profiles, order history, 
                                and personalized marketing campaigns.
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
                            <h3>Point of Sale</h3>
                            <p>
                                Intuitive POS system with barcode scanning, multiple payment methods, 
                                and real-time inventory updates. Perfect for retail operations.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- M-Pesa Integration -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-mobile-alt"></i>
                        </div>
                        <div class="uscontent">
                            <h3>M-Pesa Integration</h3>
                            <p>
                                Seamless payment processing with M-Pesa and PayPal integration. 
                                Accept payments securely and track all transactions automatically.
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
                            <h3>Staff & Employee Management</h3>
                            <p>
                                Manage your team with employee records, commission tracking, 
                                performance analytics, and automated payroll calculations.
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
                            <h3>Advanced Inventory</h3>
                            <p>
                                Real-time inventory tracking with transaction history, 
                                multi-location support, and automated reorder alerts.
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
                                Comprehensive reports and analytics for sales, inventory, 
                                customer behavior, and staff performance. Make data-driven decisions.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Marketing Tools -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Marketing & Promotions</h3>
                            <p>
                                Create targeted campaigns, manage promotions, track advertising spend, 
                                and engage customers with automated marketing tools.
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

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 bg-green text-light">
                        <div class="card-body">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="card-text">
                                "Shopybook transformed my retail business. The M-Pesa integration and inventory management 
                                have increased my efficiency by 300%. I can now focus on growing instead of tracking stock manually."
                            </p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle me-3"
                                    width="50" alt="Business Owner" />
                                <div>
                                    <h6 class="mb-0">James Mwangi</h6>
                                    <small class="text-muted">Electronics Store, Nairobi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 bg-green text-light">
                        <div class="card-body">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="card-text">
                                "Managing my salon services and staff commissions was a nightmare before Shopybook. 
                                Now everything is automated and my staff love the transparent commission tracking."
                            </p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://randomuser.me/api/portraits/women/45.jpg" class="rounded-circle me-3"
                                    width="50" alt="Business Owner" />
                                <div>
                                    <h6 class="mb-0">Wanjiku Kamau</h6>
                                    <small class="text-muted">Beauty Salon, Mombasa</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 bg-green text-light">
                        <div class="card-body">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="card-text">
                                "The multi-language support and local payment methods make Shopybook perfect for our market. 
                                Our customers love paying with M-Pesa, and we get instant confirmations."
                            </p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://randomuser.me/api/portraits/men/28.jpg" class="rounded-circle me-3"
                                    width="50" alt="Business Owner" />
                                <div>
                                    <h6 class="mb-0">Peter Ochieng</h6>
                                    <small class="text-muted">Hardware Store, Kisumu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            <h3 class="card-title text-primary">Free Starter</h3>
                            <div class="price mb-4">
                                <span class="currency text-primary">KSh</span>
                                <span class="amount text-primary">0</span>
                                <span class="period text-light">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4 text-start">
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Up to 50 products</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Basic inventory tracking</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Customer management</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Basic reporting</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>M-Pesa integration</li>
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
                            <h3 class="card-title text-primary">Business Pro</h3>
                            <div class="price mb-4">
                                <span class="currency text-primary">KSh</span>
                                <span class="amount text-primary">2,500</span>
                                <span class="period text-light">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4 text-start">
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Unlimited products & services</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Advanced inventory management</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Staff & commission management</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Service booking system</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Advanced analytics</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Marketing tools</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Priority support</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-primary">Start 30-Day Trial</a>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h3 class="card-title text-primary">Enterprise</h3>
                            <div class="price mb-4">
                                <span class="currency text-primary">KSh</span>
                                <span class="amount text-primary">5,000</span>
                                <span class="period text-light">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4 text-start">
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Everything in Pro</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Multi-location support</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Advanced user permissions</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Custom integrations</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Dedicated account manager</li>
                                <li class="text-light"><i class="fas fa-check text-primary me-2"></i>Custom training</li>
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
                    <form class="glass-card p-4" action="#" method="POST">
                        @csrf
                        <h3 class="mb-4 text-primary">Get Started Today</h3>
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control bg-transparent text-light" placeholder="Business Owner Name" required />
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control bg-transparent text-light" placeholder="Business Email" required />
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control bg-transparent text-light" placeholder="Phone Number (e.g., +254717745891)" required />
                        </div>
                        <div class="mb-3">
                            <select name="business_type" class="form-select bg-dark text-light" required>
                                <option value="">Select Your Business Type...</option>
                                <option value="retail">Retail Store</option>
                                <option value="service">Service Business</option>
                                <option value="restaurant">Restaurant/Food</option>
                                <option value="salon">Salon/Beauty</option>
                                <option value="electronics">Electronics</option>
                                <option value="hardware">Hardware Store</option>
                                <option value="clothing">Clothing/Fashion</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea name="message" class="form-control bg-transparent text-light" rows="4"
                                placeholder="Tell us about your business challenges or questions" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-rocket me-2"></i>Start Free Account
                        </button>
                        <p class="small text-center mt-3 text-muted">
                            No credit card required • Setup takes less than 5 minutes
                        </p>
                    </form>
                </div>

                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="glass-card h-100 p-4">
                        <h3 class="card-title mb-4 text-primary">Get in Touch</h3>
                        <ul class="list-unstyled contact-info">
                            <li class="mb-3">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <div>
                                    <strong>Email Support</strong><br>
                                    <span>harveyelvis24@gmail.com</span>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <div>
                                    <strong>Phone Support</strong><br>
                                    <span>+254 717745891</span><br>
                                    <small class="text-muted">Mon-Fri, 8AM-6PM EAT</small>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-whatsapp text-primary me-3"></i>
                                <div>
                                    <strong>WhatsApp Support</strong><br>
                                    <span>+254 717745891</span><br>
                                    <small class="text-muted">Quick responses</small>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                <div>
                                    <strong>Location</strong><br>
                                    <span>Nairobi, Kenya</span><br>
                                    <small class="text-muted">Serving all of East Africa</small>
                                </div>
                            </li>
                        </ul>

                        <div class="mt-4">
                            <h5 class="mb-3 text-primary">Connect With Us</h5>
                            <div class="social-bubbles d-flex gap-3">
                                <a href="#" class="social-bubble text-primary">
                                    <i class="fab fa-twitter fa-lg"></i>
                                </a>
                                <a href="#" class="social-bubble text-primary">
                                    <i class="fab fa-linkedin-in fa-lg"></i>
                                </a>
                                <a href="#" class="social-bubble text-primary">
                                    <i class="fab fa-facebook-f fa-lg"></i>
                                </a>
                                <a href="#" class="social-bubble text-primary">
                                    <i class="fab fa-instagram fa-lg"></i>
                                </a>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-dark rounded">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-headset me-2"></i>Need Immediate Help?
                            </h6>
                            <p class="mb-2 small">Our support team is ready to help you get started</p>
                            <a href="tel:+254717745891" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-phone me-1"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add loaded class to trigger animations
            setTimeout(function () {
                document.querySelector('.brandimgs-container').classList.add('loaded');
                document.querySelector('.feature-bubbles-container').classList.add('loaded');
            }, 500);

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
 

@endsection