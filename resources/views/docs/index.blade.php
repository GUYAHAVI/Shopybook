@extends('layouts.public')
@section('title', 'User Guide & Documentation - Shopybook')
@section('meta_description', 'Complete user guide and documentation for Shopybook business management platform. Learn how to manage your products, staff, services, and generate reports.')
@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <h1><i class="fas fa-book-open me-3"></i>User Guide &amp; Documentation</h1>
        <p class="mx-auto" style="max-width:620px;">
            Everything you need to know to master Shopybook and grow your business.
            From getting started to advanced features, we've got you covered.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
            <a href="#getting-started" class="btn1">Get Started</a>
            <a href="{{ route('register') }}" class="btnb">Create Account</a>
        </div>
    </div>
</section>

{{-- ═══════════════ DOCUMENTATION ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row">

            {{-- Sidebar --}}
            <div class="col-lg-3 mb-4 mb-lg-0">
                <div class="sb-docs-sidebar">
                    <h5 class="mb-3"><i class="fas fa-bars me-2"></i>Contents</h5>
                    <nav>
                        <a href="#getting-started" class="sb-docs-nav active"><i class="fas fa-rocket"></i>Getting Started</a>
                        <a href="#account-setup" class="sb-docs-nav"><i class="fas fa-user-cog"></i>Account Setup</a>
                        <a href="#business-setup" class="sb-docs-nav"><i class="fas fa-building"></i>Business Setup</a>
                        <a href="#products" class="sb-docs-nav"><i class="fas fa-box"></i>Products</a>
                        <a href="#inventory" class="sb-docs-nav"><i class="fas fa-warehouse"></i>Inventory</a>
                        <a href="#sales" class="sb-docs-nav"><i class="fas fa-shopping-cart"></i>Sales &amp; POS</a>
                        <a href="#services" class="sb-docs-nav"><i class="fas fa-concierge-bell"></i>Services</a>
                        <a href="#staff" class="sb-docs-nav"><i class="fas fa-users"></i>Staff Management</a>
                        <a href="#suppliers" class="sb-docs-nav"><i class="fas fa-truck"></i>Suppliers</a>
                        <a href="#returns" class="sb-docs-nav"><i class="fas fa-undo"></i>Returns &amp; Refunds</a>
                        <a href="#reports" class="sb-docs-nav"><i class="fas fa-chart-bar"></i>Reports</a>
                        <a href="#settings" class="sb-docs-nav"><i class="fas fa-cog"></i>Settings</a>
                    </nav>
                </div>
            </div>

            {{-- Content --}}
            <div class="col-lg-9">
                <div class="sb-docs-content">

                    <section id="getting-started" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-rocket me-3"></i>Getting Started</h2>
                        <p class="lead" style="color:#444;">Welcome to Shopybook! This guide will help you set up and use all features of our platform.</p>

                        <div class="sb-tip-box">
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
                                <span class="sb-feature-badge"><i class="fas fa-check me-2"></i>Product Inventory</span>
                                <span class="sb-feature-badge"><i class="fas fa-check me-2"></i>Sales &amp; POS</span>
                                <span class="sb-feature-badge"><i class="fas fa-check me-2"></i>Services</span>
                            </div>
                            <div class="col-md-6">
                                <span class="sb-feature-badge"><i class="fas fa-check me-2"></i>Staff</span>
                                <span class="sb-feature-badge"><i class="fas fa-check me-2"></i>Customers</span>
                                <span class="sb-feature-badge"><i class="fas fa-check me-2"></i>Reports</span>
                            </div>
                        </div>
                    </section>

                    <section id="account-setup" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-user-cog me-3"></i>Account Setup</h2>

                        <h4 class="mb-3">Step 1: Create Your Account</h4>
                        <div class="sb-step-card">
                            <div class="d-flex align-items-start">
                                <div class="sb-step-number">1</div>
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

                        <div class="sb-step-card">
                            <div class="d-flex align-items-start">
                                <div class="sb-step-number">2</div>
                                <div class="flex-grow-1">
                                    <h5>Verify Your Email</h5>
                                    <p class="mb-2">Check your email inbox for a verification link from Shopybook.</p>
                                    <p style="color:#dc3545;"><i class="fas fa-exclamation-triangle me-2"></i>Check your spam folder if you don't see it within 5 minutes.</p>
                                </div>
                            </div>
                        </div>

                        <div class="sb-step-card">
                            <div class="d-flex align-items-start">
                                <div class="sb-step-number">3</div>
                                <div class="flex-grow-1">
                                    <h5>Login to Dashboard</h5>
                                    <p class="mb-0">After verification, login at <code>{{ route('login') }}</code> to access your dashboard.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="business-setup" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-building me-3"></i>Business Setup</h2>

                        <h4 class="mb-3">Setting Up Your Business Profile</h4>
                        <div class="sb-step-card">
                            <div class="d-flex align-items-start">
                                <div class="sb-step-number">1</div>
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

                        <div class="sb-step-card">
                            <div class="d-flex align-items-start">
                                <div class="sb-step-number">2</div>
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

                        <div class="sb-step-card">
                            <div class="d-flex align-items-start">
                                <div class="sb-step-number">3</div>
                                <div class="flex-grow-1">
                                    <h5>Upload Business Logo</h5>
                                    <p class="mb-0">Add your business logo for a professional appearance on receipts and invoices.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="products" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-box me-3"></i>Product Management</h2>

                        <h4 class="mb-3">Adding Products</h4>
                        <div class="sb-step-card">
                            <h5><i class="fas fa-plus-circle me-2"></i>Single Product Entry</h5>
                            <p>Navigate to <strong>Products &rarr; Add Product</strong></p>
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

                        <div class="sb-step-card">
                            <h5><i class="fas fa-upload me-2"></i>Bulk Import</h5>
                            <p>Import multiple products at once using Excel/CSV:</p>
                            <ol>
                                <li>Go to <strong>Products &rarr; Bulk Import</strong></li>
                                <li>Download the template file</li>
                                <li>Fill in your products (name, price, stock, etc.)</li>
                                <li>Upload the completed file</li>
                                <li>Review and confirm the import</li>
                            </ol>
                        </div>

                        <div class="sb-tip-box">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-star"></i>
                                <div>
                                    <strong>Pro Tip:</strong> Use the OCR feature (Take Photo) to scan product labels and automatically fill in product details!
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="inventory" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-warehouse me-3"></i>Inventory Management</h2>

                        <h4 class="mb-3">Receiving Stock</h4>
                        <div class="sb-step-card">
                            <h5>Proper Stock Receipt Process</h5>
                            <p>Always use the <strong>Receive Stock</strong> feature for accurate accounting:</p>
                            <ol>
                                <li>Go to <strong>Products &rarr; Receive Stock</strong></li>
                                <li>Select supplier</li>
                                <li>Add products and quantities received</li>
                                <li>Enter unit cost for each product</li>
                                <li>Add invoice number (if available)</li>
                                <li>Save the stock receipt</li>
                            </ol>
                            <p class="mt-3" style="color:#ff511a;"><i class="fas fa-info-circle me-2"></i>This creates a proper audit trail and tracks your purchase costs for accurate profit calculations.</p>
                        </div>

                        <h4 class="mb-3 mt-4">Low Stock Notifications</h4>
                        <p>Get automatic alerts when products run low:</p>
                        <ul>
                            <li>Email notifications sent to business email</li>
                            <li>Dashboard notifications</li>
                            <li>24-hour cooldown to prevent spam</li>
                        </ul>
                    </section>

                    <section id="sales" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-shopping-cart me-3"></i>Sales &amp; Point of Sale (POS)</h2>

                        <h4 class="mb-3">Making a Sale</h4>
                        <div class="sb-step-card">
                            <h5>Using the POS System</h5>
                            <ol>
                                <li>Go to <strong>Sales &rarr; POS</strong></li>
                                <li>Search and add products to cart</li>
                                <li>Adjust quantities as needed</li>
                                <li>Select customer (or use "Walk-in Customer")</li>
                                <li>Choose payment method</li>
                                <li>Complete the sale</li>
                                <li>Print receipt (optional)</li>
                            </ol>
                        </div>

                        <h4 class="mb-3 mt-4">Managing Customers</h4>
                        <div class="sb-step-card">
                            <h5>Add New Customer</h5>
                            <p>Navigate to <strong>Sales &rarr; Customers &rarr; Add Customer</strong></p>
                            <ul>
                                <li>Enter customer name</li>
                                <li>Add phone number</li>
                                <li>Add email (optional)</li>
                                <li>Track customer purchase history</li>
                            </ul>
                        </div>
                    </section>

                    <section id="services" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-concierge-bell me-3"></i>Service Management</h2>

                        <h4 class="mb-3">Setting Up Services</h4>
                        <div class="sb-step-card">
                            <h5>Create a Service</h5>
                            <p>Go to <strong>Services &rarr; Add Service</strong></p>
                            <ul>
                                <li>Enter service name (e.g., "Haircut", "Oil Change")</li>
                                <li>Set service price</li>
                                <li>Add service description</li>
                                <li>Set duration (optional)</li>
                            </ul>
                        </div>

                        <h4 class="mb-3 mt-4">Booking Services</h4>
                        <div class="sb-step-card">
                            <h5>Create a Service Booking</h5>
                            <ol>
                                <li>Go to <strong>Service Bookings &rarr; New Booking</strong></li>
                                <li>Select customer</li>
                                <li>Choose service(s)</li>
                                <li>Assign staff member</li>
                                <li>Set date and time</li>
                                <li>Mark payment status</li>
                            </ol>
                        </div>
                    </section>

                    <section id="staff" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-users me-3"></i>Staff Management</h2>

                        <h4 class="mb-3">Adding Staff Members</h4>
                        <div class="sb-step-card">
                            <h5>Register New Staff</h5>
                            <p>Navigate to <strong>Staff &rarr; Add Staff</strong></p>
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
                        <div class="sb-step-card">
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

                    <section id="suppliers" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-truck me-3"></i>Supplier Management</h2>

                        <h4 class="mb-3">Adding Suppliers</h4>
                        <div class="sb-step-card">
                            <h5>Register a Supplier</h5>
                            <p>Go to <strong>Suppliers &rarr; Add Supplier</strong></p>
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

                    <section id="returns" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-undo me-3"></i>Returns &amp; Refunds</h2>

                        <h4 class="mb-3">Processing Returns</h4>
                        <div class="sb-step-card">
                            <h5>Create a Return</h5>
                            <ol>
                                <li>Go to <strong>Returns &amp; Refunds &rarr; New Return</strong></li>
                                <li>Select the original order</li>
                                <li>Choose return type (Full or Partial)</li>
                                <li>Select reason category</li>
                                <li>Provide detailed reason</li>
                                <li>Set restocking fee (optional)</li>
                                <li>Choose whether to return items to stock</li>
                            </ol>
                        </div>

                        <h4 class="mb-3 mt-4">Return Workflow</h4>
                        <div class="sb-step-card">
                            <h5>Approval Process</h5>
                            <ul>
                                <li><strong>Pending:</strong> Return request created</li>
                                <li><strong>Approved:</strong> Manager approves the return</li>
                                <li><strong>Completed:</strong> Refund processed and stock returned</li>
                            </ul>
                            <p class="mt-3" style="color:#ff511a;"><i class="fas fa-shield-alt me-2"></i>Password verification required for all return actions.</p>
                        </div>
                    </section>

                    <section id="reports" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-chart-bar me-3"></i>Reports &amp; Analytics</h2>

                        <h4 class="mb-3">Available Reports</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="sb-step-card">
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
                                <div class="sb-step-card">
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
                                <div class="sb-step-card">
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
                                <div class="sb-step-card">
                                    <h5><i class="fas fa-money-bill-wave me-2"></i>Profit &amp; Loss</h5>
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
                        <span class="sb-feature-badge">PDF</span>
                        <span class="sb-feature-badge">Excel</span>
                        <span class="sb-feature-badge">CSV</span>
                    </section>

                    <section id="settings" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-cog me-3"></i>Settings &amp; Configuration</h2>

                        <h4 class="mb-3">General Settings</h4>
                        <div class="sb-step-card">
                            <h5>Configure Your Business</h5>
                            <p>Go to <strong>Settings &rarr; All Settings</strong></p>
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
                        <div class="sb-step-card">
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

                    <section id="support" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-life-ring me-3"></i>Help &amp; Support</h2>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="sb-step-card">
                                    <h5><i class="fas fa-envelope me-2"></i>Email Support</h5>
                                    <p class="mb-2">For technical issues or questions:</p>
                                    <p><strong>support@shopybook.com</strong></p>
                                    <p class="text-muted">Response time: 24-48 hours</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card">
                                    <h5><i class="fas fa-phone me-2"></i>Phone Support</h5>
                                    <p class="mb-2">Call us for urgent assistance:</p>
                                    <p><strong>0717 745 891</strong></p>
                                    <p class="text-muted">Mon-Fri: 8:00 AM - 6:00 PM EAT</p>
                                </div>
                            </div>
                        </div>

                        <div class="sb-tip-box mt-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-question-circle"></i>
                                <div>
                                    <strong>Can't find what you're looking for?</strong><br>
                                    Contact our support team and we'll be happy to help you get the most out of Shopybook.
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="text-center mt-5 pt-4 pb-3">
                        <h3 class="mb-4" style="color:#7b2e2e;">Ready to Get Started?</h3>
                        <a href="{{ route('register') }}" class="btn1">Create Your Free Account</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.sb-docs-nav').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetSection = document.querySelector(targetId);
        if (targetSection) {
            document.querySelectorAll('.sb-docs-nav').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

const sections = document.querySelectorAll('.sb-docs-section');
const navLinks = document.querySelectorAll('.sb-docs-nav');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
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
