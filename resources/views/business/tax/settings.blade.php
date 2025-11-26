@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">Tax Settings</h5>
                            <p class="text-sm mb-0">Configure tax rates and display options for your business</p>
                        </div>
                        <a href="{{ route('tax.reports') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-chart-line me-2"></i>View Tax Reports
                        </a>
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

                    <form action="{{ route('tax.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Tax Enabled -->
                            <div class="col-md-12 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="tax_enabled" 
                                           name="tax_enabled" value="1" 
                                           {{ old('tax_enabled', $taxSettings->tax_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tax_enabled">
                                        <strong>Enable Tax Management</strong>
                                        <p class="text-xs text-muted mb-0">Automatically calculate and track taxes on sales</p>
                                    </label>
                                </div>
                            </div>

                            <div id="tax-options" class="{{ old('tax_enabled', $taxSettings->tax_enabled) ? '' : 'd-none' }}">
                                <!-- Tax Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="tax_type" class="form-label">Tax Type</label>
                                    <select class="form-select" id="tax_type" name="tax_type" required>
                                        <option value="VAT" {{ old('tax_type', $taxSettings->tax_type) == 'VAT' ? 'selected' : '' }}>VAT (Value Added Tax)</option>
                                        <option value="GST" {{ old('tax_type', $taxSettings->tax_type) == 'GST' ? 'selected' : '' }}>GST (Goods and Services Tax)</option>
                                        <option value="Sales Tax" {{ old('tax_type', $taxSettings->tax_type) == 'Sales Tax' ? 'selected' : '' }}>Sales Tax</option>
                                        <option value="Other" {{ old('tax_type', $taxSettings->tax_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <!-- Tax Rate -->
                                <div class="col-md-6 mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                    <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                                           min="0" max="100" step="0.01" 
                                           value="{{ old('tax_rate', $taxSettings->tax_rate) }}" 
                                           placeholder="e.g., 16.00" required>
                                    <small class="text-muted">Kenya VAT is typically 16%</small>
                                </div>

                                <!-- Tax Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="tax_number" class="form-label">Tax Registration Number</label>
                                    <input type="text" class="form-control" id="tax_number" name="tax_number" 
                                           value="{{ old('tax_number', $taxSettings->tax_number) }}" 
                                           placeholder="e.g., KRA PIN: P051234567X">
                                    <small class="text-muted">Optional - Your KRA PIN or tax registration number</small>
                                </div>

                                <!-- Tax Display Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="tax_name" class="form-label">Tax Display Name</label>
                                    <input type="text" class="form-control" id="tax_name" name="tax_name" 
                                           value="{{ old('tax_name', $taxSettings->tax_name) }}" 
                                           placeholder="e.g., VAT" required>
                                    <small class="text-muted">How tax appears on receipts</small>
                                </div>

                                <!-- Tax Inclusive -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tax Calculation Method</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tax_inclusive" 
                                               id="tax_exclusive" value="0" 
                                               {{ old('tax_inclusive', $taxSettings->tax_inclusive) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tax_exclusive">
                                            <strong>Tax Exclusive</strong>
                                            <p class="text-xs text-muted mb-0">Tax is added to the price</p>
                                        </label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="tax_inclusive" 
                                               id="tax_inclusive" value="1" 
                                               {{ old('tax_inclusive', $taxSettings->tax_inclusive) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tax_inclusive">
                                            <strong>Tax Inclusive</strong>
                                            <p class="text-xs text-muted mb-0">Tax is already included in the price</p>
                                        </label>
                                    </div>
                                </div>

                                <!-- Tax Period -->
                                <div class="col-md-6 mb-3">
                                    <label for="tax_period" class="form-label">Tax Filing Period</label>
                                    <select class="form-select" id="tax_period" name="tax_period" required>
                                        <option value="monthly" {{ old('tax_period', $taxSettings->tax_period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('tax_period', $taxSettings->tax_period) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="annual" {{ old('tax_period', $taxSettings->tax_period) == 'annual' ? 'selected' : '' }}>Annual</option>
                                    </select>
                                    <small class="text-muted">How often you file tax returns</small>
                                </div>

                                <!-- Receipt Display Options -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Receipt Display Options</label>
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_tax_on_receipt" 
                                               name="show_tax_on_receipt" value="1" 
                                               {{ old('show_tax_on_receipt', $taxSettings->show_tax_on_receipt) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_tax_on_receipt">
                                            Show tax information on receipts
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="separate_tax_column" 
                                               name="separate_tax_column" value="1" 
                                               {{ old('separate_tax_column', $taxSettings->separate_tax_column) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="separate_tax_column">
                                            Show tax as a separate line item
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Tax Settings
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tax Information Card -->
            <div class="card mt-4">
                <div class="card-header pb-0">
                    <h6>Tax Management Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-sm">Tax Exclusive Pricing</h6>
                            <p class="text-xs text-muted">
                                With tax exclusive pricing, the tax amount is calculated and added to the product price at checkout.
                                <br><strong>Example:</strong> Product KSh 100 + 16% VAT = KSh 116 total
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-sm">Tax Inclusive Pricing</h6>
                            <p class="text-xs text-muted">
                                With tax inclusive pricing, the tax is already included in your product prices. The system will extract the tax component.
                                <br><strong>Example:</strong> Product KSh 116 includes KSh 16 VAT (subtotal: KSh 100)
                            </p>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important:</strong> Tax settings will apply to all new sales. Existing orders will not be affected.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('tax_enabled').addEventListener('change', function() {
    const taxOptions = document.getElementById('tax-options');
    if (this.checked) {
        taxOptions.classList.remove('d-none');
    } else {
        taxOptions.classList.add('d-none');
    }
});
</script>
@endsection

