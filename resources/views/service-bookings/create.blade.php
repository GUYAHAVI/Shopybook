@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Record Service Payment</h2>
                <div class="alert alert-info mb-4">
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
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id">
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
                                <label for="payment_method" class="form-label">Payment Method *</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
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

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Select Services *</h5>
                        <div class="row">
                            @foreach($services as $service)
                                <div class="col-md-6 mb-3">
                                    <div class="card service-card" style="cursor: pointer;">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input service-checkbox" type="checkbox" 
                                                       value="{{ $service->id }}" id="service_{{ $service->id }}"
                                                       name="selected_services[]"
                                                       data-price="{{ $service->price }}"
                                                       data-commission-rate="{{ $service->commission_rate }}"
                                                       data-name="{{ $service->name }}"
                                                       data-bundle-trigger="{{ $service->is_bundle_trigger ? 'true' : 'false' }}"
                                                       data-bundled-services="{{ $service->bundled_services ? json_encode($service->bundled_services) : '[]' }}"
                                                       data-is-complimentary="{{ $service->is_complimentary ? 'true' : 'false' }}"
                                                       data-parent-service-id="{{ $service->parent_service_id }}">
                                                <label class="form-check-label w-100" for="service_{{ $service->id }}">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>{{ $service->name }}</strong>
                                                            @if($service->description)
                                                                <p class="text-muted small mb-0">{{ $service->description }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="fw-bold">KSh {{ number_format($service->price, 2) }}</span>
                                                            @if($service->commission_rate > 0)
                                                                <br><small class="text-muted">{{ $service->commission_rate }}% commission</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('selected_services')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <!-- Dynamic Service-Staff Mapping Section -->
                    <div id="service-staff-section" style="display: none;">
                        <h5 class="fw-bold mb-3">Assign Staff to Services</h5>
                        <div id="service-staff-mappings"></div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes">{{ old('notes') }}</textarea>
                        @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <!-- Summary Section -->
                    <div class="card bg-light mb-3" id="summary-section" style="display: none;">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2">Payment Summary</h6>
                            <div id="summary-content"></div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total Amount: </strong>
                                <strong id="total-amount">KSh 0.00</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('service-bookings.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <span class="submit-text">Record Payment</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="loading-text d-none">Recording...</span>
                        </button>
                    </div>
                </form>

                <!-- Commission Summary Modal -->
                <div class="modal fade" id="commissionSummaryModal" tabindex="-1" aria-labelledby="commissionSummaryModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="commissionSummaryModalLabel">Commission Summary</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="commission-summary-content">
                                    <!-- Commission summary will be populated here -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <a href="{{ route('service-bookings.index') }}" class="btn btn-primary">View All Bookings</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.service-card {
    transition: all 0.2s;
    border: 2px solid #e9ecef;
}
.service-card:hover {
    border-color: #4a5cff;
    box-shadow: 0 2px 4px rgba(74, 92, 255, 0.1);
}
.service-card.selected {
    border-color: #4a5cff;
    background-color: rgba(74, 92, 255, 0.05);
}
.staff-assignment {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const staffData = @json($staff);
    
    // Validate staffData
    if (!staffData || !Array.isArray(staffData)) {
        console.error('Staff data is not available or invalid');
        return;
    }
    
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

        // Clear existing mappings
        serviceStaffMappings.innerHTML = '';

        selectedServices.forEach((checkbox, index) => {
            const serviceId = checkbox.value;
            const serviceName = checkbox.dataset.name;
            const servicePrice = parseFloat(checkbox.dataset.price);
            const commissionRate = parseFloat(checkbox.dataset.commissionRate);
            const isComplimentary = checkbox.dataset.isComplimentary === 'true';
            const parentServiceId = checkbox.dataset.parentServiceId;
            
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

        // Add event listeners to staff selects
        document.querySelectorAll('.staff-select').forEach(select => {
            select.addEventListener('change', updateSummaryAndValidation);
        });

        updateSummaryAndValidation();
    }

    function updateSummaryAndValidation() {
        const selectedServices = Array.from(serviceCheckboxes).filter(cb => cb.checked);
        const staffSelects = document.querySelectorAll('.staff-select');
        const paymentMethod = document.getElementById('payment_method').value;
        
        // Check if all requirements are met
        const allStaffAssigned = Array.from(staffSelects).every(select => select.value !== '');
        const hasPaymentMethod = paymentMethod !== '';
        
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
        totalAmountElement.textContent = `KSh ${totalAmount.toFixed(2)}`;
        
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
});
</script>
@endsection
