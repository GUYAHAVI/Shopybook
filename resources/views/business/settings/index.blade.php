@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">⚙️ Business Settings</h5>
                            <p class="text-sm mb-0">Configure all aspects of your business</p>
                        </div>
                        <div>
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error!</strong> Please fix the following errors:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-globe me-2"></i>General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pos-tab" data-bs-toggle="tab" data-bs-target="#pos" type="button" role="tab">
                                <i class="fas fa-cash-register me-2"></i>POS
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
                                <i class="fas fa-boxes me-2"></i>Inventory
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button" role="tab">
                                <i class="fas fa-file-invoice me-2"></i>Invoices
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tax-tab" data-bs-toggle="tab" data-bs-target="#tax" type="button" role="tab">
                                <i class="fas fa-percentage me-2"></i>Tax
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="display-tab" data-bs-toggle="tab" data-bs-target="#display" type="button" role="tab">
                                <i class="fas fa-desktop me-2"></i>Display
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subscription-tab" data-bs-toggle="tab" data-bs-target="#subscription" type="button" role="tab">
                                <i class="fas fa-crown me-2"></i>Subscription
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Security
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                                <i class="fas fa-credit-card me-2"></i>Payments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-danger" id="danger-zone-tab" data-bs-toggle="tab" data-bs-target="#danger-zone" type="button" role="tab">
                                <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="settingsTabContent">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <h6 class="mb-3">Regional & Language Settings</h6>
                            <form action="{{ route('settings.update.general') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Currency</label>
                                        <select class="form-select" name="currency" required>
                                            @foreach($currencies as $code => $name)
                                                <option value="{{ $code }}" {{ $settings->currency == $code ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Currency Symbol</label>
                                        <input type="text" class="form-control" name="currency_symbol" 
                                               value="{{ $settings->currency_symbol }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Timezone</label>
                                        <select class="form-select" name="timezone" required>
                                            @foreach($timezones as $tz)
                                                <option value="{{ $tz }}" {{ $settings->timezone == $tz ? 'selected' : '' }}>
                                                    {{ $tz }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Language</label>
                                        <select class="form-select" name="language" required>
                                            @foreach($languages as $code => $name)
                                                <option value="{{ $code }}" {{ $settings->language == $code ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date Format</label>
                                        <select class="form-select" name="date_format" required>
                                            <option value="Y-m-d" {{ $settings->date_format == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2025-10-04)</option>
                                            <option value="d/m/Y" {{ $settings->date_format == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (04/10/2025)</option>
                                            <option value="m/d/Y" {{ $settings->date_format == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (10/04/2025)</option>
                                            <option value="d-M-Y" {{ $settings->date_format == 'd-M-Y' ? 'selected' : '' }}>DD-Mon-YYYY (04-Oct-2025)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Time Format</label>
                                        <select class="form-select" name="time_format" required>
                                            <option value="H:i" {{ $settings->time_format == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                            <option value="h:i A" {{ $settings->time_format == 'h:i A' ? 'selected' : '' }}>12 Hour (02:30 PM)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save General Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- POS Settings -->
                        <div class="tab-pane fade" id="pos" role="tabpanel">
                            <h6 class="mb-3">Point of Sale Configuration</h6>
                            <form action="{{ route('settings.update.pos') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Default Payment Method</label>
                                        <select class="form-select" name="default_payment_method" required>
                                            <option value="cash" {{ $settings->default_payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="card" {{ $settings->default_payment_method == 'card' ? 'selected' : '' }}>Card</option>
                                            <option value="mobile_money" {{ $settings->default_payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                            <option value="bank_transfer" {{ $settings->default_payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Receipt Header</label>
                                        <input type="text" class="form-control" name="receipt_header" 
                                               value="{{ $settings->receipt_header }}" 
                                               placeholder="Thank you for shopping with us!">
                                        <small class="text-muted">Text to display at the top of receipts</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Receipt Footer</label>
                                        <input type="text" class="form-control" name="receipt_footer" 
                                               value="{{ $settings->receipt_footer }}" 
                                               placeholder="Please come again!">
                                        <small class="text-muted">Text to display at the bottom of receipts</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="auto_print_receipt" value="1" 
                                                   {{ $settings->auto_print_receipt ? 'checked' : '' }}>
                                            <label class="form-check-label">Auto-print receipts after sale</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="show_logo_on_receipt" value="1" 
                                                   {{ $settings->show_logo_on_receipt ? 'checked' : '' }}>
                                            <label class="form-check-label">Show business logo on receipts</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="require_customer_on_sale" value="1" 
                                                   {{ $settings->require_customer_on_sale ? 'checked' : '' }}>
                                            <label class="form-check-label">Require customer selection for all sales</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save POS Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Inventory Settings -->
                        <div class="tab-pane fade" id="inventory" role="tabpanel">
                            <h6 class="mb-3">Inventory Management Settings</h6>
                            <form action="{{ route('settings.update.inventory') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Default Low Stock Threshold</label>
                                        <input type="number" class="form-control" name="default_low_stock_threshold" 
                                               value="{{ $settings->default_low_stock_threshold }}" min="0" required>
                                        <small class="text-muted">Applied to new products by default</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="auto_deduct_stock" value="1" 
                                                   {{ $settings->auto_deduct_stock ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <strong>Auto-deduct stock on sale</strong>
                                                <p class="text-xs text-muted mb-0">Automatically reduce inventory when orders are completed</p>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="allow_negative_stock" value="1" 
                                                   {{ $settings->allow_negative_stock ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <strong>Allow negative stock</strong>
                                                <p class="text-xs text-muted mb-0">Permit sales even when stock is zero (backorders)</p>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="track_stock_movements" value="1" 
                                                   {{ $settings->track_stock_movements ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <strong>Track stock movements</strong>
                                                <p class="text-xs text-muted mb-0">Keep detailed logs of all inventory changes</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Inventory Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Notifications Settings -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            <h6 class="mb-3">Notification Preferences</h6>
                            <form action="{{ route('settings.update.notifications') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Notification Email</label>
                                        <input type="email" class="form-control" name="notification_email" 
                                               value="{{ $settings->notification_email ?? $business->email }}" 
                                               placeholder="{{ $business->email }}">
                                        <small class="text-muted">Receives all automated notifications</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Reply-To Email</label>
                                        <input type="email" class="form-control" name="reply_to_email" 
                                               value="{{ $settings->reply_to_email }}" 
                                               placeholder="Optional">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CC Email</label>
                                        <input type="email" class="form-control" name="cc_email" 
                                               value="{{ $settings->cc_email }}" 
                                               placeholder="Optional">
                                    </div>

                                    <div class="col-md-12">
                                        <h6 class="mt-3 mb-3">Email Notifications</h6>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="enable_email_notifications" value="1" 
                                                   {{ $settings->enable_email_notifications ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <strong>Enable Email Notifications</strong>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="notify_on_new_order" value="1" 
                                                   {{ $settings->notify_on_new_order ? 'checked' : '' }}>
                                            <label class="form-check-label">Notify on new order</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="notify_on_low_stock" value="1" 
                                                   {{ $settings->notify_on_low_stock ? 'checked' : '' }}>
                                            <label class="form-check-label">Notify on low stock</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="notify_on_new_customer" value="1" 
                                                   {{ $settings->notify_on_new_customer ? 'checked' : '' }}>
                                            <label class="form-check-label">Notify on new customer</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h6 class="mt-3 mb-3">Automated Reports</h6>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="daily_sales_report" value="1" 
                                                   {{ $settings->daily_sales_report ? 'checked' : '' }}>
                                            <label class="form-check-label">Daily sales report</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="weekly_sales_report" value="1" 
                                                   {{ $settings->weekly_sales_report ? 'checked' : '' }}>
                                            <label class="form-check-label">Weekly sales report</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="monthly_sales_report" value="1" 
                                                   {{ $settings->monthly_sales_report ? 'checked' : '' }}>
                                            <label class="form-check-label">Monthly sales report</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Notification Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Invoice Settings -->
                        <div class="tab-pane fade" id="invoice" role="tabpanel">
                            <h6 class="mb-3">Invoice & Receipt Configuration</h6>
                            <form action="{{ route('settings.update.invoice') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Invoice Prefix</label>
                                        <input type="text" class="form-control" name="invoice_prefix" 
                                               value="{{ $settings->invoice_prefix }}" required>
                                        <small class="text-muted">e.g., INV-2025-001</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Receipt Prefix</label>
                                        <input type="text" class="form-control" name="receipt_prefix" 
                                               value="{{ $settings->receipt_prefix }}" required>
                                        <small class="text-muted">e.g., RCP-2025-001</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Order Prefix</label>
                                        <input type="text" class="form-control" name="order_prefix" 
                                               value="{{ $settings->order_prefix }}" required>
                                        <small class="text-muted">e.g., ORD-2025-001</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Invoice Starting Number</label>
                                        <input type="number" class="form-control" name="invoice_starting_number" 
                                               value="{{ $settings->invoice_starting_number }}" min="1" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Payment Terms (Days)</label>
                                        <input type="number" class="form-control" name="payment_terms_days" 
                                               value="{{ $settings->payment_terms_days }}" min="0" required>
                                        <small class="text-muted">Default payment due period</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Invoice Terms & Conditions</label>
                                        <textarea class="form-control" name="invoice_terms" rows="3" 
                                                  placeholder="Enter your invoice terms and conditions here...">{{ $settings->invoice_terms }}</textarea>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Invoice Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Tax Settings -->
                        <div class="tab-pane fade" id="tax" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Tax settings are managed separately for detailed configuration.
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h6>Current Tax Configuration</h6>
                                    <p class="mb-2"><strong>Status:</strong> <span class="badge bg-{{ $taxSettings->tax_enabled ? 'success' : 'secondary' }}">{{ $taxSettings->tax_enabled ? 'Enabled' : 'Disabled' }}</span></p>
                                    @if($taxSettings->tax_enabled)
                                        <p class="mb-2"><strong>Tax Type:</strong> {{ $taxSettings->tax_type }}</p>
                                        <p class="mb-2"><strong>Tax Rate:</strong> {{ $taxSettings->tax_rate }}%</p>
                                        <p class="mb-2"><strong>Tax Method:</strong> {{ $taxSettings->tax_inclusive ? 'Tax Inclusive' : 'Tax Exclusive' }}</p>
                                        @if($taxSettings->tax_number)
                                            <p class="mb-2"><strong>Tax Number:</strong> {{ $taxSettings->tax_number }}</p>
                                        @endif
                                    @endif
                                    <a href="{{ route('tax.settings') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-cog me-2"></i>Configure Tax Settings
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Display Settings -->
                        <div class="tab-pane fade" id="display" role="tabpanel">
                            <h6 class="mb-3">Display & Interface Preferences</h6>
                            <form action="{{ route('settings.update.display') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Items Per Page</label>
                                        <select class="form-select" name="items_per_page" required>
                                            <option value="10" {{ $settings->items_per_page == 10 ? 'selected' : '' }}>10</option>
                                            <option value="20" {{ $settings->items_per_page == 20 ? 'selected' : '' }}>20</option>
                                            <option value="50" {{ $settings->items_per_page == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ $settings->items_per_page == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                        <small class="text-muted">Number of items to display in lists</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dashboard Layout</label>
                                        <select class="form-select" name="dashboard_layout" required>
                                            <option value="grid" {{ $settings->dashboard_layout == 'grid' ? 'selected' : '' }}>Grid Layout</option>
                                            <option value="list" {{ $settings->dashboard_layout == 'list' ? 'selected' : '' }}>List Layout</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="show_product_images" value="1" 
                                                   {{ $settings->show_product_images ? 'checked' : '' }}>
                                            <label class="form-check-label">Show product images in lists</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="show_stock_levels" value="1" 
                                                   {{ $settings->show_stock_levels ? 'checked' : '' }}>
                                            <label class="form-check-label">Show stock levels in product listings</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Display Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Subscription Settings -->
                        <div class="tab-pane fade" id="subscription" role="tabpanel">
                            <h6 class="mb-3">Subscription & Plan Management</h6>
                            
                            <!-- Current Plan Card -->
                            <div class="card mb-4" style="border: 2px solid {{ $business->isEnterprise() ? '#10b981' : '#6b7280' }};">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-2">
                                                @if($business->isEnterprise())
                                                    <i class="fas fa-crown text-warning me-2"></i>Enterprise Plan
                                                    <span class="badge bg-success ms-2">Active</span>
                                                @else
                                                    <i class="fas fa-box text-secondary me-2"></i>Free Plan
                                                    <span class="badge bg-secondary ms-2">Current</span>
                                                @endif
                                            </h5>
                                            <p class="text-muted mb-0">
                                                @if($business->isEnterprise())
                                                    You have access to all premium features
                                                @else
                                                    You're using the basic free plan
                                                @endif
                                            </p>
                                        </div>
                                        @if(!$business->isEnterprise())
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upgradePlanModal">
                                            <i class="fas fa-arrow-up me-2"></i>Upgrade Plan
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(!$business->isEnterprise())
                            <!-- Upgrade Benefits -->
                            <div class="alert alert-info mb-4">
                                <h6 class="mb-3"><i class="fas fa-gift me-2"></i>Why Upgrade to Enterprise?</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <div>
                                                <strong>AI Website Auto-Build</strong>
                                                <p class="mb-0 small text-muted">Create complete websites in minutes</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <div>
                                                <strong>Advanced Analytics</strong>
                                                <p class="mb-0 small text-muted">Deep insights & custom reports</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <div>
                                                <strong>Priority Support</strong>
                                                <p class="mb-0 small text-muted">Get help within 2 hours</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                            <div>
                                                <strong>Unlimited Features</strong>
                                                <p class="mb-0 small text-muted">No restrictions on products, orders & more</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-center">
                                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#upgradePlanModal">
                                        <i class="fas fa-rocket me-2"></i>Upgrade to Enterprise Now
                                    </button>
                                </div>
                            </div>
                            @else
                            <!-- Enterprise Benefits -->
                            <div class="alert alert-success">
                                <h6 class="mb-2"><i class="fas fa-check-circle me-2"></i>You're on the Enterprise Plan!</h6>
                                <p class="mb-0">You have access to all premium features including AI website builder, advanced analytics, priority support, and unlimited usage.</p>
                            </div>
                            @endif
                        </div>

                        <!-- Security Settings -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <h6 class="mb-3">Security & Access Control</h6>
                            <form action="{{ route('settings.update.security') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Session Timeout (Minutes)</label>
                                        <input type="number" class="form-control" name="session_timeout_minutes" 
                                               value="{{ $settings->session_timeout_minutes }}" min="5" max="1440" required>
                                        <small class="text-muted">Time before automatic logout (5-1440 minutes)</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="require_2fa" value="1" 
                                                   {{ $settings->require_2fa ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <strong>Require Two-Factor Authentication (2FA)</strong>
                                                <p class="text-xs text-muted mb-0">Enhance security with 2FA for all users</p>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="enable_session_timeout" value="1" 
                                                   {{ $settings->enable_session_timeout ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <strong>Enable Session Timeout</strong>
                                                <p class="text-xs text-muted mb-0">Automatically log out inactive users</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Security Tip:</strong> Enable 2FA and session timeout for enhanced security, especially on shared devices.
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Security Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Payments / Paystack -->
                        <div class="tab-pane fade" id="payments" role="tabpanel">
                            <h6 class="mb-1">Payment Gateway &mdash; Paystack (M-Pesa STK Push)</h6>
                            <p class="text-muted small mb-4">Connect your own Paystack account so your customers can pay via M-Pesa directly from the POS. Funds go straight into your business bank account.</p>

                            {{-- Setup guide --}}
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body py-3">
                                    <h6 class="text-primary mb-2"><i class="fas fa-info-circle me-2"></i>How to get your API keys</h6>
                                    <ol class="mb-0 ps-3 small text-muted">
                                        <li>Create a free account at <a href="https://paystack.com/signup" target="_blank" rel="noopener">paystack.com/signup</a></li>
                                        <li>Verify your business and activate your account</li>
                                        <li>Navigate to <strong>Settings &rarr; API Keys &amp; Webhooks</strong></li>
                                        <li>Copy your <strong>Public Key</strong> and <strong>Secret Key</strong></li>
                                        <li>Paste them below and save &mdash; then test with a small amount in Test Mode first</li>
                                    </ol>
                                </div>
                            </div>

                            <form action="{{ route('settings.update.payments') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="paystack_enabled" name="paystack_enabled" value="1"
                                                   {{ $settings->paystack_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="paystack_enabled">
                                                <strong>Enable Paystack Payments</strong>
                                                <p class="text-xs text-muted mb-0">Allow M-Pesa STK push from the POS</p>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="paystack_test_mode" name="paystack_test_mode" value="1"
                                                   {{ $settings->paystack_test_mode ? 'checked' : '' }}>
                                            <label class="form-check-label" for="paystack_test_mode">
                                                <strong>Test Mode</strong>
                                                <p class="text-xs text-muted mb-0">Use Paystack test keys (no real money moved)</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Public Key</label>
                                        <input type="text" class="form-control" name="paystack_public_key"
                                               placeholder="pk_test_xxxxxxxxxxxxxxxxxxxx"
                                               value="{{ $settings->paystack_public_key }}">
                                        <small class="text-muted">Starts with <code>pk_test_</code> (test) or <code>pk_live_</code> (live)</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Secret Key</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="paystack_secret_key"
                                                   id="paystack_secret_key_input"
                                                   placeholder="{{ $settings->paystack_secret_key ? '••••••••••••••• (saved — leave blank to keep)' : 'sk_test_xxxxxxxxxxxxxxxxxxxx' }}">
                                            <button type="button" class="btn btn-outline-secondary" onclick="toggleSecretKey()" title="Show / hide">
                                                <i class="fas fa-eye" id="toggleSecretKeyIcon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Starts with <code>sk_test_</code> or <code>sk_live_</code> &mdash; never share this</small>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-1">
                                    <i class="fas fa-lock me-2"></i>
                                    <strong>Security:</strong> Your Secret Key is encrypted before storage and never displayed in full. Only submit a new value when rotating keys.
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Payment Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Danger Zone -->
                        <div class="tab-pane fade" id="danger-zone" role="tabpanel">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> Actions in this section are irreversible and can result in permanent data loss.
                            </div>

                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-trash-alt me-2"></i>Delete Business</h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-danger">Permanently delete this business</h6>
                                    <p class="text-muted mb-3">
                                        Once you delete your business, there is no going back. This will permanently delete:
                                    </p>
                                    <ul class="text-muted mb-3">
                                        <li>All products and inventory records</li>
                                        <li>All sales and transaction history</li>
                                        <li>All customer data and records</li>
                                        <li>All employee and staff records</li>
                                        <li>All business settings and configurations</li>
                                        <li>All reports and analytics data</li>
                                    </ul>
                                    <p class="text-muted mb-4">
                                        <strong>This action cannot be undone.</strong> You will need to verify this action by entering a verification code sent to your email address.
                                    </p>
                                    <button type="button" class="btn btn-danger" id="deleteBusiness">
                                        <i class="fas fa-trash-alt me-2"></i>Delete This Business
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Plan Modal -->
<div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #7b2e2e 0%, #ff511a 100%); border: none; padding: 2rem;">
                <div class="text-center w-100">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-crown text-warning"></i>
                    </div>
                    <h4 class="mb-1" style="color: white; font-weight: 700;">Upgrade to Enterprise</h4>
                    <p class="mb-0" style="color: rgba(255,255,255,0.9);">Unlock all premium features</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 1rem; right: 1rem;"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('subscription.upgrade') }}" method="POST" id="upgradePlanForm">
                    @csrf
                    <!-- Plan Selection -->
                    <h6 class="mb-3">Choose Your Plan:</h6>
                    <div class="row g-3 mb-4">
                        <!-- Premium Plan -->
                        <div class="col-md-6">
                            <div class="card plan-card h-100" data-plan="premium" style="cursor: pointer; transition: all 0.3s; border: 2px solid #e5e7eb;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-star fa-2x text-primary"></i>
                                    </div>
                                    <h5 class="mb-2">Premium Plan</h5>
                                    <h3 class="mb-1">
                                        <span style="font-size: 2rem; font-weight: 700; color: #7b2e2e;">KSH 5</span>
                                        <span class="text-muted">/month</span>
                                    </h3>
                                    <p class="text-muted small mb-3">Perfect for growing businesses</p>
                                    <ul class="list-unstyled text-start small">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>AI Website Builder</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced Analytics</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email Support</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Up to 1,000 products</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Enterprise Plan -->
                        <div class="col-md-6">
                            <div class="card plan-card h-100" data-plan="enterprise" style="cursor: pointer; transition: all 0.3s; border: 2px solid #10b981;">
                                <div class="card-body text-center p-4 position-relative">
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Most Popular</span>
                                    <div class="mb-3">
                                        <i class="fas fa-crown fa-2x text-warning"></i>
                                    </div>
                                    <h5 class="mb-2">Enterprise Plan</h5>
                                    <h3 class="mb-1">
                                        <span style="font-size: 2rem; font-weight: 700; color: #10b981;">KSH 10</span>
                                        <span class="text-muted">/month</span>
                                    </h3>
                                    <p class="text-muted small mb-3">Everything you need to scale</p>
                                    <ul class="list-unstyled text-start small">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Everything in Premium</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority Support (2hrs)</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited Products</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom Branding</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>API Access</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="plan" id="selectedPlan" value="enterprise" required>

                    <!-- Payment Details -->
                    <h6 class="mb-3">M-Pesa Payment Details:</h6>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">M-Pesa Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phoneNumber" name="phone_number" 
                               placeholder="07XXXXXXXX or 254XXXXXXXXX" 
                               pattern="^(254|0)[17]\d{8}$"
                               required>
                        <div class="form-text">Enter the M-Pesa number to receive the payment prompt</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-mobile-alt me-2"></i>
                        <strong>How it works:</strong>
                        <ol class="mb-0 mt-2 ps-3">
                            <li>Click "Pay with M-Pesa" below</li>
                            <li>You'll receive an STK push on your phone</li>
                            <li>Enter your M-Pesa PIN to complete payment</li>
                            <li>Your plan will be upgraded instantly</li>
                        </ol>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I agree to the subscription terms and M-Pesa payment will be processed
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border: none; padding: 1.5rem 2rem;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="upgradePlanForm" class="btn btn-success btn-lg" style="font-weight: 700; padding: 0.75rem 2rem;">
                    <i class="fas fa-mobile-alt me-2"></i>Pay with M-Pesa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Business Deletion Verification Modal -->
<div class="modal fade" id="deletionVerificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Verify Business Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="deletionStep1">
                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-circle me-2"></i>Final Warning</strong>
                        <p class="mb-0 mt-2">You are about to permanently delete <strong>{{ $business->name }}</strong> and all associated data. This action cannot be undone.</p>
                    </div>
                    <p class="mb-3">To proceed, we will send a verification code to your email address: <strong>{{ Auth::user()->email }}</strong></p>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmUnderstand">
                        <label class="form-check-label" for="confirmUnderstand">
                            I understand that this action is permanent and cannot be reversed
                        </label>
                    </div>
                </div>
                
                <div id="deletionStep2" style="display: none;">
                    <p class="mb-3">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        A verification code has been sent to <strong>{{ Auth::user()->email }}</strong>
                    </p>
                    <div class="mb-3">
                        <label for="verificationCode" class="form-label">Enter 6-digit verification code</label>
                        <input type="text" class="form-control form-control-lg text-center" id="verificationCode" 
                               maxlength="6" placeholder="000000" 
                               style="letter-spacing: 0.5em; font-size: 1.5rem;">
                        <div class="form-text">Code expires in 10 minutes</div>
                    </div>
                    <div id="verificationError" class="alert alert-danger" style="display: none;"></div>
                    <div id="verificationSuccess" class="alert alert-success" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="deletionStep1Footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="sendVerificationCode" disabled>
                        <i class="fas fa-paper-plane me-2"></i>Send Verification Code
                    </button>
                </div>
                <div id="deletionStep2Footer" style="display: none;">
                    <button type="button" class="btn btn-secondary" id="resendCode">
                        <i class="fas fa-redo me-2"></i>Resend Code
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeletion">
                        <i class="fas fa-trash-alt me-2"></i>Delete Business
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deletionModal = new bootstrap.Modal(document.getElementById('deletionVerificationModal'));
    const deleteBtn = document.getElementById('deleteBusiness');
    const confirmUnderstand = document.getElementById('confirmUnderstand');
    const sendCodeBtn = document.getElementById('sendVerificationCode');
    const resendCodeBtn = document.getElementById('resendCode');
    const confirmDeletionBtn = document.getElementById('confirmDeletion');
    const verificationCode = document.getElementById('verificationCode');
    const verificationError = document.getElementById('verificationError');
    const verificationSuccess = document.getElementById('verificationSuccess');

    // Show modal when delete button is clicked
    deleteBtn.addEventListener('click', function() {
        deletionModal.show();
    });

    // Enable send code button when checkbox is checked
    confirmUnderstand.addEventListener('change', function() {
        sendCodeBtn.disabled = !this.checked;
    });

    // Send verification code
    sendCodeBtn.addEventListener('click', function() {
        sendCodeBtn.disabled = true;
        sendCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

        fetch('{{ route('business.deletion.send-code') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Switch to step 2
                document.getElementById('deletionStep1').style.display = 'none';
                document.getElementById('deletionStep1Footer').style.display = 'none';
                document.getElementById('deletionStep2').style.display = 'block';
                document.getElementById('deletionStep2Footer').style.display = 'flex';
                verificationCode.focus();
            } else {
                alert(data.message || 'Failed to send verification code. Please try again.');
                sendCodeBtn.disabled = false;
                sendCodeBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Verification Code';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            sendCodeBtn.disabled = false;
            sendCodeBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Verification Code';
        });
    });

    // Resend verification code
    resendCodeBtn.addEventListener('click', function() {
        resendCodeBtn.disabled = true;
        resendCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

        fetch('{{ route('business.deletion.send-code') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                verificationSuccess.textContent = 'Verification code resent successfully!';
                verificationSuccess.style.display = 'block';
                verificationError.style.display = 'none';
                setTimeout(() => {
                    verificationSuccess.style.display = 'none';
                }, 3000);
            } else {
                verificationError.textContent = data.message || 'Failed to resend code.';
                verificationError.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            verificationError.textContent = 'An error occurred. Please try again.';
            verificationError.style.display = 'block';
        })
        .finally(() => {
            resendCodeBtn.disabled = false;
            resendCodeBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Resend Code';
        });
    });

    // Confirm deletion with verification code
    confirmDeletionBtn.addEventListener('click', function() {
        const code = verificationCode.value.trim();
        
        if (code.length !== 6) {
            verificationError.textContent = 'Please enter a valid 6-digit code.';
            verificationError.style.display = 'block';
            return;
        }

        confirmDeletionBtn.disabled = true;
        confirmDeletionBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
        verificationError.style.display = 'none';

        fetch('{{ route('business.deletion.verify') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                verificationSuccess.textContent = data.message;
                verificationSuccess.style.display = 'block';
                verificationError.style.display = 'none';
                
                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                verificationError.textContent = data.message || 'Invalid verification code.';
                verificationError.style.display = 'block';
                confirmDeletionBtn.disabled = false;
                confirmDeletionBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Delete Business';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            verificationError.textContent = 'An error occurred. Please try again.';
            verificationError.style.display = 'block';
            confirmDeletionBtn.disabled = false;
            confirmDeletionBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Delete Business';
        });
    });

    // Allow Enter key to submit code
    verificationCode.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmDeletionBtn.click();
        }
    });

    // Reset modal when closed
    document.getElementById('deletionVerificationModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('deletionStep1').style.display = 'block';
        document.getElementById('deletionStep1Footer').style.display = 'flex';
        document.getElementById('deletionStep2').style.display = 'none';
        document.getElementById('deletionStep2Footer').style.display = 'none';
        confirmUnderstand.checked = false;
        sendCodeBtn.disabled = true;
        sendCodeBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Verification Code';
        verificationCode.value = '';
        verificationError.style.display = 'none';
        verificationSuccess.style.display = 'none';
        confirmDeletionBtn.disabled = false;
        confirmDeletionBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Delete Business';
    });
});

// Plan selection for subscription upgrade
document.addEventListener('DOMContentLoaded', function() {
    const planCards = document.querySelectorAll('.plan-card');
    const selectedPlanInput = document.getElementById('selectedPlan');
    
    if (planCards.length > 0 && selectedPlanInput) {
        // Set Enterprise as default selection
        const enterpriseCard = document.querySelector('.plan-card[data-plan="enterprise"]');
        if (enterpriseCard) {
            enterpriseCard.style.borderColor = '#10b981';
            enterpriseCard.style.boxShadow = '0 4px 16px rgba(16, 185, 129, 0.3)';
            enterpriseCard.style.transform = 'scale(1.02)';
        }
        
        planCards.forEach(card => {
            card.addEventListener('click', function() {
                const plan = this.getAttribute('data-plan');
                
                // Remove selection from all cards
                planCards.forEach(c => {
                    c.style.borderColor = '#e5e7eb';
                    c.style.boxShadow = 'none';
                    c.style.transform = 'scale(1)';
                });
                
                // Highlight selected card
                if (plan === 'premium') {
                    this.style.borderColor = '#7b2e2e';
                    this.style.boxShadow = '0 4px 16px rgba(102, 126, 234, 0.3)';
                } else {
                    this.style.borderColor = '#10b981';
                    this.style.boxShadow = '0 4px 16px rgba(16, 185, 129, 0.3)';
                }
                this.style.transform = 'scale(1.02)';
                
                // Update hidden input
                selectedPlanInput.value = plan;
            });
        });
    }
});

function toggleSecretKey() {
    const input = document.getElementById('paystack_secret_key_input');
    const icon  = document.getElementById('toggleSecretKeyIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Activate the Payments tab when the URL hash is #payments
document.addEventListener('DOMContentLoaded', function () {
    if (window.location.hash === '#payments') {
        const tab = document.getElementById('payments-tab');
        if (tab) { tab.click(); }
    }
});
</script>

@endsection







