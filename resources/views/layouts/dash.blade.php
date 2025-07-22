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
        
        /* Prevent body scroll when mobile menu is open */
        body.sidebar-open {
            overflow: hidden;
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
            top: 0;
            left: 0;
            transition: all 0.3s ease-in-out;
            z-index: 1040;
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
            padding: 0 1rem;
        }

        /* Ensure sidebar is always visible on desktop */
        @media (min-width: 992px) {
            #sidebar {
                transform: translateX(0) !important;
            }
            
            /* Hide mobile overlay on desktop */
            .mobile-overlay {
                display: none !important;
            }
            
            /* Ensure content is properly offset */
            #content {
                margin-left: 250px;
            }
            
            /* Hide mobile menu toggle on desktop */
            #sidebarToggle {
                display: none !important;
            }
        }

        /* Mobile styles */
        @media (max-width: 992px) {
            #sidebar {
                transform: translateX(-100%);
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                width: 250px;
                z-index: 1050 !important;
                transition: transform 0.3s ease-in-out;
            }
            
            #sidebar.show {
                transform: translateX(0) !important;
            }
            
            #content {
                margin-left: 0 !important;
            }
            
            .top-navbar {
                left: 0;
            }
            
            /* Mobile overlay */
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1045;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease-in-out;
            }
            
            .mobile-overlay.show {
                display: block !important;
                opacity: 1 !important;
            }
            
            /* Prevent body scroll when sidebar is open */
            body.sidebar-open {
                overflow: hidden;
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
        
        .modal-danger .text-danger {
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
            /* Hide search bar on mobile, move to sidebar */
            .desktop-search {
                display: none;
            }
            
            /* Show dark mode toggle only on mobile topbar */
            .mobile-dark-toggle {
                display: block;
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
            
            /* Mobile Card Optimizations */
            .mobile-card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 1rem;
            }
            
            .mobile-card .card-body {
                padding: 1rem;
            }
            
            /* Replace tables with cards on mobile */
            .table-mobile-cards .table {
                display: none;
            }
            
            .table-mobile-cards .mobile-cards-container {
                display: block;
            }
            
            /* Mobile card item styling */
            .mobile-card-item {
                background: var(--white);
                border: 1px solid var(--gray-200);
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }
            
            .mobile-card-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                transform: translateY(-1px);
            }
            
            .mobile-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.5rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid var(--gray-200);
            }
            
            .mobile-card-title {
                font-weight: 600;
                color: var(--primary-color);
                margin: 0;
                font-size: 1rem;
            }
            
            .mobile-card-badge {
                font-size: 0.75rem;
            }
            
            .mobile-card-content {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }
            
            .mobile-card-field {
                display: flex;
                flex-direction: column;
            }
            
            .mobile-card-field label {
                font-size: 0.75rem;
                color: var(--gray-600);
                margin-bottom: 0.25rem;
                font-weight: 500;
            }
            
            .mobile-card-field span {
                font-weight: 600;
                color: var(--gray-800);
            }
            
            .mobile-card-actions {
                margin-top: 1rem;
                padding-top: 0.75rem;
                border-top: 1px solid var(--gray-200);
                display: flex;
                gap: 0.5rem;
                flex-wrap: wrap;
            }
            
            .mobile-card-actions .btn {
                flex: 1;
                min-width: auto;
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            
            /* Top navbar mobile responsive */
            .top-navbar .container-fluid {
                padding: 0 0.5rem;
            }
            
            .top-navbar .btn-link {
                padding: 0.5rem;
                margin: 0;
            }
            
            /* Ensure notifications and dark mode icons are properly spaced */
            .top-navbar .d-flex {
                gap: 0.25rem;
            }
            
            .top-navbar .dropdown, 
            .top-navbar button {
                flex-shrink: 0;
            }
            
            /* Hide brand text on very small screens */
            @media (max-width: 400px) {
                .navbar-brand {
                    display: none !important;
                }
                
                .top-navbar .container-fluid {
                    padding: 0 0.25rem;
                }
                
                .top-navbar .btn-link {
                    padding: 0.375rem;
                }
                
                .ai-chat-container {
                    width: 280px;
                    height: 350px;
                    right: -10px;
                }
            }
        }
        
        @media (min-width: 769px) {
            /* Hide mobile cards on desktop */
            .mobile-cards-container {
                display: none;
            }
            
            .table-mobile-cards .table {
                display: table;
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
                
                <!-- Services - Show for service and hybrid businesses -->
                @if(auth()->user()->business && in_array(auth()->user()->business->business_type, ['service', 'hybrid']))
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
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#inventorySubmenu" aria-expanded="false" aria-controls="inventorySubmenu">
                                    <i class="fas fa-boxes me-2"></i> Inventory Management
                                </a>
                                <div class="collapse" id="inventorySubmenu">
                                    <ul class="list-unstyled ps-4">
                                        <li><a href="{{ route('inventory.index') }}" class="dropdown-item"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                        <li><a href="{{ route('inventory.create') }}" class="dropdown-item"><i class="fas fa-plus me-2"></i>Add Item</a></li>
                                        <li><a href="{{ route('inventory.low-stock') }}" class="dropdown-item"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock</a></li>
                                        <li><a href="{{ route('inventory.reports') }}" class="dropdown-item"><i class="fas fa-chart-line me-2"></i>Reports</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('costs.index') }}" class="nav-link">
                                    <i class="fas fa-money-bill-wave me-2"></i> Costs & Expenses
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
                @endif
                
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
            <!-- User Profile Section -->
            <div class="border-top border-light border-opacity-10 pt-3 mb-3">
                <div class="nav-item">
                    <div class="d-flex align-items-center px-3 py-2">
                        <img src="https://placehold.co/600x400/png" alt="Profile" class="rounded-circle me-3" width="40" height="40">
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-white">{{ Auth::user()->name }}</div>
                            <small class="text-white-50">{{ Auth::user()->email }}</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <ul class="nav flex-column">
                <!-- Search -->
                <li class="nav-item">
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search me-2"></i>
                        {{ t('search') }}
                    </a>
                </li>
                
                <!-- Language Switcher -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#languageCollapse" role="button">
                        <i class="fas fa-globe me-2"></i>
                        {{ t('language') }}
                    </a>
                    <div class="collapse" id="languageCollapse">
                        <div class="ps-4">
                            @include('components.language-switcher')
                        </div>
                    </div>
                </li>
                
                <!-- Dark Mode Toggle (for mobile in sidebar) -->
                <li class="nav-item d-md-none">
                    <a href="#" class="nav-link" id="sidebarDarkModeToggle">
                        <i class="fas fa-moon me-2" id="sidebarDarkModeIcon"></i>
                        <span id="sidebarDarkModeText">{{ t('dark_mode') }}</span>
                    </a>
                </li>
                
                <!-- Profile -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-user me-2"></i>
                        {{ t('profile') }}
                    </a>
                </li>
                
                <!-- Settings -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#settingsCollapse" role="button">
                        <i class="fas fa-cog me-2"></i>
                        {{ t('settings') }}
                    </a>
                    <div class="collapse" id="settingsCollapse">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item d-none d-md-block">
                                <a href="#" class="nav-link" id="desktopSidebarDarkModeToggle">
                                    <i class="fas fa-moon me-2" id="desktopSidebarDarkModeIcon"></i>
                                    <span id="desktopSidebarDarkModeText">{{ t('dark_mode') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-bell me-2"></i>
                                    {{ t('notifications') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    {{ t('privacy') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Logout -->
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
        <!-- Top Navbar - Minimal Design -->
        <nav class="navbar top-navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <!-- Left Side: Menu Toggle (Hidden on desktop) -->
                <button class="btn btn-link d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                
                <!-- Center: Brand Logo for Mobile (Hidden on desktop) -->
                <div class="d-lg-none mx-auto">
                    <span class="navbar-brand mb-0 h1 text-primary fw-bold d-none d-sm-inline">Shopybook</span>
                </div>
                
                <!-- Desktop Search Bar (Hidden on Mobile) -->
                <form class="d-none d-lg-flex ms-3 me-auto desktop-search">
                    <input class="form-control me-2" type="search" placeholder="{{ t('search_products_orders') }}" aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <!-- Right Side: Essential Icons -->
                <div class="d-flex align-items-center">
                    <!-- Notifications -->
                    <div class="dropdown me-2">
                        <a class="position-relative btn btn-link p-2" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
                    <button class="btn btn-link p-2" id="darkModeToggle" title="Toggle Dark Mode">
                        <i class="fas fa-moon fa-lg" id="darkModeIcon"></i>
                    </button>
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

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">
                        <i class="fas fa-search me-2"></i>{{ t('search') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="{{ t('search_products_orders') }}" aria-label="Search">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div class="mt-3">
                        <h6 class="text-muted">{{ t('quick_search') }}</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark">{{ t('products') }}</span>
                            <span class="badge bg-light text-dark">{{ t('orders') }}</span>
                            <span class="badge bg-light text-dark">{{ t('customers') }}</span>
                            <span class="badge bg-light text-dark">{{ t('services') }}</span>
                        </div>
                    </div>
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
            // Dark Mode Toggle - Multiple Elements
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const sidebarDarkModeToggle = document.getElementById('sidebarDarkModeToggle');
            const sidebarDarkModeIcon = document.getElementById('sidebarDarkModeIcon');
            const sidebarDarkModeText = document.getElementById('sidebarDarkModeText');
            const desktopSidebarDarkModeToggle = document.getElementById('desktopSidebarDarkModeToggle');
            const desktopSidebarDarkModeIcon = document.getElementById('desktopSidebarDarkModeIcon');
            const desktopSidebarDarkModeText = document.getElementById('desktopSidebarDarkModeText');
            const body = document.body;
            
            // Check for saved theme preference or default to light mode
            const savedTheme = localStorage.getItem('theme') || 'light';
            body.setAttribute('data-theme', savedTheme);
            updateDarkModeIcon(savedTheme);
            
            // Add event listeners to all dark mode toggle buttons
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', toggleDarkMode);
            }
            if (sidebarDarkModeToggle) {
                sidebarDarkModeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleDarkMode();
                });
            }
            if (desktopSidebarDarkModeToggle) {
                desktopSidebarDarkModeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleDarkMode();
                });
            }
            
            function toggleDarkMode() {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateDarkModeIcon(newTheme);
            }
            
            function updateDarkModeIcon(theme) {
                const isDark = theme === 'dark';
                const iconClass = isDark ? 'fa-sun' : 'fa-moon';
                const iconClassToRemove = isDark ? 'fa-moon' : 'fa-sun';
                const title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
                const text = isDark ? '{{ t("light_mode") }}' : '{{ t("dark_mode") }}';
                
                // Update topbar icon
                if (darkModeIcon) {
                    darkModeIcon.classList.remove(iconClassToRemove);
                    darkModeIcon.classList.add(iconClass);
                    darkModeToggle.title = title;
                }
                
                // Update sidebar mobile icon
                if (sidebarDarkModeIcon) {
                    sidebarDarkModeIcon.classList.remove(iconClassToRemove);
                    sidebarDarkModeIcon.classList.add(iconClass);
                    sidebarDarkModeText.textContent = text;
                }
                
                // Update sidebar desktop icon
                if (desktopSidebarDarkModeIcon) {
                    desktopSidebarDarkModeIcon.classList.remove(iconClassToRemove);
                    desktopSidebarDarkModeIcon.classList.add(iconClass);
                    desktopSidebarDarkModeText.textContent = text;
                }
            }
            
            // Sidebar Toggle - Fixed Implementation
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Toggle sidebar on mobile
                    if (window.innerWidth <= 992) {
                        if (sidebar) {
                            const isCurrentlyShown = sidebar.classList.contains('show');
                            
                            if (isCurrentlyShown) {
                                sidebar.classList.remove('show');
                                document.body.classList.remove('sidebar-open');
                                if (mobileOverlay) mobileOverlay.classList.remove('show');
                            } else {
                                sidebar.classList.add('show');
                                document.body.classList.add('sidebar-open');
                                if (mobileOverlay) mobileOverlay.classList.add('show');
                            }
                        }
                    }
                });
            }

            // Close sidebar when clicking overlay
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (sidebar) {
                        sidebar.classList.remove('show');
                    }
                    mobileOverlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                // Only apply on mobile screens
                if (window.innerWidth <= 992) {
                    if (sidebar && sidebar.classList.contains('show')) {
                        const isClickInsideSidebar = sidebar.contains(e.target);
                        const isClickOnToggle = sidebarToggle && sidebarToggle.contains(e.target);
                        
                        if (!isClickInsideSidebar && !isClickOnToggle) {
                            sidebar.classList.remove('show');
                            document.body.classList.remove('sidebar-open');
                            if (mobileOverlay) {
                                mobileOverlay.classList.remove('show');
                            }
                        }
                    }
                }
            });

            // Handle window resize
            function handleResize() {
                if (window.innerWidth > 992) {
                    // On desktop, ensure sidebar is visible and overlay is hidden
                    if (sidebar) sidebar.classList.remove('show');
                    if (mobileOverlay) mobileOverlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                }
            }

            // Debounce resize events
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(handleResize, 250);
            });

            // Initialize on load
            handleResize();
            
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