<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shopybook Dashboard</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #020258;
            --primary-light: #13e8e9;
            --primary-dark: #010138;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #13e8e9;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
            --black: #000000;
            
            /* Light mode colors */
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --card-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
        }
        
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --card-bg: #1e293b;
            --sidebar-bg: #1e293b;
            --header-bg: #1e293b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
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
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-brand img {
            width: 32px;
            height: 32px;
        }
        
        .sidebar-nav {
            padding: 1.5rem 0;
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1.5rem 0.5rem;
            margin-bottom: 0.5rem;
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
        }
        
        .nav-link:hover {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
        }
        
        .nav-link.active {
            background-color: var(--primary-color);
            color: var(--white);
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
        }
        
        .top-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
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
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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
            overflow-y: auto;
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
    </style>
</head>

<body>
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
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('business.analysis.index') }}" class="nav-link {{ request()->routeIs('business.analysis.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Analysis</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Business</div>
                <div class="nav-item">
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('sales.orders') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('sales.customers') }}" class="nav-link {{ request()->routeIs('sales.customers*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Services</div>
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
                <div class="nav-item">
                    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Staff</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Marketing</div>
                <div class="nav-item">
                    <a href="{{ route('marketing.social-media') }}" class="nav-link {{ request()->routeIs('marketing.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i>
                        <span>Marketing</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('ai-content.index') }}" class="nav-link {{ request()->routeIs('ai-content.*') ? 'active' : '' }}">
                        <i class="fas fa-magic"></i>
                        <span>AI Content Enhancer</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">AI Assistant</div>
                <div class="nav-item">
                    <a href="{{ route('ai.chat') }}" class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                        <i class="fas fa-robot"></i>
                        <span>AI Chat</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('knowledge.dashboard') }}" class="nav-link {{ request()->routeIs('knowledge.*') ? 'active' : '' }}">
                        <i class="fas fa-brain"></i>
                        <span>Knowledge Base</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('learning.dashboard') }}" class="nav-link {{ request()->routeIs('learning.*') ? 'active' : '' }}">
                        <i class="fas fa-cogs"></i>
                        <span>Learning Dashboard</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Settings</div>
                <div class="nav-item">
                    <a href="{{ route('business.edit') }}" class="nav-link {{ request()->routeIs('business.edit') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
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
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
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
            </div>
        </div>
        
        <div class="dashboard-grid">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
            font-family: 'Inter', sans-serif;
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
            box-shadow: 0 0 0 2px rgba(2, 2, 88, 0.1);
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
            fetch('{{ route("ai.status") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.status.knowledge_available) {
                    // Check for unread advice
                    fetch('{{ route("business.ai-advice.unread-count") }}')
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
            fetch('{{ route("ai.process-message") }}', {
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
            fetch('{{ route("ai.history") }}')
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
    </script>
    
    @stack('scripts')
</body>
</html>