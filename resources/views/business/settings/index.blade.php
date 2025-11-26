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
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Security
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






