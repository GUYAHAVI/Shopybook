<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->getMetaTitle() }} - {{ $website->business->name }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $page->getMetaDescription() }}">
    @if($page->meta_keywords)
    <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $page->url }}">
    <meta property="og:title" content="{{ $page->getMetaTitle() }}">
    <meta property="og:description" content="{{ $page->getMetaDescription() }}">
    @if($page->getOgImage())
    <meta property="og:image" content="{{ asset('storage/' . $page->getOgImage()) }}">
    @endif

    <!-- Favicon -->
    @if($website->favicon_path)
    <link rel="icon" href="{{ asset('storage/' . $website->favicon_path) }}">
    @endif

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    @php
    $fonts = $website->getFonts();
    $headingFont = $fonts['heading'] ?? 'Poppins';
    $bodyFont = $fonts['body'] ?? 'Open Sans';
    $colors = $website->getColorScheme();
    $primaryColor   = $colors['primary']    ?? '#1a237e';
    $secondaryColor = $colors['secondary']  ?? '#ff6f00';
    $accentColor    = $colors['accent']     ?? '#00bcd4';
    $bgColor        = $colors['background'] ?? '#ffffff';
    $textColor      = $colors['text']       ?? '#212121';

    // Derive dark variant of primary for gradient
    // Simple approach: darken by reducing hex
    function hexDarken($hex, $amount = 30) {
        $hex = ltrim($hex, '#');
        if(strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        $r = max(0, hexdec(substr($hex,0,2)) - $amount);
        $g = max(0, hexdec(substr($hex,2,2)) - $amount);
        $b = max(0, hexdec(substr($hex,4,2)) - $amount);
        return '#'.sprintf('%02x%02x%02x', $r, $g, $b);
    }
    $primaryDark = hexDarken($primaryColor, 40);

    // Build Google Fonts URL
    $hf = urlencode($headingFont);
    $bf = urlencode($bodyFont);
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $hf }}:wght@300;400;500;600;700;800&family={{ $bf }}:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:     {{ $primaryColor }};
            --primary-dark: {{ $primaryDark }};
            --secondary:   {{ $secondaryColor }};
            --accent:      {{ $accentColor }};
            --bg:          {{ $bgColor }};
            --text:        {{ $textColor }};
            --font-heading: '{{ $headingFont }}', sans-serif;
            --font-body:    '{{ $bodyFont }}', sans-serif;
            --light-bg:    #f8f9fa;
            --dark-bg:     #1a1a2e;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
            --card-hover:  0 8px 30px rgba(0,0,0,0.15);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            color: var(--text);
            background-color: var(--bg);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
        }

        a { text-decoration: none; transition: var(--transition); }

        /* ── TOP BAR ─────────────────────────────── */
        .topbar {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 8px 0;
            font-size: 0.85rem;
        }
        .topbar a { color: var(--text); margin-right: 20px; }
        .topbar a:hover { color: var(--primary); }
        .topbar a i { color: var(--primary); margin-right: 5px; }
        .topbar .social a { color: var(--primary); margin-left: 14px; margin-right: 0; font-size: 0.95rem; }
        .topbar .social a:hover { color: var(--secondary); }

        /* ── NAVBAR ──────────────────────────────── */
        .site-navbar {
            background-color: var(--primary);
            padding: 14px 0;
            transition: var(--transition);
        }
        .site-navbar.scrolled {
            padding: 8px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
        }
        .navbar-brand-text {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff !important;
            letter-spacing: -0.5px;
        }
        .navbar-brand img { height: 48px; object-fit: contain; }
        .site-navbar .nav-link {
            color: rgba(255,255,255,0.88) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 10px 16px !important;
            border-radius: 6px;
            transition: var(--transition);
        }
        .site-navbar .nav-link:hover,
        .site-navbar .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.12); }
        .site-navbar .nav-cta {
            background-color: var(--secondary);
            color: #fff !important;
            border-radius: 6px;
            padding: 10px 24px !important;
            font-weight: 600;
        }
        .site-navbar .nav-cta:hover { background-color: #fff; color: var(--primary) !important; }
        .navbar-toggler { border-color: rgba(255,255,255,0.4); }
        .navbar-toggler-icon { filter: invert(1); }

        /* ── BUTTONS ─────────────────────────────── */
        .btn-brand-primary {
            background-color: var(--primary);
            color: #fff;
            border: 2px solid var(--primary);
            padding: 12px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-block;
            transition: var(--transition);
        }
        .btn-brand-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .btn-brand-outline {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.7);
            padding: 12px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-block;
            transition: var(--transition);
        }
        .btn-brand-outline:hover {
            background: #fff;
            color: var(--primary);
            border-color: #fff;
            transform: translateY(-2px);
        }
        .btn-brand-secondary {
            background-color: var(--secondary);
            color: #fff;
            border: 2px solid var(--secondary);
            padding: 12px 32px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: var(--transition);
        }
        .btn-brand-secondary:hover {
            background-color: transparent;
            color: var(--secondary);
            transform: translateY(-2px);
        }

        /* ── SECTION LABEL ───────────────────────── */
        .section-label {
            display: inline-block;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--secondary);
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text);
            margin-bottom: 16px;
        }
        .section-subtitle {
            font-size: 1.05rem;
            color: #6c757d;
            max-width: 600px;
            line-height: 1.7;
        }
        .section-divider {
            width: 60px;
            height: 4px;
            background: var(--secondary);
            border-radius: 2px;
            margin-bottom: 20px;
        }

        /* ── HERO SECTION ────────────────────────── */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 88vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 80px 0;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
            top: -150px; right: -150px;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            bottom: -100px; left: -100px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 24px;
        }
        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
        }
        .hero-title span { color: var(--secondary); }
        .hero-subtitle {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 520px;
        }
        .hero-visual {
            position: relative;
            z-index: 2;
        }
        .hero-visual-box {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
        }
        .hero-visual-box .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 6px;
        }
        .hero-image-wrap {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, var(--primary), var(--accent));
            min-height: 380px;
        }
        .hero-image-wrap img { width: 100%; height: 380px; object-fit: cover; display: block; opacity: 0; transition: opacity 0.6s ease; }
        .hero-image-wrap img.img-loaded { opacity: 1; }

        /* ── SECTION IMAGE EDIT OVERLAY (preview mode only) ── */
        .section-img-wrap { position: relative; }
        .section-img-edit-overlay {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0); display: flex; align-items: center; justify-content: center;
            gap: 10px; opacity: 0; transition: opacity 0.25s, background 0.25s;
            border-radius: inherit; z-index: 10;
        }
        .section-img-wrap:hover .section-img-edit-overlay { opacity: 1; background: rgba(0,0,0,0.55); }
        .img-edit-btn {
            background: #fff; border: none; border-radius: 8px; padding: 9px 16px;
            font-size: 0.82rem; font-weight: 600; cursor: pointer; display: flex;
            align-items: center; gap: 6px; color: #111; box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: transform 0.15s;
        }
        .img-edit-btn:hover { transform: scale(1.05); }
        .img-edit-btn.ai-btn { background: var(--primary); color: #fff; }
        .img-generating { display:flex; align-items:center; gap:8px; color:#fff; font-weight:600; font-size:0.9rem; }
        .img-spinner { width:20px; height:20px; border:3px solid rgba(255,255,255,0.4); border-top-color:#fff; border-radius:50%; animation: spin 0.8s linear infinite; display:inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── STATS BAR ───────────────────────────── */
        .stats-bar {
            background-color: #fff;
            padding: 36px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .stat-item { text-align: center; padding: 10px 20px; }
        .stat-item .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            font-family: var(--font-heading);
            line-height: 1;
        }
        .stat-item .stat-label {
            font-size: 0.88rem;
            color: #6c757d;
            margin-top: 4px;
            font-weight: 500;
        }
        .stat-divider {
            width: 1px;
            height: 50px;
            background: #e9ecef;
            margin: auto;
        }

        /* ── SERVICES STRIP ──────────────────────── */
        .services-strip {
            background: var(--light-bg);
            padding: 28px 0;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }
        .service-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: #fff;
            border-radius: 50px;
            box-shadow: var(--card-shadow);
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
            white-space: nowrap;
            transition: var(--transition);
        }
        .service-pill:hover { box-shadow: var(--card-hover); transform: translateY(-2px); }
        .service-pill i { color: var(--primary); font-size: 1.1rem; }

        /* ── SECTION WRAPPER ─────────────────────── */
        .section-pad { padding: 90px 0; }
        .section-pad-sm { padding: 60px 0; }

        /* ── ABOUT SECTION ───────────────────────── */
        .about-section { background: #fff; }
        .about-img-wrap { position: relative; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: var(--border-radius); min-height: 360px; }
        .about-img-wrap img {
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
            display: block;
            opacity: 0; transition: opacity 0.6s ease;
        }
        .about-img-wrap img.img-loaded { opacity: 1; }
        .about-stats-badge {
            position: absolute;
            bottom: -20px;
            right: -20px;
            background: var(--primary);
            color: #fff;
            border-radius: var(--border-radius);
            padding: 20px 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .about-stats-badge .badge-number {
            font-size: 2rem;
            font-weight: 800;
            display: block;
            line-height: 1;
            font-family: var(--font-heading);
        }
        .about-stats-badge .badge-label { font-size: 0.78rem; opacity: 0.9; margin-top: 4px; }
        .about-feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }
        .about-feature-list li i {
            color: var(--secondary);
            font-size: 1rem;
            margin-top: 3px;
            flex-shrink: 0;
        }

        /* ── SERVICES GRID ───────────────────────── */
        .services-section { background: var(--light-bg); }
        .service-card {
            background: #fff;
            border-radius: var(--border-radius);
            padding: 32px 28px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
            border-bottom: 4px solid transparent;
        }
        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-hover);
            border-bottom-color: var(--primary);
        }
        .service-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .service-icon i { color: #fff; font-size: 1.5rem; }
        .service-card h4 { font-size: 1.1rem; margin-bottom: 10px; color: var(--text); }
        .service-card p { font-size: 0.9rem; color: #6c757d; line-height: 1.6; margin-bottom: 0; }

        /* ── WHY CHOOSE US ───────────────────────── */
        .why-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            position: relative;
            overflow: hidden;
        }
        .why-section::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 50%;
            top: -200px; right: -100px;
        }
        .why-card {
            text-align: center;
            color: #fff;
            padding: 20px;
        }
        .why-card .icon-wrap {
            width: 70px; height: 70px;
            background: rgba(255,255,255,0.12);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.6rem;
        }
        .why-card h5 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }
        .why-card p { font-size: 0.87rem; opacity: 0.82; line-height: 1.6; }

        /* ── PRODUCTS SECTION ────────────────────── */
        .products-section { background: #fff; }
        .product-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
            background: #fff;
        }
        .product-card:hover { transform: translateY(-6px); box-shadow: var(--card-hover); }
        .product-card .product-img {
            width: 100%; height: 200px;
            object-fit: cover; display: block;
        }
        .product-card .product-img-placeholder {
            width: 100%; height: 200px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
        }
        .product-card .product-img-placeholder i { color: rgba(255,255,255,0.5); font-size: 3rem; }
        .product-card .product-body { padding: 20px; }
        .product-card .product-name { font-weight: 700; font-size: 1rem; margin-bottom: 6px; color: var(--text); }
        .product-price { font-size: 1.3rem; font-weight: 800; color: var(--primary); }
        .add-to-cart-btn { width: 100%; margin-top: 12px; background: var(--primary); color: #fff;
            border: none; border-radius: 8px; padding: 10px 14px; font-weight: 600; font-size: 0.88rem;
            cursor: pointer; transition: background 0.15s; display: flex; align-items: center;
            justify-content: center; gap: 6px; }
        .add-to-cart-btn:hover { filter: brightness(1.1); }
        /* ── CART FAB ─────────────────────────────── */
        .cart-fab { position: fixed; bottom: 28px; right: 28px; z-index: 900;
            width: 60px; height: 60px; border-radius: 50%; background: var(--primary);
            color: #fff; border: none; cursor: pointer;
            box-shadow: 0 4px 24px rgba(0,0,0,0.28);
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s; }
        .cart-fab:hover { transform: scale(1.08); box-shadow: 0 6px 32px rgba(0,0,0,0.35); }
        .cart-fab-badge { position: absolute; top: -3px; right: -3px; background: #f43f5e;
            color: #fff; border-radius: 50%; width: 22px; height: 22px; font-size: 0.7rem;
            font-weight: 700; display: none; align-items: center; justify-content: center; border: 2px solid #fff; }
        .cart-fab-badge.show { display: flex; }
        /* ── CART OVERLAY & DRAWER ───────────────── */
        .cart-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1040;
            opacity: 0; pointer-events: none; transition: opacity 0.3s; }
        .cart-overlay.open { opacity: 1; pointer-events: all; }
        .cart-drawer { position: fixed; top: 0; right: 0; width: min(420px, 100vw); height: 100vh;
            background: #fff; z-index: 1050; transform: translateX(110%);
            transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
            display: flex; flex-direction: column; box-shadow: -4px 0 40px rgba(0,0,0,0.18); }
        .cart-drawer.open { transform: translateX(0); }
        .cart-drawer-hdr { padding: 18px 22px; border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center; justify-content: space-between; }
        .cart-drawer-hdr h5 { margin: 0; font-weight: 700; font-size: 1.05rem; }
        .cart-close-btn { background: none; border: none; font-size: 1.6rem; cursor: pointer;
            color: #9ca3af; line-height: 1; padding: 0 4px; transition: color 0.15s; }
        .cart-close-btn:hover { color: #374151; }
        .cart-items-list { flex: 1; overflow-y: auto; padding: 12px 20px; }
        .cart-empty-msg { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .cart-empty-msg i { font-size: 3rem; opacity: 0.4; display: block; margin-bottom: 10px; }
        .cart-item { display: flex; align-items: flex-start; gap: 12px;
            padding: 13px 0; border-bottom: 1px solid #f3f4f6; }
        .cart-item-thumb { width: 54px; height: 54px; border-radius: 8px; object-fit: cover;
            background: #f3f4f6; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .cart-item-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-name { font-weight: 600; font-size: 0.9rem; color: #1e293b;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cart-item-sub { font-size: 0.82rem; color: #6c757d; margin-top: 2px; }
        .cart-qty-ctrl { display: flex; align-items: center; gap: 8px; margin-top: 7px; }
        .cart-qty-btn { width: 26px; height: 26px; border-radius: 50%;
            border: 1.5px solid #d1d5db; background: #fff; cursor: pointer;
            font-size: 1rem; line-height: 1; display: flex; align-items: center;
            justify-content: center; color: #374151; transition: background 0.15s; }
        .cart-qty-btn:hover { background: #f0f0f0; }
        .cart-qty-n { font-weight: 700; font-size: 0.9rem; min-width: 20px; text-align: center; }
        .cart-item-del { background: none; border: none; color: #f87171; font-size: 1rem;
            cursor: pointer; padding: 4px; flex-shrink: 0; margin-top: 2px; }
        .cart-footer { padding: 18px 22px; border-top: 1px solid #e9ecef; }
        .cart-total-row { display: flex; justify-content: space-between;
            font-weight: 700; font-size: 1.05rem; margin-bottom: 14px; }
        /* ── ORDER MODAL ─────────────────────────── */
        .order-modal-wrap { position: fixed; inset: 0; background: rgba(0,0,0,0.55);
            z-index: 1060; display: none; align-items: center; justify-content: center; padding: 16px; }
        .order-modal-wrap.open { display: flex; }
        .order-modal { background: #fff; border-radius: 16px; width: min(490px, 100%);
            max-height: 92vh; overflow-y: auto; padding: 28px 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.28); }
        .order-modal h4 { font-weight: 700; margin-bottom: 4px; font-size: 1.2rem; }
        .order-note-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;
            padding: 10px 14px; font-size: 0.82rem; color: #92400e; margin: 14px 0 18px; }
        .order-summary-box { background: #f8fafc; border-radius: 8px;
            padding: 10px 14px; margin-bottom: 18px; }
        .order-summary-row { display: flex; justify-content: space-between;
            font-size: 0.87rem; padding: 3px 0; border-bottom: 1px solid #e9ecef; }
        .order-summary-row:last-child { border: none; font-weight: 700; padding-top: 7px; }
        .order-form-group { margin-bottom: 13px; }
        .order-form-group label { font-size: 0.84rem; font-weight: 600;
            color: #374151; margin-bottom: 4px; display: block; }
        .order-form-group input, .order-form-group textarea {
            width: 100%; padding: 9px 13px; border: 1.5px solid #d1d5db;
            border-radius: 8px; font-size: 0.91rem; transition: border-color 0.15s;
            outline: none; font-family: inherit; box-sizing: border-box; }
        .order-form-group input:focus, .order-form-group textarea:focus { border-color: var(--primary); }
        .order-submit-btn { width: 100%; padding: 12px; background: var(--primary); color: #fff;
            border: none; border-radius: 10px; font-weight: 700; font-size: 0.95rem;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px;
            transition: filter 0.15s; }
        .order-submit-btn:hover:not(:disabled) { filter: brightness(1.1); }
        .order-submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }
        .order-cancel-btn { width: 100%; margin-top: 10px; padding: 11px; background: none;
            border: 1.5px solid #d1d5db; border-radius: 10px; font-weight: 600;
            font-size: 0.9rem; color: #6c757d; cursor: pointer; }
        .order-cancel-btn:hover { background: #f9fafb; }
        .order-err { color: #dc3545; font-size: 0.84rem; margin-bottom: 10px; display: none; }
        /* ── TESTIMONIALS ────────────────────────── */
        .testimonials-section { background: var(--light-bg); }
        .testimonial-card {
            background: #fff;
            border-radius: var(--border-radius);
            padding: 28px;
            box-shadow: var(--card-shadow);
            height: 100%;
            position: relative;
        }
        .testimonial-card::before {
            content: '\201C';
            font-size: 5rem;
            color: var(--primary);
            opacity: 0.12;
            position: absolute;
            top: 10px; left: 20px;
            line-height: 1;
            font-family: serif;
        }
        .testimonial-card .stars { color: var(--secondary); margin-bottom: 12px; }
        .testimonial-card .quote { font-size: 0.95rem; color: #495057; line-height: 1.7; margin-bottom: 16px; }
        .testimonial-card .author { font-weight: 700; font-size: 0.9rem; color: var(--text); }
        .testimonial-card .role { font-size: 0.82rem; color: #6c757d; }

        /* ── CTA SECTION ─────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            padding: 80px 0;
            text-align: center;
        }
        .cta-section h2 { color: #fff; font-size: 2.4rem; margin-bottom: 16px; }
        .cta-section p { color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 32px; max-width: 600px; margin-left: auto; margin-right: auto; }

        /* ── CONTACT SECTION ─────────────────────── */
        .contact-section { background: #fff; }
        .contact-form-card {
            background: var(--light-bg);
            border-radius: var(--border-radius);
            padding: 40px;
        }
        .contact-form-card .form-control, .contact-form-card .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.95rem;
            background: #fff;
            transition: var(--transition);
        }
        .contact-form-card .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,0,0,0.06);
        }
        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 28px;
        }
        .contact-info-item .icon-box {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .contact-info-item .icon-box i { color: #fff; font-size: 1rem; }
        .contact-info-item .info-text strong { display: block; font-weight: 700; margin-bottom: 2px; color: var(--text); }
        .contact-info-item .info-text span { font-size: 0.9rem; color: #6c757d; }

        /* ── FOOTER ──────────────────────────────── */
        .site-footer { background: var(--dark-bg); color: rgba(255,255,255,0.75); padding: 70px 0 0; }
        .site-footer .footer-brand { font-size: 1.5rem; font-weight: 800; color: #fff; font-family: var(--font-heading); margin-bottom: 12px; }
        .site-footer .footer-desc { font-size: 0.9rem; line-height: 1.7; max-width: 280px; }
        .site-footer h5 { color: #fff; font-weight: 700; font-size: 1rem; margin-bottom: 20px; }
        .site-footer ul { list-style: none; padding: 0; margin: 0; }
        .site-footer ul li { margin-bottom: 10px; }
        .site-footer ul li a { color: rgba(255,255,255,0.65); font-size: 0.9rem; transition: var(--transition); }
        .site-footer ul li a:hover { color: var(--secondary); padding-left: 4px; }
        .site-footer .footer-contact li { font-size: 0.9rem; margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px; }
        .site-footer .footer-contact li i { color: var(--secondary); margin-top: 2px; flex-shrink: 0; }
        .footer-social a {
            width: 38px; height: 38px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.7);
            margin-right: 6px; margin-bottom: 6px;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        .footer-social a:hover { background: var(--secondary); border-color: var(--secondary); color: #fff; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: 50px;
            padding: 20px 0;
            font-size: 0.85rem;
        }
        .footer-bottom a { color: var(--secondary); }

        /* ── PREVIEW BAR / EDITOR TOOLBAR ───────── */
        .preview-bar {
            position: fixed; top: 0; left: 0; right: 0;
            background: #f59e0b; color: #fff;
            text-align: center; padding: 8px 16px;
            font-weight: 700; font-size: 0.85rem;
            z-index: 100000; letter-spacing: 1px;
        }
        /* ── EDITOR TOOLBAR ──────────────────────── */
        .editor-toolbar {
            position: fixed; top: 0; left: 0; right: 0; height: 52px;
            background: #1e293b; display: flex; align-items: center; gap: 7px;
            padding: 0 14px; z-index: 100001; font-family: system-ui,-apple-system,sans-serif;
            box-shadow: 0 2px 16px rgba(0,0,0,0.35); flex-wrap: nowrap;
        }
        .et-brand { font-weight: 800; font-size: 0.88rem; color: #f59e0b; white-space: nowrap; flex-shrink: 0; }
        .et-sep { width: 1px; height: 26px; background: rgba(255,255,255,0.12); margin: 0 3px; flex-shrink: 0; }
        .et-label { font-size: 0.7rem; color: rgba(255,255,255,0.5); white-space: nowrap; flex-shrink: 0; }
        .et-page-select { background: rgba(255,255,255,0.09); color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; padding: 5px 8px; font-size: 0.78rem; cursor: pointer; max-width: 160px; flex-shrink: 1; }
        .et-page-select option { background: #1e293b; color: #fff; }
        .et-btn { background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; padding: 5px 11px; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: background 0.15s; white-space: nowrap; flex-shrink: 0; }
        .et-btn:hover { background: rgba(255,255,255,0.2); }
        .et-btn-green { background: #16a34a !important; border-color: #16a34a !important; }
        .et-btn-green:hover { background: #15803d !important; }
        .et-spacer { flex: 1; min-width: 4px; }
        .et-status { font-size: 0.72rem; color: rgba(255,255,255,0.4); white-space: nowrap; flex-shrink: 0; transition: color 0.3s; }
        .et-status.saved { color: #4ade80; }
        .et-status.saving { color: #fbbf24; }
        .et-exit { color: rgba(255,255,255,0.55); text-decoration: none; font-size: 0.74rem; padding: 5px 10px; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; transition: all 0.15s; white-space: nowrap; flex-shrink: 0; }
        .et-exit:hover { color: #fff; border-color: rgba(255,255,255,0.4); }
        /* ── SECTION EDIT WRAPPER ────────────────── */
        .sew { position: relative; }
        .seb { position: absolute; top: 14px; left: 14px; z-index: 90; background: rgba(30,41,59,0.82); color: #fff; border: 1px solid rgba(255,255,255,0.22); border-radius: 6px; padding: 5px 13px; font-size: 0.74rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px; opacity: 0; transition: opacity 0.2s; backdrop-filter: blur(4px); }
        .sew:hover .seb { opacity: 1; }
        .seb:hover { background: rgba(30,41,59,1); }
        /* ── EDITOR PANEL (right drawer) ─────────── */
        .editor-panel { position: fixed; top: 52px; right: -380px; bottom: 0; width: 360px; background: #fff; box-shadow: -4px 0 28px rgba(0,0,0,0.18); z-index: 100000; transition: right 0.28s ease; display: flex; flex-direction: column; font-family: system-ui,-apple-system,sans-serif; }
        .editor-panel.open { right: 0; }
        .ep-hdr { padding: 13px 18px; background: #1e293b; color: #fff; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
        .ep-hdr h6 { margin: 0; font-size: 0.88rem; font-weight: 700; color: #fff; }
        .ep-hdr button { background: none; border: none; color: rgba(255,255,255,0.6); font-size: 1.3rem; cursor: pointer; line-height: 1; padding: 0 4px; }
        .ep-hdr button:hover { color: #fff; }
        .ep-tabs { display: flex; gap: 4px; padding: 10px 14px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
        .ep-tab { flex: 1; text-align: center; padding: 6px 2px; border: 1.5px solid #e5e7eb; border-radius: 6px; font-size: 0.7rem; font-weight: 600; cursor: pointer; background: #f8fafc; color: #64748b; transition: all 0.15s; white-space: nowrap; }
        .ep-tab.active { background: #1e293b; color: #fff; border-color: #1e293b; }
        .ep-body { flex: 1; overflow-y: auto; padding: 16px; }
        .ep-lbl { font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block; }
        .ep-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 7px; padding: 7px 10px; font-size: 0.83rem; font-family: inherit; color: #1e293b; transition: border-color 0.15s; background: #fff; box-sizing: border-box; }
        .ep-input:focus { border-color: #64748b; outline: none; }
        textarea.ep-input { resize: vertical; min-height: 68px; }
        .ep-field { margin-bottom: 11px; }
        .ep-btn { width: 100%; padding: 9px; border: none; border-radius: 8px; font-size: 0.84rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px; transition: all 0.15s; margin-bottom: 8px; }
        .ep-btn-dark { background: #1e293b; color: #fff; }
        .ep-btn-dark:hover { background: #0f172a; }
        .ep-btn-ai { background: linear-gradient(135deg,#ff511a,#7b2e2e); color: #fff; }
        .ep-btn-ai:hover { opacity: 0.9; }
        .ep-btn-green { background: #16a34a; color: #fff; }
        .ep-btn-green:hover { background: #15803d; }
        .ep-msg { border-radius: 6px; padding: 7px 10px; font-size: 0.78rem; margin-bottom: 10px; display: none; }
        .ep-msg-success { background: #dcfce7; color: #166534; }
        .ep-msg-error { background: #fee2e2; color: #991b1b; }
        .ep-msg-info { background: #dbeafe; color: #1e40af; }
        .ep-divider { border: none; border-top: 1px solid #e5e7eb; margin: 13px 0; }
        .ep-clr-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .ep-clr-swatch { width: 34px; height: 34px; border-radius: 50%; border: 2.5px solid #e5e7eb; cursor: pointer; overflow: hidden; padding: 0; flex-shrink: 0; }
        .ep-clr-swatch input[type=color] { width: 46px; height: 46px; border: none; cursor: pointer; margin: -6px; padding: 0; }
        .ep-clr-lbl { font-size: 0.8rem; color: #475569; }
        .ep-font-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 12px; }
        .ep-font-opt { border: 1.5px solid #e5e7eb; border-radius: 7px; padding: 8px 6px; text-align: center; cursor: pointer; transition: all 0.15s; background: #fff; }
        .ep-font-opt:hover { border-color: #94a3b8; }
        .ep-font-opt.selected { border-color: #1e293b; background: #f0f4ff; }
        .ep-font-opt .fname { font-size: 0.8rem; font-weight: 700; color: #1e293b; display: block; }
        .ep-font-opt .fpair { font-size: 0.64rem; color: #94a3b8; margin-top: 2px; display: block; }
        .ep-logo-drop { border: 2px dashed #e2e8f0; border-radius: 10px; padding: 22px 16px; text-align: center; cursor: pointer; transition: border-color 0.2s; background: #fafafa; margin-bottom: 12px; display: block; }
        .ep-logo-drop:hover { border-color: #94a3b8; }
        .ep-logo-preview { max-height: 64px; max-width: 200px; object-fit: contain; margin-bottom: 10px; }
        .ep-note { font-size: 0.73rem; color: #64748b; line-height: 1.45; margin-bottom: 12px; }
        .ep-spinner { display: inline-block; width: 15px; height: 15px; border: 2.5px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; }
        @keyframes ep-spin { to { transform: rotate(360deg); } }
        .ep-spinner { animation: ep-spin 0.8s linear infinite; }

        /* ── CUSTOM MODALS ───────────────────────── */
        #seToast { position: fixed; top: 64px; left: 50%; transform: translateX(-50%) translateY(-80px); z-index: 99999; min-width: 280px; max-width: 420px; background: #1e293b; color: #fff; border-radius: 12px; padding: 14px 20px 14px 16px; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.28); transition: transform 0.3s cubic-bezier(.4,0,.2,1), opacity 0.3s; opacity: 0; pointer-events: none; }
        #seToast.se-toast-show { transform: translateX(-50%) translateY(0); opacity: 1; pointer-events: auto; }
        #seToast .se-toast-icon { font-size: 1.3rem; flex-shrink: 0; margin-top: 1px; }
        #seToast .se-toast-body { flex: 1; }
        #seToast .se-toast-title { font-weight: 700; font-size: 0.92rem; margin-bottom: 2px; }
        #seToast .se-toast-msg { font-size: 0.82rem; color: #cbd5e1; line-height: 1.4; }
        #seToast.se-success { border-left: 4px solid #22c55e; }
        #seToast.se-error { border-left: 4px solid #ef4444; }
        #seToast.se-info { border-left: 4px solid #3b82f6; }
        #seToast .se-toast-close { background: none; border: none; color: #94a3b8; font-size: 1.1rem; cursor: pointer; padding: 0; line-height: 1; flex-shrink: 0; }
        #seToast .se-toast-close:hover { color: #fff; }
        #seConfirmBackdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 99998; display: none; align-items: center; justify-content: center; }
        #seConfirmBackdrop.se-open { display: flex; }
        #seConfirmBox { background: #fff; border-radius: 16px; padding: 28px 28px 24px; max-width: 380px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.22); text-align: center; animation: sePopIn 0.18s ease; }
        @keyframes sePopIn { from { transform: scale(0.88); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        #seConfirmBox .se-ci { font-size: 2.4rem; margin-bottom: 10px; }
        #seConfirmBox .se-ct { font-weight: 800; font-size: 1.05rem; color: #1e293b; margin-bottom: 6px; }
        #seConfirmBox .se-cm { font-size: 0.88rem; color: #64748b; line-height: 1.5; margin-bottom: 22px; }
        #seConfirmBox .se-cbtns { display: flex; gap: 10px; justify-content: center; }
        #seConfirmBox .se-cbtn { padding: 9px 22px; border-radius: 8px; font-size: 0.88rem; font-weight: 600; border: none; cursor: pointer; transition: opacity 0.15s; }
        #seConfirmBox .se-cbtn:hover { opacity: 0.85; }
        #seConfirmBox .se-cbtn-cancel { background: #f1f5f9; color: #475569; }
        #seConfirmBox .se-cbtn-ok { background: #1e293b; color: #fff; }
        #seConfirmBox .se-cbtn-ok.se-danger { background: #ef4444; }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 991.98px) {
            .hero-section { min-height: auto; padding: 100px 0 60px; }
            .hero-title { font-size: 2.2rem; }
            .section-title { font-size: 1.9rem; }
            .about-stats-badge { position: static; margin-top: 20px; display: inline-block; }
            .section-pad { padding: 60px 0; }
        }
        @media (max-width: 767.98px) {
            body { padding-top: {{ $isPreview ? '52px' : '0' }}; }
            .topbar { display: none; }
            .stat-divider { display: none; }
            .hero-visual { margin-top: 40px; }
        }
    </style>
</head>
<body{{ $isPreview ? ' style="padding-top:52px"' : '' }}>

    @if($isPreview)
    {{-- ── RICH EDITOR TOOLBAR ───────────────────────────────────── --}}
    <div class="editor-toolbar" id="editorToolbar">
        <span class="et-brand">✏️&nbsp;ShopyEditor</span>
        <div class="et-sep"></div>
        <span class="et-label">Page:</span>
        <select class="et-page-select" id="pageNavSelect" onchange="_gotoPage(this.value)">
            @foreach($menuPages as $menuPage)
            <option value="{{ route('website.builder.preview.page', $menuPage->id) }}" {{ $page->id === $menuPage->id ? 'selected' : '' }}>
                {{ $menuPage->title }}
            </option>
            @endforeach
        </select>
        <div class="et-sep"></div>
        <button class="et-btn" onclick="_openPanel('design')">🎨 Design</button>
        <button class="et-btn" onclick="_openPanel('logo')">🖼 Logo</button>
        <button class="et-btn et-btn-green" onclick="_publishSite()">🚀 Publish</button>
        <div class="et-spacer"></div>
        <span class="et-status" id="etStatus"></span>
        <div class="et-sep"></div>
        <a href="{{ route('dashboard') }}" class="et-exit">← Exit Editor</a>
    </div>

    {{-- ── EDITOR PANEL (right drawer) ──────────────────────────── --}}
    <div class="editor-panel" id="editorPanel">
        <div class="ep-hdr">
            <h6 id="epTitle">Editor</h6>
            <button onclick="_closePanel()" title="Close">×</button>
        </div>
        <div class="ep-tabs">
            <button class="ep-tab active" id="tab_content" onclick="_switchTab('content')">✏️ Content</button>
            <button class="ep-tab" id="tab_design" onclick="_switchTab('design')">🎨 Design</button>
            <button class="ep-tab" id="tab_logo" onclick="_switchTab('logo')">🖼️ Logo</button>
        </div>
        <div class="ep-body" id="epBody">
            <p style="color:#94a3b8;font-size:0.82rem;text-align:center;margin-top:28px;padding:0 10px;">
                Hover over any section and click <strong style="color:#1e293b;">Edit</strong> to start editing.
            </p>
        </div>
    </div>

    {{-- ── TOAST NOTIFICATION ────────────────────────────────────── --}}
    <div id="seToast">
        <span class="se-toast-icon" id="seToastIcon"></span>
        <div class="se-toast-body">
            <div class="se-toast-title" id="seToastTitle"></div>
            <div class="se-toast-msg" id="seToastMsg"></div>
        </div>
        <button class="se-toast-close" onclick="_hideToast()">×</button>
    </div>

    {{-- ── CONFIRM DIALOG ───────────────────────────────────────── --}}
    <div id="seConfirmBackdrop">
        <div id="seConfirmBox">
            <div class="se-ci" id="seConfirmIcon"></div>
            <div class="se-ct" id="seConfirmTitle"></div>
            <div class="se-cm" id="seConfirmMsg"></div>
            <div class="se-cbtns">
                <button class="se-cbtn se-cbtn-cancel" id="seConfirmCancel">Cancel</button>
                <button class="se-cbtn se-cbtn-ok" id="seConfirmOk">Confirm</button>
            </div>
        </div>
    </div>
    @endif

    <!-- ── TOP BAR ─────────────────────────────────── -->
    <div class="topbar d-none d-md-block">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if($website->settings['contact_phone'] ?? $website->business->phone)
                    <a href="tel:{{ $website->settings['contact_phone'] ?? $website->business->phone }}">
                        <i class="fas fa-phone-alt"></i>{{ $website->settings['contact_phone'] ?? $website->business->phone }}
                    </a>
                    @endif
                    @if($website->settings['contact_email'] ?? $website->business->email)
                    <a href="mailto:{{ $website->settings['contact_email'] ?? $website->business->email }}">
                        <i class="fas fa-envelope"></i>{{ $website->settings['contact_email'] ?? $website->business->email }}
                    </a>
                    @endif
                    @if($website->business->address)
                    <span><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-right:4px;"></i>{{ $website->business->city ?? $website->business->address }}</span>
                    @endif
                </div>
                <div class="social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- ── NAVBAR ──────────────────────────────────── -->
    <nav class="site-navbar sticky-top" id="siteNav">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <!-- Brand -->
                <a href="{{ $isPreview ? route('website.builder.preview') : $website->url }}" class="d-flex align-items-center gap-3 text-decoration-none">
                    @if($website->logo_path || $website->business->logo_path)
                    <img src="{{ asset('storage/' . ($website->logo_path ?? $website->business->logo_path)) }}"
                         id="siteLogoImg"
                         alt="{{ $website->business->name }}" class="navbar-brand-img" style="height:46px;object-fit:contain;">
                    @endif
                    <span class="navbar-brand-text">{{ $website->business->name }}</span>
                </a>

                <!-- Desktop Nav -->
                <div class="d-none d-lg-flex align-items-center gap-1">
                    @foreach($menuPages as $menuPage)
                    @php
                        // Strip leading filler words then keep max 2 words
                        $navLabel = preg_replace('/^(our|the|my)\s+/i', '', $menuPage->title);
                        $navWords = explode(' ', trim($navLabel));
                        $navLabel = implode(' ', array_slice($navWords, 0, 2));
                    @endphp
                    <a href="{{ $isPreview ? route('website.builder.preview.page', $menuPage->id) : $menuPage->url }}"
                       class="nav-link {{ $page->id === $menuPage->id ? 'active' : '' }}">
                        {{ $navLabel }}
                    </a>
                    @endforeach
                    @if($website->settings['contact_email'] ?? $website->business->email)
                    <a href="#contact" class="nav-link nav-cta ms-2">Contact</a>
                    @endif
                </div>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler d-lg-none" type="button" id="mobileToggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" style="display:none;" class="py-3 border-top border-white border-opacity-25 mt-2">
                @foreach($menuPages as $menuPage)
                @php
                    $mNavLabel = preg_replace('/^(our|the|my)\s+/i', '', $menuPage->title);
                    $mNavWords = explode(' ', trim($mNavLabel));
                    $mNavLabel = implode(' ', array_slice($mNavWords, 0, 2));
                @endphp
                <a href="{{ $isPreview ? route('website.builder.preview.page', $menuPage->id) : $menuPage->url }}" class="nav-link d-block py-2">{{ $mNavLabel }}</a>
                @endforeach
                <a href="#contact" class="nav-link nav-cta d-inline-block mt-2">Contact</a>
            </div>
        </div>
    </nav>

    <!-- ── MAIN SECTIONS ────────────────────────────── -->
    <main>
    @php $sections = $page->visibleSections; @endphp

    @foreach($sections as $section)
    @php
        $content  = $section->getContentWithDefaults();
        $settings = $section->getSettingsWithDefaults();
        // Sanitize any bare internal paths AI might generate (e.g. "/contact" → "#contact")
        $safeCtaLink = function($link, $default = '#contact') {
            if (!$link) return $default;
            // Already an anchor or external URL — use as-is
            if (str_starts_with($link, '#') || str_starts_with($link, 'http')) return $link;
            // Bare relative path like "/contact" → "#contact"
            if (str_starts_with($link, '/')) return '#' . ltrim(basename($link), '/');
            return $default;
        };
    @endphp

    @if($isPreview)
    <div class="sew" id="sew-{{ $section->id }}">
        <button class="seb" onclick="_openContentPanel({{ $section->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
        </button>
    @endif

    {{-- ════════ HERO ════════ --}}
    @if($section->type === 'hero')
    <section class="hero-section" id="home">
        <div class="container position-relative" style="z-index:2;">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-badge">
                        <i class="fas fa-star" style="color:var(--secondary);"></i>
                        {{ $website->settings['site_name'] ?? $website->business->name }}
                    </div>
                    <h1 class="hero-title">
                        {!! str_replace(
                            ['{name}', '{business}'],
                            ['<span>'.$website->business->name.'</span>', '<span>'.$website->business->name.'</span>'],
                            e($content['heading'] ?? $website->business->name)
                        ) !!}
                    </h1>
                    <p class="hero-subtitle">
                        {{ $content['subheading'] ?? ($website->settings['tagline'] ?? $website->business->description) }}
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        @if(isset($content['cta_text']))
                        <a href="{{ $safeCtaLink($content['cta_link'] ?? null, '#contact') }}" class="btn-brand-secondary">
                            <i class="fas fa-arrow-right me-2"></i>{{ $content['cta_text'] }}
                        </a>
                        @endif
                        @if(isset($content['cta_text_2']))
                        <a href="{{ $safeCtaLink($content['cta_link_2'] ?? null, '#about') }}" class="btn-brand-outline">
                            {{ $content['cta_text_2'] }}
                        </a>
                        @else
                        <a href="#about" class="btn-brand-outline">
                            <i class="fas fa-play me-2"></i>Learn More
                        </a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-5 hero-visual">
                    @php $heroImgSrc = isset($content['image']) && $content['image'] ? (str_starts_with($content['image'], 'http') ? $content['image'] : asset('storage/'.$content['image'])) : null; @endphp
                    @if($heroImgSrc)
                    <div class="hero-image-wrap section-img-wrap" id="img-wrap-{{ $section->id }}">
                        <img src="{{ $heroImgSrc }}" alt="{{ $content['heading'] ?? '' }}" loading="eager"
                             id="section-img-{{ $section->id }}" class="img-loaded"
                             onerror="this.onerror=null;this.classList.add('img-loaded');">
                        @if($isPreview)
                        @include('public-website.partials.image-edit-overlay', ['section' => $section, 'imageQuery' => $content['image_query'] ?? null])
                        @endif
                    </div>
                    @else
                    <div class="hero-visual-box section-img-wrap" id="img-wrap-{{ $section->id }}">
                        @if($isPreview)
                        <div style="position:absolute;top:12px;right:12px;z-index:10;">
                            <button class="img-edit-btn ai-btn" onclick="openImageModal({{ $section->id }}, '{{ addslashes($content['image_query'] ?? '') }}')">
                                <i class="fas fa-magic"></i> Add Image
                            </button>
                        </div>
                        @endif
                        <div class="mb-4" style="font-size:4rem;">🚀</div>
                        <h4 style="color:#fff;font-size:1.3rem;margin-bottom:16px;">{{ $website->business->name }}</h4>
                        @php $heroStats = $content['stats'] ?? []; @endphp
                        @if(!empty($heroStats))
                            @foreach(array_slice($heroStats, 0, 3) as $stat)
                            <div class="stat-pill">
                                <i class="fas fa-check-circle" style="color:var(--secondary);"></i>
                                {{ $stat['label'] ?? $stat }}
                            </div>
                            @endforeach
                        @else
                            <div class="stat-pill"><i class="fas fa-check-circle" style="color:var(--secondary);"></i> Professional Service</div>
                            <div class="stat-pill"><i class="fas fa-check-circle" style="color:var(--secondary);"></i> Quality Results</div>
                            <div class="stat-pill"><i class="fas fa-check-circle" style="color:var(--secondary);"></i> Trusted by Clients</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ════════ STATS (from hero content or standalone) ════════ --}}
    @php
        $heroContent = $content;
        $statsItems = $heroContent['stats'] ?? [];
    @endphp
    @if(!empty($statsItems) && is_array($statsItems))
    <div class="stats-bar">
        <div class="container">
            <div class="row justify-content-center g-3">
                @foreach(array_slice($statsItems, 0, 4) as $i => $stat)
                @if($i > 0)<div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>@endif
                <div class="col-auto stat-item">
                    <div class="stat-number">{{ $stat['value'] ?? ($stat['number'] ?? '—') }}</div>
                    <div class="stat-label">{{ $stat['label'] ?? '' }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ════════ ABOUT ════════ --}}
    @elseif($section->type === 'about')
    <section class="about-section section-pad" id="about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-label">About Us</span>
                    <div class="section-divider"></div>
                    <h2 class="section-title">{{ $content['heading'] ?? 'About '.$website->business->name }}</h2>
                    <p style="color:#6c757d;line-height:1.8;font-size:0.97rem;margin-bottom:24px;">
                        {!! nl2br(e($content['text'] ?? $website->business->description ?? '')) !!}
                    </p>
                    @if(isset($content['features']) && is_array($content['features']))
                    <ul class="about-feature-list list-unstyled mb-4">
                        @foreach($content['features'] as $feat)
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>{{ is_array($feat) ? ($feat['text'] ?? $feat['title'] ?? '') : $feat }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    <a href="#contact" class="btn-brand-primary">
                        <i class="fas fa-arrow-right me-2"></i>Work With Us
                    </a>
                </div>
                <div class="col-lg-6">
                    @php $aboutImgSrc = isset($content['image']) && $content['image'] ? (str_starts_with($content['image'], 'http') ? $content['image'] : asset('storage/'.$content['image'])) : null; @endphp
                    <div class="about-img-wrap section-img-wrap" id="img-wrap-{{ $section->id }}">
                        @if($aboutImgSrc)
                        <img src="{{ $aboutImgSrc }}" alt="About {{ $website->business->name }}"
                             id="section-img-{{ $section->id }}" class="img-loaded"
                             onerror="this.onerror=null;this.classList.add('img-loaded');">
                        @if($isPreview)
                        @include('public-website.partials.image-edit-overlay', ['section' => $section, 'imageQuery' => $content['image_query'] ?? null])
                        @endif
                        @else
                        <div style="background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:var(--border-radius);height:380px;display:flex;align-items:center;justify-content:center;position:relative;">
                            @if($isPreview)
                            <div style="position:absolute;top:12px;right:12px;z-index:10;">
                                <button class="img-edit-btn ai-btn" onclick="openImageModal({{ $section->id }}, '{{ addslashes($content['image_query'] ?? '') }}')">
                                    <i class="fas fa-magic"></i> Add Image
                                </button>
                            </div>
                            @endif
                            <div class="text-center text-white p-4">
                                <i class="fas fa-building" style="font-size:4rem;opacity:0.4;margin-bottom:16px;display:block;"></i>
                                <h4 class="text-white">{{ $website->business->name }}</h4>
                                @if($website->business->description)
                                <p class="mb-0" style="opacity:0.8;font-size:0.9rem;">{{ Str::limit($website->business->description, 120) }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if(isset($content['stats']) && is_array($content['stats']))
                        <div class="about-stats-badge">
                            @php $firstStat = $content['stats'][0] ?? null; @endphp
                            @if($firstStat)
                            <span class="badge-number">{{ $firstStat['value'] ?? $firstStat['number'] ?? '★' }}</span>
                            <div class="badge-label">{{ $firstStat['label'] ?? '' }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats row below about --}}
            @if(isset($content['stats']) && count($content['stats']) > 1)
            <div class="row g-4 mt-5 pt-3 border-top">
                @foreach(array_slice($content['stats'], 0, 4) as $stat)
                <div class="col-6 col-md-3 text-center">
                    <div class="stat-number">{{ $stat['value'] ?? $stat['number'] ?? '' }}</div>
                    <div style="color:#6c757d;font-size:0.88rem;margin-top:4px;">{{ $stat['label'] ?? '' }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ════════ FEATURES ════════ --}}
    @elseif($section->type === 'features')
    <section class="services-strip section-pad-sm" id="features">
        <div class="container">
            @if(isset($content['heading']))
            <div class="text-center mb-5">
                <span class="section-label">What We Do</span>
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title">{{ $content['heading'] }}</h2>
                @if(isset($content['subheading']))
                <p class="section-subtitle mx-auto">{{ $content['subheading'] }}</p>
                @endif
            </div>
            @endif
            @if(isset($content['items']) && is_array($content['items']))
            <div class="d-flex flex-wrap justify-content-center gap-3">
                @foreach($content['items'] as $item)
                <div class="service-pill">
                    <i class="{{ $item['icon'] ?? 'fas fa-check-circle' }}"></i>
                    {{ $item['title'] ?? $item['name'] ?? (is_string($item) ? $item : '') }}
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ════════ SERVICES ════════ --}}
    @elseif($section->type === 'services')
    <section class="services-section section-pad" id="services">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Our Services</span>
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title">{{ $content['heading'] ?? 'What We Offer' }}</h2>
                @if(isset($content['subheading']))
                <p class="section-subtitle mx-auto">{{ $content['subheading'] }}</p>
                @endif
            </div>
            @if(isset($content['items']) && is_array($content['items']))
            <div class="row g-4">
                @foreach($content['items'] as $item)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="{{ $item['icon'] ?? 'fas fa-cog' }}"></i>
                        </div>
                        <h4>{{ $item['title'] ?? $item['name'] ?? '' }}</h4>
                        <p>{{ $item['description'] ?? $item['desc'] ?? '' }}</p>
                        @if(isset($item['price']))
                        <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                            <strong style="color:var(--primary);">{{ $item['price'] }}</strong>
                            <a href="#contact" class="btn-brand-primary" style="padding:8px 20px;font-size:0.85rem;">Order Now</a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ════════ STATS ════════ --}}
    @elseif($section->type === 'stats')
    <section class="why-section section-pad" id="stats">
        <div class="container position-relative" style="z-index:2;">
            @if(isset($content['heading']))
            <div class="text-center mb-5">
                <h2 style="color:#fff;font-size:2.2rem;margin-bottom:8px;">{{ $content['heading'] }}</h2>
                @if(isset($content['subheading']))
                <p style="color:rgba(255,255,255,0.8);">{{ $content['subheading'] }}</p>
                @endif
            </div>
            @endif
            @if(isset($content['items']) && is_array($content['items']))
            <div class="row g-4 justify-content-center">
                @foreach($content['items'] as $item)
                <div class="col-6 col-md-3">
                    <div class="why-card">
                        <div class="icon-wrap">
                            <i class="{{ $item['icon'] ?? 'fas fa-star' }}"></i>
                        </div>
                        <h5>{{ $item['value'] ?? $item['number'] ?? '' }}</h5>
                        <p>{{ $item['label'] ?? $item['title'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ════════ TESTIMONIALS ════════ --}}
    @elseif($section->type === 'testimonials')
    @php
        // Use DB-approved testimonials if >= 3, otherwise fall back to content items from builder
        $hardcodedItems    = $content['items'] ?? [];
        $dbTestimonials    = $businessTestimonials ?? collect();
        $useDb             = $dbTestimonials->count() >= 3;
        $displayItems      = $useDb ? $dbTestimonials : collect($hardcodedItems);
    @endphp
    <section class="testimonials-section section-pad" id="testimonials">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Testimonials</span>
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title">{{ $content['heading'] ?? 'What Our Clients Say' }}</h2>
            </div>
            @if($displayItems->count())
            <div class="row g-4">
                @foreach($displayItems as $t)
                @php
                    $isModel = $t instanceof \App\Models\Testimonial;
                    $tName   = $isModel ? $t->name   : ($t['name']   ?? 'Client');
                    $tRole   = $isModel ? $t->role   : ($t['role']   ?? $t['position'] ?? null);
                    $tQuote  = $isModel ? $t->quote  : ($t['quote']  ?? $t['text'] ?? '');
                    $tRating = $isModel ? $t->rating : 5;
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fas fa-star{{ $s > $tRating ? ' text-muted' : '' }}"></i>
                            @endfor
                        </div>
                        <p class="quote">"{{ $tQuote }}"</p>
                        <div class="author">{{ $tName }}</div>
                        @if($tRole)
                        <div class="role">{{ $tRole }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @if(!$isPreview && isset($testimonialUrl))
            <div class="text-center mt-4">
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#businessReviewModal">
                    <i class="fas fa-pen me-1"></i> Leave a Review
                </button>
            </div>
            @endif
        </div>
    </section>

    {{-- Business Review Modal (rendered once, outside the section loop via @once) --}}
    @once
    @if(!$isPreview && isset($testimonialUrl))
    <div class="modal fade" id="businessReviewModal" tabindex="-1" aria-labelledby="businessReviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="businessReviewLabel"><i class="fas fa-star text-warning me-2"></i>Leave a Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Success panel (shown after AJAX submit) -->
                <div id="wsReviewSuccess" class="modal-body text-center py-5" style="display:none;">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h5 class="mb-2">Thank you!</h5>
                    <p class="text-muted mb-4">Your review has been submitted and will appear after approval.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
                <!-- Form panel -->
                <form id="businessReviewForm" action="{{ $testimonialUrl }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div id="wsReviewError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Mary Njoroge" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role / Location <span class="text-muted">(optional)</span></label>
                            <input type="text" name="role" class="form-control" placeholder="e.g. Loyal Customer" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Your Review <span class="text-danger">*</span></label>
                            <textarea name="quote" class="form-control" rows="4" minlength="20" maxlength="1000" placeholder="Share your experience..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rating</label>
                            <div id="wsStarRating" class="d-flex gap-2 fs-4">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-warning ws-star-pick" data-val="{{ $i }}" style="cursor:pointer;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="wsRatingInput" value="5">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="wsReviewBtn" class="btn btn-primary">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    (function(){
        var stars   = document.querySelectorAll('#wsStarRating .ws-star-pick');
        var inp     = document.getElementById('wsRatingInput');
        var form    = document.getElementById('businessReviewForm');
        var btn     = document.getElementById('wsReviewBtn');
        var errDiv  = document.getElementById('wsReviewError');
        var success = document.getElementById('wsReviewSuccess');
        var modal   = document.getElementById('businessReviewModal');

        // ── Star rating ─────────────────────────────────────
        function hl(v){ stars.forEach(function(s){ s.style.opacity = s.dataset.val <= v ? '1' : '0.3'; }); }
        if (stars.length && inp) {
            hl(5);
            stars.forEach(function(s){
                s.addEventListener('click', function(){ inp.value = s.dataset.val; hl(s.dataset.val); });
                s.addEventListener('mouseenter', function(){ hl(s.dataset.val); });
            });
            document.getElementById('wsStarRating').addEventListener('mouseleave', function(){ hl(inp.value); });
        }

        // ── AJAX submit ──────────────────────────────────────
        if (form) {
            form.addEventListener('submit', async function(e){
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
                        success.style.display = 'block';
                    } else {
                        var data = await resp.json();
                        var msg = data.message
                            || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Something went wrong.');
                        errDiv.textContent = msg;
                        errDiv.classList.remove('d-none');
                        btn.disabled = false;
                        btn.textContent = 'Submit Review';
                    }
                } catch(err) {
                    errDiv.textContent = 'Network error. Please try again.';
                    errDiv.classList.remove('d-none');
                    btn.disabled = false;
                    btn.textContent = 'Submit Review';
                }
            });
        }

        // ── Reset on modal close ─────────────────────────────
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function(){
                if (form) { form.style.display = ''; form.reset(); }
                if (success) success.style.display = 'none';
                if (errDiv) errDiv.classList.add('d-none');
                if (btn) { btn.disabled = false; btn.textContent = 'Submit Review'; }
                if (inp) { inp.value = 5; hl(5); }
            });
        }
    })();
    </script>
    @endif
    @endonce

    {{-- ════════ CTA ════════ --}}
    @elseif($section->type === 'cta')
    <section class="cta-section">
        <div class="container">
            <h2>{{ $content['heading'] ?? 'Ready to Get Started?' }}</h2>
            @if(isset($content['subheading']))
            <p>{{ $content['subheading'] }}</p>
            @endif
            <a href="{{ $safeCtaLink($content['cta_link'] ?? null, '#contact') }}" 
               style="background:#fff;color:var(--secondary);padding:14px 40px;border-radius:8px;font-weight:700;display:inline-block;font-size:1rem;">
                {{ $content['cta_text'] ?? 'Contact Us Today' }}
            </a>
        </div>
    </section>

    {{-- ════════ PRODUCTS ════════ --}}
    @elseif($section->type === 'products' && $products->count())
    @php
        // null means "show all" (used on the dedicated products page)
        $maxProducts   = $content['max_products'] ?? null;
        $previewCount  = ($maxProducts && $maxProducts > 0) ? (int)$maxProducts : null;
        $displayProducts = $previewCount ? $products->take($previewCount) : $products;

        $productsPage = $menuPages->firstWhere('slug', 'products');
        $onProductsPage = isset($page) && $page->slug === 'products';
        $productsPageUrl = (!$onProductsPage && $productsPage)
            ? route('public.website.page', [$website->subdomain, $productsPage->slug])
            : null;
    @endphp
    <section class="products-section section-pad" id="products">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Our Products</span>
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title">{{ $content['heading'] ?? 'Our Products &amp; Services' }}</h2>
                @if(isset($content['subheading']))
                <p class="section-subtitle mx-auto">{{ $content['subheading'] }}</p>
                @endif
            </div>
            <div class="row g-4">
                @foreach($displayProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card"
                         data-id="{{ $product->id }}"
                         data-name="{{ addslashes($product->name) }}"
                         data-price="{{ $product->price }}"
                         data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}" class="product-img">
                        @else
                        <div class="product-img-placeholder">
                            <i class="fas fa-box-open"></i>
                        </div>
                        @endif
                        <div class="product-body">
                            <div class="product-name">{{ $product->name }}</div>
                            @if($content['show_price'] ?? true)
                            <div class="product-price">KSh {{ number_format($product->price, 0) }}</div>
                            @endif
                            <button class="add-to-cart-btn"
                                @if($isPreview)
                                    onclick="alert('Cart preview only — customers can add items here.')"
                                    style="opacity:0.6;"
                                    title="Preview mode: cart is active for real visitors"
                                @else
                                    onclick="wsAddToCart(this.closest('[data-id]'))"
                                @endif>
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($productsPageUrl)
            <div class="text-center mt-5">
                <a href="{{ $productsPageUrl }}"
                   class="btn-brand-primary" style="padding:14px 40px;font-size:1rem;">
                    <i class="fas fa-th me-2"></i>View All Products
                    @if($previewCount && $products->count() > $previewCount)
                    <span class="ms-2 badge" style="background:rgba(255,255,255,0.2);color:#fff;border-radius:50px;padding:3px 10px;font-size:0.8rem;">
                        {{ $products->count() - $previewCount }}+ more
                    </span>
                    @endif
                </a>
            </div>
            @endif
        </div>
    </section>

    {{-- ════════ CONTACT ════════ --}}
    @elseif($section->type === 'contact')
    <section class="contact-section section-pad" id="contact">
        <div class="container">
            <div class="row g-5">
                <!-- Form -->
                <div class="col-lg-7">
                    <span class="section-label">Get In Touch</span>
                    <div class="section-divider"></div>
                    <h2 class="section-title">{{ $content['heading'] ?? 'Contact Us' }}</h2>
                    @if(isset($content['subheading']))
                    <p class="section-subtitle mb-4">{{ $content['subheading'] }}</p>
                    @endif
                    <div class="contact-form-card">
                        @if(session('contact_success'))
                        <div class="alert alert-success rounded-3 mb-4">
                            <i class="fas fa-check-circle me-2"></i>{{ session('contact_success') }}
                        </div>
                        @endif
                        <form action="{{ route('public.website.contact', $website->subdomain) }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="name" placeholder="Your Full Name" required
                                           class="form-control" value="{{ old('name') }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="email" placeholder="Email Address" required
                                           class="form-control" value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" name="phone" placeholder="Phone Number"
                                           class="form-control" value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="subject" placeholder="Subject"
                                           class="form-control" value="{{ old('subject') }}">
                                </div>
                                <div class="col-12">
                                    <textarea name="message" rows="5" placeholder="Tell us about your project..." required
                                              class="form-control">{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-brand-primary w-100"
                                            style="padding:14px;font-size:1rem;border:none;cursor:pointer;">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info -->
                <div class="col-lg-5">
                    <div class="pt-5">
                        <h4 class="mb-4" style="font-size:1.3rem;">Contact Information</h4>

                        @if($website->settings['contact_phone'] ?? $website->business->phone)
                        <div class="contact-info-item">
                            <div class="icon-box"><i class="fas fa-phone-alt"></i></div>
                            <div class="info-text">
                                <strong>Phone</strong>
                                <span>{{ $website->settings['contact_phone'] ?? $website->business->phone }}</span>
                            </div>
                        </div>
                        @endif

                        @if($website->settings['contact_email'] ?? $website->business->email)
                        <div class="contact-info-item">
                            <div class="icon-box"><i class="fas fa-envelope"></i></div>
                            <div class="info-text">
                                <strong>Email</strong>
                                <span>{{ $website->settings['contact_email'] ?? $website->business->email }}</span>
                            </div>
                        </div>
                        @endif

                        @if($website->business->address)
                        <div class="contact-info-item">
                            <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="info-text">
                                <strong>Location</strong>
                                <span>{{ $website->business->address }}{{ $website->business->city ? ', '.$website->business->city : '' }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="mt-4 pt-4 border-top">
                            <strong class="d-block mb-3" style="font-size:0.9rem;">Follow Us</strong>
                            <div class="footer-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @endif {{-- end section type --}}
    @if($isPreview)</div>@endif{{-- /sew --}}
    @endforeach
    </main>

    <!-- ── FOOTER ───────────────────────────────────── -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-5">
                <!-- Brand -->
                <div class="col-lg-4">
                    <div class="footer-brand">{{ $website->business->name }}</div>
                    @php
                        $footerDesc = $website->business->description ?? '';
                        $footerWords = explode(' ', $footerDesc);
                        if (count($footerWords) > 25) {
                            $footerDesc = implode(' ', array_slice($footerWords, 0, 25)) . '...';
                        }
                    @endphp
                    <p class="footer-desc">{{ $footerDesc }}</p>
                    <div class="footer-social mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-sm-6 col-lg-2">
                    <h5>Quick Links</h5>
                    <ul>
                        @foreach($menuPages as $menuPage)
                        @php
                            $lNavLabel = preg_replace('/^(our|the|my)\s+/i', '', $menuPage->title);
                            $lNavWords = explode(' ', trim($lNavLabel));
                            $lNavLabel = implode(' ', array_slice($lNavWords, 0, 2));
                        @endphp
                        <li><a href="{{ $isPreview ? route('website.builder.preview.page', $menuPage->id) : $menuPage->url }}">{{ $lNavLabel }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Services -->
                @php $servicesSection = $sections->firstWhere('type','services'); @endphp
                @if($servicesSection)
                @php $servContent = $servicesSection->getContentWithDefaults(); @endphp
                <div class="col-sm-6 col-lg-2">
                    <h5>Services</h5>
                    <ul>
                        @foreach(array_slice($servContent['items'] ?? [], 0, 5) as $item)
                        <li><a href="#services">{{ $item['title'] ?? $item['name'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Contact -->
                <div class="col-lg-3">
                    <h5>Contact</h5>
                    <ul class="footer-contact">
                        @if($website->settings['contact_email'] ?? $website->business->email)
                        <li><i class="fas fa-envelope"></i><span>{{ $website->settings['contact_email'] ?? $website->business->email }}</span></li>
                        @endif
                        @if($website->settings['contact_phone'] ?? $website->business->phone)
                        <li><i class="fas fa-phone-alt"></i><span>{{ $website->settings['contact_phone'] ?? $website->business->phone }}</span></li>
                        @endif
                        @if($website->business->address)
                        <li><i class="fas fa-map-marker-alt"></i><span>{{ $website->business->address }}{{ $website->business->city ? ', '.$website->business->city : '' }}</span></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center">
                <span>© {{ date('Y') }} <strong style="color:#fff;">{{ $website->business->name }}</strong>. All rights reserved.</span>
                <span>Built with <a href="https://shopybook.com">Shopybook</a></span>
            </div>
        </div>
    </footer>

    @if(session('success'))
    <div id="successToast" style="position:fixed;bottom:24px;right:24px;background:#10b981;color:#fff;padding:16px 24px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.2);z-index:9999;font-weight:600;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
    </div>
    <script>setTimeout(()=>{const t=document.getElementById('successToast');if(t)t.remove();},4000);</script>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Sticky nav scroll ──────────────────────────────────
        const nav = document.getElementById('siteNav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 50);
        });

        // ── Mobile toggle ──────────────────────────────────────
        document.getElementById('mobileToggle').addEventListener('click', () => {
            const menu = document.getElementById('mobileMenu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        });

        // ── Smooth scroll for anchor links ─────────────────────
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('mobileMenu').style.display = 'none';
                }
            });
        });

        function toggleMobileMenu() {
            document.getElementById('mobileMenu').style.display =
                document.getElementById('mobileMenu').style.display === 'none' ? 'block' : 'none';
        }

        // Graceful AI image loading — Pollinations images are generated on demand (may take
        // 10-30 seconds). Show branded gradient placeholder, fade image in when ready.
        (function() {
            function loadAiImage(img) {
                if (!img.src) return;
                if (img.complete && img.naturalWidth > 0) {
                    img.classList.add('img-loaded');
                    return;
                }
                img.addEventListener('load', function() { img.classList.add('img-loaded'); });
                img.addEventListener('error', function() {
                    // Fallback to a picsum photo using section-specific seed
                    var seed = img.dataset.seed || Math.floor(Math.random() * 9000);
                    var w = img.dataset.w || 800;
                    var h = img.dataset.h || 500;
                    img.src = 'https://picsum.photos/seed/' + seed + '/' + w + '/' + h;
                    img.classList.add('img-loaded');
                });
            }
            document.querySelectorAll('.hero-image-wrap img, .about-img-wrap img').forEach(loadAiImage);
        })();
    </script>

@if($isPreview)
{{-- ── FULL IN-PREVIEW VISUAL EDITOR ─────────────────────────────── --}}

{{-- IMAGE EDITOR MODAL ──────────────────────────────────────────── --}}
<div id="imgEditModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:32px;width:min(500px,95vw);box-shadow:0 24px 80px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h5 style="margin:0;font-weight:700;font-size:1.1rem;">✨ Generate AI Image</h5>
            <button onclick="closeImageModal()" style="background:none;border:none;font-size:1.4rem;cursor:pointer;line-height:1;">×</button>
        </div>
        <p style="color:#666;font-size:0.88rem;margin-bottom:16px;">Describe what should appear in this image. Be specific about your products or business.</p>
        <textarea id="imgPromptInput" rows="3" placeholder="e.g. luxury bedding store display with duvets and pillows, warm lighting, professional photo"
            style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:0.9rem;resize:vertical;margin-bottom:16px;font-family:inherit;"></textarea>
        <input type="hidden" id="imgModalSectionId">
        <div id="imgGeneratingState" style="display:none;text-align:center;padding:12px 0;">
            <span class="img-spinner" style="display:inline-block;"></span>
            <span style="margin-left:10px;color:#555;font-size:0.9rem;">Generating image… this takes 20–40 seconds</span>
        </div>
        <div id="imgErrorMsg" style="display:none;color:#dc2626;font-size:0.85rem;margin-bottom:12px;"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeImageModal()" style="padding:9px 20px;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;cursor:pointer;font-size:0.9rem;">Cancel</button>
            <button id="imgGenerateBtn" onclick="runAiGenerate()"
                style="padding:9px 24px;border:none;border-radius:8px;background:var(--primary);color:#fff;font-weight:600;cursor:pointer;font-size:0.9rem;">
                <i class="fas fa-magic" style="margin-right:6px;"></i>Generate
            </button>
        </div>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════════
   SHOPY VISUAL EDITOR — All-in-one in-preview editor
   ═══════════════════════════════════════════════════════════════════ */

// ── GLOBAL CONFIG ───────────────────────────────────────────────────
var _imgCsrf        = '{{ csrf_token() }}';
var _csrf           = '{{ csrf_token() }}';
var _websiteId      = {{ $website->id }};
var _generateUrl    = '{{ route("website.builder.ai.generate-section-image") }}';
var _uploadBase     = '{{ url("website-builder/sections") }}';
var _publishUrl     = '{{ route("website.builder.publish") }}';
var _customizeUrl   = '{{ route("website.builder.customize") }}';
var _genContentUrl  = '{{ route("website.builder.ai.generate-content") }}';
var _uploadLogoUrl  = '{{ route("website.builder.upload-logo") }}';
var _genLogoUrl     = '{{ route("website.builder.ai.generate-logo") }}';

// Current colours / fonts (from PHP rendering context)
var _colors = {
    primary:    '{{ $primaryColor }}',
    secondary:  '{{ $secondaryColor }}',
    accent:     '{{ $accentColor }}',
    background: '{{ $bgColor }}',
    text:       '{{ $textColor }}'
};
var _fonts = { heading: '{{ $headingFont }}', body: '{{ $bodyFont }}' };

// All section data indexed by id
var _sectionsData = {!! json_encode(
    $sections->mapWithKeys(fn($s) => [$s->id => ['type' => $s->type, 'content' => $s->getContentWithDefaults()]])->all(),
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
) !!};

// ── FIELD DEFINITIONS BY SECTION TYPE ──────────────────────────────
var _FIELDS = {
    hero: [
        { key:'heading',           label:'Headline',                type:'text'     },
        { key:'subheading',        label:'Subheading / Tagline',    type:'textarea' },
        { key:'cta_text',          label:'Primary Button Text',     type:'text'     },
        { key:'secondary_cta_text',label:'Secondary Button Text',   type:'text'     },
    ],
    about: [
        { key:'heading',    label:'Section Heading', type:'text'     },
        { key:'subheading', label:'Subheading',      type:'text'     },
        { key:'text',       label:'Body Text',       type:'textarea' },
    ],
    features: [
        { key:'heading',    label:'Section Heading', type:'text'     },
        { key:'subheading', label:'Subheading',      type:'textarea' },
    ],
    services: [
        { key:'heading',    label:'Section Heading', type:'text'     },
        { key:'subheading', label:'Subheading',      type:'textarea' },
    ],
    cta: [
        { key:'heading',  label:'Heading',     type:'text'     },
        { key:'text',     label:'Body Text',   type:'textarea' },
        { key:'cta_text', label:'Button Text', type:'text'     },
    ],
    testimonials: [
        { key:'heading', label:'Section Heading', type:'text' },
    ],
    contact: [
        { key:'heading', label:'Section Heading', type:'text' },
        { key:'email',   label:'Email Address',   type:'text' },
        { key:'phone',   label:'Phone Number',    type:'text' },
        { key:'address', label:'Address',         type:'text' },
    ],
    stats: [
        { key:'heading',    label:'Section Heading', type:'text' },
        { key:'subheading', label:'Subheading',      type:'text' },
    ],
    products: [
        { key:'heading',    label:'Section Heading', type:'text'     },
        { key:'subheading', label:'Subheading',      type:'textarea' },
    ],
    gallery:      [{ key:'heading', label:'Section Heading', type:'text' }],
    team:         [{ key:'heading', label:'Section Heading', type:'text' }],
    faq: [
        { key:'heading',    label:'Section Heading', type:'text'     },
        { key:'subheading', label:'Subheading',      type:'textarea' },
    ],
};

// ── FONT PAIRS ──────────────────────────────────────────────────────
var _FONT_PAIRS = [
    { name:'Modern Pro',        heading:'Poppins',            body:'Inter'           },
    { name:'Classic Serif',     heading:'Playfair Display',   body:'Source Sans Pro' },
    { name:'Bold Business',     heading:'Montserrat',         body:'Open Sans'       },
    { name:'Elegant Luxury',    heading:'Cormorant Garamond', body:'Raleway'         },
    { name:'Clean Slab',        heading:'Roboto Slab',        body:'Roboto'          },
    { name:'Fresh & Friendly',  heading:'Nunito',             body:'Lato'            },
    { name:'Luxury Editorial',  heading:'Libre Baskerville',  body:'Mulish'          },
    { name:'Tech Modern',       heading:'Space Grotesk',      body:'DM Sans'         },
];

// ── EDITOR STATE ────────────────────────────────────────────────────
var _activeTab       = 'content';
var _activeSectionId = null;
var _editColors      = Object.assign({}, _colors);
var _editFonts       = Object.assign({}, _fonts);

// ── PANEL OPEN / CLOSE ──────────────────────────────────────────────
function _openPanel(tab) {
    _activeSectionId = null;
    _switchTab(tab || 'content');
    document.getElementById('editorPanel').classList.add('open');
    document.getElementById('epTitle').textContent =
        tab === 'design' ? '🎨 Design' : tab === 'logo' ? '🖼️ Logo' : '✏️ Editor';
}

function _openContentPanel(sectionId) {
    _activeSectionId = sectionId;
    var sd = _sectionsData[sectionId];
    var typeName = sd ? sd.type.charAt(0).toUpperCase() + sd.type.slice(1) : 'Section';
    document.getElementById('epTitle').textContent = '✏️ ' + typeName;
    document.getElementById('editorPanel').classList.add('open');
    _switchTab('content');
}

function _closePanel() {
    document.getElementById('editorPanel').classList.remove('open');
}

// ── TAB SWITCHING ───────────────────────────────────────────────────
function _switchTab(tab) {
    _activeTab = tab;
    document.querySelectorAll('.ep-tab').forEach(function(t) { t.classList.remove('active'); });
    var tabEl = document.getElementById('tab_' + tab);
    if (tabEl) tabEl.classList.add('active');
    if (tab === 'content') _renderContentTab();
    else if (tab === 'design') _renderDesignTab();
    else if (tab === 'logo')   _renderLogoTab();
}

// ── CONTENT TAB ─────────────────────────────────────────────────────
function _renderContentTab() {
    var body = document.getElementById('epBody');
    if (!_activeSectionId) {
        body.innerHTML = '<p style="color:#94a3b8;font-size:0.82rem;text-align:center;margin-top:30px;padding:0 10px;">Hover a section and click <strong style="color:#1e293b;">Edit</strong> to edit its text.</p>';
        return;
    }
    var sd = _sectionsData[_activeSectionId];
    if (!sd) { body.innerHTML = '<p style="color:#ef4444;font-size:0.82rem;">Section not found.</p>'; return; }

    var fields = _FIELDS[sd.type] || [{ key:'heading', label:'Heading', type:'text' }];
    var html = '<div id="epMsg" class="ep-msg"></div>';
    fields.forEach(function(f) {
        var val = (sd.content[f.key] !== undefined && sd.content[f.key] !== null)
                  ? String(sd.content[f.key]) : '';
        var safeVal = val.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;');
        html += '<div class="ep-field">';
        html += '<label class="ep-lbl" for="epf_' + f.key + '">' + f.label + '</label>';
        if (f.type === 'textarea') {
            html += '<textarea class="ep-input" id="epf_' + f.key + '" rows="3">' + safeVal + '</textarea>';
        } else {
            html += '<input type="text" class="ep-input" id="epf_' + f.key + '" value="' + safeVal + '">';
        }
        html += '</div>';
    });
    html += '<hr class="ep-divider">';
    html += '<button class="ep-btn ep-btn-dark" onclick="_saveContent()">'
          + '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0"><path d="M5 13l4 4L19 7"/></svg>'
          + ' Save Changes</button>';
    html += '<button class="ep-btn ep-btn-ai" onclick="_aiRegen()">'
          + '<span id="aiSpin" class="ep-spinner" style="display:none"></span>'
          + ' ✨ AI Regenerate</button>';
    body.innerHTML = html;
}

function _saveContent() {
    if (!_activeSectionId) return;
    var sd = _sectionsData[_activeSectionId];
    if (!sd) return;
    var fields = _FIELDS[sd.type] || [];
    var updated = Object.assign({}, sd.content);
    fields.forEach(function(f) {
        var el = document.getElementById('epf_' + f.key);
        if (el) updated[f.key] = el.value;
    });
    _setStatus('saving');
    _showMsg('');
    fetch('{{ url("website-builder/sections") }}/' + _activeSectionId, {
        method: 'PUT',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':_csrf, 'Accept':'application/json' },
        body: JSON.stringify({ content: updated })
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success !== false) {
            _sectionsData[_activeSectionId].content = updated;
            _updateSectionDOM(_activeSectionId, sd.type, updated);
            _setStatus('saved');
            _showMsg('✓ Changes saved!', 'success');
        } else {
            _showMsg('Save failed: ' + (data.message || 'Error'), 'error');
            _setStatus('');
        }
    })
    .catch(function() { _showMsg('Network error. Please try again.', 'error'); _setStatus(''); });
}

function _aiRegen() {
    if (!_activeSectionId) return;
    var sd = _sectionsData[_activeSectionId];
    if (!sd) return;
    var btn = document.querySelector('.ep-btn-ai');
    var spin = document.getElementById('aiSpin');
    if (btn) btn.disabled = true;
    if (spin) spin.style.display = 'inline-block';
    _showMsg('Generating new content…', 'info');
    fetch(_genContentUrl, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':_csrf, 'Accept':'application/json' },
        body: JSON.stringify({ section_type: sd.type, website_id: _websiteId, existing_content: sd.content })
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (btn) btn.disabled = false;
        if (spin) spin.style.display = 'none';
        if (data.content) {
            Object.assign(_sectionsData[_activeSectionId].content, data.content);
            _renderContentTab();
            _showMsg('✓ New content ready — review and save.', 'success');
        } else {
            _showMsg('Generation failed: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(function() {
        if (btn) btn.disabled = false;
        if (spin) spin.style.display = 'none';
        _showMsg('Network error.', 'error');
    });
}

function _updateSectionDOM(sectionId, type, content) {
    var wrapper = document.getElementById('sew-' + sectionId);
    if (!wrapper) return;
    if (content.heading) {
        var h1 = wrapper.querySelector('.hero-title');
        if (h1) h1.textContent = content.heading;
        var st = wrapper.querySelector('.section-title');
        if (st) st.textContent = content.heading;
        // Fallback: first h2 that is not section-title
        if (!h1 && !st) {
            var h2 = wrapper.querySelector('h2');
            if (h2) h2.textContent = content.heading;
        }
    }
    if (content.subheading) {
        var sub = wrapper.querySelector('.hero-subtitle, .section-subtitle');
        if (sub) sub.textContent = content.subheading;
    }
    if (type === 'hero' && content.cta_text) {
        var ctaLink = wrapper.querySelector('.btn-brand-secondary, .btn-brand-primary');
        if (ctaLink) ctaLink.textContent = content.cta_text;
    }
    if (type === 'about' && content.text) {
        var aboutTxt = wrapper.querySelector('p.section-subtitle, .about-text');
        // Find the longest <p> as the body text paragraph
        if (!aboutTxt) {
            var ps = wrapper.querySelectorAll('p');
            var longest = null;
            ps.forEach(function(p) { if (!longest || p.textContent.length > longest.textContent.length) longest = p; });
            aboutTxt = longest;
        }
        if (aboutTxt) aboutTxt.textContent = content.text;
    }
    if (type === 'cta') {
        if (content.heading) {
            var ctaH2 = wrapper.querySelector('h2');
            if (ctaH2) ctaH2.textContent = content.heading;
        }
        if (content.cta_text) {
            var ctaBtn = wrapper.querySelector('a[style], a.btn');
            if (ctaBtn) ctaBtn.textContent = content.cta_text;
        }
    }
}

// ── DESIGN TAB ──────────────────────────────────────────────────────
function _renderDesignTab() {
    document.getElementById('epTitle').textContent = '🎨 Design';
    var colorDefs = [
        { key:'primary',   label:'Primary Color'       },
        { key:'secondary', label:'CTA / Accent Color'  },
        { key:'accent',    label:'Highlight Color'     },
    ];
    var html = '<div id="epMsg" class="ep-msg"></div>';
    html += '<p class="ep-note">Changes apply instantly so you can preview before saving.</p>';
    html += '<strong style="font-size:0.72rem;font-weight:700;color:#1e293b;display:block;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Colors</strong>';
    colorDefs.forEach(function(c) {
        var val = _editColors[c.key] || '#000000';
        html += '<div class="ep-clr-row">';
        html += '<div class="ep-clr-swatch"><input type="color" id="clr_' + c.key + '" value="' + val + '" oninput="_liveColor(\'' + c.key + '\',this.value)"></div>';
        html += '<span class="ep-clr-lbl">' + c.label + '</span>';
        html += '</div>';
    });
    html += '<hr class="ep-divider">';
    html += '<strong style="font-size:0.72rem;font-weight:700;color:#1e293b;display:block;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Font Combinations</strong>';
    html += '<div class="ep-font-grid">';
    _FONT_PAIRS.forEach(function(fp, i) {
        var sel = (fp.heading === _editFonts.heading && fp.body === _editFonts.body) ? ' selected' : '';
        html += '<div class="ep-font-opt' + sel + '" onclick="_selectFont(' + i + ')" id="fp_' + i + '">';
        html += '<span class="fname">' + fp.name + '</span>';
        html += '<span class="fpair">' + fp.heading + '</span>';
        html += '</div>';
    });
    html += '</div>';
    html += '<hr class="ep-divider">';
    html += '<button class="ep-btn ep-btn-dark" onclick="_saveDesign()">💾 Save Design</button>';
    document.getElementById('epBody').innerHTML = html;
}

function _liveColor(key, val) {
    _editColors[key] = val;
    document.documentElement.style.setProperty('--' + key, val);
    if (key === 'primary') {
        document.documentElement.style.setProperty('--primary-dark', _hexDarken(val, 40));
    }
}

function _hexDarken(hex, amt) {
    hex = hex.replace('#','');
    if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    var r = Math.max(0, parseInt(hex.substr(0,2),16)-amt);
    var g = Math.max(0, parseInt(hex.substr(2,2),16)-amt);
    var b = Math.max(0, parseInt(hex.substr(4,2),16)-amt);
    return '#' + [r,g,b].map(function(n){ return n.toString(16).padStart(2,'0'); }).join('');
}

function _selectFont(idx) {
    document.querySelectorAll('.ep-font-opt').forEach(function(el) { el.classList.remove('selected'); });
    var fp = _FONT_PAIRS[idx];
    var el = document.getElementById('fp_' + idx);
    if (el) el.classList.add('selected');
    _editFonts.heading = fp.heading;
    _editFonts.body    = fp.body;
    // Load font from Google and apply live
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=' + fp.heading.replace(/ /g,'+')
              + ':wght@400;700&family=' + fp.body.replace(/ /g,'+') + ':wght@400;600&display=swap';
    document.head.appendChild(link);
    document.documentElement.style.setProperty('--font-heading', "'" + fp.heading + "', sans-serif");
    document.documentElement.style.setProperty('--font-body', "'" + fp.body + "', sans-serif");
}

function _saveDesign() {
    _setStatus('saving');
    _showMsg('');
    fetch(_customizeUrl, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':_csrf, 'Accept':'application/json' },
        body: JSON.stringify({
            primary_color:    _editColors.primary,
            secondary_color:  _editColors.secondary,
            accent_color:     _editColors.accent,
            background_color: _editColors.background || '#ffffff',
            text_color:       _editColors.text || '#212121',
            heading_font:     _editFonts.heading,
            body_font:        _editFonts.body,
        })
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success) {
            _setStatus('saved');
            _showMsg('✓ Design saved! Refreshing to apply…', 'success');
            setTimeout(function(){ location.reload(); }, 1200);
        } else {
            _showMsg('Save failed: ' + (data.message || ''), 'error');
            _setStatus('');
        }
    })
    .catch(function() { _showMsg('Network error.', 'error'); _setStatus(''); });
}

// ── LOGO TAB ────────────────────────────────────────────────────────
function _renderLogoTab() {
    document.getElementById('epTitle').textContent = '🖼️ Logo';
    var navLogo = document.getElementById('siteLogoImg');
    var html = '<div id="epMsg" class="ep-msg"></div>';
    // Current logo preview
    if (navLogo && navLogo.src && !navLogo.src.endsWith('undefined')) {
        html += '<div style="text-align:center;margin-bottom:14px;">';
        html += '<img src="' + navLogo.src + '" id="epLogoPreview" class="ep-logo-preview">';
        html += '</div>';
    } else {
        html += '<div style="text-align:center;margin-bottom:14px;padding:18px;background:#f8fafc;border-radius:8px;">';
        html += '<span style="font-size:2.2rem;">🏢</span><br><span style="color:#94a3b8;font-size:0.8rem;margin-top:6px;display:block;">No logo yet</span>';
        html += '</div>';
    }
    // Upload section
    html += '<label class="ep-logo-drop" onclick="document.getElementById(\'logoFileInput\').click()">';
    html += '<svg width="28" height="28" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M8 12l4-4 4 4M12 8v8"/></svg>';
    html += '<div style="margin-top:8px;font-size:0.82rem;color:#64748b;font-weight:600;">Click to upload logo</div>';
    html += '<div style="font-size:0.7rem;color:#94a3b8;margin-top:3px;">PNG, JPG, SVG — max 5 MB</div>';
    html += '</label>';
    html += '<input type="file" id="logoFileInput" accept="image/*" style="display:none;" onchange="_uploadLogo(this)">';
    // Divider
    html += '<div style="display:flex;align-items:center;gap:8px;margin:14px 0 12px;">';
    html += '<div style="flex:1;height:1px;background:#e2e8f0;"></div>';
    html += '<span style="font-size:0.72rem;color:#94a3b8;white-space:nowrap;">or generate with AI</span>';
    html += '<div style="flex:1;height:1px;background:#e2e8f0;"></div>';
    html += '</div>';
    // AI generation section
    html += '<div style="background:linear-gradient(135deg,#f0f4ff 0%,#faf5ff 100%);border-radius:10px;padding:14px;border:1px solid #e0e7ff;margin-bottom:12px;">';
    html += '<div style="font-size:0.8rem;font-weight:700;color:#4338ca;margin-bottom:8px;">✨ AI Logo Generator</div>';
    html += '<textarea id="logoPromptInput" rows="3" placeholder="Describe your logo idea...&#10;e.g. \"Modern tech logo with circuit board motif in blue\"" style="width:100%;border:1.5px solid #c7d2fe;border-radius:7px;padding:8px 10px;font-size:0.8rem;color:#334155;background:#fff;resize:vertical;outline:none;"></textarea>';
    html += '<button id="btnGenLogo" onclick="_generateLogo()" style="width:100%;margin-top:8px;padding:9px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:7px;font-size:0.83rem;font-weight:700;cursor:pointer;">✨ Generate Logo</button>';
    html += '</div>';
    html += '<p class="ep-note">Your logo appears in the navbar and footer. For best results, use a transparent PNG or regenerate until you find one you like.</p>';
    document.getElementById('epBody').innerHTML = html;
}

function _generateLogo() {
    var prompt = (document.getElementById('logoPromptInput') || {}).value || '';
    var btn = document.getElementById('btnGenLogo');
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Generating…'; }
    _setStatus('saving');
    _showMsg('Generating AI logo — this may take ~20 seconds…', 'info');
    fetch(_genLogoUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ prompt: prompt })
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (btn) { btn.disabled = false; btn.textContent = '✨ Generate Logo'; }
        if (data.success && data.logo_url) {
            _applyNewLogo(data.logo_url);
            _setStatus('saved');
            _showMsg('✓ Logo generated!', 'success');
            _toast('success', '✨ Logo Generated!', 'Your new AI logo is live. Regenerate anytime to try a different style.');
        } else {
            _showMsg('Generation failed: ' + (data.error || 'Unknown error'), 'error');
            _setStatus('');
        }
    })
    .catch(function() {
        if (btn) { btn.disabled = false; btn.textContent = '✨ Generate Logo'; }
        _showMsg('Network error. Please try again.', 'error');
        _setStatus('');
    });
}

function _applyNewLogo(url) {
    var navLogo = document.getElementById('siteLogoImg');
    if (navLogo) {
        navLogo.src = url;
    } else {
        var brand = document.querySelector('.navbar-brand-text');
        if (brand) {
            var img = document.createElement('img');
            img.id = 'siteLogoImg'; img.src = url; img.alt = '';
            img.style.cssText = 'height:46px;object-fit:contain;';
            brand.parentNode.insertBefore(img, brand);
        }
    }
    var prev = document.getElementById('epLogoPreview');
    if (prev) prev.src = url;
    else _renderLogoTab();
}

function _uploadLogo(input) {
    if (!input.files || !input.files[0]) return;
    var fd = new FormData();
    fd.append('logo', input.files[0]);
    fd.append('_token', _csrf);
    _setStatus('saving');
    _showMsg('Uploading logo…', 'info');
    fetch(_uploadLogoUrl, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: fd
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success && data.logo_url) {
            _applyNewLogo(data.logo_url);
            _setStatus('saved');
            _showMsg('✓ Logo updated!', 'success');
        } else {
            _showMsg('Upload failed: ' + (data.error || 'Unknown error'), 'error');
            _setStatus('');
        }
    })
    .catch(function() { _showMsg('Network error.', 'error'); _setStatus(''); });
}

// ── PUBLISH ─────────────────────────────────────────────────────────
function _publishSite() {
    _confirm('🚀 Publish Website', 'Your website will be visible to the public. Ready to go live?', 'Go Live!', false, function() {
        _setStatus('saving');
        fetch(_publishUrl, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':_csrf, 'Accept':'application/json' }
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success) {
                _setStatus('saved');
                _toast('success', '🎉 You\'re Live!', 'Your website is now visible to visitors.');
            } else {
                _toast('error', 'Publish Failed', data.message || 'An unknown error occurred.');
                _setStatus('');
            }
        })
        .catch(function() { _toast('error', 'Network Error', 'Could not reach the server. Please try again.'); _setStatus(''); });
    });
}

// ── PAGE NAVIGATION ─────────────────────────────────────────────────
function _gotoPage(url) { if (url) window.location.href = url; }

// ── TOAST NOTIFICATION ──────────────────────────────────────────────
var _toastTimer = null;
function _toast(type, title, msg, duration) {
    duration = duration || 4500;
    var icons = { success: '✅', error: '❌', info: 'ℹ️' };
    document.getElementById('seToastIcon').textContent  = icons[type] || 'ℹ️';
    document.getElementById('seToastTitle').textContent = title || '';
    document.getElementById('seToastMsg').textContent   = msg   || '';
    var el = document.getElementById('seToast');
    el.className = 'se-toast-show se-' + (type || 'info');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(_hideToast, duration);
}
function _hideToast() {
    var el = document.getElementById('seToast');
    if (el) el.className = '';
}

// ── CONFIRM DIALOG ───────────────────────────────────────────────────
function _confirm(title, msg, okLabel, danger, onOk) {
    var iconMatch = (title || '').match(/^(\S+)\s/);
    document.getElementById('seConfirmIcon').textContent  = iconMatch ? iconMatch[1] : '❓';
    document.getElementById('seConfirmTitle').textContent = title || 'Are you sure?';
    document.getElementById('seConfirmMsg').textContent   = msg   || '';
    var okBtn = document.getElementById('seConfirmOk');
    okBtn.textContent = okLabel || 'Confirm';
    okBtn.className = 'se-cbtn se-cbtn-ok' + (danger ? ' se-danger' : '');
    var bd = document.getElementById('seConfirmBackdrop');
    bd.classList.add('se-open');
    // Clone buttons to remove previous listeners
    var newOk = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOk, okBtn);
    var cancelBtn = document.getElementById('seConfirmCancel');
    var newCancel = cancelBtn.cloneNode(true);
    cancelBtn.parentNode.replaceChild(newCancel, cancelBtn);
    document.getElementById('seConfirmOk').addEventListener('click', function() {
        bd.classList.remove('se-open');
        if (onOk) onOk();
    });
    document.getElementById('seConfirmCancel').addEventListener('click', function() {
        bd.classList.remove('se-open');
    });
    bd.addEventListener('click', function(e) {
        if (e.target === bd) bd.classList.remove('se-open');
    }, { once: true });
}

// ── UTILITIES ───────────────────────────────────────────────────────
function _setStatus(state) {
    var el = document.getElementById('etStatus');
    if (!el) return;
    el.className = 'et-status';
    if (state === 'saving') { el.textContent = '● Saving…'; el.classList.add('saving'); }
    else if (state === 'saved') {
        el.textContent = '✓ Saved'; el.classList.add('saved');
        setTimeout(function(){ el.textContent = ''; }, 3000);
    } else { el.textContent = ''; }
}

function _showMsg(msg, type) {
    var el = document.getElementById('epMsg');
    if (!el) return;
    if (!msg) { el.style.display = 'none'; return; }
    el.className = 'ep-msg ep-msg-' + (type === 'success' ? 'success' : type === 'error' ? 'error' : 'info');
    el.style.display = 'block';
    el.textContent = msg;
}

// ── IMAGE EDITING (existing functionality) ──────────────────────────
function openImageModal(sectionId, defaultPrompt) {
    document.getElementById('imgModalSectionId').value = sectionId;
    document.getElementById('imgPromptInput').value = defaultPrompt || '';
    document.getElementById('imgErrorMsg').style.display = 'none';
    document.getElementById('imgGeneratingState').style.display = 'none';
    document.getElementById('imgGenerateBtn').disabled = false;
    document.getElementById('imgEditModal').style.display = 'flex';
    setTimeout(function(){ document.getElementById('imgPromptInput').focus(); }, 50);
}

function closeImageModal() {
    document.getElementById('imgEditModal').style.display = 'none';
}

function runAiGenerate() {
    var sectionId = document.getElementById('imgModalSectionId').value;
    var prompt    = document.getElementById('imgPromptInput').value.trim();
    if (!prompt) { document.getElementById('imgPromptInput').focus(); return; }

    document.getElementById('imgGenerateBtn').disabled = true;
    document.getElementById('imgGeneratingState').style.display = 'block';
    document.getElementById('imgErrorMsg').style.display = 'none';

    var wrap = document.getElementById('img-wrap-' + sectionId);
    if (wrap) {
        var spinner = wrap.querySelector('.section-img-edit-overlay');
        if (spinner) spinner.innerHTML = '<div class="img-generating"><span class="img-spinner"></span> Generating…</div>';
    }

    fetch(_generateUrl, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':_imgCsrf, 'Accept':'application/json' },
        body: JSON.stringify({ section_id: sectionId, prompt: prompt })
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success && data.image_url) {
            applyNewImage(sectionId, data.image_url);
            closeImageModal();
        } else {
            _showImgError(data.error || 'Generation failed. Try again.');
        }
    })
    .catch(function(){ _showImgError('Network error. Please try again.'); });
}

function uploadSectionImage(sectionId, input) {
    if (!input.files || !input.files[0]) return;
    var formData = new FormData();
    formData.append('image', input.files[0]);
    formData.append('_token', _imgCsrf);

    var wrap = document.getElementById('img-wrap-' + sectionId);
    if (wrap) {
        var overlay = wrap.querySelector('.section-img-edit-overlay');
        if (overlay) overlay.innerHTML = '<div class="img-generating"><span class="img-spinner"></span> Uploading…</div>';
    }

    fetch(_uploadBase + '/' + sectionId + '/upload-image', {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success && data.image_url) {
            applyNewImage(sectionId, data.image_url);
        } else {
            _toast('error', 'Upload Failed', data.error || 'An unknown error occurred.');
            location.reload();
        }
    })
    .catch(function(){ _toast('error', 'Upload Failed', 'Could not upload image. Please try again.'); location.reload(); });
}

function applyNewImage(sectionId, imageUrl) {
    var wrap = document.getElementById('img-wrap-' + sectionId);
    if (!wrap) { location.reload(); return; }

    var existing = document.getElementById('section-img-' + sectionId);
    if (existing) {
        existing.src = imageUrl;
        existing.classList.add('img-loaded');
    } else {
        var isHero = wrap.classList.contains('hero-image-wrap');
        var imgEl = document.createElement('img');
        imgEl.src = imageUrl;
        imgEl.id = 'section-img-' + sectionId;
        imgEl.alt = '';
        imgEl.classList.add('img-loaded');
        if (isHero) { imgEl.style.width='100%'; imgEl.style.height='380px'; imgEl.style.objectFit='cover'; }
        wrap.innerHTML = '';
        wrap.appendChild(imgEl);
    }

    var ov = wrap.querySelector('.section-img-edit-overlay');
    var ovHtml = '<button class="img-edit-btn ai-btn" onclick="openImageModal('+sectionId+',\'\')">'
               +'<i class="fas fa-magic"></i> AI Image</button>'
               +'<label class="img-edit-btn" style="cursor:pointer;"><i class="fas fa-upload"></i> Upload'
               +'<input type="file" accept="image/*" style="display:none;" onchange="uploadSectionImage('+sectionId+',this)"></label>';
    if (ov) {
        ov.innerHTML = ovHtml;
    } else {
        var newOv = document.createElement('div');
        newOv.className = 'section-img-edit-overlay';
        newOv.innerHTML = ovHtml;
        wrap.appendChild(newOv);
    }
}

function _showImgError(msg) {
    var el = document.getElementById('imgErrorMsg');
    el.textContent = msg; el.style.display = 'block';
    document.getElementById('imgGeneratingState').style.display = 'none';
    document.getElementById('imgGenerateBtn').disabled = false;
}

// Close modal on backdrop click
document.getElementById('imgEditModal').addEventListener('click', function(e) {
    if (e.target === this) closeImageModal();
});

// Close editor panel when clicking outside panel + toolbar + section wrappers
document.addEventListener('click', function(e) {
    var panel = document.getElementById('editorPanel');
    if (!panel) return;
    if (!panel.classList.contains('open')) return;
    var toolbar = document.getElementById('editorToolbar');
    if (panel.contains(e.target) || (toolbar && toolbar.contains(e.target))) return;
    if (e.target.closest && e.target.closest('.seb')) return; // section edit buttons
    _closePanel();
});
</script>
@endif

{{-- ══════ STOREFRONT CART (public pages only) ══════ --}}
@if(!$isPreview && $products->count())
<!-- Cart floating button -->
<button class="cart-fab" id="wsCartFab" onclick="wsToggleCart()" aria-label="Cart" style="display:none;">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-fab-badge" id="wsCartBadge">0</span>
</button>

<!-- Drawer overlay -->
<div class="cart-overlay" id="wsCartOverlay" onclick="wsToggleCart()"></div>

<!-- Cart drawer -->
<div class="cart-drawer" id="wsCartDrawer">
    <div class="cart-drawer-hdr">
        <h5><i class="fas fa-shopping-bag me-2"></i>Your Cart</h5>
        <button class="cart-close-btn" onclick="wsToggleCart()">&times;</button>
    </div>
    <div class="cart-items-list" id="wsCartList">
        <div class="cart-empty-msg" id="wsCartEmpty">
            <i class="fas fa-shopping-cart"></i>
            <p style="font-weight:600;margin-bottom:4px;">Your cart is empty</p>
            <p style="font-size:0.84rem;">Add products to get started.</p>
        </div>
    </div>
    <div class="cart-footer" id="wsCartFooter" style="display:none;">
        <div class="cart-total-row">
            <span>Total</span>
            <span id="wsCartTotal">KSh 0</span>
        </div>
        <button class="add-to-cart-btn" onclick="wsOpenOrderForm()" style="margin-top:0;">
            <i class="fas fa-check-circle"></i> Place Order
        </button>
    </div>
</div>

<!-- Order form modal -->
<div class="order-modal-wrap" id="wsOrderModal">
    <div class="order-modal">
        <h4>Complete Your Order</h4>
        <p style="color:#6c757d;font-size:0.87rem;margin-bottom:0;">
            No payment needed now — the business owner will contact you to arrange delivery and payment.
        </p>
        <div class="order-note-box">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Free to order.</strong> You only pay when you and the seller agree on delivery details.
        </div>

        <div class="order-summary-box" id="wsOrderSummary"></div>

        <div id="wsOrderFormBody">
            <div class="order-form-group">
                <label>Your Name <span style="color:#dc3545;">*</span></label>
                <input type="text" id="wsOName" placeholder="Full name">
            </div>
            <div class="order-form-group">
                <label>Phone Number <span style="color:#dc3545;">*</span></label>
                <input type="tel" id="wsOPhone" placeholder="e.g. 0712 345 678">
            </div>
            <div class="order-form-group">
                <label>Email <span style="color:#6c757d;font-weight:400;">(optional)</span></label>
                <input type="email" id="wsOEmail" placeholder="your@email.com">
            </div>
            <div class="order-form-group">
                <label>Delivery Address <span style="color:#6c757d;font-weight:400;">(optional)</span></label>
                <input type="text" id="wsOAddress" placeholder="Where should we deliver?">
            </div>
            <div class="order-form-group">
                <label>Notes <span style="color:#6c757d;font-weight:400;">(optional)</span></label>
                <textarea id="wsONotes" rows="2" placeholder="Any special requests..."></textarea>
            </div>
            <div class="order-err" id="wsOError"></div>
            <button class="order-submit-btn" id="wsOSubmitBtn" onclick="wsSubmitOrder()">
                <i class="fas fa-paper-plane"></i> Place Order
            </button>
            <button class="order-cancel-btn" onclick="wsCloseOrderModal()">Cancel</button>
        </div>

        <div id="wsOrderSuccess" style="display:none;text-align:center;padding:10px 0 6px;">
            <div style="font-size:3.5rem;margin-bottom:10px;">✅</div>
            <h5 style="color:#16a34a;font-weight:700;">Order Sent!</h5>
            <p style="color:#6c757d;font-size:0.9rem;margin-bottom:20px;">
                Your order has been received. The business owner will reach out to confirm delivery and payment.
            </p>
            <button class="order-submit-btn" onclick="wsCloseOrderModal(true)">
                <i class="fas fa-check"></i> Done
            </button>
        </div>
    </div>
</div>

<script>
// ══ WEBSITE STOREFRONT CART ═══════════════════════════════════════════
var _wsCart = [];
var _wsOrderUrl = "{{ $orderUrl }}";

function wsAddToCart(card) {
    var id    = parseInt(card.dataset.id);
    var name  = card.dataset.name;
    var price = parseFloat(card.dataset.price);
    var image = card.dataset.image || '';
    var existing = _wsCart.find(function(i) { return i.id === id; });
    if (existing) {
        existing.qty++;
    } else {
        _wsCart.push({ id: id, name: name, price: price, image: image, qty: 1 });
    }
    wsRenderCart();
    wsOpenCart();
}

function wsRenderCart() {
    var list   = document.getElementById('wsCartList');
    var empty  = document.getElementById('wsCartEmpty');
    var footer = document.getElementById('wsCartFooter');
    var badge  = document.getElementById('wsCartBadge');
    var fab    = document.getElementById('wsCartFab');

    var totalQty   = _wsCart.reduce(function(s, i) { return s + i.qty; }, 0);
    var totalPrice = _wsCart.reduce(function(s, i) { return s + i.price * i.qty; }, 0);

    // Badge & FAB
    if (totalQty > 0) {
        badge.textContent = totalQty;
        badge.classList.add('show');
        fab.style.display = '';
    } else {
        badge.classList.remove('show');
    }
    document.getElementById('wsCartTotal').textContent = 'KSh ' + totalPrice.toLocaleString();

    if (_wsCart.length === 0) {
        list.innerHTML = '';
        list.appendChild(empty);
        empty.style.display = '';
        footer.style.display = 'none';
        return;
    }

    empty.style.display = 'none';
    footer.style.display = '';

    var html = '';
    _wsCart.forEach(function(item, idx) {
        var thumbHtml = item.image
            ? '<div class="cart-item-thumb"><img src="' + item.image + '" alt="' + item.name + '"></div>'
            : '<div class="cart-item-thumb"><i class="fas fa-box-open" style="color:#b0b8c1;font-size:1.3rem;"></i></div>';
        html += '<div class="cart-item">'
            + thumbHtml
            + '<div class="cart-item-info">'
            + '<div class="cart-item-name">' + item.name + '</div>'
            + '<div class="cart-item-sub">KSh ' + item.price.toLocaleString() + ' each</div>'
            + '<div class="cart-qty-ctrl">'
            + '<button class="cart-qty-btn" onclick="wsQty(' + idx + ',-1)">&#8722;</button>'
            + '<span class="cart-qty-n">' + item.qty + '</span>'
            + '<button class="cart-qty-btn" onclick="wsQty(' + idx + ',1)">&#43;</button>'
            + '</div></div>'
            + '<button class="cart-item-del" onclick="wsRemove(' + idx + ')" title="Remove"><i class="fas fa-trash-alt"></i></button>'
            + '</div>';
    });
    list.innerHTML = html;
    list.appendChild(empty);
}

function wsQty(idx, delta) {
    _wsCart[idx].qty += delta;
    if (_wsCart[idx].qty < 1) _wsCart.splice(idx, 1);
    wsRenderCart();
}

function wsRemove(idx) {
    _wsCart.splice(idx, 1);
    wsRenderCart();
}

function wsOpenCart() {
    document.getElementById('wsCartDrawer').classList.add('open');
    document.getElementById('wsCartOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function wsToggleCart() {
    var open = document.getElementById('wsCartDrawer').classList.toggle('open');
    document.getElementById('wsCartOverlay').classList.toggle('open');
    document.body.style.overflow = open ? 'hidden' : '';
}

function wsOpenOrderForm() {
    if (_wsCart.length === 0) return;
    var total = _wsCart.reduce(function(s, i) { return s + i.price * i.qty; }, 0);
    var html = '';
    _wsCart.forEach(function(item) {
        html += '<div class="order-summary-row"><span>' + item.name + ' &times; ' + item.qty + '</span>'
            + '<span>KSh ' + (item.price * item.qty).toLocaleString() + '</span></div>';
    });
    html += '<div class="order-summary-row"><span>Total</span><span>KSh ' + total.toLocaleString() + '</span></div>';
    document.getElementById('wsOrderSummary').innerHTML = html;
    document.getElementById('wsOrderFormBody').style.display = '';
    document.getElementById('wsOrderSuccess').style.display = 'none';
    document.getElementById('wsOError').style.display = 'none';
    document.getElementById('wsOError').textContent = '';
    document.getElementById('wsOrderModal').classList.add('open');
}

function wsCloseOrderModal(clearCart) {
    document.getElementById('wsOrderModal').classList.remove('open');
    if (clearCart) {
        _wsCart = [];
        wsRenderCart();
        document.getElementById('wsCartDrawer').classList.remove('open');
        document.getElementById('wsCartOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
}

function wsSubmitOrder() {
    var name  = document.getElementById('wsOName').value.trim();
    var phone = document.getElementById('wsOPhone').value.trim();
    var email = document.getElementById('wsOEmail').value.trim();
    var addr  = document.getElementById('wsOAddress').value.trim();
    var notes = document.getElementById('wsONotes').value.trim();
    var errEl = document.getElementById('wsOError');
    errEl.style.display = 'none';

    if (!name)  { errEl.textContent = 'Please enter your name.';         errEl.style.display = ''; return; }
    if (!phone) { errEl.textContent = 'Please enter your phone number.'; errEl.style.display = ''; return; }

    var btn = document.getElementById('wsOSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="img-spinner me-2"></span>Sending order\u2026';

    var items = _wsCart.map(function(i) { return { id: i.id, qty: i.qty }; });

    fetch(_wsOrderUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ name: name, phone: phone, email: email || null,
                               address: addr || null, notes: notes || null, items: items })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Place Order';
        if (data.success) {
            document.getElementById('wsOrderFormBody').style.display = 'none';
            document.getElementById('wsOrderSuccess').style.display = '';
        } else {
            errEl.textContent = data.message || 'Something went wrong. Please try again.';
            errEl.style.display = '';
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Place Order';
        errEl.textContent = 'Network error. Please check your connection and try again.';
        errEl.style.display = '';
    });
}

wsRenderCart();
</script>
@endif
</body>
</html>


