@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Services -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('services.index') }}" class="nav-tab">
                <i class="fas fa-list me-1"></i> All Services
            </a>
            <a href="{{ route('services.create') }}" class="nav-tab">
                <i class="fas fa-plus me-1"></i> Add Service
            </a>
            <a href="{{ route('service-bookings.index') }}" class="nav-tab">
                <i class="fas fa-calendar-check me-1"></i> Bookings
            </a>
            <a href="{{ route('service-bookings.create') }}" class="nav-tab active">
                <i class="fas fa-plus-circle me-1"></i> New Booking
            </a>
            <a href="{{ route('service-bookings.bulk-create') }}" class="nav-tab">
                <i class="fas fa-layer-group me-1"></i> Bulk Entry
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card p-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <h2 class="fw-bold mb-4" style="color: var(--text-primary);">Record Service Payment</h2>
                <div class="alert alert-info mb-4" style="background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    <i class="fas fa-info-circle"></i> Record services that have been completed and paid for. Service date and time will be automatically captured.
                </div>
                
                <!-- Form Feedback Area -->
                    <div id="form-feedback" class="d-none mb-3">
                        <div class="alert alert-warning">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            <span>Recording service payment...</span>
                        </div>
                    </div>                <form method="POST" action="{{ route('service-bookings.store') }}" id="serviceBookingForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label" style="color: var(--text-primary);">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id"
                                        style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <option value="">Walk-in/Anonymous</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" @if(old('customer_id') == $customer->id) selected @endif>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label" style="color: var(--text-primary);">Payment Method *</label>
                                <select class="form-control" id="payment_method" name="payment_method" required
                                        style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" @if(old('payment_method') == 'cash') selected @endif>Cash</option>
                                    <option value="mpesa" @if(old('payment_method') == 'mpesa') selected @endif>M-Pesa</option>
                                    <option value="card" @if(old('payment_method') == 'card') selected @endif>Card</option>
                                    <option value="bank_transfer" @if(old('payment_method') == 'bank_transfer') selected @endif>Bank Transfer</option>
                                    <option value="other" @if(old('payment_method') == 'other') selected @endif>Other</option>
                                </select>
                                @error('payment_method')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Discount Section -->
                    <div class="card mb-4" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: var(--text-primary);"><i class="fas fa-percentage me-2"></i>Discount (Optional)</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="discount_type" class="form-label" style="color: var(--text-primary);">Discount Type</label>
                                        <select class="form-control" id="discount_type" name="discount_type"
                                                style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                            <option value="none" @if(old('discount_type') == 'none' || !old('discount_type')) selected @endif>No Discount</option>
                                            <option value="percentage" @if(old('discount_type') == 'percentage') selected @endif>Percentage (%)</option>
                                            <option value="fixed" @if(old('discount_type') == 'fixed') selected @endif>Fixed Amount (KSh)</option>
                                        </select>
                                        @error('discount_type')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="discount_value" class="form-label" style="color: var(--text-primary);">Discount Value</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="discount_value" name="discount_value" value="{{ old('discount_value') }}" placeholder="0.00" disabled
                                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                        @error('discount_value')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: var(--text-primary);">Discount Preview</label>
                                        <div class="form-control-plaintext" id="discount_preview" style="color: var(--text-muted);">
                                            <small style="color: var(--text-muted);">Select discount type to preview</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: var(--text-primary);">Select Services *</h5>
                        <div class="row">
                            @foreach($services as $service)
                                <div class="col-md-6 mb-3">
                                    <div class="card service-card" style="cursor: pointer; background: var(--card-bg); border: 1px solid var(--border-color);">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input service-checkbox" type="checkbox" 
                                                       value="{{ $service->id }}" id="service_{{ $service->id }}"
                                                       name="selected_services[]"
                                                       data-price="{{ $service->price }}"
                                                       data-commission-rate="{{ $service->commission_rate }}"
                                                       data-name="{{ $service->name }}"
                                                       data-bundle-trigger="{{ $service->is_bundle_trigger ? 'true' : 'false' }}"
                                                       data-is-complimentary="false"
                                                       data-parent-service-id=""
                                                       data-bundled-services="{{ $service->bundled_services ? json_encode($service->bundled_services) : '[]' }}"
                                                       style="border-color: var(--border-color);">
                                                <label class="form-check-label" for="service_{{ $service->id }}" style="color: var(--text-primary);">
                                                    <strong>{{ $service->name }}</strong>
                                                    <br>
                                                    <small style="color: var(--text-muted);">KSh {{ number_format($service->price, 2) }}</small>
                                                    @if($service->is_bundle_trigger)
                                                        <br><small class="badge bg-warning" style="color: var(--text-primary);">Bundle Trigger</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Staff Assignment Section -->
                    <div class="card mb-4" id="service-staff-section" style="display: none; background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: var(--text-primary);"><i class="fas fa-users me-2"></i>Assign Staff to Services</h6>
                            <div id="service-staff-mappings">
                                <!-- Staff assignments will be dynamically generated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="card mb-4" id="summary-section" style="display: none; background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: var(--text-primary);"><i class="fas fa-calculator me-2"></i>Payment Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: var(--text-primary);">Selected Services</label>
                                        <div id="summary-content" style="color: var(--text-secondary);">
                                            <small style="color: var(--text-muted);">No services selected</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: var(--text-primary);">Payment Summary</label>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span style="color: var(--text-secondary);">Subtotal:</span>
                                            <span id="subtotal-amount" style="color: var(--text-primary);">KSh 0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1" id="discount-row" style="display: none;">
                                            <span style="color: var(--text-secondary);">Discount:</span>
                                            <span id="discount-amount" style="color: var(--text-success);">- KSh 0.00</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <span style="color: var(--text-primary); font-weight: bold;">Total Amount:</span>
                                            <span class="h5 mb-0" id="total-amount" style="color: var(--text-primary);">KSh 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('service-bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Bookings
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <span class="submit-text"><i class="fas fa-save me-1"></i> Record Payment</span>
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                            <span class="loading-text d-none">Recording...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.nav-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.nav-tab {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
    border-color: var(--border-color);
}

.nav-tab.active {
    color: var(--white);
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.service-card:hover {
    box-shadow: 0 4px 6px var(--shadow-color);
    transition: box-shadow 0.2s ease;
}

.service-card.selected {
    border-color: var(--primary-color);
    background: var(--bg-tertiary);
}

.staff-assignment {
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    background: var(--card-bg);
}

.staff-assignment:last-child {
    margin-bottom: 0;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tab {
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const staffData = @json($staff);
    
    // Validate staffData
    if (!staffData || !Array.isArray(staffData)) {
        console.error('Staff data is not available or invalid:', staffData);
        return;
    }
    
    console.log('Staff data loaded:', staffData.length, 'staff members');
    
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const serviceStaffSection = document.getElementById('service-staff-section');
    const serviceStaffMappings = document.getElementById('service-staff-mappings');
    const summarySection = document.getElementById('summary-section');
    const summaryContent = document.getElementById('summary-content');
    const totalAmountElement = document.getElementById('total-amount');
    const submitBtn = document.getElementById('submit-btn');

    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleServiceSelection(this);
            updateServiceStaffMappings();
        });
    });

    // Add payment method validation
    document.getElementById('payment_method').addEventListener('change', updateSummaryAndValidation);

    // Make service cards clickable
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = card.querySelector('.service-checkbox');
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    function handleServiceSelection(checkbox) {
        const serviceId = checkbox.value;
        const isBundleTrigger = checkbox.dataset.bundleTrigger === 'true';
        
        let bundledServices = [];
        try {
            bundledServices = checkbox.dataset.bundledServices ? JSON.parse(checkbox.dataset.bundledServices) : [];
        } catch (e) {
            console.error('Error parsing bundled services:', e);
            bundledServices = [];
        }
        
        if (checkbox.checked && isBundleTrigger && bundledServices.length > 0) {
            // Automatically select bundled services
            bundledServices.forEach(bundledServiceId => {
                const bundledCheckbox = document.querySelector(`input[value="${bundledServiceId}"]`);
                if (bundledCheckbox && !bundledCheckbox.checked) {
                    bundledCheckbox.checked = true;
                    const bundledCard = bundledCheckbox.closest('.service-card');
                    bundledCard.classList.add('selected');
                    
                    // Show notification that service was auto-selected
                    const serviceName = bundledCheckbox.dataset.name;
                    showAutoSelectionNotification(serviceName);
                }
            });
        } else if (!checkbox.checked && isBundleTrigger && bundledServices.length > 0) {
            // When unchecking a bundle trigger, ask if user wants to uncheck bundled services too
            bundledServices.forEach(bundledServiceId => {
                const bundledCheckbox = document.querySelector(`input[value="${bundledServiceId}"]`);
                if (bundledCheckbox && bundledCheckbox.checked) {
                    const bundledServiceName = bundledCheckbox.dataset.name;
                    if (confirm(`Do you want to remove ${bundledServiceName} as well?`)) {
                        bundledCheckbox.checked = false;
                        const bundledCard = bundledCheckbox.closest('.service-card');
                        bundledCard.classList.remove('selected');
                    }
                }
            });
        }
    }

    function showAutoSelectionNotification(serviceName) {
        // Create and show a temporary notification
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        notification.innerHTML = `
            <small><strong>Auto-selected:</strong> ${serviceName} was automatically included</small>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    function updateServiceStaffMappings() {
        const selectedServices = Array.from(serviceCheckboxes).filter(cb => cb.checked);
        
        console.log('Selected services:', selectedServices.length);
        
        // Update card styling
        serviceCheckboxes.forEach(checkbox => {
            const card = checkbox.closest('.service-card');
            if (checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });

        if (selectedServices.length === 0) {
            serviceStaffSection.style.display = 'none';
            summarySection.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }

        serviceStaffSection.style.display = 'block';
        summarySection.style.display = 'block';
        
        console.log('Staff section and summary section should now be visible');

        // Clear existing mappings
        serviceStaffMappings.innerHTML = '';

        selectedServices.forEach((checkbox, index) => {
            const serviceId = checkbox.value;
            const serviceName = checkbox.dataset.name;
            const servicePrice = parseFloat(checkbox.dataset.price);
            const commissionRate = parseFloat(checkbox.dataset.commissionRate);
            const isComplimentary = checkbox.dataset.isComplimentary === 'true';
            const parentServiceId = checkbox.dataset.parentServiceId;
            
            console.log('Creating staff assignment for service:', serviceName, 'Price:', servicePrice, 'Commission:', commissionRate);
            
            // Calculate commission based on service type
            let commissionAmount;
            let commissionSource = servicePrice;
            let commissionNote = '';
            
            if (isComplimentary && parentServiceId) {
                // Find parent service price for commission calculation
                const parentCheckbox = document.querySelector(`input[value="${parentServiceId}"]`);
                if (parentCheckbox && parentCheckbox.checked) {
                    commissionSource = parseFloat(parentCheckbox.dataset.price);
                    commissionNote = ' (from parent service)';
                }
            }
            
            commissionAmount = (commissionSource * commissionRate / 100);

            const mappingDiv = document.createElement('div');
            mappingDiv.className = 'staff-assignment';
            mappingDiv.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="mb-1">${serviceName}</h6>
                        <small class="text-muted">KSh ${servicePrice.toFixed(2)}${isComplimentary ? ' (Complimentary)' : ''}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Assign Staff *</label>
                        <select class="form-control form-control-sm staff-select" name="services[${index}][staff_id]" required>
                            <option value="">Select Staff Member</option>
                            ${staffData.map(staff => '<option value="' + staff.id + '">' + staff.name + '</option>').join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">
                            Commission: ${commissionRate}%${commissionNote}<br>
                            KSh ${commissionAmount.toFixed(2)}
                        </small>
                    </div>
                </div>
                <input type="hidden" name="services[${index}][service_id]" value="${serviceId}">
                <input type="hidden" name="services[${index}][amount]" value="${servicePrice}">
                <input type="hidden" name="services[${index}][commission]" value="${commissionAmount.toFixed(2)}">
                <input type="hidden" name="services[${index}][is_complimentary]" value="${isComplimentary}">
                <input type="hidden" name="services[${index}][parent_service_id]" value="${parentServiceId || ''}">
            `;
            serviceStaffMappings.appendChild(mappingDiv);
        });

        console.log('Staff assignments created, adding event listeners...');

        // Add event listeners to staff selects
        document.querySelectorAll('.staff-select').forEach(select => {
            select.addEventListener('change', updateSummaryAndValidation);
        });
        
        console.log('Event listeners added to', document.querySelectorAll('.staff-select').length, 'staff selects');

        updateSummaryAndValidation();
    }

    function updateSummaryAndValidation() {
        const selectedServices = Array.from(serviceCheckboxes).filter(cb => cb.checked);
        const staffSelects = document.querySelectorAll('.staff-select');
        const paymentMethod = document.getElementById('payment_method').value;
        
        console.log('Summary validation - Services:', selectedServices.length, 'Staff selects:', staffSelects.length, 'Payment method:', paymentMethod);
        
        // Check if all requirements are met
        const allStaffAssigned = Array.from(staffSelects).every(select => select.value !== '');
        const hasPaymentMethod = paymentMethod !== '';
        
        console.log('Validation - All staff assigned:', allStaffAssigned, 'Has payment method:', hasPaymentMethod);
        
        submitBtn.disabled = !allStaffAssigned || selectedServices.length === 0 || !hasPaymentMethod;

        // Update summary
        let summaryHtml = '';
        let totalAmount = 0;

        selectedServices.forEach((checkbox, index) => {
            const serviceName = checkbox.dataset.name;
            const servicePrice = parseFloat(checkbox.dataset.price);
            const isComplimentary = checkbox.dataset.isComplimentary === 'true';
            const staffSelect = staffSelects[index];
            const staffName = staffSelect.value ? staffSelect.options[staffSelect.selectedIndex].text : 'Not assigned';
            
            // Only add to total if not complimentary
            if (!isComplimentary) {
                totalAmount += servicePrice;
            }
            
            summaryHtml += `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span>${serviceName}${isComplimentary ? ' (Included)' : ''} <small class="text-muted">(${staffName})</small></span>
                    <span>${isComplimentary ? 'Included' : 'KSh ' + servicePrice.toFixed(2)}</span>
                </div>
            `;
        });

        summaryContent.innerHTML = summaryHtml;
        
        // Calculate discount and final amount
        const discountInfo = calculateDiscount(totalAmount);
        updateSummaryDisplay(totalAmount, discountInfo);
        
        // Update commission summary
        updateCommissionSummary();
    }

    function updateCommissionSummary() {
        const selectedServices = Array.from(serviceCheckboxes).filter(cb => cb.checked);
        const staffSelects = document.querySelectorAll('.staff-select');
        const commissionSummary = {};
        
        selectedServices.forEach((checkbox, index) => {
            const serviceName = checkbox.dataset.name;
            const servicePrice = parseFloat(checkbox.dataset.price);
            const commissionRate = parseFloat(checkbox.dataset.commissionRate);
            const isComplimentary = checkbox.dataset.isComplimentary === 'true';
            const parentServiceId = checkbox.dataset.parentServiceId;
            const staffSelect = staffSelects[index];
            
            if (!staffSelect.value) return; // Skip if no staff assigned
            
            const staffId = staffSelect.value;
            const staffName = staffSelect.options[staffSelect.selectedIndex].text;
            
            // Calculate commission based on service type
            let commissionSource = servicePrice;
            if (isComplimentary && parentServiceId) {
                const parentCheckbox = document.querySelector(`input[value="${parentServiceId}"]`);
                if (parentCheckbox && parentCheckbox.checked) {
                    commissionSource = parseFloat(parentCheckbox.dataset.price);
                }
            }
            
            const commissionAmount = (commissionSource * commissionRate / 100);
            
            if (!commissionSummary[staffId]) {
                commissionSummary[staffId] = {
                    name: staffName,
                    services: [],
                    totalCommission: 0
                };
            }
            
            commissionSummary[staffId].services.push({
                serviceName: serviceName,
                servicePrice: servicePrice,
                commissionRate: commissionRate,
                commissionAmount: commissionAmount,
                isComplimentary: isComplimentary
            });
            
            commissionSummary[staffId].totalCommission += commissionAmount;
        });
        
        // Store commission summary for modal display
        window.currentCommissionSummary = commissionSummary;
    }

    // Add form submission loading state
    document.getElementById('serviceBookingForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        const submitText = submitBtn.querySelector('.submit-text');
        const spinner = submitBtn.querySelector('.spinner-border');
        const loadingText = submitBtn.querySelector('.loading-text');
        const formFeedback = document.getElementById('form-feedback');
        
        // Show loading state
        submitText.classList.add('d-none');
        spinner.classList.remove('d-none');
        loadingText.classList.remove('d-none');
        submitBtn.disabled = true;
        
        // Show form feedback
        formFeedback.classList.remove('d-none');
        formFeedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Store commission summary and form data for success page
        localStorage.setItem('commission_summary', JSON.stringify(window.currentCommissionSummary || {}));
        localStorage.setItem('booking_submitting', 'true');
        localStorage.setItem('booking_submit_time', Date.now().toString());
        
        // Prevent double submission
        setTimeout(() => {
            // Re-enable if still on the same page after 15 seconds
            if (localStorage.getItem('booking_submitting') === 'true') {
                submitText.classList.remove('d-none');
                spinner.classList.add('d-none');
                loadingText.classList.add('d-none');
                submitBtn.disabled = false;
                formFeedback.classList.add('d-none');
                localStorage.removeItem('booking_submitting');
                localStorage.removeItem('booking_submit_time');
            }
        }, 15000);
    });
    
    // Clear submission state when page loads (in case of successful redirect)
    window.addEventListener('load', function() {
        localStorage.removeItem('booking_submitting');
        localStorage.removeItem('booking_submit_time');
    });

    // Discount functionality
    const discountTypeSelect = document.getElementById('discount_type');
    const discountValueInput = document.getElementById('discount_value');
    const discountPreview = document.getElementById('discount_preview');

    // Enable/disable discount value input based on type
    discountTypeSelect.addEventListener('change', function() {
        if (this.value === 'none') {
            discountValueInput.disabled = true;
            discountValueInput.value = '';
            discountPreview.innerHTML = '<small class="text-muted">No discount applied</small>';
        } else {
            discountValueInput.disabled = false;
            discountPreview.innerHTML = '<small class="text-muted">Enter value to preview</small>';
        }
        updateSummaryIfServicesSelected();
    });

    // Update preview when discount value changes
    discountValueInput.addEventListener('input', function() {
        updateSummaryIfServicesSelected();
    });

    function calculateDiscount(subtotal) {
        const discountType = discountTypeSelect.value;
        const discountValue = parseFloat(discountValueInput.value) || 0;
        
        if (discountType === 'none' || discountValue <= 0) {
            return {
                type: 'none',
                value: 0,
                amount: 0,
                display: 'No discount'
            };
        }

        let discountAmount = 0;
        let discountDisplay = '';

        if (discountType === 'percentage') {
            discountAmount = (subtotal * discountValue) / 100;
            discountDisplay = `${discountValue}% off`;
        } else if (discountType === 'fixed') {
            discountAmount = Math.min(discountValue, subtotal); // Don't exceed subtotal
            discountDisplay = `KSh ${discountValue.toFixed(2)} off`;
        }

        return {
            type: discountType,
            value: discountValue,
            amount: discountAmount,
            display: discountDisplay
        };
    }

    function updateSummaryDisplay(subtotal, discountInfo) {
        const subtotalElement = document.getElementById('subtotal-amount');
        const discountRow = document.getElementById('discount-row');
        const discountAmountElement = document.getElementById('discount-amount');
        const totalAmountElement = document.getElementById('total-amount');
        const finalAmount = subtotal - discountInfo.amount;

        // Update subtotal
        subtotalElement.textContent = `KSh ${subtotal.toFixed(2)}`;

        // Update discount display
        if (discountInfo.amount > 0) {
            discountRow.style.display = 'flex';
            discountAmountElement.textContent = `- KSh ${discountInfo.amount.toFixed(2)}`;
            discountPreview.innerHTML = `<small class="text-success"><i class="fas fa-check"></i> ${discountInfo.display}</small>`;
        } else {
            discountRow.style.display = 'none';
            discountPreview.innerHTML = discountInfo.type === 'none' ? 
                '<small class="text-muted">No discount applied</small>' : 
                '<small class="text-muted">Enter value to preview</small>';
        }

        // Update final amount
        totalAmountElement.textContent = `KSh ${finalAmount.toFixed(2)}`;
    }

    function updateSummaryIfServicesSelected() {
        const selectedServices = Array.from(serviceCheckboxes).filter(cb => cb.checked);
        if (selectedServices.length > 0) {
            updateSummaryAndValidation();
        }
    }
});
</script>
@endsection
