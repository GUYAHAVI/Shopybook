<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shopybook - Business Management Platform for Small Businesses in Kenya')</title>
    <meta name="description" content="@yield('meta_description', 'Shopybook - Complete business management platform for small businesses in Kenya. Manage products, services, customers, inventory, staff, and sales with M-Pesa integration.')">
    <meta name="keywords" content="@yield('meta_keywords', 'business management software Kenya, inventory management system Kenya, POS system Kenya, M-Pesa integration, small business software Kenya')">
    <meta name="author" content="Shopybook">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Shopybook - Business Management Platform')">
    <meta property="og:description" content="@yield('meta_description', 'Complete business management platform for small businesses in Kenya.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('img/logo.png') }}">
    <meta property="og:site_name" content="Shopybook">
    <meta property="og:locale" content="en_KE">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Shopybook - Business Management Platform')">
    <meta name="twitter:description" content="@yield('meta_description', 'Complete business management platform for small businesses in Kenya.')">
    <meta name="twitter:image" content="{{ asset('img/logo.png') }}">

    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Jeriah design system (versioned so CSS edits aren't served from cache) -->
    @php
        $cssVersion = fn ($path) => asset($path) . '?v=' . (file_exists(public_path($path)) ? filemtime(public_path($path)) : 1);
    @endphp
    <link rel="stylesheet" href="{{ $cssVersion('jeriah/css/templatemo-574-mexant.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('jeriah/css/navbar.css') }}">
    <link rel="stylesheet" href="{{ $cssVersion('jeriah/css/shopybook.css') }}">

    @stack('styles')
</head>

<body>

    {{-- Tracking consent banner --}}
    @if(!isset($_COOKIE['tracking_consent']))
    <div id="tracking-consent-banner" class="fixed-bottom p-3" style="z-index: 9999; background:#222; color:#fff;">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                <div class="small">
                    <i class="fas fa-cookie-bite me-1"></i>
                    We use analytics to improve your experience.
                    See our <a href="{{ route('privacy-policy') }}" style="color:#ff511a;">Privacy Policy</a> for details.
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <button id="consent-accept" class="btn1" style="border:none;">Accept</button>
                    <button id="consent-decline" class="btnb" style="border:none;">Decline</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var banner = document.getElementById('tracking-consent-banner');
            document.getElementById('consent-accept').addEventListener('click', function () {
                document.cookie = 'tracking_consent=accepted; max-age=31536000; path=/; samesite=lax';
                banner.style.display = 'none';
            });
            document.getElementById('consent-decline').addEventListener('click', function () {
                document.cookie = 'tracking_consent=declined; max-age=31536000; path=/; samesite=lax';
                banner.style.display = 'none';
            });
        })();
    </script>
    @endif

    <!-- Header -->
    <header class="jeriah-header">
        <!-- Top Bar -->
        <div class="header-topbar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-contact-info d-flex flex-wrap">
                            <a href="tel:+254717745891"><i class="fas fa-phone-alt"></i> 0717 745 891</a>
                            <a href="tel:+254728831972"><i class="fas fa-phone-alt"></i> 0728 831 972</a>
                            <a href="mailto:support@shopybook.com"><i class="fas fa-envelope"></i> support@shopybook.com</a>
                            <a href="#"><i class="fas fa-map-marker-alt"></i> Nairobi, Kenya</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="header-social d-flex justify-content-end">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="header-main">
            <div class="container">
                <nav class="navbar navbar-expand-lg p-0">
                    <a class="navbar-brand" href="{{ route('index') }}">
                        <img src="{{ asset('img/logo.png') }}" alt="Shopybook" class="img-fluid">
                        <span class="brand-text">Shopybook</span>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto jeriah-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('index') ? 'active' : '' }}" href="{{ route('index') }}">Home</a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="featuresDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">Features</a>
                                <div class="dropdown-menu" aria-labelledby="featuresDropdown">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4 dropdown-img-col">
                                                <img src="{{ asset('jeriah/images/ai-data-analysis-storytelling.webp') }}"
                                                    alt="Shopybook Features" class="dropdown-img">
                                                <h3 class="dropdown-title mt-3">One Platform</h3>
                                                <p class="text-muted">Everything your business needs, connected in a single system.</p>
                                            </div>
                                            <div class="col-md-4 dropdown-links-col">
                                                <h5>Operations</h5>
                                                <a class="dropdown-item" href="#features">Product Management</a>
                                                <a class="dropdown-item" href="#features">Inventory Tracking</a>
                                                <a class="dropdown-item" href="#features">Point of Sale</a>
                                                <a class="dropdown-item" href="#features">Service Bookings</a>
                                                <a class="dropdown-item" href="#features">Stock Receiving</a>
                                            </div>
                                            <div class="col-md-4 dropdown-links-col">
                                                <h5>Growth</h5>
                                                <a class="dropdown-item" href="#features">Customer CRM</a>
                                                <a class="dropdown-item" href="#features">SMS Marketing</a>
                                                <a class="dropdown-item" href="#features">AI Website Builder</a>
                                                <a class="dropdown-item" href="#features">Business Analytics</a>
                                                <a class="dropdown-item" href="#features">KENADA AI Advisor</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="businessDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">Businesses</a>
                                <div class="dropdown-menu" aria-labelledby="businessDropdown">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4 dropdown-img-col">
                                                <img src="{{ asset('jeriah/images/handshake.png') }}"
                                                    alt="Businesses on Shopybook" class="dropdown-img">
                                                <h3 class="dropdown-title mt-3">Our Community</h3>
                                                <p class="text-muted">Discover businesses already growing with Shopybook.</p>
                                            </div>
                                            <div class="col-md-4 dropdown-links-col">
                                                <h5>Browse</h5>
                                                <a class="dropdown-item" href="{{ route('businesses') }}?type=product">Product Businesses</a>
                                                <a class="dropdown-item" href="{{ route('businesses') }}?type=service">Service Businesses</a>
                                                <a class="dropdown-item" href="{{ route('businesses') }}?type=hybrid">Hybrid Businesses</a>
                                                <a class="dropdown-item" href="{{ route('businesses') }}">All Businesses</a>
                                            </div>
                                            <div class="col-md-4 dropdown-links-col">
                                                <h5>Resources</h5>
                                                <a class="dropdown-item" href="{{ route('docs') }}">Documentation</a>
                                                <a class="dropdown-item" href="{{ route('pricing') }}">Pricing</a>
                                                <a class="dropdown-item" href="{{ route('register') }}">Create Account</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" href="{{ route('pricing') }}">Pricing</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('docs') }}">Docs</a>
                            </li>

                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link cta-btn" href="{{ route('register') }}">Get Started</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link cta-btn" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2>About Shopybook</h2>
                    <p>
                        Shopybook is a complete business management platform built for small businesses in Kenya.
                        Manage products, inventory, sales, staff and customers in one place — with M-Pesa payments
                        and AI-powered insights included.
                    </p>
                </div>

                <div class="col-md-3">
                    <h2>Quick Links</h2>
                    <ul class="list-unstyled">
                        <li><i class="fa-solid fa-forward"></i><a href="{{ route('index') }}">Home</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="{{ route('businesses') }}">Businesses</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="{{ route('pricing') }}">Pricing</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="{{ route('docs') }}">Documentation</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h2>Features</h2>
                    <ul class="list-unstyled">
                        <li><i class="fa-solid fa-forward"></i><a href="#features">Point of Sale</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="#features">Inventory Management</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="#features">Customer CRM</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="#features">Staff & Payroll</a></li>
                        <li><i class="fa-solid fa-forward"></i><a href="#features">AI Website Builder</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h2>Contact Us</h2>
                    <h4>Get in Touch</h4>
                    <p>
                        <i class="fa fa-map-marker"></i> Nairobi, Kenya<br>
                        <i class="fa fa-phone"></i> 0717 745 891<br>
                        <i class="fa fa-phone"></i> 0728 831 972<br>
                        <i class="fa-brands fa-whatsapp"></i> 0717 745 891
                    </p>
                    <h4>Email Us</h4>
                    <p>
                        <i class="fa fa-envelope"></i> support@shopybook.com
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>Copyright &copy; {{ date('Y') }} Shopybook. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>

    <script>
        // Fixed header: offset the page by the header's real height, and
        // compact the header once the page is scrolled.
        (function () {
            var header = document.querySelector('.jeriah-header');
            if (!header) return;

            var basePad = 0;

            function measure() {
                // Only measure at rest, when the top bar is still visible,
                // otherwise the compact height would be baked in.
                var wasCompact = header.classList.contains('header-sticky');
                if (wasCompact) header.classList.remove('header-sticky');
                basePad = header.offsetHeight;
                document.body.style.paddingTop = basePad + 'px';
                if (wasCompact) header.classList.add('header-sticky');
            }

            function onScroll() {
                header.classList.toggle('header-sticky', window.scrollY > 60);
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', measure);
            window.addEventListener('load', measure);
            measure();
            onScroll();
        })();
    </script>

    @stack('scripts')
</body>

</html>
