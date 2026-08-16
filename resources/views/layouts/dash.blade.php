<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shopybook Dashboard</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#7b2e2e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Shopybook">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileColor" content="#7b2e2e">
    <meta name="msapplication-tap-highlight" content="no">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/icon-180x180.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('icons/icon-167x167.png') }}">
    
    <!-- Microsoft Tiles -->
    <meta name="msapplication-TileImage" content="{{ asset('icons/icon-144x144.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('icons/icon-310x310.png') }}">
    
    <!-- Splash Screen Images -->
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-2048x2732.png') }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-1668x2388.png') }}" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-1536x2048.png') }}" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-1125x2436.png') }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-1242x2688.png') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-750x1334.png') }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-640x1136.png') }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Jeriah: Playfair + Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair:wght@400;500;600;700;800;900&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Jeriah color palette */
            --primary-color: #7b2e2e;
            --primary-light: #ff511a;
            --primary-dark: #5a2020;
            --secondary-color: #64748b;
            --success-color: #43ba7f;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #ff511a;
            --gray-50: #faf8f7;
            --gray-100: #f7f4f3;
            --gray-200: #e0ded9;
            --gray-300: #d0cbc4;
            --gray-400: #b09b9b;
            --gray-500: #8a7d7d;
            --gray-600: #6b5d5d;
            --gray-700: #4a4040;
            --gray-800: #332b2b;
            --gray-900: #1e1818;
            --white: #ffffff;
            --black: #000000;
            
            /* Light mode colors */
            --bg-primary: #f7f4f3;
            --bg-secondary: #ffffff;
            --bg-tertiary: #faf8f7;
            --text-primary: #332b2b;
            --text-secondary: #6b5d5d;
            --text-muted: #8a7d7d;
            --border-color: #e0ded9;
            --shadow-color: rgba(123, 46, 46, 0.08);
            --card-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
        }
        
        [data-theme="dark"] {
            --bg-primary: #1e1818;
            --bg-secondary: #332b2b;
            --bg-tertiary: #3d3333;
            --text-primary: #f7f4f3;
            --text-secondary: #d0cbc4;
            --text-muted: #b09b9b;
            --border-color: #4a4040;
            --shadow-color: rgba(0, 0, 0, 0.4);
            --card-bg: #332b2b;
            --sidebar-bg: #2a2222;
            --header-bg: #332b2b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            margin: 0;
            padding: 0;
        }
        
        /* Ensure PWA banner doesn't cause spacing when hidden */
        #pwa-install-banner.hidden {
            display: none !important;
            height: 0 !important;
            overflow: hidden !important;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 0;
            padding: 0;
        }
        
        /* Modern Sidebar */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-brand {
            font-family: 'Playfair', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-brand img {
            width: 32px;
            height: 32px;
        }
        
        .sidebar-search {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .search-container {
            position: relative;
        }
        
        .search-container .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .search-input {
            width: 100%;
            padding: 0.5rem 0.75rem 0.5rem 2.25rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(255, 81, 26, 0.12);
        }
        
        .sidebar-nav {
            padding: 1.5rem 0;
        }
        
        .nav-section {
            margin-bottom: 0.5rem;
        }
        
        .nav-section.collapsed .nav-section-content {
            display: none;
        }
        
        .nav-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.7rem 1.25rem 0.5rem;
            margin-bottom: 0.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            border-radius: 0.5rem;
            margin: 0 0.5rem;
            user-select: none;
        }
        
        .nav-section-title:hover {
            color: var(--primary-color);
            background: var(--bg-tertiary);
        }
        
        .nav-section-title i.collapse-icon {
            font-size: 0.75rem;
            transition: transform 0.2s ease;
            color: var(--text-muted);
        }
        
        .nav-section.collapsed .collapse-icon {
            transform: rotate(-90deg);
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            margin: 0 0.75rem;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-link:hover {
            background-color: var(--bg-tertiary);
            color: var(--primary-color);
            transform: translateX(3px);
        }
        
        .nav-link.active {
            background: var(--primary-color);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(123, 46, 46, 0.3);
        }
        
        .nav-link .badge {
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            margin-left: auto;
        }
        
        .nav-link.text-danger {
            color: var(--danger-color) !important;
        }
        
        .nav-link.text-danger:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color) !important;
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        .notification-badge {
            background: var(--danger-color);
            color: var(--white);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.125rem 0.375rem;
            border-radius: 0.75rem;
            margin-left: auto;
        }
        
        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            background: var(--bg-tertiary);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
        }
        
        .user-info h6 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }
        
        .user-info p {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin: 0;
        }
        
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: var(--gray-100);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .theme-toggle:hover {
            background: var(--gray-200);
        }
        
        .toggle-switch {
            width: 40px;
            height: 20px;
            background: var(--gray-300);
            border-radius: 10px;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .toggle-switch.active {
            background: var(--primary-color);
        }
        
        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            background: var(--white);
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: all 0.2s ease;
        }
        
        .toggle-switch.active::after {
            transform: translateX(20px);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--bg-primary);
            display: flex;
            flex-direction: column;
            margin-top: 0;
            padding-top: 0;
        }
        
        .top-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 1px 6px var(--shadow-color);
        }
        
        .top-header > div:first-child {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .page-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.05rem;
            line-height: 1.2;
        }
        
        .page-title {
            font-family: 'Playfair', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            line-height: 1.1;
            padding: 0;
        }
        .page-breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
        }
        .page-breadcrumb a:hover {
            color: var(--text-primary);
        }
        .page-breadcrumb .crumb-app {
            font-weight: 600;
            color: var(--text-primary);
        }
        .page-breadcrumb .crumb-sep {
            opacity: 0.5;
        }

        .nav-all-apps {
            font-weight: 600;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .nav-tabs {
            display: flex;
            gap: 1rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-tab {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }
        
        .nav-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .search-bar {
            position: relative;
            width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            background: var(--card-bg);
            color: var(--text-primary);
            transition: all 0.2s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(255, 81, 26, 0.12);
        }
        
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        
        /* Dashboard Cards */
        .dashboard-grid {
            padding: 2rem;
            flex: 1;
        }
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .kpi-card {
            background: var(--card-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }
        
        .kpi-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .kpi-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            margin: 0;
        }
        
        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        
        .kpi-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .kpi-change.positive {
            color: var(--success-color);
        }
        
        .kpi-change.negative {
            color: var(--danger-color);
        }

        .kpi-change.neutral {
            color: var(--text-muted);
        }
        
        .chart-container {
            background: var(--card-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            height: fit-content;
        }
        
        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }
        
        .chart-legend {
            display: flex;
            gap: 1rem;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-600);
        }
        
        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .legend-dot.investments {
            background: var(--black);
        }
        
        .legend-dot.reinvestment {
            background: var(--primary-color);
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-grid {
                padding: 1rem;
            }
            
            .top-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .header-nav {
                justify-content: space-between;
            }
            
            .search-bar {
                width: 100%;
            }
        }
        
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--gray-600);
            cursor: pointer;
        }
        
        /* Mobile Sidebar Enhancements */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }
        
        .mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--gray-600);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .mobile-toggle:hover {
            background: var(--gray-100);
            color: var(--gray-800);
        }
        
        .mobile-toggle:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* Enhanced Mobile Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.15);
                width: 300px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block;
            }
            
            .top-header {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .nav-tabs {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .nav-tab {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            .search-bar {
                width: 100%;
                max-width: 250px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
            }
            
            .sidebar-header {
                padding: 1.5rem 1rem 1rem;
            }
            
            .sidebar-nav {
                padding: 1rem 0;
            }
            
            .nav-link {
                padding: 0.875rem 1rem;
                margin: 0 0.5rem;
                font-size: 0.9rem;
            }
            
            .nav-section-title {
                padding: 0 1rem 0.5rem;
                font-size: 0.7rem;
            }
            
            .sidebar-footer {
                padding: 1rem;
            }
            
            .user-profile {
                padding: 0.875rem;
            }
            
            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 0.875rem;
            }
            
            .user-info h6 {
                font-size: 0.8rem;
            }
            
            .user-info p {
                font-size: 0.7rem;
            }
            
            .theme-toggle {
                margin-top: 0.75rem;
                padding: 0.375rem;
            }
            
            .toggle-switch {
                width: 36px;
                height: 18px;
            }
            
            .toggle-switch::after {
                width: 14px;
                height: 14px;
            }
            
            .toggle-switch.active::after {
                transform: translateX(18px);
            }
            
            .top-header {
                padding: 0.75rem 1rem;
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .header-nav {
                width: 100%;
                justify-content: space-between;
            }
            
            .page-title {
                font-size: 1.25rem;
                text-align: center;
            }
            
            .nav-tabs {
                justify-content: center;
                width: 100%;
            }
            
            .search-bar {
                width: 100%;
                max-width: none;
            }
            
            .dashboard-grid {
                padding: 1rem;
            }
            
            .kpi-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .kpi-card {
                padding: 1.25rem;
            }
            
            .kpi-value {
                font-size: 1.75rem;
            }
            
            .chart-container {
                padding: 1.25rem;
            }
            
            .chart-header {
                flex-direction: column;
                gap: 0.75rem;
                align-items: flex-start;
            }
            
            .chart-legend {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .legend-item {
                font-size: 0.8rem;
            }
            
            .investor-list {
                max-height: 250px;
            }
            
            .investor-item {
                padding: 0.5rem;
            }
            
            .investor-avatar img {
                width: 32px;
                height: 32px;
            }
            
            .investor-info h6 {
                font-size: 0.8rem;
            }
            
            .investor-info small {
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 480px) {
            .sidebar {
                width: 260px;
            }
            
            .sidebar-header {
                padding: 1.25rem 0.75rem 0.75rem;
            }
            
            .sidebar-brand {
                font-size: 1.25rem;
            }
            
            .sidebar-brand img {
                width: 28px;
                height: 28px;
            }
            
            .nav-link {
                padding: 0.75rem 0.75rem;
                margin: 0 0.25rem;
                font-size: 0.85rem;
            }
            
            .nav-link i {
                width: 18px;
                font-size: 0.9rem;
            }
            
            .sidebar-footer {
                padding: 0.75rem;
            }
            
            .user-profile {
                padding: 0.75rem;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }
            
            .user-info h6 {
                font-size: 0.75rem;
            }
            
            .user-info p {
                font-size: 0.65rem;
            }
            
            .top-header {
                padding: 0.5rem 0.75rem;
            }
            
            .page-title {
                font-size: 1.125rem;
            }
            
            .nav-tabs {
                gap: 0.25rem;
            }
            
            .nav-tab {
                font-size: 0.7rem;
                padding: 0.25rem 0.375rem;
            }
            
            .dashboard-grid {
                padding: 0.75rem;
            }
            
            .kpi-card {
                padding: 1rem;
            }
            
            .kpi-value {
                font-size: 1.5rem;
            }
            
            .chart-container {
                padding: 1rem;
            }
            
            .chart-content {
                height: 250px;
            }
            
            .investor-list {
                max-height: 200px;
            }
            
            .investor-item {
                padding: 0.375rem;
            }
            
            .investor-avatar img {
                width: 28px;
                height: 28px;
            }
            
            .investor-info h6 {
                font-size: 0.75rem;
            }
            
            .investor-info small {
                font-size: 0.65rem;
            }
        }
        
        /* Smooth animations for mobile */
        .sidebar,
        .mobile-overlay,
        .main-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Prevent body scroll when sidebar is open */
        body.sidebar-open {
            overflow: hidden;
        }
        
        /* Enhanced focus states for mobile */
        .nav-link:focus,
        .mobile-toggle:focus,
        .btn:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* Touch-friendly button sizes */
        @media (max-width: 768px) {
            .btn {
                min-height: 44px;
                padding: 0.75rem 1rem;
            }
            
            .btn-sm {
                min-height: 36px;
                padding: 0.5rem 0.75rem;
            }
            
            .nav-link {
                min-height: 48px;
            }
        }
        
        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 8px 12px;
            margin-left: 10px;
            border-radius: 8px;
            background: var(--bg-tertiary);
            color: var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .notification-bell:hover {
            background: rgba(255, 81, 26, 0.12);
            transform: translateY(-1px);
        }
        
        .notification-bell i {
            font-size: 18px;
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Notifications Panel */
        .notifications-panel {
            position: absolute;
            top: 60px;
            right: 20px;
            width: 400px;
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid #e1e8ed;
            z-index: 1000;
            overflow: hidden;
        }
        
        .notifications-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e1e8ed;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notifications-header h6 {
            margin: 0;
            color: #7b2e2e;
            font-weight: 600;
        }
        
        .btn-mark-all-read {
            background: none;
            border: none;
            color: #ff511a;
            font-size: 12px;
            cursor: pointer;
            text-decoration: underline;
        }
        
        .btn-mark-all-read:hover {
            color: #7b2e2e;
        }
        
        .notifications-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f3f4;
            cursor: pointer;
            transition: background 0.2s ease;
            position: relative;
        }
        
        .notification-item:hover {
            background: #f8f9fa;
        }
        
        .notification-item.unread {
            background: rgba(255, 81, 26, 0.06);
            border-left: 3px solid #ff511a;
        }
        
        .notification-item.unread::before {
            content: '';
            position: absolute;
            top: 20px;
            right: 20px;
            width: 8px;
            height: 8px;
            background: #ff511a;
            border-radius: 50%;
        }
        
        .notification-icon {
            display: inline-block;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            margin-right: 12px;
            font-size: 14px;
            color: white;
        }
        
        .notification-icon.success {
            background: #28a745;
        }
        
        .notification-icon.info {
            background: #17a2b8;
        }
        
        .notification-icon.warning {
            background: #ffc107;
            color: #7b2e2e;
        }
        
        .notification-content {
            display: inline-block;
            vertical-align: top;
            width: calc(100% - 47px);
        }
        
        .notification-title {
            font-weight: 600;
            color: #7b2e2e;
            margin-bottom: 4px;
            font-size: 14px;
        }
        
        .notification-message {
            color: #666;
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 6px;
        }
        
        .notification-time {
            color: #999;
            font-size: 11px;
        }
        
        .loading-notifications {
            padding: 30px;
            text-align: center;
            color: #666;
        }
        
        .empty-notifications {
            padding: 40px;
            text-align: center;
            color: #999;
        }
        
        .empty-notifications i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .notifications-footer {
            padding: 10px 20px;
            border-top: 1px solid #e1e8ed;
            text-align: center;
            background: #f8f9fa;
        }
        
        .notifications-footer a {
            color: #ff511a;
            text-decoration: none;
            font-size: 13px;
        }
        
        .notifications-footer a:hover {
            color: #7b2e2e;
        }
        
        @media (max-width: 768px) {
            .notifications-panel {
                width: 350px;
                right: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .notifications-panel {
                width: calc(100vw - 20px);
                right: 10px;
                max-height: 70vh;
            }
        }

        /* ═══════════════════════════════════════
           Google-style App Launcher
        ═══════════════════════════════════════ */
        .app-launcher-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            transition: background 0.2s ease, color 0.2s ease;
            position: relative;
        }
        .app-launcher-btn:hover {
            background: var(--bg-tertiary);
            color: var(--primary-color);
        }
        .app-launcher-btn i {
            font-size: 1.2rem;
        }

        .app-launcher-panel {
            position: fixed;
            top: 64px;
            right: 16px;
            width: 360px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18);
            z-index: 1100;
            padding: 16px 12px 20px;
            display: none;
            animation: launcherIn 0.18s ease;
        }
        @keyframes launcherIn {
            from { opacity: 0; transform: translateY(-10px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)  scale(1); }
        }
        .app-launcher-panel.open {
            display: block;
        }
        .app-launcher-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8px 14px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 14px;
        }
        .app-launcher-header span {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .app-launcher-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            line-height: 1;
            transition: background 0.2s;
        }
        .app-launcher-close:hover { background: var(--bg-tertiary); }

        .app-launcher-search {
            width: 100%;
            padding: 7px 12px;
            margin-bottom: 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 0.85rem;
        }
        .app-launcher-search:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .app-launcher-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
        }
        .app-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 12px 6px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-primary);
            transition: background 0.2s ease, transform 0.15s ease;
            cursor: pointer;
        }
        .app-tile:hover {
            background: var(--bg-tertiary);
            transform: translateY(-2px);
            color: var(--text-primary);
        }
        .app-tile-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            flex-shrink: 0;
        }
        .app-tile-label {
            font-size: 0.7rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.3;
            color: var(--text-secondary);
        }
        .app-tile.active .app-tile-icon {
            box-shadow: 0 0 0 2px var(--primary-color);
        }

        /* Top-right user avatar */
        .topbar-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s;
            flex-shrink: 0;
        }
        .topbar-user-avatar:hover {
            border-color: var(--primary-light);
        }

        .topbar-user-dropdown {
            position: fixed;
            top: 64px;
            right: 16px;
            width: 240px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            z-index: 1100;
            padding: 8px 0;
            display: none;
            animation: launcherIn 0.18s ease;
        }
        .topbar-user-dropdown.open { display: block; }
        .topbar-user-dropdown .user-dd-header {
            padding: 12px 16px 8px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 6px;
        }
        .topbar-user-dropdown .user-dd-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        .topbar-user-dropdown .user-dd-email {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .topbar-user-dropdown a,
        .topbar-user-dropdown button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background 0.15s;
        }
        .topbar-user-dropdown a:hover,
        .topbar-user-dropdown button:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }
        .topbar-user-dropdown .divider {
            height: 1px;
            background: var(--border-color);
            margin: 4px 0;
        }

        /* ─── Clean Sidebar tweaks ─── */
        .sidebar-nav-group-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            padding: 1rem 1.5rem 0.4rem;
        }
        .sidebar-divider {
            height: 1px;
            background: var(--border-color);
            margin: 0.75rem 1.5rem;
        }

        @media (max-width: 480px) {
            .app-launcher-panel {
                width: calc(100vw - 20px);
                right: 10px;
            }
            .app-launcher-grid {
                grid-template-columns: repeat(3,1fr);
            }
        }

        /* ═══════════════════════════════════════
           Jeriah Bootstrap overrides (global)
        ═══════════════════════════════════════ */
        .btn-primary {
            background-color: #ff511a;
            border-color: #ff511a;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #e8450f;
            border-color: #e8450f;
        }
        .btn-outline-primary {
            color: #7b2e2e;
            border-color: #7b2e2e;
        }
        .btn-outline-primary:hover {
            background-color: #7b2e2e;
            border-color: #7b2e2e;
        }
        .text-primary {
            color: #7b2e2e !important;
        }
        a {
            color: #ff511a;
        }
        a:hover {
            color: #e8450f;
        }
        .card {
            border-color: var(--border-color);
            border-radius: 0.75rem;
        }
        .card-header {
            background-color: var(--bg-tertiary);
            border-bottom-color: var(--border-color);
        }
        .bg-primary {
            background-color: #7b2e2e !important;
        }
        .bg-info {
            background-color: #ff511a !important;
        }
        .alert-info {
            background-color: rgba(255, 81, 26, 0.08);
            border-color: rgba(255, 81, 26, 0.3);
            color: #7b2e2e;
        }
        .alert-info a {
            color: #7b2e2e;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: #ff511a;
            box-shadow: 0 0 0 0.2rem rgba(255, 81, 26, 0.15);
        }
        .page-link {
            color: #7b2e2e;
        }
        .page-item.active .page-link {
            background-color: #7b2e2e;
            border-color: #7b2e2e;
        }
        .nav-tabs .nav-link.active {
            color: #7b2e2e;
            border-bottom-color: #ff511a;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair', serif;
        }
        .modal-header {
            background-color: var(--bg-tertiary);
        }
        .modal-title {
            color: var(--primary-color);
        }
    </style>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QEHQPSK885"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QEHQPSK885');
