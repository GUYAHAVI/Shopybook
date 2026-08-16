<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shopybook - Business Management Platform for Small Businesses in Kenya')</title>
    <meta name="description" content="@yield('meta_description', 'Shopybook - Complete business management platform for small businesses in Kenya. Manage products, services, customers, inventory, staff, and sales with M-Pesa integration. Free POS system, stock tracking, and AI-powered business insights.')">
    <meta name="keywords" content="@yield('meta_keywords', 'shopybook, shopy book, shopybooks, shopibook, shopi book, shopybuk, shoppybook, shopybock, shopybok, shopibooks, business management software Kenya, small business software, inventory management system Kenya, stock management Kenya, POS system Kenya, point of sale Kenya, M-Pesa integration, mobile money payment, business accounting software, sales tracking Kenya, customer management Kenya, CRM Kenya, invoice software Kenya, receipt generator Kenya, barcode system Kenya, business analytics Kenya, retail management software, shop management system, store management Kenya, product management, service booking system, staff management Kenya, employee tracking, business dashboard Kenya, cloud business software, online business platform, SME software Kenya, MSME platform Kenya, kiosk management, boutique software, pharmacy management, restaurant POS Kenya, salon booking system, garage management software, hardware store software, free business software Kenya, accounting software Kenya, financial management Kenya, profit tracking, expense tracking Kenya, low stock alerts, supplier management Kenya, vendor management, business reports Kenya, inventory costing, FIFO costing, stock valuation, business growth tools Kenya, entrepreneur software Kenya, startup tools Kenya, digitize business Kenya, business automation Kenya, smart business solutions, modern POS Kenya, touch screen POS, barcode scanner software, receipt printer software, cash register software, till system Kenya, shop till software, store POS system, retail POS Kenya, wholesale management, distribution software Kenya, warehouse management Kenya, stock control system, inventory audit, product tracking, batch tracking, serial number tracking, multi-location inventory, branch management Kenya, chain store software, franchise management Kenya, business intelligence Kenya, sales forecasting, demand planning Kenya, order management Kenya, purchase order system, goods received note, stock transfer, stock adjustment, inventory optimization Kenya, dead stock analysis, fast moving items, slow moving stock, stock turnover ratio, Kenya business software, Nairobi business tools, Mombasa POS system, Kisumu inventory software, Nakuru retail software, Eldoret shop management, African business platform, East Africa business software, pan-African SME tools')">
    <meta name="author" content="Shopybook">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="KE">
    <meta name="geo.placename" content="Kenya">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <meta name="rating" content="general">
    <meta name="distribution" content="global">
    <meta name="coverage" content="Worldwide">
    <meta name="target" content="all">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('title', 'Shopybook - Business Management Platform for Small Businesses in Kenya')">
    <meta property="og:description" content="@yield('meta_description', 'Shopybook - Complete business management platform for small businesses in Kenya. Free POS system, inventory management, M-Pesa integration, sales tracking, and AI-powered insights. Perfect for retail, services, restaurants, pharmacies, salons & more.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('img/logo.png') }}">
    <meta property="og:image:alt" content="Shopybook Logo - Business Management Software">
    <meta property="og:locale" content="en_KE">
    <meta property="og:site_name" content="Shopybook">
    <meta property="og:locale:alternate" content="en_GB">
    <meta property="og:locale:alternate" content="en_US">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Shopybook - Business Management Platform')">
    <meta name="twitter:description" content="@yield('meta_description', 'Complete business management platform for small businesses in Kenya. Free POS, inventory management, M-Pesa integration & AI insights.')">
    <meta name="twitter:image" content="{{ asset('img/logo.png') }}">
    <meta name="twitter:image:alt" content="Shopybook Logo">
    <meta name="twitter:site" content="@shopybook">
    <meta name="twitter:creator" content="@shopybook">

    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('css/marketing.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <!-- Schema.org structured data -->
   
    <style>
        /* Custom styles for the navbar */

        .navbar-dark .navbar-toggler {
            border-color: #000;
            color: white;
        }

        .navbar {
            top: 0;
            z-index: 1000;
            position: fixed;
            width: 100%;
        }

        .navbar-dark .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar {
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);

        }

        .nav-link {
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            opacity: 0.8;
        }

        .btn-reg {

            background: rgb(46, 12, 110) !important;
            color: white !important;
        }

        .btn-reg:hover {
            background: #fff;
            color: #000;
        }
    </style>
    
    <!-- Schema.org structured data -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => 'Shopybook',
        'alternateName' => ['Shopy Book', 'ShopyBooks', 'Shopibook', 'Shopi Book', 'Shopybuk', 'Shopybok'],
        'description' => 'Complete business management platform for small businesses in Kenya. Free POS system, inventory management, M-Pesa integration, sales tracking, customer management, and AI-powered business insights. Perfect for retail stores, service businesses, restaurants, pharmacies, salons, and more.',
        'url' => url('/'),
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'Web',
        'softwareVersion' => '1.0',
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.8',
            'ratingCount' => '150',
            'bestRating' => '5',
            'worstRating' => '1'
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'KES',
            'availability' => 'https://schema.org/InStock',
            'priceValidUntil' => date('Y-m-d', strtotime('+1 year'))
        ],
        'author' => [
            '@type' => 'Organization',
            'name' => 'Shopybook',
            'url' => url('/'),
            'logo' => asset('img/logo.png'),
            'sameAs' => [
                'https://www.facebook.com/shopybook',
                'https://twitter.com/shopybook',
                'https://www.linkedin.com/company/shopybook',
                'https://www.instagram.com/shopybook'
            ]
        ],
        'keywords' => 'business management software Kenya, inventory management, POS system, M-Pesa integration, small business platform, retail software, stock management, sales tracking, customer management, invoice software, receipt generator, business analytics',
        'featureList' => [
            'Inventory Management',
            'POS System',
            'M-Pesa Integration',
            'Sales Tracking',
            'Customer Management',
            'Staff Management',
            'Business Reports',
            'AI-Powered Insights',
            'Multi-location Support',
            'Mobile Access'
        ],
        'inLanguage' => 'en-KE',
        'availableLanguage' => ['en', 'sw'],
        'countriesSupported' => ['KE', 'UG', 'TZ', 'RW'],
        'screenshot' => asset('img/logo.png')
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    
    <!-- Additional Schema.org - Organization -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Shopybook',
        'alternateName' => ['Shopy Book', 'ShopyBooks', 'Shopibook'],
        'url' => url('/'),
        'logo' => asset('img/logo.png'),
        'description' => 'Business management platform for small and medium enterprises in Kenya and East Africa',
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Kenya'
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'contactType' => 'Customer Support',
            'areaServed' => 'KE',
            'availableLanguage' => ['English', 'Swahili']
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    
    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/')
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QEHQPSK885"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QEHQPSK885');
</script>

