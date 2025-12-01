@extends('layouts.master')
@section('title', 'User Guide & Documentation - Shopybook')
@section('meta_description', 'Complete user guide and documentation for Shopybook business management platform. Learn how to manage your products, staff, services, and generate reports.')
@section('content')

<style>
body, html {
    background: #020258 !important;
    color: #fff !important;
}

h1, h2, h3, h4, h5, h6 {
    color: #13e8e9 !important;
}

.docs-hero {
    background: linear-gradient(135deg, #020258 0%, #0a0a7a 100%);
    padding: 120px 0 80px;
    position: relative;
    overflow: hidden;
}

.docs-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 50%, rgba(19, 232, 233, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(19, 232, 233, 0.08) 0%, transparent 50%);
}

.docs-container {
    position: relative;
    z-index: 1;
}

.docs-sidebar {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid #13e8e9;
    border-radius: 15px;
    padding: 30px;
    position: sticky;
    top: 100px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.docs-sidebar::-webkit-scrollbar {
    width: 8px;
}

.docs-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.docs-sidebar::-webkit-scrollbar-thumb {
    background: #13e8e9;
    border-radius: 10px;
}

.docs-content {
    background: rgba(255, 255, 255, 0.03);
    border: 2px solid rgba(19, 232, 233, 0.3);
    border-radius: 15px;
    padding: 40px;
}

.docs-section {
    margin-bottom: 60px;
    scroll-margin-top: 100px;
}

.docs-nav-link {
    display: block;
    padding: 12px 20px;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
    margin-bottom: 8px;
    border-left: 3px solid transparent;
}

.docs-nav-link:hover, .docs-nav-link.active {
    background: rgba(19, 232, 233, 0.1);
    border-left-color: #13e8e9;
    color: #13e8e9;
    padding-left: 25px;
}

.docs-nav-link i {
    margin-right: 10px;
    width: 20px;
}

.step-card {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(19, 232, 233, 0.5);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    transition: all 0.3s;
}

.step-card:hover {
    border-color: #13e8e9;
    box-shadow: 0 5px 20px rgba(19, 232, 233, 0.2);
    transform: translateY(-5px);
}

.step-number {
    width: 40px;
    height: 40px;
    background: #13e8e9;
    color: #020258;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    margin-right: 15px;
}

.feature-badge {
    display: inline-block;
    background: rgba(19, 232, 233, 0.2);
    color: #13e8e9;
    border: 1px solid #13e8e9;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    margin: 5px;
}

.screenshot-placeholder {
    background: rgba(255, 255, 255, 0.08);
    border: 2px dashed rgba(19, 232, 233, 0.5);
    border-radius: 12px;
    padding: 60px 20px;
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
    margin: 20px 0;
}

code {
    background: rgba(19, 232, 233, 0.2);
    color: #13e8e9;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}

.tip-box {
    background: rgba(19, 232, 233, 0.1);
    border-left: 4px solid #13e8e9;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.tip-box i {
    color: #13e8e9;
    font-size: 24px;
    margin-right: 15px;
}

.btn-primary {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 8px;
}

.btn-primary:hover {
    background: #020258 !important;
    color: #13e8e9 !important;
}

.btn-outline-primary {
    background: transparent !important;
    color: #13e8e9 !important;
    border: 2px solid #13e8e9 !important;
}

.btn-outline-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
}
</style>

<!-- Hero Section -->
<div class="docs-hero">
    <div class="container docs-container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-book-open me-3"></i>User Guide & Documentation
                </h1>
                <p class="lead mb-4">
                    Everything you need to know to master Shopybook and grow your business.
                    From getting started to advanced features, we've got you covered.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="#getting-started" class="btn btn-primary">
                        <i class="fas fa-rocket me-2"></i>Get Started
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Documentation Content -->
<div class="container my-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="docs-sidebar">
                <h5 class="mb-4"><i class="fas fa-bars me-2"></i>Contents</h5>
                <nav>
                    <a href="#getting-started" class="docs-nav-link active">
                        <i class="fas fa-rocket"></i>Getting Started
                    </a>
                    <a href="#account-setup" class="docs-nav-link">
                        <i class="fas fa-user-cog"></i>Account Setup
                    </a>
                    <a href="#business-setup" class="docs-nav-link">
                        <i class="fas fa-building"></i>Business Setup
                    </a>
                    <a href="#products" class="docs-nav-link">
                        <i class="fas fa-box"></i>Products
                    </a>
                    <a href="#inventory" class="docs-nav-link">
                        <i class="fas fa-warehouse"></i>Inventory
                    </a>
                    <a href="#sales" class="docs-nav-link">
                        <i class="fas fa-shopping-cart"></i>Sales & POS
                    </a>
                    <a href="#services" class="docs-nav-link">
                        <i class="fas fa-concierge-bell"></i>Services
                    </a>
                    <a href="#staff" class="docs-nav-link">
                        <i class="fas fa-users"></i>Staff Management
                    </a>
                    <a href="#suppliers" class="docs-nav-link">
                        <i class="fas fa-truck"></i>Suppliers
                    </a>
                    <a href="#returns" class="docs-nav-link">
                        <i class="fas fa-undo"></i>Returns & Refunds
                    </a>
                    <a href="#reports" class="docs-nav-link">
                        <i class="fas fa-chart-bar"></i>Reports
                    </a>
                    <a href="#settings" class="docs-nav-link">
                        <i class="fas fa-cog"></i>Settings
                    </a>
                </nav>
            </div>
        </div>

        <!-- Documentation Content -->
        <div class="col-lg-9">
            <div class="docs-content">
                
                <!-- Getting Started -->
                <section id="getting-started" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-rocket me-3"></i>Getting Started</h2>
                    <p class="lead">Welcome to Shopybook! This guide will help you set up and use all features of our platform.</p>
                    
                    <div class="tip-box">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Quick Tip:</strong> Shopybook is designed for ease of use. Follow these steps in order for the smoothest setup experience.
                            </div>
                        </div>
                    </div>

                    <h4 class="mt-4 mb-3">What is Shopybook?</h4>
                    <p>Shopybook is a comprehensive business management platform designed specifically for Kenyan small businesses. It helps you manage:</p>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <span class="feature-badge"><i class="fas fa-check me-2"></i>Product Inventory</span>
                            <span class="feature-badge"><i class="fas fa-check me-2"></i>Sales & POS</span>
                            <span class="feature-badge"><i class="fas fa-check me-2"></i>Services</span>
                        </div>
                        <div class="col-md-6">
                            <span class="feature-badge"><i class="fas fa-check me-2"></i>Staff</span>
                            <span class="feature-badge"><i class="fas fa-check me-2"></i>Customers</span>
                            <span class="feature-badge"><i class="fas fa-check me-2"></i>Reports</span>
                        </div>
                    </div>
                </section>

                <!-- Account Setup -->
                <section id="account-setup" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-user-cog me-3"></i>Account Setup</h2>
                    
                    <h4 class="mb-3">Step 1: Create Your Account</h4>
                    <div class="step-card">
                        <div class="d-flex align-items-start">
                            <div class="step-number">1</div>
                            <div class="flex-grow-1">
                                <h5>Visit Registration Page</h5>
                                <p class="mb-2">Go to <code>{{ route('register') }}</code> or click "Get Started" on the homepage.</p>
                                <ul>
                                    <li>Enter your full name</li>
                                    <li>Provide a valid email address</li>
                                    <li>Create a strong password</li>
                                    <li>Confirm your password</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="d-flex align-items-start">
                            <div class="step-number">2</div>
                            <div class="flex-grow-1">
                                <h5>Verify Your Email</h5>
                                <p class="mb-2">Check your email inbox for a verification link from Shopybook.</p>
                                <p class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Check your spam folder if you don't see it within 5 minutes.</p>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="d-flex align-items-start">
                            <div class="step-number">3</div>
                            <div class="flex-grow-1">
                                <h5>Login to Dashboard</h5>
                                <p class="mb-0">After verification, login at <code>{{ route('login') }}</code> to access your dashboard.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Business Setup -->
                <section id="business-setup" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-building me-3"></i>Business Setup</h2>
                    
                    <h4 class="mb-3">Setting Up Your Business Profile</h4>
                    <div class="step-card">
                        <div class="d-flex align-items-start">
                            <div class="step-number">1</div>
                            <div class="flex-grow-1">
                                <h5>Choose Business Type</h5>
                                <p>Select what describes your business best:</p>
                                <ul>
                                    <li><strong>Product-Based:</strong> Sell physical products (retail, wholesale)</li>
                                    <li><strong>Service-Based:</strong> Offer services (salon, garage, consultancy)</li>
                                    <li><strong>Both:</strong> Sell products and offer services</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="d-flex align-items-start">
                            <div class="step-number">2</div>
                            <div class="flex-grow-1">
                                <h5>Complete Business Information</h5>
                                <p>Fill in the following details:</p>
                                <ul>
                                    <li>Business name</li>
                                    <li>Business email</li>
                                    <li>Phone number</li>
                                    <li>Physical address</li>
                                    <li>Business registration number (optional)</li>
                                    <li>KRA PIN (optional)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="d-flex align-items-start">
                            <div class="step-number">3</div>
                            <div class="flex-grow-1">
                                <h5>Upload Business Logo</h5>
                                <p class="mb-0">Add your business logo for a professional appearance on receipts and invoices.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Products -->
                <section id="products" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-box me-3"></i>Product Management</h2>
                    
                    <h4 class="mb-3">Adding Products</h4>
                    <div class="step-card">
                        <h5><i class="fas fa-plus-circle me-2"></i>Single Product Entry</h5>
                        <p>Navigate to <strong>Products → Add Product</strong></p>
                        <ul>
                            <li>Enter product name</li>
                            <li>Set SKU (Stock Keeping Unit)</li>
                            <li>Add description</li>
                            <li>Set selling price</li>
                            <li>Set cost price (for profit tracking)</li>
                            <li>Select category</li>
                            <li>Add initial stock quantity</li>
                            <li>Set low stock threshold</li>
                            <li>Upload product image</li>
                        </ul>
                    </div>

                    <div class="step-card">
                        <h5><i class="fas fa-upload me-2"></i>Bulk Import</h5>
                        <p>Import multiple products at once using Excel/CSV:</p>
                        <ol>
                            <li>Go to <strong>Products → Bulk Import</strong></li>
                            <li>Download the template file</li>
                            <li>Fill in your products (name, price, stock, etc.)</li>
                            <li>Upload the completed file</li>
                            <li>Review and confirm the import</li>
                        </ol>
                    </div>

                    <div class="tip-box">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-star"></i>
                            <div>
                                <strong>Pro Tip:</strong> Use the OCR feature (Take Photo) to scan product labels and automatically fill in product details!
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Inventory -->
                <section id="inventory" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-warehouse me-3"></i>Inventory Management</h2>
                    
                    <h4 class="mb-3">Receiving Stock</h4>
                    <div class="step-card">
                        <h5>Proper Stock Receipt Process</h5>
                        <p>Always use the <strong>Receive Stock</strong> feature for accurate accounting:</p>
                        <ol>
                            <li>Go to <strong>Products → Receive Stock</strong></li>
                            <li>Select supplier</li>
                            <li>Add products and quantities received</li>
                            <li>Enter unit cost for each product</li>
                            <li>Add invoice number (if available)</li>
                            <li>Save the stock receipt</li>
                        </ol>
                        <p class="text-info mt-3"><i class="fas fa-info-circle me-2"></i>This creates a proper audit trail and tracks your purchase costs for accurate profit calculations.</p>
                    </div>

                    <h4 class="mb-3 mt-4">Low Stock Notifications</h4>
                    <p>Get automatic alerts when products run low:</p>
                    <ul>
                        <li>Email notifications sent to business email</li>
                        <li>Dashboard notifications</li>
                        <li>24-hour cooldown to prevent spam</li>
                    </ul>
                </section>

                <!-- Sales & POS -->
                <section id="sales" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-shopping-cart me-3"></i>Sales & Point of Sale (POS)</h2>
                    
                    <h4 class="mb-3">Making a Sale</h4>
                    <div class="step-card">
                        <h5>Using the POS System</h5>
                        <ol>
                            <li>Go to <strong>Sales → POS</strong></li>
                            <li>Search and add products to cart</li>
                            <li>Adjust quantities as needed</li>
                            <li>Select customer (or use "Walk-in Customer")</li>
                            <li>Choose payment method</li>
                            <li>Complete the sale</li>
                            <li>Print receipt (optional)</li>
                        </ol>
                    </div>

                    <h4 class="mb-3 mt-4">Managing Customers</h4>
                    <div class="step-card">
                        <h5>Add New Customer</h5>
                        <p>Navigate to <strong>Sales → Customers → Add Customer</strong></p>
                        <ul>
                            <li>Enter customer name</li>
                            <li>Add phone number</li>
                            <li>Add email (optional)</li>
                            <li>Track customer purchase history</li>
                        </ul>
                    </div>
                </section>

                <!-- Services -->
                <section id="services" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-concierge-bell me-3"></i>Service Management</h2>
                    
                    <h4 class="mb-3">Setting Up Services</h4>
                    <div class="step-card">
                        <h5>Create a Service</h5>
                        <p>Go to <strong>Services → Add Service</strong></p>
                        <ul>
                            <li>Enter service name (e.g., "Haircut", "Oil Change")</li>
                            <li>Set service price</li>
                            <li>Add service description</li>
                            <li>Set duration (optional)</li>
                        </ul>
                    </div>

                    <h4 class="mb-3 mt-4">Booking Services</h4>
                    <div class="step-card">
                        <h5>Create a Service Booking</h5>
                        <ol>
                            <li>Go to <strong>Service Bookings → New Booking</strong></li>
                            <li>Select customer</li>
                            <li>Choose service(s)</li>
                            <li>Assign staff member</li>
                            <li>Set date and time</li>
                            <li>Mark payment status</li>
                        </ol>
                    </div>
                </section>

                <!-- Staff Management -->
                <section id="staff" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-users me-3"></i>Staff Management</h2>
                    
                    <h4 class="mb-3">Adding Staff Members</h4>
                    <div class="step-card">
                        <h5>Register New Staff</h5>
                        <p>Navigate to <strong>Staff → Add Staff</strong></p>
                        <ul>
                            <li>Enter staff name</li>
                            <li>Add employee ID</li>
                            <li>Set position/role</li>
                            <li>Enter contact information</li>
                            <li>Set salary amount</li>
                            <li>Choose payment frequency (daily, weekly, monthly)</li>
                        </ul>
                    </div>

                    <h4 class="mb-3 mt-4">Salary Advances</h4>
                    <div class="step-card">
                        <h5>Managing Salary Advances</h5>
                        <p>Go to <strong>Salary Advances</strong></p>
                        <ul>
                            <li>Create advance requests</li>
                            <li>Approve or reject requests</li>
                            <li>Track advance balances</li>
                            <li>Auto-deduct from salaries</li>
                        </ul>
                    </div>

                    <h4 class="mb-3 mt-4">Commission Tracking</h4>
                    <p>For sales-based staff, set up commission structures and track earnings automatically.</p>
                </section>

                <!-- Suppliers -->
                <section id="suppliers" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-truck me-3"></i>Supplier Management</h2>
                    
                    <h4 class="mb-3">Adding Suppliers</h4>
                    <div class="step-card">
                        <h5>Register a Supplier</h5>
                        <p>Go to <strong>Suppliers → Add Supplier</strong></p>
                        <ul>
                            <li>Enter supplier/company name</li>
                            <li>Add contact person</li>
                            <li>Enter email and phone</li>
                            <li>Add address details</li>
                            <li>Set payment terms (Net 30, Net 60, etc.)</li>
                            <li>Set credit limit (optional)</li>
                            <li>Add company registration details</li>
                        </ul>
                    </div>

                    <h4 class="mb-3 mt-4">Tracking Purchases</h4>
                    <p>View complete purchase history per supplier:</p>
                    <ul>
                        <li>Total amount spent</li>
                        <li>Number of orders</li>
                        <li>Last order date</li>
                        <li>All stock receipts</li>
                    </ul>
                </section>

                <!-- Returns & Refunds -->
                <section id="returns" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-undo me-3"></i>Returns & Refunds</h2>
                    
                    <h4 class="mb-3">Processing Returns</h4>
                    <div class="step-card">
                        <h5>Create a Return</h5>
                        <ol>
                            <li>Go to <strong>Returns & Refunds → New Return</strong></li>
                            <li>Select the original order</li>
                            <li>Choose return type (Full or Partial)</li>
                            <li>Select reason category</li>
                            <li>Provide detailed reason</li>
                            <li>Set restocking fee (optional)</li>
                            <li>Choose whether to return items to stock</li>
                        </ol>
                    </div>

                    <h4 class="mb-3 mt-4">Return Workflow</h4>
                    <div class="step-card">
                        <h5>Approval Process</h5>
                        <ul>
                            <li><strong>Pending:</strong> Return request created</li>
                            <li><strong>Approved:</strong> Manager approves the return</li>
                            <li><strong>Completed:</strong> Refund processed and stock returned</li>
                        </ul>
                        <p class="text-info mt-3"><i class="fas fa-shield-alt me-2"></i>Password verification required for all return actions.</p>
                    </div>
                </section>

                <!-- Reports -->
                <section id="reports" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-chart-bar me-3"></i>Reports & Analytics</h2>
                    
                    <h4 class="mb-3">Available Reports</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="step-card">
                                <h5><i class="fas fa-chart-line me-2"></i>Sales Reports</h5>
                                <p>Comprehensive sales analytics:</p>
                                <ul>
                                    <li>Sales by period</li>
                                    <li>Top-selling products</li>
                                    <li>Sales by payment method</li>
                                    <li>Customer purchase patterns</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="step-card">
                                <h5><i class="fas fa-box me-2"></i>Product Performance</h5>
                                <p>Analyze product performance:</p>
                                <ul>
                                    <li>Best sellers</li>
                                    <li>Slow-moving items</li>
                                    <li>Stock turnover rates</li>
                                    <li>Profit margins</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="step-card">
                                <h5><i class="fas fa-users me-2"></i>Customer Analytics</h5>
                                <p>Understand your customers:</p>
                                <ul>
                                    <li>Top customers</li>
                                    <li>Customer lifetime value</li>
                                    <li>Repeat purchase rate</li>
                                    <li>Customer acquisition</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="step-card">
                                <h5><i class="fas fa-money-bill-wave me-2"></i>Profit & Loss</h5>
                                <p>Complete financial overview:</p>
                                <ul>
                                    <li>Revenue breakdown</li>
                                    <li>Cost analysis</li>
                                    <li>Net profit/loss</li>
                                    <li>Period comparisons</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h4 class="mb-3 mt-4">Exporting Reports</h4>
                    <p>All reports can be exported as:</p>
                    <span class="feature-badge">PDF</span>
                    <span class="feature-badge">Excel</span>
                    <span class="feature-badge">CSV</span>
                </section>

                <!-- Settings -->
                <section id="settings" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-cog me-3"></i>Settings & Configuration</h2>
                    
                    <h4 class="mb-3">General Settings</h4>
                    <div class="step-card">
                        <h5>Configure Your Business</h5>
                        <p>Go to <strong>Settings → All Settings</strong></p>
                        <ul>
                            <li><strong>General:</strong> Currency, timezone, language</li>
                            <li><strong>POS:</strong> Receipt settings, auto-print</li>
                            <li><strong>Inventory:</strong> Low stock threshold, auto-deduct</li>
                            <li><strong>Notifications:</strong> Email alerts, dashboard notifications</li>
                            <li><strong>Invoice:</strong> Customize invoice layout and terms</li>
                            <li><strong>Tax:</strong> VAT settings, tax calculations</li>
                            <li><strong>Display:</strong> Items per page, dark mode</li>
                            <li><strong>Security:</strong> Session timeout, 2FA</li>
                        </ul>
                    </div>

                    <h4 class="mb-3 mt-4">Tax Management</h4>
                    <div class="step-card">
                        <h5>Configure Tax Settings</h5>
                        <p>Navigate to <strong>Tax Management</strong></p>
                        <ul>
                            <li>Enable/disable tax</li>
                            <li>Set VAT rate (16% for Kenya)</li>
                            <li>Choose tax type (inclusive/exclusive)</li>
                            <li>Add KRA PIN</li>
                            <li>Configure tax display on receipts</li>
                        </ul>
                    </div>
                </section>

                <!-- Help & Support -->
                <section id="support" class="docs-section">
                    <h2 class="mb-4"><i class="fas fa-life-ring me-3"></i>Help & Support</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="step-card">
                                <h5><i class="fas fa-envelope me-2"></i>Email Support</h5>
                                <p class="mb-2">For technical issues or questions:</p>
                                <p><strong>support@shopybook.com</strong></p>
                                <p class="text-muted">Response time: 24-48 hours</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="step-card">
                                <h5><i class="fas fa-phone me-2"></i>Phone Support</h5>
                                <p class="mb-2">Call us for urgent assistance:</p>
                                <p><strong>+254 XXX XXX XXX</strong></p>
                                <p class="text-muted">Mon-Fri: 8:00 AM - 6:00 PM EAT</p>
                            </div>
                        </div>
                    </div>

                    <div class="tip-box mt-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-question-circle"></i>
                            <div>
                                <strong>Can't find what you're looking for?</strong><br>
                                Contact our support team and we'll be happy to help you get the most out of Shopybook.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CTA Section -->
                <div class="text-center mt-5 pt-5 pb-4">
                    <h3 class="mb-4">Ready to Get Started?</h3>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket me-2"></i>Create Your Free Account
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Smooth scrolling for navigation links
document.querySelectorAll('.docs-nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetSection = document.querySelector(targetId);
        
        if (targetSection) {
            // Remove active class from all links
            document.querySelectorAll('.docs-nav-link').forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Smooth scroll to section
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Highlight active section on scroll
const sections = document.querySelectorAll('.docs-section');
const navLinks = document.querySelectorAll('.docs-nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (pageYOffset >= (sectionTop - 150)) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});
</script>

@endsection







