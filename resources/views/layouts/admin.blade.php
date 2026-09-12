<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin — Shopybook')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #7b2e2e;
            --primary-light: #ff511a;
            --primary-dark: #5a2020;
            --success-color: #43ba7f;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --white: #ffffff;
            --bg-primary: #f7f4f3;
            --bg-tertiary: #faf8f7;
            --text-primary: #332b2b;
            --text-secondary: #6b5d5d;
            --text-muted: #8a7d7d;
            --border-color: #e0ded9;
            --card-bg: #ffffff;
            --sidebar-bg: #2a1f1f;
            --sidebar-text: #d8cfcf;
            --sidebar-active: #ff511a;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; color: var(--primary-color); }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.25s ease;
        }
        .admin-sidebar::-webkit-scrollbar { width: 6px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }

        .admin-sidebar-header {
            padding: 1.4rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 10px;
        }
        .admin-sidebar-header .brand-mark {
            width: 36px; height: 36px; border-radius: 8px;
            background: linear-gradient(135deg, #7b2e2e, #ff511a);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-family: 'Playfair Display', serif;
        }
        .admin-sidebar-header .brand-name { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #fff; }
        .admin-sidebar-header .brand-sub { font-size: 0.7rem; color: var(--text-muted); letter-spacing: 0.06em; text-transform: uppercase; }

        .admin-nav { padding: 0.75rem 0; }
        .admin-nav-section { padding: 0.5rem 1.25rem 0.25rem; font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(255,255,255,0.4); }
        .admin-nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 0.6rem 1.25rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.88rem;
            border-left: 3px solid transparent;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }
        .admin-nav a i { width: 18px; text-align: center; opacity: 0.85; }
        .admin-nav a:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .admin-nav a.active {
            background: rgba(255,81,26,0.12);
            color: #fff;
            border-left-color: var(--sidebar-active);
        }
        .admin-nav a .badge { margin-left: auto; font-size: 0.68rem; }

        /* Main */
        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }
        .admin-topbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0.85rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 900;
        }
        .admin-topbar .page-title { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: var(--primary-color); margin: 0; }
        .admin-topbar .admin-user { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: var(--text-secondary); }
        .admin-topbar .admin-user .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, #7b2e2e, #ff511a);
            color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600;
        }
        .admin-topbar .back-to-app { color: var(--text-muted); font-size: 0.8rem; text-decoration: none; }
        .admin-topbar .back-to-app:hover { color: var(--primary-color); }
        .admin-topbar .menu-toggle { display: none; background: none; border: none; font-size: 1.2rem; color: var(--text-secondary); }

        .admin-content { padding: 1.5rem; flex: 1; }

        /* Cards */
        .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; }
        .card-header { background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color); font-weight: 600; }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.25rem 1.4rem;
            transition: transform 0.15s, box-shadow 0.15s;
            cursor: pointer;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(123,46,46,0.08); }
        .stat-card .stat-label { font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card .stat-value { font-family: 'Playfair Display', serif; font-size: 1.9rem; color: var(--primary-color); font-weight: 700; line-height: 1.1; margin: 0.25rem 0; }
        .stat-card .stat-trend { font-size: 0.78rem; color: var(--success-color); }
        .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

        .table { color: var(--text-primary); }
        .table thead { background: var(--bg-tertiary); }
        .table th { color: var(--text-secondary); font-weight: 600; font-size: 0.82rem; }
        .table-hover tbody tr:hover { background: var(--bg-tertiary); }

        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-outline-primary { color: var(--primary-color); border-color: var(--primary-color); }
        .btn-outline-primary:hover { background: var(--primary-color); border-color: var(--primary-color); }

        .text-primary { color: var(--primary-color) !important; }
        .text-muted { color: var(--text-muted) !important; }
        .bg-tertiary { background: var(--bg-tertiary) !important; }
        .border { border-color: var(--border-color) !important; }

        a { color: var(--primary-color); }

        .pagination .page-link { color: var(--primary-color); }
        .pagination .active .page-link { background: var(--primary-color); border-color: var(--primary-color); }

        .admin-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.78rem; text-align: center; }

        @media (max-width: 992px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .admin-topbar .menu-toggle { display: inline-block; }
        }
    </style>
</head>
<body>
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-header">
            <div class="brand-mark">S</div>
            <div>
                <div class="brand-name">Shopybook</div>
                <div class="brand-sub">Super Admin</div>
            </div>
        </div>
        <nav class="admin-nav">
            <div class="admin-nav-section">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="@if(request()->routeIs('admin.dashboard')) active @endif"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="{{ route('admin.analytics.index') }}" class="@if(request()->routeIs('admin.analytics.*')) active @endif"><i class="fas fa-chart-line"></i> Analytics</a>
            <a href="{{ route('admin.usage.index') }}" class="@if(request()->routeIs('admin.usage.*')) active @endif"><i class="fas fa-tachometer-alt"></i> Usage</a>
            <a href="{{ route('admin.ai-analysis.index') }}" class="@if(request()->routeIs('admin.ai-analysis.*')) active @endif"><i class="fas fa-robot"></i> AI analysis</a>

            <div class="admin-nav-section">People</div>
            <a href="{{ route('admin.users.index') }}" class="@if(request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show')) active @endif"><i class="fas fa-users"></i> Users</a>
            <a href="{{ route('admin.users.dormant') }}" class="@if(request()->routeIs('admin.users.dormant')) active @endif"><i class="fas fa-user-slash"></i> Dormant users</a>
            <a href="{{ route('admin.reengagement.index') }}" class="@if(request()->routeIs('admin.reengagement.*')) active @endif"><i class="fas fa-paper-plane"></i> Re-engage</a>

            <div class="admin-nav-section">Businesses</div>
            <a href="{{ route('admin.businesses.index') }}" class="@if(request()->routeIs('admin.businesses.*')) active @endif"><i class="fas fa-store"></i> Businesses</a>
            <a href="{{ route('admin.subscriptions.index') }}" class="@if(request()->routeIs('admin.subscriptions.*')) active @endif"><i class="fas fa-credit-card"></i> Subscriptions</a>
            <a href="{{ route('admin.website-builder.index') }}" class="@if(request()->routeIs('admin.website-builder.*')) active @endif"><i class="fas fa-globe"></i> Websites</a>

            <div class="admin-nav-section">Content</div>
            <a href="{{ route('admin.testimonials.index') }}" class="@if(request()->routeIs('admin.testimonials.*')) active @endif"><i class="fas fa-star"></i> Testimonials</a>

            <div class="admin-nav-section" style="margin-top:1rem;"></div>
            <a href="{{ route('dashboard') }}" class=""><i class="fas fa-arrow-left"></i> Back to app</a>
            <a href="{{ route('logout') }}" class="" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Log out</a>
            <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="admin-user">
                <a href="{{ route('dashboard') }}" class="back-to-app me-3 d-none d-md-inline"><i class="fas fa-arrow-left me-1"></i>Back to app</a>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="d-none d-sm-block">
                    <div style="font-weight:600; color: var(--text-primary);">{{ auth()->user()->name }}</div>
                    <div style="font-size:0.72rem;">Super admin</div>
                </div>
            </div>
        </header>

        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('content')
        </main>

        <footer class="admin-footer">
            Shopybook super admin &middot; {{ now()->format('Y') }}
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
