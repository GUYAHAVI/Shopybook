<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Shopybook</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Boldonse&family=Cinzel+Decorative:wght@400;700;900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #020258;
            --primary-dark: #020258;
            --secondary-color: #6c757d;
            --accent-color: #007bff;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #007bff;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
        }
        
        /* Dark mode variables */
        [data-theme="dark"] {
            --primary-color: #4a5cff;
            --primary-dark: #3a4bef;
            --secondary-color: #6c757d;
            --accent-color: #5a6fff;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #5a6fff;
            --dark-color: #1a1a1a;
            --light-color: #2d2d2d;
            --white: #1e1e1e;
            --gray-100: #2d2d2d;
            --gray-200: #3a3a3a;
            --gray-300: #4a4a4a;
            --gray-600: #9ca3af;
            --gray-700: #d1d5db;
            --gray-800: #f3f4f6;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
            background-color: var(--gray-100);
            color: var(--gray-800);
        }
        
        .container-fluid, .dashboard-content, .main-content, .content {
            background: transparent;
            color: var(--gray-800);
        }
        
        /* Sidebar styling */
        #sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--white);
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        #sidebar .brand {
            padding: 1.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-family: "Cinzel Decorative", serif;
            background: rgba(255, 255, 255, 0.1);
        }
        
        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.75rem 1rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        #sidebar .nav-link:hover {
            color: var(--white);
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid var(--white);
            transform: translateX(2px);
        }
        
        #sidebar .nav-link.active {
            color: var(--white);
            background: rgba(255, 255, 255, 0.15);
            border-left: 3px solid var(--white);
            font-weight: 600;
        }
        
        #sidebar .dropdown-menu {
            background: rgba(0, 0, 0, 0.2);
            border: none;
            padding: 0;
            margin-left: 1rem;
        }
        
        #sidebar .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 0;
        }
        
        #sidebar .dropdown-item:hover {
            color: var(--white);
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Main content area */
        #content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s;
            background-color: var(--gray-100);
            padding-top: 70px; /* Account for fixed navbar */
        }
        
        /* Top navbar */
        .top-navbar {
            height: 70px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            z-index: 1020;
            transition: all 0.3s;
        }

        /* Responsive adjustments for topbar */
        @media (max-width: 768px) {
            .top-navbar {
                left: 0;
                right: 0;
            }
            
            #content {
                margin-left: 0 !important;
                padding-top: 70px;
            }
            
            #sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1030;
            }
            
            #sidebar.show {
                transform: translateX(0);
            }

            /* Mobile overlay */
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1025;
                display: none;
            }

            .mobile-overlay.show {
                display: block;
            }
        }
        
        .top-navbar .navbar-brand,
        .top-navbar .nav-link {
            color: var(--gray-700);
        }
        
        .top-navbar .nav-link:hover {
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
            background: var(--danger-color);
            color: var(--white);
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
            background: var(--primary-color);
            color: var(--white);
            font-size: 28px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ai-assistant-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(0, 123, 255, 0.4);
        }
        
        .ai-chat-container {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 350px;
            height: 500px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
        }
        
        .ai-chat-container.active {
            display: flex;
        }
        
        .ai-chat-header {
            padding: 15px;
            background: var(--primary-color);
            color: var(--white);
            border-radius: 15px 15px 0 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .ai-chat-body {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            background: var(--white);
            color: var(--gray-800);
        }
        
        .ai-chat-body .fa-robot {
            color: var(--white);
            background: var(--primary-color);
            border-radius: 50%;
            padding: 8px;
            font-size: 18px;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ai-chat-body .fa-user {
            color: var(--white);
            background: var(--gray-600);
            border-radius: 50%;
            padding: 8px;
            font-size: 18px;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ai-chat-body .card.bg-light {
            background: var(--gray-100) !important;
            color: var(--gray-800);
            border: 1px solid var(--gray-200);
        }
        
        .ai-chat-body .card.bg-primary {
            background: var(--primary-color) !important;
            color: var(--white);
            border: 1px solid var(--primary-color);
        }
        
        .ai-chat-footer {
            padding: 15px;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
            border-radius: 0 0 15px 15px;
        }
        
        /* Delete Business Modal Styling */
        .modal-danger .modal-content {
            background: var(--white);
            border: 1px solid var(--danger-color);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        
        .modal-danger .modal-header {
            background: var(--danger-color);
            color: var(--white);
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }
        
        .modal-danger .modal-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }
        
        .modal-danger .modal-title .fa-exclamation-triangle {
            color: var(--white);
            font-size: 1.2rem;
        }
        
        .modal-danger .modal-body {
            background: var(--white);
            color: var(--gray-800);
            padding: 2rem;
        }
        
        .modal-danger .modal-body .text-danger {
            color: var(--danger-color) !important;
        }
        
        .modal-danger .form-label {
            color: var(--gray-700);
            font-weight: 500;
        }
        
        .modal-danger .form-control {
            border: 1px solid var(--gray-300);
            background: var(--white);
            color: var(--gray-800);
        }
        
        .modal-danger .form-control:focus {
            border-color: var(--danger-color);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .modal-danger .btn-danger {
            background: var(--danger-color);
            border: 1px solid var(--danger-color);
            color: var(--white);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
        }
        
        .modal-danger .btn-danger:hover {
            background: #c82333;
            border-color: #c82333;
        }
        
        /* Form controls */
        .form-control {
            background: var(--white);
            color: var(--gray-800);
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            transition: all 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            background: var(--white);
            color: var(--gray-800);
        }
        
        .form-control::placeholder {
            color: var(--gray-600);
        }
        
        .form-label {
            color: var(--gray-700);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: var(--white);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: var(--white);
            transform: translateY(-1px);
        }
        
        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: var(--success-color);
            border: 1px solid var(--success-color);
            color: var(--white);
        }
        
        .btn-success:hover {
            background: #218838;
            border-color: #218838;
        }
        
        .btn-danger {
            background: var(--danger-color);
            border: 1px solid var(--danger-color);
            color: var(--white);
        }
        
        .btn-danger:hover {
            background: #c82333;
            border-color: #c82333;
        }
        
        .btn-info {
            background: var(--info-color);
            border: 1px solid var(--info-color);
            color: var(--white);
        }
        
        .btn-outline-info {
            border: 1px solid var(--info-color);
            color: var(--info-color);
            background: transparent;
        }
        
        .btn-outline-info:hover {
            background: var(--info-color);
            color: var(--white);
        }
        
        /* Cards */
        .card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            color: var(--gray-800);
        }
        
        .card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            border-radius: 10px 10px 0 0;
            padding: 1.25rem;
            color: var(--gray-800);
        }
        
        .card-header h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }
        
        .card-body {
            padding: 1.25rem;
            color: var(--gray-800);
        }
        
        /* Tables */
        .table {
            color: var(--gray-800);
        }
        
        .table th {
            background: var(--gray-50);
            color: var(--gray-700);
            font-weight: 600;
            border-bottom: 2px solid var(--gray-200);
            padding: 1rem;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .table-hover tbody tr:hover {
            background: var(--gray-50);
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border-left-color: var(--success-color);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border-left-color: var(--danger-color);
        }
        
        .alert-info {
            background: rgba(0, 123, 255, 0.1);
            color: var(--info-color);
            border-left-color: var(--info-color);
        }
        
        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border-left-color: var(--warning-color);
        }
        
        /* Enhanced Flash Messages */
        .flash-messages-container {
            position: sticky;
            top: 0;
            z-index: 1050;
        }
        
        .flash-messages-container .alert {
            margin-bottom: 0;
            border-radius: 0;
            animation: slideDown 0.3s ease-out;
            border-left-width: 5px;
            font-weight: 500;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .alert-success {
            background: linear-gradient(90deg, rgba(40, 167, 69, 0.15), rgba(40, 167, 69, 0.05));
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .alert-danger {
            background: linear-gradient(90deg, rgba(220, 53, 69, 0.15), rgba(220, 53, 69, 0.05));
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .alert-warning {
            background: linear-gradient(90deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .alert-info {
            background: linear-gradient(90deg, rgba(0, 123, 255, 0.15), rgba(0, 123, 255, 0.05));
            border: 1px solid rgba(0, 123, 255, 0.3);
        }
        
        /* Modals */
        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: var(--primary-color);
            color: var(--white);
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            padding: 1.25rem;
        }
        
        .modal-header.bg-success {
            background: var(--success-color) !important;
        }
        
        .modal-body {
            padding: 2rem;
            color: var(--gray-800);
        }
        
        .modal-footer {
            border-top: 1px solid var(--gray-200);
            padding: 1.25rem;
        }
        
        /* Text colors */
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-danger { color: var(--danger-color) !important; }
        .text-info { color: var(--info-color) !important; }
        .text-warning { color: #856404 !important; }
        .text-gray-800 { color: var(--gray-800) !important; }
        .text-muted { color: var(--gray-600) !important; }
        
        /* Utility classes */
        .shadow {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        }
        
        .shadow-sm {
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Responsive Design */
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
            
            .ai-chat-container {
                width: 300px;
                height: 400px;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 576px) {
            .ai-chat-container {
                width: 280px;
                height: 350px;
                right: -10px;
            }
            
            .container-fluid {
                padding: 0.5rem;
            }
            
            .card-header {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
        }
        
        /* Smooth animations */
        * {
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
        }
        
        /* Dark mode toggle button */
        #darkModeToggle {
            color: var(--gray-700);
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #darkModeToggle:hover {
            color: var(--primary-color);
            background-color: var(--gray-100);
            transform: scale(1.1);
        }
        
        #darkModeToggle:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* Dark mode specific styles */
        [data-theme="dark"] body {
            background-color: var(--dark-color);
            color: var(--gray-800);
        }
        
        [data-theme="dark"] .top-navbar {
            background-color: var(--white) !important;
            border-bottom: 1px solid var(--gray-200);
        }
        
        [data-theme="dark"] #darkModeToggle {
            color: var(--gray-700);
        }
        
        [data-theme="dark"] #darkModeToggle:hover {
            color: var(--primary-color);
            background-color: var(--gray-100);
        }
        
        /* Focus states for accessibility */
        .btn:focus,
        .form-control:focus,
        .nav-link:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* Loading states */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Custom scrollbar */
        .ai-chat-body::-webkit-scrollbar {
            width: 4px;
        }
        
        .ai-chat-body::-webkit-scrollbar-track {
            background: var(--gray-100);
        }
        
        .ai-chat-body::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 2px;
        }
        
        .ai-chat-body::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }
    </style>
</head>

<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
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
                
                <!-- Products - Show for product and hybrid businesses -->
                @if(auth()->user()->business && in_array(auth()->user()->business->business_type, ['product', 'hybrid']))
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
                            <li class="nav-item">
                                <a href="{{ route('categories.index', ['business_id' => auth()->user()->business->id]) }}" class="nav-link">
                                    <i class="fas fa-tags me-2"></i>
                                    {{ t('categories') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('brands.index', ['business_id' => auth()->user()->business->id]) }}" class="nav-link">
                                    <i class="fas fa-award me-2"></i>
                                    {{ t('brands') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                
                <!-- DEBUG: Business Type Check -->
                @if(auth()->user()->business)
                    <!-- Debug: Business Type = {{ auth()->user()->business->business_type }} -->
                    <!-- Debug: Is Service/Hybrid = {{ in_array(auth()->user()->business->business_type, ['service', 'hybrid']) ? 'YES' : 'NO' }} -->
                @else
                    <!-- Debug: No business found for user -->
                @endif
                
                <!-- Services - Show for service and hybrid businesses -->
                <!-- @if(auth()->user()->business && in_array(auth()->user()->business->business_type, ['service', 'hybrid'])) -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#servicesCollapse" role="button">
                        <i class="fas fa-cut me-2"></i>
                        Services
                    </a>
                    <div class="collapse" id="servicesCollapse">
                        <ul class="nav flex-column ps-3">
                            <li class="nav-item">
                                <a href="{{ route('services.index') }}" class="nav-link">
                                    <i class="fas fa-list me-2"></i> Manage Services
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('staff.index') }}" class="nav-link">
                                    <i class="fas fa-users me-2"></i> Staff
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('service-bookings.index') }}" class="nav-link">
                                    <i class="fas fa-clipboard-list me-2"></i> Service Bookings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('costs.index') }}" class="nav-link">
                                    <i class="fas fa-money-bill-wave me-2"></i> Costs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('commission-reports') }}" class="nav-link">
                                    <i class="fas fa-hand-holding-usd me-2"></i> Commission Reports
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- @endif -->
                
                <!-- Sales -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#salesCollapse" role="button">
                        <i class="fas fa-cash-register me-2"></i>
                        {{ t('sales') }}
                    </a>
                    <div class="collapse" id="salesCollapse">
                        <ul class="nav flex-column ps-3">
                            @if(auth()->user()->business && in_array(auth()->user()->business->business_type, ['product', 'hybrid']))
                            <li class="nav-item">
                                <a href="{{ route('sales.pos') }}" class="nav-link">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    {{ t('pos_system') }}
                                </a>
                            </li>
                            @endif
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
                                    <i class="fas fa-users"></i>
                                    <span>Customers</span>
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
                
                <!-- Suppliers - Show for product and hybrid businesses -->
                @if(auth()->user()->business && in_array(auth()->user()->business->business_type, ['product', 'hybrid']))
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}" class="nav-link">
                        <i class="fas fa-truck me-2"></i>
                        {{ t('suppliers') }}
                    </a>
                </li>
                @endif
                <!-- Customers -->
                <li class="nav-item">
                    <a href="{{ route('sales.customers') }}" class="nav-link">
                        <i class="fas fa-users me-2"></i>
                        Customers
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
                                <a href="{{ route('employees.index') }}" class="nav-link">
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
                    
                    <!-- Dark Mode Toggle -->
                    <div class="me-3">
                        <button class="btn btn-link p-0 border-0 bg-transparent" id="darkModeToggle" title="Toggle Dark Mode">
                            <i class="fas fa-moon fa-lg" id="darkModeIcon"></i>
                        </button>
                    </div>
                    
                    <!-- User Profile -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://placehold.co/600x400/png" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
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
        
        <!-- Flash Messages - Positioned at top for immediate visibility -->
        <div class="flash-messages-container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 position-relative" role="alert" style="z-index: 1050;">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3 position-relative" role="alert" style="z-index: 1050;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show m-3 position-relative" role="alert" style="z-index: 1050;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show m-3 position-relative" role="alert" style="z-index: 1050;">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

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
            // Dark Mode Toggle
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const body = document.body;
            
            // Check for saved theme preference or default to light mode
            const savedTheme = localStorage.getItem('theme') || 'light';
            body.setAttribute('data-theme', savedTheme);
            updateDarkModeIcon(savedTheme);
            
            darkModeToggle.addEventListener('click', function() {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateDarkModeIcon(newTheme);
            });
            
            function updateDarkModeIcon(theme) {
                if (theme === 'dark') {
                    darkModeIcon.classList.remove('fa-moon');
                    darkModeIcon.classList.add('fa-sun');
                    darkModeToggle.title = 'Switch to Light Mode';
                } else {
                    darkModeIcon.classList.remove('fa-sun');
                    darkModeIcon.classList.add('fa-moon');
                    darkModeToggle.title = 'Switch to Dark Mode';
                }
            }
            
            // Sidebar Toggle
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                mobileOverlay.classList.toggle('show');
            });

            // Close sidebar when clicking overlay
            mobileOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                mobileOverlay.classList.remove('show');
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
        
        // Flash Message Auto-scroll and Enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const flashContainer = document.querySelector('.flash-messages-container');
            if (flashContainer && flashContainer.children.length > 0) {
                // Scroll to flash message immediately
                flashContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Auto-dismiss after 5 seconds unless user interacts
                setTimeout(function() {
                    const alerts = flashContainer.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        if (alert && !alert.matches(':hover')) {
                            const bsAlert = new bootstrap.Alert(alert);
                            if (bsAlert) {
                                bsAlert.close();
                            }
                        }
                    });
                }, 5000);
            }
            
            // Prevent auto-dismiss on hover
            const alerts = document.querySelectorAll('.flash-messages-container .alert');
            alerts.forEach(function(alert) {
                alert.addEventListener('mouseenter', function() {
                    alert.classList.add('stay-open');
                });
                alert.addEventListener('mouseleave', function() {
                    alert.classList.remove('stay-open');
                });
            });
        });
    </script>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</body>
</html>