<body class="marketing-page">

    {{-- Tracking consent banner (Kenya DPA compliance) --}}
    @if(!isset($_COOKIE['tracking_consent']))
    <div id="tracking-consent-banner" class="fixed-bottom bg-dark text-white p-3" style="z-index: 9999;">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                <div class="small">
                    <i class="fas fa-cookie-bite me-1"></i>
                    We use analytics to improve your experience. We collect page visits, usage patterns, and performance metrics.
                    See our <a href="{{ route('privacy-policy') }}" class="text-warning text-decoration-none">Privacy Policy</a> for details.
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <button id="consent-accept" class="btn btn-sm btn-success">Accept</button>
                    <button id="consent-decline" class="btn btn-sm btn-outline-light">Decline</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function() {
            var banner = document.getElementById('tracking-consent-banner');
            document.getElementById('consent-accept').addEventListener('click', function() {
                document.cookie = 'tracking_consent=accepted; max-age=31536000; path=/; samesite=lax';
                banner.style.display = 'none';
            });
            document.getElementById('consent-decline').addEventListener('click', function() {
                document.cookie = 'tracking_consent=declined; max-age=31536000; path=/; samesite=lax';
                banner.style.display = 'none';
            });
        })();
    </script>
    @endif

    <header>
        <div class="header-topbar d-none d-md-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-contact-info d-flex flex-wrap">
                            <a href="tel:+254717745891"><i class="fas fa-phone-alt"></i> 0717 745 891</a>
                            <a href="tel:+254728831972"><i class="fas fa-phone-alt"></i> 0728 831 972</a>
                            <a href="mailto:info@shopybook.com"><i class="fas fa-envelope"></i> info@shopybook.com</a>
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
        <nav class="navbar navbar-expand-lg marketing-navbar">
            <div class="container">
                <a class="navbar-brand" href="{{ route('index') }}" title="Shopybook - Business Management Software Kenya">
                    <img src="{{ asset('img/logo.png') }}" alt="Shopybook Logo - Business Management Platform Kenya, Inventory Management, POS System" width="30" height="30"
                        class="d-inline-block align-text-top">
                    <span class="text-primary">Shopy</span>book
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('index') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Features</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="businessDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Businesses
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="businessDropdown"
                                style="color:#020258 !important;">
                                <li><a class="dropdown-item" href="{{ route('businesses') }}?type=product">Products</a></li>
                                <li><a class="dropdown-item" href="{{ route('businesses') }}?type=service">Services</a></li>
                                <li><a class="dropdown-item" href="{{ route('businesses') }}?type=hybrid">Hybrid</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="{{ route('businesses') }}">All Businesses</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Solutions
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown"
                                style="color:#020258 !important;">
                                <li><a class="dropdown-item" href="#">Inventory Management</a></li>
                                <li><a class="dropdown-item" href="#">Sales Tracking</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">AI Tools</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" href="{{ route('pricing') }}">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('docs') }}">
                                <i class="fas fa-book me-1"></i>Documentation
                            </a>
                        </li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link cta-btn" href="{{ route('register') }}">{{ __('Get Started') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    @yield('content')

    <footer class="marketing-footer py-4 mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-2">&copy; {{ date('Y') }} Shopybook Pro. All rights reserved.</p>
                    <p class="mb-0 small text-muted">
                        Business Management Software | Inventory System | POS Solution Kenya
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 small">
                        <span class="text-muted">
                            Shopybook, Shopy Book, ShopyBooks - Kenya's Leading Business Platform
                        </span>
                    </p>
                    <p class="mb-0 small text-muted">
                        Nairobi | Mombasa | Kisumu | Nakuru | Eldoret
                    </p>
                </div>
            </div>
            
            <!-- SEO Keywords Footer (Hidden but crawlable) -->
            <div class="d-none">
                <p>Shopybook, Shopy Book, ShopyBooks, Shopibook, Shopi Book, Shopybuk, Shoppybook, Shopybock, Shopybok, Shopibooks - Complete business management software for small businesses in Kenya. Inventory management system, POS system, point of sale, stock management, retail software, shop management, store management, M-Pesa integration, mobile money, business accounting, sales tracking, customer management, CRM, invoice software, receipt generator, barcode system, business analytics, pharmacy software, restaurant POS, salon booking, boutique management, hardware store software, free business software Kenya, accounting software, financial management, profit tracking, expense tracking, supplier management, business reports, FIFO costing, stock valuation, business automation, smart business solutions, Kenya business software, Nairobi, Mombasa, Kisumu, Nakuru, Eldoret, SME software, MSME platform, entrepreneur tools, startup software, digitize business Kenya.</p>
            </div>
        </div>
    </footer>

    <!-- <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script> -->
<script src="https://kit.fontawesome.com/c9cd52846d.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>