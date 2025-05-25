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

    <style>
        /* Sidebar base styling */





        /* Dropdown toggle style */
        .dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        /* Dropdown menu */
        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding-left: 1.5rem;
            z-index: 3000;
            width: 100%;
        }

        .dropdown-menu li {
            padding: 0.5rem 0;
        }

        /* Active/open dropdown */
        .dropdown.open .dropdown-menu {
            max-height: 500px;
            /* Large enough to accommodate all items */
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        /* Icon rotation when open */
        .dropdown.open .dropdown-icon {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Modal Content */
        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(-50px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        /* Modal Header */
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f44336;
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.25rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
            padding: 0 5px;
        }

        /* Modal Body */
        .modal-body {
            padding: 20px;
        }

        .warning-text {
            color: #f44336;
            font-weight: bold;
            margin: 10px 0;
        }

        .form-group {
            margin-top: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .error-message {
            color: #f44336;
            font-size: 0.875rem;
            margin-top: 5px;
            min-height: 20px;
        }

        /* Modal Footer */
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .cancel-btn,
        .delete-btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .cancel-btn {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .modal-content {
                width: 95%;
            }

            .modal-footer {
                flex-direction: column;
            }

            .cancel-btn,
            .delete-btn {
                width: 100%;
            }
        }
    </style>


</head>

<body>
    <!-- Sidebar with dropdown functionality -->
    <section id="sidebar">
        <!-- Brand Section -->
        <a href="{{ route('index') }}" class="brand">
            <i class='bx bxs-store-alt bx-lg'></i>
            <span class="text">Shopybook</span>
        </a>

        <!-- Main Menu -->
        <ul class="side-menu top">
            <!-- Dashboard -->
            <li class="active">
                <a href="{{ route('dashboard') }}">
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
                            <a href="#" data-delete-business>
                                <i class='bx bxs-trash'></i>
                                <span>Delete Business</span>
                            </a>
                            <form id="delete-business-form" action="{{ route('business.destroy', $business->id) }}"
                                method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="password" id="formPassword">
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
                        <a href="#">
                            <i class='bx bxs-grid'></i>
                            <span>All Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class='bx bxs-plus-circle'></i>
                            <span>Add New Product</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class='bx bxs-cloud-upload'></i>
                            <span>Bulk Import</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Invoices -->
            <li>
                <a href="#">
                    <i class='bx bxs-receipt bx-sm'></i>
                    <span class="text">Invoices</span>
                </a>
            </li>
        </ul>


        <!-- Bottom Menu -->
        <ul class="side-menu bottom">
            <!-- Settings -->
            <li>
                <a href="#">
                    <i class='bx bxs-cog bx-sm bx-spin-hover'></i>
                    <span class="text">Settings</span>
                </a>
            </li>

            <!-- Logout -->
            <li>
                <a href="{{ route('logout') }}" class="logout">
                    <i class='bx bx-power-off bx-sm bx-burst-hover'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>

    <!-- CONTENT -->
    <section id="content">
        <!-- Modal Overlay -->
        <div id="deleteBusinessModal" class="modal-overlay">
            <!-- Modal Content -->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Confirm Deletion</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete this business and all its data?</p>
                    <p class="warning-text">This action cannot be undone!</p>

                    <div class="form-group">
                        <label for="passwordConfirmation">Enter your password to confirm:</label>
                        <input type="password" id="passwordConfirmation" placeholder="Your account password" required>
                        <div id="passwordError" class="error-message"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="cancel-btn">Cancel</button>
                    <button id="confirmDeleteBtn" class="delete-btn">Delete Business</button>
                </div>
            </div>
        </div>
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
                    <li><a href="{{ route('logout') }}">Log Out</a></li>
                </ul>
            </div>
        </nav>
        <!-- NAVBAR -->

        @yield('content')
    </section>
    <!-- CONTENT -->

    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Then load Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Load Chart.js first -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const modal = document.getElementById('deleteBusinessModal');
        const modalContent = modal.querySelector('.modal-content');
        const closeBtn = modal.querySelector('.modal-close');
        const cancelBtn = modal.querySelector('.cancel-btn');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const passwordInput = document.getElementById('passwordConfirmation');
        const passwordError = document.getElementById('passwordError');
        const deleteLinks = document.querySelectorAll('[data-delete-business]');

        // Show modal function
        function showModal() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        // Hide modal function
        function hideModal() {
            modal.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
            passwordInput.value = '';
            passwordInput.classList.remove('error');
            passwordError.textContent = '';
        }

        // Show modal when delete is clicked
        deleteLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                showModal();
            });
        });

        // Close modal when X, cancel, or overlay is clicked
        closeBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                hideModal();
            }
        });

        // Prevent modal content from closing modal when clicked
        modalContent.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        async function verifyPassword(password) {
            try {
                const response = await fetch('{{ route("password.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ password })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Verification failed');
                }

                return await response.json();
            } catch (error) {
                console.error('Verification error:', error);
                throw error;
            }
        }

        confirmBtn.addEventListener('click', async function () {
            // Reset UI state
            passwordInput.classList.remove('error');
            passwordError.textContent = '';
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';

            try {
                const data = await verifyPassword(passwordInput.value);

                if (data.valid) {
                    document.getElementById('formPassword').value = passwordInput.value;
                    document.getElementById('delete-business-form').submit();
                } else {
                    passwordInput.classList.add('error');
                    passwordError.textContent = data.message || 'Incorrect password';
                }
            } catch (error) {
                passwordInput.classList.add('error');
                passwordError.textContent = error.message || 'Failed to verify password';
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Delete Business';
            }
        });
        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                hideModal();
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();

                const parent = this.closest('.dropdown');
                parent.classList.toggle('open');

                // Optional: Close other dropdowns when one opens
                document.querySelectorAll('.dropdown').forEach(drop => {
                    if (drop !== parent) {
                        drop.classList.remove('open');
                    }
                });
            });
        });
    });
</script>