</script>
<body>
    <!-- PWA Installation Banner -->
    <div id="pwa-install-banner" class="hidden fixed top-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 z-[9999] shadow-lg" style="display: none;">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-download text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">📱 Install Shopybook App</h3>
                    <p class="text-sm opacity-90">Get quick access to your business dashboard</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button id="pwa-install-btn" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                    <i class="fas fa-download me-2"></i>Install Now
                </button>
                <button id="pwa-dismiss-btn" class="text-white opacity-70 hover:opacity-100 transition-opacity">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('index') }}" class="sidebar-brand">
                <img src="{{ asset('img/logo.png') }}" alt="Shopybook">
                <span>Shopybook</span>
            </a>
        </div>
        
        <!-- Sidebar Search -->
        <div class="sidebar-search">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search menu..." id="sidebarSearch">
            </div>
        </div>

        @php
            // Get enabled apps for the current business
            $business = auth()->user()->business ?? null;
            $enabledAppSlugs = [];
            
            if ($business) {
                $enabledAppSlugs = \App\Models\BusinessApp::where('business_id', $business->id)
                    ->where('is_active', true)
                    ->pluck('app_slug')
                    ->toArray();
            }
            
            // If no apps configured yet, show everything (first-time users)
            $showAll = empty($enabledAppSlugs);
            
            // Helper function to check if app section should be shown
            function shouldShowApp($appSlug, $enabledApps, $showAll) {
                return $showAll || in_array($appSlug, $enabledApps);
            }
        @endphp
        
        @php
            // ── Permission helpers (used by both sidebar and app launcher) ──────────
            $__isOwner  = auth()->user()->business &&
                          auth()->user()->business->user_id === auth()->id();
            $__membership = $__isOwner ? null :
                (auth()->user()->relationLoaded('activeMembership')
                    ? auth()->user()->getRelation('activeMembership')
                    : auth()->user()->activeMembership);
            $__perms = $__isOwner ? [] : ($__membership?->permissions ?? []);
            // $__can('module') → true for owners always; true for members if the module is granted
            $__can = fn(string $m): bool => $__isOwner || in_array($m, $__perms);

            // ── Context-aware navigation (Odoo-style) ───────────────────────────────
            // Resolve which "app" the current route belongs to so the sidebar and the
            // topbar breadcrumb can adapt to it. See config/navigation.php.
            $__navActive = fn(string $patterns): bool => request()->routeIs(...explode('|', $patterns));

            $navApps = config('navigation.apps', []);
            $activeAppKey = null;
            foreach ($navApps as $__key => $__app) {
                foreach (($__app['match'] ?? []) as $__pattern) {
                    if (request()->routeIs($__pattern)) { $activeAppKey = $__key; break 2; }
                }
            }
            $activeApp = $activeAppKey ? $navApps[$activeAppKey] : null;

            $activeItemLabel = null;
            if ($activeApp) {
                foreach ($activeApp['items'] as $__it) {
                    if ($__navActive($__it['active'] ?? $__it['route'])) { $activeItemLabel = $__it['label']; break; }
                }
            }
        @endphp

        @php
            // Determine which sections are currently active (open) by route
            $activeSection = null;
            $sections = [
                'sales'    => request()->routeIs('sales.*', 'returns.*'),
                'products' => request()->routeIs('products.*', 'product-conversions.*', 'ocr.*'),
                'services' => request()->routeIs('services.*', 'service-bookings.*'),
                'staff'    => request()->routeIs('staff.*', 'salary-advances.*'),
                'suppliers'=> request()->routeIs('suppliers.*'),
                'finance'  => request()->routeIs('costs.*', 'tax.*'),
                'reports'  => request()->routeIs('reports.*', 'business.analysis.*'),
                'growth'   => request()->routeIs('marketing.*', 'website.*', 'testimonials.*'),
                'ai'       => request()->routeIs('ai-comm.*', 'ai-content.*'),
                'system'   => request()->routeIs('business.profile', 'business.edit', 'team.*', 'settings.*', 'pwa.*', 'apps.*'),
                'danger'   => false,
            ];
            foreach ($sections as $key => $isActive) {
                if ($isActive) { $activeSection = $key; break; }
            }
        @endphp

        <nav class="sidebar-nav" id="sidebarNav">

            {{-- Dashboard always at top --}}
            <div class="nav-item mb-2">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="sidebar-divider"></div>

            {{-- Sales --}}
            @php $isOpen = $activeSection === 'sales'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Sales</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('pos'))
                    <div class="nav-item">
                        <a href="{{ route('sales.pos') }}" class="nav-link {{ request()->routeIs('sales.pos') ? 'active' : '' }}">
                            <i class="fas fa-cash-register"></i>
                            <span>Point of Sale</span>
                            <span class="badge bg-success ms-auto" style="font-size:0.6rem;">Live</span>
                        </a>
                    </div>
                    @endif
                    @if($__can('orders'))
                    <div class="nav-item">
                        <a href="{{ route('sales.orders') }}" class="nav-link {{ request()->routeIs('sales.orders*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </div>
                    @endif
                    @if($__can('customers'))
                    <div class="nav-item">
                        <a href="{{ route('sales.customers') }}" class="nav-link {{ request()->routeIs('sales.customers*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Customers</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('sales.customer-debts') }}" class="nav-link {{ request()->routeIs('sales.customer-debts') ? 'active' : '' }}">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>Customer Debts</span>
                        </a>
                    </div>
                    @endif
                    @if($__can('suppliers'))
                    <div class="nav-item">
                        <a href="{{ route('sales.supplier-debts') }}" class="nav-link {{ request()->routeIs('sales.supplier-debts*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice"></i>
                            <span>Supplier Debts</span>
                        </a>
                    </div>
                    @endif
                    @if($__can('orders'))
                    <div class="nav-item">
                        <a href="{{ route('sales.credit-notes.index') }}" class="nav-link {{ request()->routeIs('sales.credit-note*') ? 'active' : '' }}">
                            <i class="fas fa-receipt"></i>
                            <span>Credit Notes</span>
                        </a>
                    </div>
                    @endif
                    @if($__can('returns'))
                    <div class="nav-item">
                        <a href="{{ route('returns.index') }}" class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                            <i class="fas fa-undo"></i>
                            <span>Returns</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Products --}}
            @php $isOpen = $activeSection === 'products'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Products</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('products'))
                    <div class="nav-item">
                        <a href="{{ route('products.index') }}" class="nav-link {{ $__navActive('products.index|products.show|products.create|products.edit|products.quick-create|products.bulk-import*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('products.inventory') }}" class="nav-link {{ request()->routeIs('products.inventory*') ? 'active' : '' }}">
                            <i class="fas fa-warehouse"></i>
                            <span>Inventory</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('products.receive') }}" class="nav-link {{ request()->routeIs('products.receive*') ? 'active' : '' }}">
                            <i class="fas fa-dolly"></i>
                            <span>Receive Stock</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('product-conversions.index') }}" class="nav-link {{ request()->routeIs('product-conversions.*') ? 'active' : '' }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Conversions</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('ocr.index') }}" class="nav-link {{ request()->routeIs('ocr.*') ? 'active' : '' }}">
                            <i class="fas fa-camera"></i>
                            <span>OCR Scan</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Services --}}
            @php $isOpen = $activeSection === 'services'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Services</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('services'))
                    <div class="nav-item">
                        <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                            <i class="fas fa-concierge-bell"></i>
                            <span>Services</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('service-bookings.index') }}" class="nav-link {{ request()->routeIs('service-bookings.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Bookings</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Staff --}}
            @php $isOpen = $activeSection === 'staff'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Staff</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('staff'))
                    <div class="nav-item">
                        <a href="{{ route('staff.index') }}" class="nav-link {{ $__navActive('staff.index|staff.show|staff.create|staff.edit|staff.salary-details') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            <span>Staff</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('salary-advances.index') }}" class="nav-link {{ request()->routeIs('salary-advances.*') ? 'active' : '' }}">
                            <i class="fas fa-money-check-alt"></i>
                            <span>Salary Advances</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('staff.salary-calculations') }}" class="nav-link {{ request()->routeIs('staff.salary-calculations') ? 'active' : '' }}">
                            <i class="fas fa-calculator"></i>
                            <span>Salary Calculations</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Suppliers --}}
            @php $isOpen = $activeSection === 'suppliers'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Suppliers</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('suppliers'))
                    <div class="nav-item">
                        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <i class="fas fa-truck"></i>
                            <span>Suppliers</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Finance --}}
            @php $isOpen = $activeSection === 'finance'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Finance</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('expenses'))
                    <div class="nav-item">
                        <a href="{{ route('costs.index') }}" class="nav-link {{ request()->routeIs('costs.*') ? 'active' : '' }}">
                            <i class="fas fa-receipt"></i>
                            <span>Expenses</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('tax.settings') }}" class="nav-link {{ request()->routeIs('tax.settings') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Tax Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('tax.reports') }}" class="nav-link {{ $__navActive('tax.reports|tax.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Tax Reports</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Reports & Analytics --}}
            @php $isOpen = $activeSection === 'reports'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Reports & Analytics</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('reports'))
                    <div class="nav-item">
                        <a href="{{ route('business.analysis.index') }}" class="nav-link {{ request()->routeIs('business.analysis.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('business.analysis.financial') }}" class="nav-link {{ request()->routeIs('business.analysis.financial') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i>
                            <span>Financials</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Growth --}}
            @php $isOpen = $activeSection === 'growth'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Growth</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('marketing'))
                    <div class="nav-item">
                        <a href="{{ route('marketing.social-media') }}" class="nav-link {{ request()->routeIs('marketing.*') ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i>
                            <span>Marketing</span>
                        </a>
                    </div>
                    @endif
                    @if($__can('website'))
                    <div class="nav-item">
                        <a href="{{ route('website.builder.index') }}" class="nav-link {{ request()->routeIs('website.*') ? 'active' : '' }}">
                            <i class="fas fa-globe"></i>
                            <span>Website Builder</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('testimonials.owner.index') }}" class="nav-link {{ request()->routeIs('testimonials.owner.*') ? 'active' : '' }}">
                            <i class="fas fa-star"></i>
                            <span>Reviews</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- AI Tools --}}
            @php $isOpen = $activeSection === 'ai'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>AI Tools</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__can('ai'))
                    <div class="nav-item">
                        <a href="{{ route('ai-comm.chat') }}" class="nav-link {{ request()->routeIs('ai-comm.*') ? 'active' : '' }}">
                            <i class="fas fa-robot"></i>
                            <span>AI Assistant</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('ai-content.index') }}" class="nav-link {{ request()->routeIs('ai-content.*') ? 'active' : '' }}">
                            <i class="fas fa-magic"></i>
                            <span>AI Content</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- System --}}
            @php $isOpen = $activeSection === 'system'; @endphp
            <div class="nav-section {{ $isOpen ? '' : 'collapsed' }}">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>System</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    @if($__isOwner)
                    <div class="nav-item">
                        <a href="{{ route('business.profile') }}" class="nav-link {{ request()->routeIs('business.profile', 'business.edit') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>Business Profile</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('team.index') }}" class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>Team & Access</span>
                        </a>
                    </div>
                    @endif
                    @if($__isOwner || $__can('settings'))
                    <div class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                    @endif
                    <div class="nav-item">
                        <a href="{{ route('pwa.install-guide') }}" class="nav-link {{ request()->routeIs('pwa.*') ? 'active' : '' }}">
                            <i class="fas fa-download"></i>
                            <span>Install App</span>
                            <span class="badge bg-info ms-auto" style="font-size:0.6rem;">PWA</span>
                        </a>
                    </div>
                    @if($__can('settings'))
                    <div class="nav-item">
                        <a href="{{ route('apps.index') }}" class="nav-link {{ request()->routeIs('apps.*') ? 'active' : '' }}">
                            <i class="fas fa-th-large"></i>
                            <span>Manage Apps</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Danger zone --}}
            @if($__isOwner)
            <div class="sidebar-divider"></div>
            <div class="nav-section">
                <div class="nav-section-title" onclick="toggleSection(this)">
                    <span>Account</span>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="nav-section-content">
                    <div class="nav-item">
                        <a href="#" onclick="initiateBusinessDeletion()" class="nav-link text-danger">
                            <i class="fas fa-trash-alt"></i>
                            <span>Delete Business</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="user-info">
                    <h6>{{ auth()->user()->name }}</h6>
                    <p>{{ auth()->user()->email }}</p>
                </div>
            </div>
            
            <div class="theme-toggle" onclick="toggleTheme()">
                <i class="fas fa-sun"></i>
                <div class="toggle-switch" id="themeToggle"></div>
                <i class="fas fa-moon"></i>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-header">
            <div>
                <nav class="page-breadcrumb" aria-label="breadcrumb">
                    <a href="{{ route('dashboard') }}" class="crumb-home"><i class="fas fa-home"></i></a>
                    @if($activeApp)
                    <span class="crumb-sep">/</span>
                    <a href="{{ route($activeApp['items'][0]['route']) }}" class="crumb-app">{{ $activeApp['label'] }}</a>
                    @endif
                </nav>
                <h1 class="page-title">@yield('title', $activeItemLabel ?? ($activeApp['label'] ?? 'Dashboard'))</h1>
                <!-- <ul class="nav-tabs">
                    <li><a href="#" class="nav-tab active">Summary</a></li>
                    <li><a href="#" class="nav-tab">Statistic</a></li>
                    <li><a href="#" class="nav-tab">Overview</a></li>
                    <li><a href="#" class="nav-tab">Account</a></li>
                </ul> -->
            </div>
            
            <div class="header-nav">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="search-bar">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>
                
                <!-- Notification Bell -->
                <div class="notification-bell" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
                </div>

                <!-- App Launcher (Google-style waffle) -->
                <button class="app-launcher-btn" id="appLauncherBtn" onclick="toggleAppLauncher(event)" title="Apps">
                    <i class="fas fa-th"></i>
                </button>

                <!-- Topbar User Avatar -->
                <div class="topbar-user-avatar" id="topbarUserAvatar" onclick="toggleUserDropdown(event)" title="Account">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </div>
        
        <div class="dashboard-grid">
            @yield('content')
        </div>
    </div>
    
    <!-- ═══════════════════════════════════════════════════
         Google-style App Launcher Panel
    ════════════════════════════════════════════════════ -->
    <div id="appLauncherPanel" class="app-launcher-panel">
        <div class="app-launcher-header">
            <span>Shopybook Apps</span>
            <button class="app-launcher-close" onclick="closeAppLauncher()"><i class="fas fa-times"></i></button>
        </div>
        <input type="text" class="app-launcher-search" id="appLauncherSearch" placeholder="Search apps…" oninput="filterApps(this.value)">
        <div class="app-launcher-grid" id="appLauncherGrid">
            @if($__can('pos'))
            <a href="{{ route('sales.pos') }}" class="app-tile {{ request()->routeIs('sales.pos') ? 'active' : '' }}" data-label="Point of Sale">
                <div class="app-tile-icon" style="background:#22c55e;"><i class="fas fa-cash-register"></i></div>
                <span class="app-tile-label">Point of Sale</span>
            </a>
            @endif
            @if($__can('products'))
            <a href="{{ route('products.index') }}" class="app-tile {{ request()->routeIs('products.*') ? 'active' : '' }}" data-label="Products">
                <div class="app-tile-icon" style="background:#3b82f6;"><i class="fas fa-box"></i></div>
                <span class="app-tile-label">Products</span>
            </a>
            <a href="{{ route('products.inventory') }}" class="app-tile {{ request()->routeIs('products.inventory') ? 'active' : '' }}" data-label="Inventory">
                <div class="app-tile-icon" style="background:#8b5cf6;"><i class="fas fa-warehouse"></i></div>
                <span class="app-tile-label">Inventory</span>
            </a>
            <a href="{{ route('product-conversions.index') }}" class="app-tile {{ request()->routeIs('product-conversions.*') ? 'active' : '' }}" data-label="Unit Conversions">
                <div class="app-tile-icon" style="background:#475569;"><i class="fas fa-exchange-alt"></i></div>
                <span class="app-tile-label">Conversions</span>
            </a>
            <a href="{{ route('ocr.index') }}" class="app-tile {{ request()->routeIs('ocr.*') ? 'active' : '' }}" data-label="OCR Scanner">
                <div class="app-tile-icon" style="background:#0369a1;"><i class="fas fa-camera"></i></div>
                <span class="app-tile-label">OCR Scan</span>
            </a>
            @endif
            @if($__can('orders'))
            <a href="{{ route('sales.orders') }}" class="app-tile {{ request()->routeIs('sales.orders*') ? 'active' : '' }}" data-label="Orders">
                <div class="app-tile-icon" style="background:#f59e0b;"><i class="fas fa-shopping-cart"></i></div>
                <span class="app-tile-label">Orders</span>
            </a>
            @endif
            @if($__can('returns'))
            <a href="{{ route('returns.index') }}" class="app-tile {{ request()->routeIs('returns.*') ? 'active' : '' }}" data-label="Returns">
                <div class="app-tile-icon" style="background:#ef4444;"><i class="fas fa-undo"></i></div>
                <span class="app-tile-label">Returns</span>
            </a>
            @endif
            @if($__can('customers'))
            <a href="{{ route('sales.customers') }}" class="app-tile {{ request()->routeIs('sales.customers*') ? 'active' : '' }}" data-label="Customers">
                <div class="app-tile-icon" style="background:#06b6d4;"><i class="fas fa-users"></i></div>
                <span class="app-tile-label">Customers</span>
            </a>
            @endif
            @if($__can('suppliers'))
            <a href="{{ route('suppliers.index') }}" class="app-tile {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" data-label="Suppliers">
                <div class="app-tile-icon" style="background:#64748b;"><i class="fas fa-truck"></i></div>
                <span class="app-tile-label">Suppliers</span>
            </a>
            @endif
            @if($__can('services'))
            <a href="{{ route('services.index') }}" class="app-tile {{ request()->routeIs('services.*') ? 'active' : '' }}" data-label="Services">
                <div class="app-tile-icon" style="background:#10b981;"><i class="fas fa-concierge-bell"></i></div>
                <span class="app-tile-label">Services</span>
            </a>
            <a href="{{ route('service-bookings.index') }}" class="app-tile {{ request()->routeIs('service-bookings.*') ? 'active' : '' }}" data-label="Bookings">
                <div class="app-tile-icon" style="background:#0ea5e9;"><i class="fas fa-calendar-check"></i></div>
                <span class="app-tile-label">Bookings</span>
            </a>
            @endif
            @if($__can('staff'))
            <a href="{{ route('staff.index') }}" class="app-tile {{ request()->routeIs('staff.*') ? 'active' : '' }}" data-label="Staff">
                <div class="app-tile-icon" style="background:#ff511a;"><i class="fas fa-user-tie"></i></div>
                <span class="app-tile-label">Staff</span>
            </a>
            @endif
            @if($__can('expenses'))
            <a href="{{ route('costs.index') }}" class="app-tile {{ request()->routeIs('costs.*') ? 'active' : '' }}" data-label="Expenses">
                <div class="app-tile-icon" style="background:#f97316;"><i class="fas fa-receipt"></i></div>
                <span class="app-tile-label">Expenses</span>
            </a>
            <a href="{{ route('tax.settings') }}" class="app-tile {{ request()->routeIs('tax.*') ? 'active' : '' }}" data-label="Tax">
                <div class="app-tile-icon" style="background:#84cc16;"><i class="fas fa-file-invoice-dollar"></i></div>
                <span class="app-tile-label">Tax</span>
            </a>
            @endif
            @if($__can('reports'))
            <a href="{{ route('reports.index') }}" class="app-tile {{ request()->routeIs('reports.*') ? 'active' : '' }}" data-label="Reports">
                <div class="app-tile-icon" style="background:#7b2e2e;"><i class="fas fa-file-chart-line"></i></div>
                <span class="app-tile-label">Reports</span>
            </a>
            <a href="{{ route('business.analysis.financial') }}" class="app-tile {{ request()->routeIs('business.analysis.financial') ? 'active' : '' }}" data-label="Financials">
                <div class="app-tile-icon" style="background:#15803d;"><i class="fas fa-chart-pie"></i></div>
                <span class="app-tile-label">Financials</span>
            </a>
            @endif
            @if($__can('website'))
            <a href="{{ route('website.builder.index') }}" class="app-tile {{ request()->routeIs('website.builder.*') ? 'active' : '' }}" data-label="Website Builder">
                <div class="app-tile-icon" style="background:#6366f1;"><i class="fas fa-globe"></i></div>
                <span class="app-tile-label">Website Builder</span>
            </a>
            <a href="{{ route('testimonials.owner.index') }}" class="app-tile {{ request()->routeIs('testimonials.owner.*') ? 'active' : '' }}" data-label="Reviews">
                <div class="app-tile-icon" style="background:#f59e0b;"><i class="fas fa-star"></i></div>
                <span class="app-tile-label">Reviews</span>
                @php $pendingReviews = \App\Models\Testimonial::forBusiness(auth()->user()->business?->id ?? '')->pending()->count(); @endphp
                @if($pendingReviews > 0)
                    <span class="badge bg-danger" style="position:absolute; top:-4px; right:-4px; font-size:.65rem; padding:2px 5px; border-radius:10px;">{{ $pendingReviews }}</span>
                @endif
            </a>
            @endif
            @if($__can('marketing'))
            <a href="{{ route('marketing.social-media') }}" class="app-tile {{ request()->routeIs('marketing.*') ? 'active' : '' }}" data-label="Marketing">
                <div class="app-tile-icon" style="background:#ec4899;"><i class="fas fa-bullhorn"></i></div>
                <span class="app-tile-label">Marketing</span>
            </a>
            @endif
            @if($__can('ai'))
            <a href="{{ route('ai-comm.chat') }}" class="app-tile {{ request()->routeIs('ai-comm.*') ? 'active' : '' }}" data-label="AI Assistant">
                <div class="app-tile-icon" style="background:#ff511a; color:#7b2e2e;"><i class="fas fa-robot"></i></div>
                <span class="app-tile-label">AI Assistant</span>
            </a>
            <a href="{{ route('ai-content.index') }}" class="app-tile {{ request()->routeIs('ai-content.*') ? 'active' : '' }}" data-label="AI Content">
                <div class="app-tile-icon" style="background:#a78bfa;"><i class="fas fa-magic"></i></div>
                <span class="app-tile-label">AI Content</span>
            </a>
            @endif
            @if($__can('settings'))
            <a href="{{ route('apps.index') }}" class="app-tile {{ request()->routeIs('apps.*') ? 'active' : '' }}" data-label="Manage Apps">
                <div class="app-tile-icon" style="background:linear-gradient(135deg,#7b2e2e,#ff511a);"><i class="fas fa-th-large"></i></div>
                <span class="app-tile-label">Manage Apps</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Topbar User Dropdown -->
    <div id="topbarUserDropdown" class="topbar-user-dropdown">
        <div class="user-dd-header">
            <div class="user-dd-name">{{ auth()->user()->name }}</div>
            <div class="user-dd-email">{{ auth()->user()->email }}</div>
        </div>
        <a href="{{ route('business.profile') }}"><i class="fas fa-building"></i> Business Profile</a>
        <a href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> Settings</a>
        <div class="divider"></div>
        <a href="{{ route('pwa.install-guide') }}"><i class="fas fa-download"></i> Install App</a>
        <div class="divider"></div>
        <button onclick="document.getElementById('topbar-logout-form').submit()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
        <form id="topbar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>

    <!-- Notifications Panel -->
    <div id="notificationsPanel" class="notifications-panel" style="display: none;">
        <div class="notifications-header">
            <h6>Notifications</h6>
            <button onclick="markAllNotificationsAsRead()" class="btn-mark-all-read">Mark all as read</button>
        </div>
        <div id="notificationsList" class="notifications-list">
            <div class="loading-notifications">
                <i class="fas fa-spinner fa-spin"></i>
                Loading notifications...
            </div>
        </div>
        <div class="notifications-footer">
            <a href="#" onclick="loadMoreNotifications()">Load more</a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- PWA JavaScript -->
    <script src="{{ asset('js/pwa.js') }}"></script>
    
    <!-- PWA Install Guide -->
    @include('components.pwa-install-guide')
    
    <script>
        // Check PWA installation status and hide elements if already installed
        document.addEventListener('DOMContentLoaded', function() {
            // Function to hide all PWA installation elements
            function hidePWAInstallElements() {
                // Hide PWA banner
                const pwaBanner = document.getElementById('pwa-install-banner');
                if (pwaBanner) {
                    pwaBanner.classList.add('hidden');
                }
                
                // Hide sidebar install button
                const sidebarInstallBtn = document.getElementById('sidebar-install-app');
                if (sidebarInstallBtn) {
                    sidebarInstallBtn.style.display = 'none';
                }
                
                // Hide PWA install guide modal trigger
                const pwaInstallGuideTriggers = document.querySelectorAll('[onclick*="showPWAInstallGuide"]');
                pwaInstallGuideTriggers.forEach(trigger => {
                    trigger.style.display = 'none';
                });
            }
            
            // Check if PWA is already installed
            if (window.matchMedia('(display-mode: standalone)').matches || 
                window.navigator.standalone === true) {
                hidePWAInstallElements();
            }
            
            // Also check periodically in case the status changes
            setInterval(() => {
                if (window.matchMedia('(display-mode: standalone)').matches || 
                    window.navigator.standalone === true) {
                    hidePWAInstallElements();
                }
            }, 5000);
        });
    </script>

    <!-- Collapsible Sidebar Sections -->
    <script>
        function toggleSection(element) {
            const section = element.closest('.nav-section');
            section.classList.toggle('collapsed');
        }
    </script>
    
    <!-- Business Deletion Modal -->
    <div id="businessDeletionModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Delete Business
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> This action cannot be undone. All business data, products, sales records, and settings will be permanently deleted.
                    </div>
                    <p>To proceed with business deletion, you will need to verify your identity via email verification.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="businessDeletionForm" method="POST" action="{{ route('business.initiate-deletion') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i> Proceed with Deletion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Chat Widget -->
    <div id="ai-chat-widget" class="ai-chat-widget">
        <!-- Floating Chat Button -->
        <div id="ai-chat-button" class="ai-chat-button" onclick="toggleAIChat()">
            <i class="fas fa-robot"></i>
            <span class="ai-chat-badge" id="ai-chat-badge" style="display: none;">0</span>
        </div>
        
        <!-- Chat Interface -->
        <div id="ai-chat-interface" class="ai-chat-interface" style="display: none;">
            <div class="ai-chat-header">
                <div class="ai-chat-title">
                    <i class="fas fa-robot me-2"></i>
                    AI Business Assistant
                </div>
                <button class="ai-chat-close" onclick="toggleAIChat()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="ai-chat-messages" id="ai-chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            
            <div class="ai-chat-input-container">
                <div class="ai-chat-input-row">
                    <input type="text" id="ai-message-input" class="ai-chat-input" placeholder="Ask me anything about your business..." maxlength="1000">
                    <button class="ai-chat-send-btn" id="ai-send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                
                <div class="ai-chat-business-select">
                    <select id="ai-business-select" class="form-select form-select-sm">
                        <option value="">Select Business</option>
                        @if(Auth::user() && Auth::user()->businesses && Auth::user()->businesses->count() > 0)
                            @foreach(Auth::user()->businesses as $business)
                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>No businesses available</option>
                        @endif
                    </select>
                </div>
                
                <div class="ai-chat-suggestions" id="ai-suggestions">
                    <small class="text-muted">Quick suggestions:</small>
                    <div class="ai-suggestion-buttons">
                        <button class="btn btn-sm btn-outline-secondary ai-suggestion-btn" data-message="How are my sales performing?">Sales</button>
                        <button class="btn btn-sm btn-outline-secondary ai-suggestion-btn" data-message="What market trends should I know?">Trends</button>
                        <button class="btn btn-sm btn-outline-secondary ai-suggestion-btn" data-message="Give me business recommendations">Advice</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* AI Chat Widget Styles */
        .ai-chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            font-family: 'Poppins', sans-serif;
        }
        
        .ai-chat-button {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .ai-chat-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        
        .ai-chat-button i {
            font-size: 1.5rem;
        }
        
        .ai-chat-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .ai-chat-interface {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 400px;
            height: 500px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .ai-chat-header {
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 12px 12px 0 0;
        }
        
        .ai-chat-title {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .ai-chat-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }
        
        .ai-chat-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .ai-chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: var(--bg-primary);
        }
        
        .ai-message {
            margin-bottom: 15px;
            display: flex;
        }
        
        .ai-message.user {
            justify-content: flex-end;
        }
        
        .ai-message.ai {
            justify-content: flex-start;
        }
        
        .ai-message-content {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            word-wrap: break-word;
            font-size: 0.875rem;
        }
        
        .ai-message.user .ai-message-content {
            background-color: var(--primary-color);
            color: white;
        }
        
        .ai-message.ai .ai-message-content {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        .ai-message-time {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 5px;
        }
        
        .ai-chat-input-container {
            padding: 15px;
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
        }
        
        .ai-chat-input-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .ai-chat-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.875rem;
            background: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .ai-chat-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(255, 81, 26, 0.12);
        }
        
        .ai-chat-send-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .ai-chat-send-btn:hover {
            background: var(--primary-dark);
        }
        
        .ai-chat-business-select {
            margin-bottom: 10px;
        }
        
        .ai-chat-business-select select {
            font-size: 0.8rem;
            padding: 6px 10px;
        }
        
        .ai-chat-suggestions {
            margin-top: 10px;
        }
        
        .ai-chat-suggestions small {
            font-size: 0.75rem;
        }
        
        .ai-suggestion-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }
        
        .ai-suggestion-btn {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .ai-chat-interface {
                width: calc(100vw - 40px);
                height: 60vh;
                bottom: 80px;
                right: 0;
            }
            
            .ai-chat-widget {
                bottom: 10px;
                right: 10px;
            }
            
            .ai-chat-button {
                width: 50px;
                height: 50px;
            }
            
            .ai-chat-button i {
                font-size: 1.25rem;
            }
        }
        
        /* Loading animation */
        .ai-loading {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .ai-loading .spinner-border {
            width: 16px;
            height: 16px;
        }
        
        /* AI Notification Styles */
        .ai-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.3s ease;
            max-width: 350px;
        }
        
        .ai-notification-content {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            gap: 8px;
        }
        
        .ai-notification-content i {
            color: var(--primary-color);
        }
        
        .ai-notification-content span {
            flex: 1;
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        
        .ai-notification-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: color 0.2s ease;
        }
        
        .ai-notification-close:hover {
            color: var(--text-primary);
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
    
    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
        }

        // ── App Launcher ───────────────────────────────────────────
        function toggleAppLauncher(e) {
            e.stopPropagation();
            const panel = document.getElementById('appLauncherPanel');
            const userDd = document.getElementById('topbarUserDropdown');
            userDd.classList.remove('open');
            const isOpen = panel.classList.toggle('open');
            if (isOpen) {
                document.getElementById('appLauncherSearch').focus();
            }
        }
        function closeAppLauncher() {
            document.getElementById('appLauncherPanel').classList.remove('open');
        }
        function filterApps(q) {
            q = q.toLowerCase();
            document.querySelectorAll('#appLauncherGrid .app-tile').forEach(tile => {
                const label = tile.dataset.label.toLowerCase();
                tile.style.display = label.includes(q) ? '' : 'none';
            });
        }

        // ── Topbar User Dropdown ───────────────────────────────────
        function toggleUserDropdown(e) {
            e.stopPropagation();
            const dd = document.getElementById('topbarUserDropdown');
            const panel = document.getElementById('appLauncherPanel');
            panel.classList.remove('open');
            dd.classList.toggle('open');
        }

        // Close both panels when clicking outside
        document.addEventListener('click', function() {
            document.getElementById('appLauncherPanel').classList.remove('open');
            document.getElementById('topbarUserDropdown').classList.remove('open');
        });
        document.getElementById('appLauncherPanel').addEventListener('click', e => e.stopPropagation());
        document.getElementById('topbarUserDropdown').addEventListener('click', e => e.stopPropagation());

        // Business deletion function
        function initiateBusinessDeletion() {
            const modal = new bootstrap.Modal(document.getElementById('businessDeletionModal'));
            modal.show();
        }

        // Sidebar search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('sidebarSearch');
            const navItems = document.querySelectorAll('.nav-item');
            const navSections = document.querySelectorAll('.nav-section');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    if (searchTerm === '') {
                        // Reset: restore original item visibility and section collapse states
                        navItems.forEach(item => item.style.display = '');
                        navSections.forEach(section => {
                            section.classList.remove('search-force-open');
                            if (section.dataset.wasCollapsed === '1') {
                                section.classList.add('collapsed');
                            } else {
                                section.classList.remove('collapsed');
                            }
                            delete section.dataset.wasCollapsed;
                        });
                        return;
                    }
                    
                    // First search pass: remember which sections were collapsed, then open them
                    if (searchTerm.length === 1) {
                        navSections.forEach(section => {
                            section.dataset.wasCollapsed = section.classList.contains('collapsed') ? '1' : '0';
                        });
                    }
                    
                    navItems.forEach(item => {
                        const link = item.querySelector('.nav-link');
                        const text = (link ? link.textContent : '').toLowerCase();
                        
                        if (text.includes(searchTerm)) {
                            item.style.display = '';
                            const parentSection = item.closest('.nav-section');
                            if (parentSection) {
                                parentSection.classList.remove('collapsed');
                                parentSection.classList.add('search-force-open');
                            }
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        });
        
        // Theme toggle
        function toggleTheme() {
            const toggle = document.getElementById('themeToggle');
            const body = document.body;
            
            toggle.classList.toggle('active');
            
            if (toggle.classList.contains('active')) {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
            }
            
            // Trigger chart updates for dark mode
            if (typeof window.updateChartsForTheme === 'function') {
                window.updateChartsForTheme();
            }
        }
        
        // AI Chat Functions
        let aiConversationHistory = [];
        let aiUnreadCount = 0;
        
        function toggleAIChat() {
            const interface = document.getElementById('ai-chat-interface');
            const button = document.getElementById('ai-chat-button');
            
            if (interface.style.display === 'none') {
                interface.style.display = 'flex';
                button.style.transform = 'scale(1.1)';
                aiUnreadCount = 0;
                updateAIBadge();
                loadAIConversationHistory();
            } else {
                interface.style.display = 'none';
                button.style.transform = 'scale(1)';
            }
        }
        
        function updateAIBadge() {
            const badge = document.getElementById('ai-chat-badge');
            if (aiUnreadCount > 0) {
                badge.textContent = aiUnreadCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
        
        function checkForNewInsights() {
            // Check for new AI insights or recommendations
            fetch('{{ route("ai-comm.status") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.status.knowledge_available) {
                    // Check for unread advice
                    fetch('{{ route("ai-advice.unread-count") }}')
                    .then(response => response.json())
                    .then(adviceData => {
                        if (adviceData.success && adviceData.count > 0) {
                            aiUnreadCount = adviceData.count;
                            updateAIBadge();
                            
                            // Show notification if chat is not open
                            const chatInterface = document.getElementById('ai-chat-interface');
                            if (chatInterface.style.display === 'none') {
                                showAINotification(`You have ${adviceData.count} new business insights available!`);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking for new advice:', error);
                    });
                }
            })
            .catch(error => {
                console.error('Error checking AI status:', error);
            });
        }
        
        function handleAINotificationClick() {
            // Open the AI chat when notification is clicked
            const chatInterface = document.getElementById('ai-chat-interface');
            if (chatInterface.style.display === 'none') {
                toggleAIChat();
            }
        }
        
        function addAIMessage(text, type) {
            const messagesContainer = document.getElementById('ai-chat-messages');
            const messageHtml = `
                <div class="ai-message ${type}">
                    <div class="ai-message-content">
                        ${text}
                        <div class="ai-message-time">${new Date().toLocaleTimeString()}</div>
                    </div>
                </div>
            `;
            
            messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function sendAIMessage() {
            const messageInput = document.getElementById('ai-message-input');
            const businessSelect = document.getElementById('ai-business-select');
            const message = messageInput.value.trim();
            const businessId = businessSelect.value;
            
            if (!message) return;
            
            // Add user message
            addAIMessage(message, 'user');
            messageInput.value = '';
            
            // Show loading
            const loadingHtml = `
                <div class="ai-message ai">
                    <div class="ai-message-content">
                        <div class="ai-loading">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span>AI is thinking...</span>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('ai-chat-messages').insertAdjacentHTML('beforeend', loadingHtml);
            
            // Send to AI
            fetch('{{ route("ai-comm.process-message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    business_id: businessId
                })
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading message
                const messagesContainer = document.getElementById('ai-chat-messages');
                messagesContainer.removeChild(messagesContainer.lastElementChild);
                
                if (data.success) {
                    addAIMessage(data.response, 'ai');
                    updateAISuggestions(data.suggestions);
                } else {
                    addAIMessage('Sorry, I encountered an error. Please try again.', 'ai');
                }
            })
            .catch(error => {
                // Remove loading message
                const messagesContainer = document.getElementById('ai-chat-messages');
                messagesContainer.removeChild(messagesContainer.lastElementChild);
                
                addAIMessage('Sorry, I\'m having trouble connecting. Please try again.', 'ai');
            });
        }
        
        function updateAISuggestions(suggestions) {
            if (suggestions && suggestions.length > 0) {
                const container = document.querySelector('.ai-suggestion-buttons');
                container.innerHTML = '';
                suggestions.forEach(suggestion => {
                    container.innerHTML += `<button class="btn btn-sm btn-outline-secondary ai-suggestion-btn" data-message="${suggestion}">${suggestion}</button>`;
                });
                
                // Reattach click handlers
                document.querySelectorAll('.ai-suggestion-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const message = this.getAttribute('data-message');
                        document.getElementById('ai-message-input').value = message;
                        sendAIMessage();
                    });
                });
            }
        }
        
        function loadAIConversationHistory() {
            fetch('{{ route("ai-comm.history") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    aiConversationHistory = data.history;
                    displayAIConversationHistory();
                }
            })
            .catch(error => {
                console.error('Error loading AI conversation history:', error);
            });
        }
        
        function displayAIConversationHistory() {
            const messagesContainer = document.getElementById('ai-chat-messages');
            messagesContainer.innerHTML = '';
            
            if (aiConversationHistory.length === 0) {
                addAIMessage('Hello! I\'m your AI business assistant. How can I help you today?', 'ai');
                return;
            }
            
            aiConversationHistory.forEach(item => {
                addAIMessage(item.message, item.type);
            });
        }
        
        function showAINotification(message) {
            // Create a temporary notification
            const notification = document.createElement('div');
            notification.className = 'ai-notification';
            notification.style.cursor = 'pointer';
            notification.onclick = handleAINotificationClick;
            notification.innerHTML = `
                <div class="ai-notification-content">
                    <i class="fas fa-robot me-2"></i>
                    <span>${message}</span>
                    <button class="ai-notification-close" onclick="event.stopPropagation(); this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Initialize AI Chat
        document.addEventListener('DOMContentLoaded', function() {
            // Send button click
            document.getElementById('ai-send-button').addEventListener('click', sendAIMessage);
            
            // Enter key in input
            document.getElementById('ai-message-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendAIMessage();
                }
            });
            
            // Suggestion buttons
            document.querySelectorAll('.ai-suggestion-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const message = this.getAttribute('data-message');
                    document.getElementById('ai-message-input').value = message;
                    sendAIMessage();
                });
            });
            
            // Check for new insights every 5 minutes
            setInterval(checkForNewInsights, 300000);
            
            // Initial check
            setTimeout(checkForNewInsights, 5000);
            
            // Load saved theme
            const savedTheme = localStorage.getItem('theme');
            const toggle = document.getElementById('themeToggle');
            
            if (savedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                toggle.classList.add('active');
            }
        });
        
        // Close sidebar when clicking overlay
        document.getElementById('mobileOverlay').addEventListener('click', function() {
            toggleSidebar();
        });

        // PWA Installation Script
        let deferredPrompt;
        const pwaBanner = document.getElementById('pwa-install-banner');
        const pwaInstallBtn = document.getElementById('pwa-install-btn');
        const pwaDismissBtn = document.getElementById('pwa-dismiss-btn');

        // Check if PWA is already installed
        const isPWAInstalled = window.matchMedia('(display-mode: standalone)').matches;
        
        // Check if user has dismissed the banner before
        const hasDismissedBanner = localStorage.getItem('pwa-banner-dismissed');

        // Show banner if PWA is not installed and user hasn't dismissed it
        if (!isPWAInstalled && !hasDismissedBanner) {
            // Listen for beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                
                // Show sidebar install button
                const sidebarInstallBtn = document.getElementById('sidebar-install-app');
                if (sidebarInstallBtn) {
                    sidebarInstallBtn.style.display = 'block';
                }
                
                // Show banner after a short delay
                setTimeout(() => {
                    pwaBanner.classList.remove('hidden');
                    // Add padding to body to account for banner
                    document.body.style.paddingTop = '80px';
                }, 3000); // Show after 3 seconds
            });
        }

        // Handle install button click
        if (pwaInstallBtn) {
            pwaInstallBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    if (outcome === 'accepted') {
                        console.log('PWA installed successfully');
                        pwaBanner.classList.add('hidden');
                        document.body.style.paddingTop = '0';
                    }
                    
                    deferredPrompt = null;
                } else {
                    // Fallback for manual installation
                    showManualInstallGuide();
                }
            });
        }

        // Handle dismiss button click
        if (pwaDismissBtn) {
            pwaDismissBtn.addEventListener('click', () => {
                pwaBanner.classList.add('hidden');
                document.body.style.paddingTop = '0';
                localStorage.setItem('pwa-banner-dismissed', 'true');
            });
        }

        // Manual installation guide
        function showManualInstallGuide() {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isAndroid = /Android/.test(navigator.userAgent);
            
            let message = '';
            if (isIOS) {
                message = '📱 To install: Tap the share button (📤) then "Add to Home Screen"';
            } else if (isAndroid) {
                message = '📱 To install: Tap menu (⋮) then "Add to Home screen"';
            } else {
                message = '💻 To install: Look for the install icon in your browser\'s address bar';
            }
            
            // Show a more user-friendly alert with emojis
            alert('🚀 Install Shopybook App!\n\n' + message + '\n\n✨ Once installed, you\'ll have quick access to your business dashboard!');
        }

        // Show PWA install guide from sidebar
        function showPWAInstallGuide() {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isAndroid = /Android/.test(navigator.userAgent);
            
            let instructions = '';
            if (isIOS) {
                instructions = `
                    <div class="text-center">
                        <h4 class="mb-3">📱 Install on iPhone/iPad</h4>
                        <ol class="text-left">
                            <li>1. Tap the <strong>Share button</strong> (📤) at the bottom</li>
                            <li>2. Scroll down and tap <strong>"Add to Home Screen"</strong></li>
                            <li>3. Tap <strong>"Add"</strong> to confirm</li>
                            <li>4. Find the app on your home screen!</li>
                        </ol>
                    </div>
                `;
            } else if (isAndroid) {
                instructions = `
                    <div class="text-center">
                        <h4 class="mb-3">📱 Install on Android</h4>
                        <ol class="text-left">
                            <li>1. Tap the <strong>menu button</strong> (⋮) at the top</li>
                            <li>2. Tap <strong>"Add to Home screen"</strong></li>
                            <li>3. Tap <strong>"Add"</strong> to confirm</li>
                            <li>4. Find the app on your home screen!</li>
                        </ol>
                    </div>
                `;
            } else {
                instructions = `
                    <div class="text-center">
                        <h4 class="mb-3">💻 Install on Desktop</h4>
                        <p>Look for the install icon (📥) in your browser's address bar and click it!</p>
                    </div>
                `;
            }
            
            // Create a modal with instructions
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">🚀 Install Shopybook App</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    ${instructions}
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">✨ Once installed, you'll have quick access to your business dashboard!</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }

        // Listen for successful installation
        window.addEventListener('appinstalled', () => {
            if (pwaBanner) {
                pwaBanner.classList.add('hidden');
                document.body.style.paddingTop = '0';
            }
            console.log('PWA was installed');
        });
        
        // Notification Functions
        let notificationsLoaded = false;
        let currentNotificationOffset = 0;
        
        function toggleNotifications() {
            const panel = document.getElementById('notificationsPanel');
            const isVisible = panel.style.display !== 'none';
            
            if (isVisible) {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
                if (!notificationsLoaded) {
                    loadNotifications();
                }
            }
        }
        
        function loadNotifications() {
            fetch('{{ route("notifications.index") }}')
                .then(response => response.json())
                .then(data => {
                    displayNotifications(data.notifications);
                    updateNotificationBadge(data.unread_count);
                    notificationsLoaded = true;
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    document.getElementById('notificationsList').innerHTML = 
                        '<div class="empty-notifications"><i class="fas fa-exclamation-triangle"></i><p>Error loading notifications</p></div>';
                });
        }
        
        function displayNotifications(notifications) {
            const container = document.getElementById('notificationsList');
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="empty-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications yet</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = notifications.map(notification => `
                <div class="notification-item ${notification.read ? '' : 'unread'}" onclick="markNotificationAsRead(${notification.id})">
                    <div class="notification-icon ${notification.color}">
                        <i class="${notification.icon || 'fas fa-info'}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time">${formatTimeAgo(notification.created_at)}</div>
                    </div>
                </div>
            `).join('');
        }
        
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (count > 0) {
                badge.style.display = 'flex';
                badge.textContent = count > 99 ? '99+' : count;
            } else {
                badge.style.display = 'none';
            }
        }
        
        function markNotificationAsRead(notificationId) {
            fetch(`{{ url('notifications') }}/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI to mark as read
                    const item = document.querySelector(`[onclick="markNotificationAsRead(${notificationId})"]`);
                    if (item) {
                        item.classList.remove('unread');
                    }
                    // Update badge count
                    loadNotificationCount();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }
        
        function markAllNotificationsAsRead() {
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread class from all notifications
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    // Update badge
                    updateNotificationBadge(0);
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        }
        
        function loadNotificationCount() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.count);
                })
                .catch(error => {
                    console.error('Error loading notification count:', error);
                });
        }
        
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)}d ago`;
            return date.toLocaleDateString();
        }
        
        // Close notifications panel when clicking outside
        document.addEventListener('click', function(event) {
            const panel = document.getElementById('notificationsPanel');
            const bell = document.querySelector('.notification-bell');
            
            if (panel && bell && !panel.contains(event.target) && !bell.contains(event.target)) {
                panel.style.display = 'none';
            }
        });
        
        // Load notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotificationCount();
            
            // Periodically check for new notifications (every 30 seconds)
            setInterval(loadNotificationCount, 30000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>