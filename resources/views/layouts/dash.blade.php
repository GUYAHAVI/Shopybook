<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- My CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Shopybook Dashboard</title>
</head>
<body>
<!-- Updated SIDEBAR with dropdown functionality -->
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-store-alt bx-lg'></i>
        <span class="text">Shopybook</span>
    </a>
    <ul class="side-menu top">
        <li class="active">
            <a href="#">
                <i class='bx bxs-dashboard bx-sm'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        
        <!-- My Business Dropdown -->
        <li class="dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-business bx-sm'></i>
                <span class="text">My Business</span>
                <i class='bx bx-chevron-down dropdown-icon'></i>
            </a>
            <ul class="dropdown-menu">
                @if(auth()->user()->business)
                    <li>
                        <a href="{{ route('business.edit') }}">
                            <i class='bx bxs-edit-alt'></i>
                            <span>Edit Business Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('delete-business-form').submit();">
                            <i class='bx bxs-trash'></i>
                            <span>Delete Business</span>
                        </a>
                        <form id="delete-business-form" action="{{ route('business.destroy') }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('business.create') }}">
                            <i class='bx bxs-plus-circle'></i>
                            <span>Create Business Profile</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        
        <!-- Products Dropdown -->
        <li class="dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-package bx-sm'></i>
                <span class="text">Products</span>
                <i class='bx bx-chevron-down dropdown-icon'></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('products.index') }}">
                        <i class='bx bxs-grid'></i>
                        <span>All Products</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.create') }}">
                        <i class='bx bxs-plus-circle'></i>
                        <span>Add New Product</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.import') }}">
                        <i class='bx bxs-cloud-upload'></i>
                        <span>Bulk Import</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Other menu items remain the same -->
        <li>
            <a href="#">
                <i class='bx bxs-receipt bx-sm'></i>
                <span class="text">Invoices</span>
            </a>
        </li>
        <!-- ... rest of your menu items ... -->
    </ul>
    
    <!-- Bottom menu remains unchanged -->
    <ul class="side-menu bottom">
        <li>
            <a href="#">
                <i class='bx bxs-cog bx-sm bx-spin-hover'></i>
                <span class="text">Settings</span>
            </a>
        </li>
        <li>
            <a href="#" class="logout">
                <i class='bx bx-power-off bx-sm bx-burst-hover'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu bx-sm'></i>
            <a href="#" class="nav-link">Quick Actions</a>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search products, orders...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" class="checkbox" id="switch-mode" hidden />
            <label class="swith-lm" for="switch-mode">
                <i class="bx bxs-sun"></i>
                <i class="bx bx-moon"></i>
                <div class="ball"></div>
            </label>

            <!-- Notification Bell -->
            <a href="#" class="notification" id="notificationIcon">
                <i class='bx bxs-bell bx-tada-hover'></i>
                <span class="num">3</span>
            </a>
            <div class="notification-menu" id="notificationMenu">
                <ul>
                    <li>New order #SHB-1024</li>
                    <li>Inventory low on T-Shirts</li>
                    <li>AI Business Tip: Run a weekend promo</li>
                </ul>
            </div>

            <!-- Profile Menu -->
            <a href="#" class="profile" id="profileIcon">
                <img src="https://placehold.co/600x400/png" alt="Profile">
            </a>
            <div class="profile-menu" id="profileMenu">
                <ul>
                    <li><a href="#">Business Profile</a></li>
                    <li><a href="#">Account Settings</a></li>
                    <li><a href="#">Log Out</a></li>
                </ul>
            </div>
        </nav>
        <!-- NAVBAR -->

        @yield('content')
    </section>
    <!-- CONTENT -->
    <!-- Load Chart.js first -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>