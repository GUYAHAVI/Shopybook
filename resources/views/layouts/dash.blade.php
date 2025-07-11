<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shopybook</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Boldonse&family=Cinzel+Decorative:wght@400;700;900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #13e8e9;
            --secondary-color: #020258;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }
        
        body, .container-fluid, .card, .dashboard-content, .main-content, .content, .sidebar, .navbar, .form-control {
            background: #fff !important;
            color: #020258 !important;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar styling */
        #sidebar {
            width: 250px;
            min-height: 100vh;
            background: #020258;
            color: #13e8e9;
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(19, 232, 233, 0.2);
            border-right: 2px solid #13e8e9;
        }
        
        #sidebar .brand {
            padding: 1.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 800;
            color: #13e8e9;
            text-decoration: none;
            border-bottom: 1px solid rgba(19, 232, 233, 0.3);
            font-family: "Cinzel Decorative", serif;
        }
        
        #sidebar .nav-link {
            color: rgba(19, 232, 233, 0.8);
            padding: 0.75rem 1rem;
            font-weight: 600;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        #sidebar .nav-link:hover, 
        #sidebar .nav-link.active {
            color: #13e8e9;
            background: rgba(19, 232, 233, 0.1);
            border-left: 3px solid #13e8e9;
        }
        
        #sidebar .dropdown-menu {
            background: rgba(2, 2, 88, 0.9);
            border: 1px solid #13e8e9;
            padding: 0;
        }
        
        #sidebar .dropdown-item {
            color: rgba(19, 232, 233, 0.8);
            padding: 0.5rem 1.5rem;
            font-size: 0.85rem;
        }
        
        #sidebar .dropdown-item:hover {
            color: #13e8e9;
            background: rgba(19, 232, 233, 0.1);
        }
        
        /* Main content area */
        #content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s;
            background-color: #020258;
        }
        
        /* Top navbar */
        .top-navbar {
            height: 70px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(19, 232, 233, 0.15);
            background-color: rgba(2, 2, 88, 0.95);
            border-bottom: 2px solid #13e8e9;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
        }
        
        /* AI Assistant Chat */
        .ai-assistant {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }
        
        .ai-assistant-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #fff;
            color: #020258;
            font-size: 28px;
            border: 2px solid #13e8e9;
            box-shadow: 0 4px 20px rgba(19, 232, 233, 0.15);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ai-assistant-btn:hover {
            background: #13e8e9;
            color: #020258;
            border-color: #020258;
        }
        
        .ai-chat-container {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 350px;
            height: 500px;
            background: #fff;
            border: 2px solid #020258;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(2,2,88,0.10);
            display: none;
            flex-direction: column;
        }
        
        .ai-chat-container.active {
            display: flex;
        }
        
        .ai-chat-header {
            padding: 15px;
            background: #020258;
            color: #fff;
            border-radius: 10px 10px 0 0;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .ai-chat-body {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            background: #fff;
        }
        
        .ai-chat-body .fa-robot {
            color: #13e8e9;
            background: #020258;
            border-radius: 50%;
            padding: 6px;
            font-size: 22px;
        }
        
        .ai-chat-body .fa-user {
            color: #fff;
            background: #020258;
            border-radius: 50%;
            padding: 6px;
            font-size: 22px;
        }
        
        .ai-chat-body .card.bg-light {
            background: #f8f9fa !important;
            color: #020258;
        }
        
        .ai-chat-body .card.bg-primary {
            background: #020258 !important;
            color: #fff;
        }
        
        .ai-chat-footer {
            padding: 15px;
            border-top: 1px solid #13e8e9;
            background: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }
        
        /* Delete Business Modal Styling */
        .modal-danger .modal-content {
            background: #fff;
            border: 2px solid #dc3545;
            border-radius: 10px;
        }
        
        .modal-danger .modal-header {
            background: #020258;
            color: #fff;
            border-bottom: 1px solid #dc3545;
            border-radius: 10px 10px 0 0;
            align-items: center;
        }
        
        .modal-danger .modal-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modal-danger .modal-title .fa-exclamation-triangle {
            color: #dc3545;
            font-size: 1.5rem;
        }
        
        .modal-danger .modal-body {
            background: #fff;
            color: #020258;
            font-size: 1.1rem;
        }
        
        .modal-danger .modal-body .text-danger {
            color: #dc3545 !important;
        }
        
        .modal-danger .form-label {
            color: #020258;
            font-weight: 500;
        }
        
        .modal-danger .form-control {
            border: 2px solid #dc3545;
            background: #f8f9fa;
            color: #020258;
        }
        
        .modal-danger .form-control:focus {
            border-color: #020258;
            box-shadow: 0 0 0 2px #dc3545;
        }
        
        .modal-danger .invalid-feedback {
            color: #dc3545;
            font-size: 0.95rem;
        }
        
        .modal-danger .btn-danger {
            background: #dc3545;
            color: #fff;
            border: 2px solid #dc3545;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            transition: background 0.2s;
        }
        
        .modal-danger .btn-danger:hover {
            background: #b71c1c;
            border-color: #b71c1c;
        }
        
        /* Form controls */
        .form-control {
            background: #f8f9fa !important;
            color: #020258 !important;
            border: 2px solid #13e8e9 !important;
        }
        
        .form-control:focus {
            border-color: #020258 !important;
            box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
        }
        
        .form-control::placeholder {
            color: rgba(19, 232, 233, 0.6);
        }
        
        /* Buttons */
        .btn-primary {
            background: #020258 !important;
            color: #fff !important;
            border: 2px solid #13e8e9 !important;
        }
        
        .btn-primary:hover {
            background: #13e8e9 !important;
            color: #020258 !important;
            border: 2px solid #020258 !important;
        }
        
        .btn-outline-primary {
            border: 2px solid #13e8e9;
            color: #13e8e9;
        }
        
        .btn-outline-primary:hover {
            background: #13e8e9;
            color: #020258;
        }
        
        /* Cards */
        .card {
            background: rgba(2, 2, 88, 0.8);
            border: 2px solid rgba(19, 232, 233, 0.3);
            color: #13e8e9;
        }
        
        .card-header {
            background: #f8f9fa !important;
            color: #020258 !important;
            border-bottom: 1px solid #13e8e9 !important;
        }
        
        /* Alerts */
        .alert-success {
            background: rgba(19, 232, 233, 0.1);
            border: 2px solid #13e8e9;
            color: #13e8e9;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid #dc3545;
            color: #dc3545;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
            
            #content {
                margin-left: 0;
            }
            
            #content.active {
                margin-left: 250px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Navigation -->
    <nav id="sidebar" class="d-flex flex-column">
        <!-- Brand -->
        <a href="{{ route('index') }}" class="brand text-decoration-none mb-4">
            <i class="fas fa-store me-2"></i>
            <span class="text">Shopybook</span>
        </a>
        
        <!-- Main Menu -->
        <div class="flex-grow-1">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link active">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        {{ t('dashboard') }}
                    </a>
                </li>
                
                <!-- My Business -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#businessCollapse" role="button">
                        <i class="fas fa-briefcase me-2"></i>
                        {{ t('my_business') }}
                    </a>
                    <div class="collapse" id="businessCollapse">
                        <ul class="nav flex-column ps-3">
                            <li class="nav-item">
                                <a href="{{ route('business.edit') }}" class="nav-link">
                                    <i class="fas fa-edit me-2"></i>
                                    {{ t('edit_business_profile') }}
                                </a>
                            </li>
                            @if(auth()->user()->business)
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#deleteBusinessModal">
                                    <i class="fas fa-trash me-2"></i>
                                    {{ t('delete_business') }}
                                </a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ route('business.analysis.index') }}" class="nav-link">
                                    <i class="fas fa-chart-line me-2"></i>
                                    {{ t('business_analytics') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Products -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#productsCollapse" role="button">
                        <i class="fas fa-boxes me-2"></i>
                        {{ t('products') }}
                    </a>
                    <div class="collapse" id="productsCollapse">
                        <ul class="nav flex-column ps-3">
                            <li class="nav-item">
                                <a href="{{ route('products.index') }}" class="nav-link">
                                    <i class="fas fa-list me-2"></i>
                                    {{ t('all_products') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('products.create') }}" class="nav-link">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    {{ t('add_new_product') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('products.bulk-import') }}" class="nav-link">
                                    <i class="fas fa-file-import me-2"></i>
                                    {{ t('bulk_import') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('products.inventory') }}" class="nav-link">
                                    <i class="fas fa-warehouse me-2"></i>
                                    {{ t('inventory_management') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Sales -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#salesCollapse" role="button">
                        <i class="fas fa-cash-register me-2"></i>
                        {{ t('sales') }}
                    </a>
                    <div class="collapse" id="salesCollapse">
                        <ul class="nav flex-column ps-3">
                            <li class="nav-item">
                                <a href="{{ route('sales.pos') }}" class="nav-link">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    {{ t('pos_system') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sales.orders') }}" class="nav-link">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    {{ t('orders') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sales.invoices') }}" class="nav-link">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>
                                    {{ t('invoices') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sales.customers') }}" class="nav-link">
                                    <i class="fas fa-users me-2"></i>
                                    {{ t('customers') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sales.report') }}" class="nav-link">
                                    <i class="fas fa-chart-line me-2"></i>
                                    {{ t('sales_report') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Marketing -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#marketingCollapse" role="button">
                        <i class="fas fa-bullhorn me-2"></i>
                        {{ t('marketing') }}
                    </a>
                    <div class="collapse" id="marketingCollapse">
                        <ul class="nav flex-column ps-3">
                            <li class="nav-item">
                                <a href="{{ route('marketing.promotions') }}" class="nav-link">
                                    <i class="fas fa-percentage me-2"></i>
                                    {{ t('promotions') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('marketing.sms') }}" class="nav-link">
                                    <i class="fas fa-sms me-2"></i>
                                    {{ t('bulk_sms') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('marketing.email') }}" class="nav-link">
                                    <i class="fas fa-envelope me-2"></i>
                                    {{ t('email_marketing') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('marketing.advertising') }}" class="nav-link">
                                    <i class="fas fa-ad me-2"></i>
                                    {{ t('advertising') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('marketing.report') }}" class="nav-link">
                                    <i class="fas fa-chart-line me-2"></i>
                                    {{ t('marketing_report') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Suppliers -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-truck me-2"></i>
                        {{ t('suppliers') }}
                    </a>
                </li>
                
                <!-- Employees -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#employeesCollapse" role="button">
                        <i class="fas fa-user-tie me-2"></i>
                        {{ t('employees') }}
                    </a>
                    <div class="collapse" id="employeesCollapse">
                        <ul class="nav flex-column ps-3">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-users-cog me-2"></i>
                                    {{ t('manage_team') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-money-check-alt me-2"></i>
                                    {{ t('payroll') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    {{ t('attendance') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Reports -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-pie me-2"></i>
                        {{ t('reports') }}
                    </a>
                </li>
                
                <!-- AI Assistant -->
                <li class="nav-item">
                    <a href="#" class="nav-link" id="openAiChat">
                        <i class="fas fa-robot me-2"></i>
                        {{ t('ai_assistant') }}
                    </a>
                </li>

                @if(auth()->user()->business && in_array(auth()->user()->business->business_type, ['service', 'hybrid']))
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#servicesCollapse" role="button">
                        <i class="fas fa-cut me-2"></i>
                        {{ t('services') }}
                    </a>
                    <div class="collapse" id="servicesCollapse">
                        <ul class="nav flex-column ps-3">
                            <li class="nav-item">
                                <a href="{{ route('services.index') }}" class="nav-link">
                                    <i class="fas fa-list me-2"></i> {{ t('manage_services') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('staff.index') }}" class="nav-link">
                                    <i class="fas fa-users me-2"></i> {{ t('staff') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('service-records.index') }}" class="nav-link">
                                    <i class="fas fa-clipboard-list me-2"></i> {{ t('service_records') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('costs.index') }}" class="nav-link">
                                    <i class="fas fa-money-bill-wave me-2"></i> {{ t('costs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('commissions.index') }}" class="nav-link">
                                    <i class="fas fa-hand-holding-usd me-2"></i> {{ t('commissions') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
            </ul>
        </div>
        
        <!-- Bottom Menu -->
        <div class="mt-auto pb-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog fa-spin me-2"></i>
                        {{ t('settings') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-power-off me-2"></i>
                        {{ t('logout') }}
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="navbar top-navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <!-- Sidebar Toggle -->
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Search Form -->
                <form class="d-flex ms-md-auto">
                    <input class="form-control me-2" type="search" placeholder="{{ t('search_products_orders') }}" aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <!-- Right Side Nav Items -->
                <div class="d-flex align-items-center ms-3">
                    <!-- Language Switcher -->
                    @include('components.language-switcher')
                    
                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <a class="position-relative" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">3</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                            <li><h6 class="dropdown-header">{{ t('notifications') }}</h6></li>
                            <li><a class="dropdown-item" href="#">{{ t('new_order') }} #SHB-1024</a></li>
                            <li><a class="dropdown-item" href="#">{{ t('inventory_low') }} {{ t('on') }} T-Shirts</a></li>
                            <li><a class="dropdown-item" href="#">{{ t('ai_business_tip') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- User Profile -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://placehold.co/600x400/png" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                            <span class="d-none d-md-inline">{{ t('user_name') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">{{ t('profile') }}</a></li>
                            <li><a class="dropdown-item" href="#">{{ t('settings') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ t('logout') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page content will be inserted here -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
    
    <!-- AI Assistant Floating Button -->
    <div class="ai-assistant">
        <button class="ai-assistant-btn" id="aiAssistantBtn" title="{{ t('chat_with_shopybook_ai') }}">
            <i class="fas fa-robot"></i>
        </button>
        <div class="ai-chat-container" id="aiChatContainer">
            <div class="ai-chat-header">
                <span><i class="fas fa-robot me-2" style="color:#13e8e9;"></i>{{ t('shopybook_ai_assistant') }}</span>
                <button class="btn-close btn-close-white float-end" id="closeAiChat" style="filter:invert(0);"></button>
            </div>
            <div class="ai-chat-body" id="aiChatBody">
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0 me-2">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="card bg-light">
                            <div class="card-body p-2">
                                <p class="mb-0">{{ t('ai_assistant_greeting') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ai-chat-footer">
                <div class="input-group">
                    <input type="text" class="form-control" id="aiChatInput" placeholder="{{ t('ask_me_anything') }}">
                    <button class="btn btn-primary" id="sendAiMessage">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="deleteBusinessModal" tabindex="-1" aria-labelledby="deleteBusinessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-danger">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBusinessModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> {{ t('confirm_deletion') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ t('delete_business_confirmation') }}</p>
                    <p class="text-danger fw-bold">{{ t('action_cannot_be_undone') }}</p>
                    
                    <form id="deleteBusinessForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="password" id="formPassword">
                        <div class="mb-3">
                            <label for="passwordConfirmation" class="form-label">{{ t('enter_password_to_confirm') }}:</label>
                            <input type="password" class="form-control" id="passwordConfirmation" required>
                            <div id="passwordError" class="invalid-feedback"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ t('cancel') }}</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">{{ t('delete_business') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                content.classList.toggle('active');
            });
            
            // AI Assistant Chat
            const aiAssistantBtn = document.getElementById('aiAssistantBtn');
            const aiChatContainer = document.getElementById('aiChatContainer');
            const closeAiChat = document.getElementById('closeAiChat');
            const sendAiMessage = document.getElementById('sendAiMessage');
            const aiChatInput = document.getElementById('aiChatInput');
            const aiChatBody = document.getElementById('aiChatBody');
            const openAiChatLink = document.getElementById('openAiChat');
            
            // Delete Business Modal
            const deleteBusinessModal = document.getElementById('deleteBusinessModal');
            const deleteBusinessForm = document.getElementById('deleteBusinessForm');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const passwordConfirmation = document.getElementById('passwordConfirmation');
            const passwordError = document.getElementById('passwordError');
            
            // Set the delete form action when modal opens
            deleteBusinessModal.addEventListener('show.bs.modal', function() {
                // Get the current user's business
                const business = @json(auth()->user()->business ?? null);
                if (business && business.id) {
                    deleteBusinessForm.action = `/business/${business.id}`;
                } else {
                    // Handle case where user doesn't have a business
                    alert('No business found to delete.');
                    const modal = bootstrap.Modal.getInstance(deleteBusinessModal);
                    if (modal) {
                        modal.hide();
                    }
                }
            });
            
            // Handle delete confirmation
            confirmDeleteBtn.addEventListener('click', function() {
                const password = passwordConfirmation.value;
                if (!password) {
                    passwordError.textContent = 'Password is required';
                    passwordError.style.display = 'block';
                    return;
                }
                
                // Set the password in the hidden field
                document.getElementById('formPassword').value = password;
                
                // Submit the form
                deleteBusinessForm.submit();
            });
            
            // Clear password error when user types
            passwordConfirmation.addEventListener('input', function() {
                passwordError.style.display = 'none';
            });
            
            aiAssistantBtn.addEventListener('click', function() {
                aiChatContainer.classList.toggle('active');
            });
            
            openAiChatLink.addEventListener('click', function(e) {
                e.preventDefault();
                aiChatContainer.classList.add('active');
            });
            
            closeAiChat.addEventListener('click', function() {
                aiChatContainer.classList.remove('active');
            });
            
            function addAiMessage(message, isUser = false) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `d-flex mb-3`;
                
                messageDiv.innerHTML = `
                    <div class="flex-shrink-0 me-2">
                        <i class="fas ${isUser ? 'fa-user' : 'fa-robot text-info'}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="card ${isUser ? 'bg-primary text-white' : 'bg-light'}">
                            <div class="card-body p-2">
                                <p class="mb-0">${message}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                aiChatBody.appendChild(messageDiv);
                aiChatBody.scrollTop = aiChatBody.scrollHeight;
            }
            
            function sendMessage() {
                const message = aiChatInput.value.trim();
                if (message) {
                    addAiMessage(message, true);
                    aiChatInput.value = '';
                    
                    // Simulate AI response
                    setTimeout(() => {
                        const responses = [
                            "I can help you analyze your sales data. What would you like to know?",
                            "Based on your inventory levels, you might want to reorder T-shirts soon.",
                            "Your weekend sales are consistently higher than weekdays. Consider running promotions on Fridays.",
                            "I notice customers often buy jeans with shirts. You might create a bundle offer."
                        ];
                        
                        const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                        addAiMessage(randomResponse);
                    }, 1000);
                }
            }
            
            sendAiMessage.addEventListener('click', sendMessage);
            
            aiChatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            // Expose a function to open the delete modal and set a callback for password confirmation
            window.openDeleteBusinessModal = function(onConfirm) {
                const modal = new bootstrap.Modal(document.getElementById('deleteBusinessModal'));
                modal.show();
                // Optionally, you can set a callback for after confirmation
                window._deleteBusinessCallback = onConfirm;
            }

            // In the confirmDeleteBtn click handler, after successful password check:
            confirmDeleteBtn.addEventListener('click', function() {
                const password = passwordConfirmation.value;
                if (!password) {
                    passwordError.textContent = 'Password is required';
                    passwordError.style.display = 'block';
                    return;
                }
                document.getElementById('formPassword').value = password;
                if (window._deleteBusinessCallback) {
                    window._deleteBusinessCallback(password);
                    window._deleteBusinessCallback = null;
                } else {
                    deleteBusinessForm.submit();
                }
            });
        });
    </script>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</body>
</html>