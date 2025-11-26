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
            <a href="{{ route('service-bookings.create') }}" class="nav-tab">
                <i class="fas fa-plus-circle me-1"></i> New Booking
            </a>
            <a href="{{ route('service-bookings.bulk-create') }}" class="nav-tab active">
                <i class="fas fa-layer-group me-1"></i> Bulk Entry
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card p-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: var(--text-primary);">Bulk Service Entry</h2>
                        <p class="text-muted mb-0">Select a date, then add all services completed on that day</p>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-primary fs-6" id="service-counter">0 Services</div>
                    </div>
                </div>

                @if(session('warning'))
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        @if(session('errors'))
                            <ul class="mb-0 mt-2">
                                @foreach(session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
                
                <div class="alert alert-info mb-4" style="background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>How it works:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Choose the date when services were completed</li>
                        <li>Add all services that were done on that day</li>
                        <li>Assign staff and set payment details for each service</li>
                        <li>Submit all services at once</li>
                    </ol>
                </div>

                <form method="POST" action="{{ route('service-bookings.bulk-store') }}" id="bulkBookingForm">
                    @csrf
                    
                    <!-- Date Selection Section -->
                    <div class="card mb-4" style="background: var(--bg-tertiary); border: 2px solid var(--primary-color);">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h5 class="mb-0" style="color: var(--text-primary);">
                                        <i class="fas fa-calendar-day me-2"></i>Service Date
                                    </h5>
                                    <small class="text-muted">When were these services completed?</small>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control form-control-lg" id="service-date" name="service_date" required
                                           style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-primary" id="set-today-btn">
                                        <i class="fas fa-calendar-check me-1"></i> Set Today
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services Section -->
                    <div id="services-section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 style="color: var(--text-primary);">
                                <i class="fas fa-concierge-bell me-2"></i>Services for <span id="selected-date-display"></span>
                            </h5>
                            <button type="button" class="btn btn-primary" id="add-service-btn">
                                <i class="fas fa-plus me-1"></i> Add Service
                            </button>
                        </div>

                        <!-- Services Container -->
                        <div id="services-container" class="mb-4">
                            <!-- Dynamic services will be added here -->
                        </div>

                        <!-- No services message -->
                        <div id="no-services-message" class="text-center py-4" style="color: var(--text-muted); display: none;">
                            <i class="fas fa-plus-circle fa-2x mb-3"></i>
                            <h6>No services added yet</h6>
                            <p>Click "Add Service" to start adding services for this date</p>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between" id="form-actions">
                            <a href="{{ route('service-bookings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Bookings
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" id="validate-services-btn">
                                    <i class="fas fa-check-circle me-1"></i> Validate All
                                </button>
                                <button type="submit" class="btn btn-primary" id="submit-bulk-btn" disabled>
                                    <span class="submit-text"><i class="fas fa-save me-1"></i> Create All Services</span>
                                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                                    <span class="loading-text d-none">Creating...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Date Selection Prompt -->
                    <div id="date-prompt" class="text-center py-5" style="color: var(--text-muted);">
                        <i class="fas fa-calendar-alt fa-3x mb-3" style="color: var(--primary-color);"></i>
                        <h5>Select a Service Date</h5>
                        <p>Choose the date when services were completed to get started</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Service Template (Hidden) -->
<div id="service-template" style="display: none;">
    <div class="service-card mb-3" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem;">
        <div class="service-header d-flex justify-content-between align-items-center p-3" style="border-bottom: 1px solid var(--border-color);">
            <div class="d-flex align-items-center">
                <span class="service-number badge bg-primary me-2">1</span>
                <h6 class="mb-0 service-title" style="color: var(--text-primary);">Service Entry</h6>
            </div>
            <div class="service-actions">
                <button type="button" class="btn btn-sm btn-outline-secondary duplicate-service-btn" title="Duplicate this service">
                    <i class="fas fa-copy"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger remove-service-btn" title="Remove this service">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="service-body p-3">
            <div class="row">
                <!-- Service and Customer in one row -->
                <div class="col-md-6 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Service *</label>
                    <select class="form-control service-select" name="bookings[INDEX][service_id]" required
                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        <option value="">Select Service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" 
                                    data-price="{{ $service->price }}" 
                                    data-commission="{{ $service->commission_rate }}"
                                    data-bundle-trigger="{{ $service->is_bundle_trigger ? 'true' : 'false' }}"
                                    data-bundled-services="{{ $service->bundled_services ? json_encode($service->bundled_services) : '[]' }}"
                                    data-name="{{ $service->name }}">
                                {{ $service->name }} - KSh {{ number_format($service->price, 2) }}
                                @if($service->is_bundle_trigger)
                                    (Bundle Trigger)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Customer</label>
                    <select class="form-control customer-select" name="bookings[INDEX][customer_id]"
                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        <option value="">Walk-in/Anonymous</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Staff, Time, and Amount -->
                <div class="col-md-4 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Staff Member *</label>
                    <select class="form-control staff-select" name="bookings[INDEX][staff_id]" required
                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        <option value="">Select Staff</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Service Time</label>
                    <input type="time" class="form-control time-input" name="bookings[INDEX][service_time]" value="09:00"
                           style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Amount (KSh) *</label>
                    <input type="number" step="0.01" min="0" class="form-control amount-input" name="bookings[INDEX][amount]" required
                           style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                </div>

                <!-- Payment Details -->
                <div class="col-md-4 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Payment Method *</label>
                    <select class="form-control payment-method-select" name="bookings[INDEX][payment_method]" required
                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        <option value="cash" selected>Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Payment Status *</label>
                    <select class="form-control payment-status-select" name="bookings[INDEX][payment_status]" required
                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        <option value="paid" selected>Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label" style="color: var(--text-primary);">Discount</label>
                    <div class="input-group">
                        <select class="form-control discount-type-select" name="bookings[INDEX][discount_type]" style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary); max-width: 80px;">
                            <option value="none">None</option>
                            <option value="percentage">%</option>
                            <option value="fixed">KSh</option>
                        </select>
                        <input type="number" step="0.01" min="0" class="form-control discount-input" name="bookings[INDEX][discount_value]" placeholder="0" disabled
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                    </div>
                    <small class="text-muted discount-hint">Select type to enable</small>
                </div>

                <!-- Hidden fields for date -->
                <input type="hidden" class="service-date-input" name="bookings[INDEX][service_date]">

                <!-- Notes (collapsible) -->
                <div class="col-md-12">
                    <div class="form-check mb-2">
                        <input class="form-check-input notes-toggle" type="checkbox" id="notes-toggle-INDEX">
                        <label class="form-check-label" for="notes-toggle-INDEX" style="color: var(--text-primary);">
                            Add notes for this service
                        </label>
                    </div>
                    <div class="notes-section" style="display: none;">
                        <textarea class="form-control notes-input" name="bookings[INDEX][notes]" rows="2" maxlength="1000"
                                  style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);"
                                  placeholder="Any additional notes about this service..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Service Summary -->
            <div class="service-summary mt-3 p-2 rounded" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                <div class="row text-sm align-items-center">
                    <div class="col-md-6">
                        <strong style="color: var(--text-primary);">Summary:</strong> 
                        <span class="summary-text text-muted">Select service to see details</span>
                    </div>
                    <div class="col-md-3 text-end">
                        <strong style="color: var(--text-primary);">Total:</strong> 
                        <span class="total-amount text-success">KSh 0.00</span>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="validation-status badge bg-secondary">Incomplete</span>
                    </div>
                </div>
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

.service-card {
    transition: all 0.2s ease;
}

.service-card:hover {
    box-shadow: 0 4px 6px var(--shadow-color);
}

.service-card.has-errors {
    border-color: #dc3545;
    background: rgba(220, 53, 69, 0.05);
}

.service-card.is-valid {
    border-color: #28a745;
    background: rgba(40, 167, 69, 0.05);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25);
}

.btn-group .btn {
    border-color: var(--border-color);
}

.service-summary {
    margin-top: 1rem;
}

.text-sm {
    font-size: 0.875rem;
}

.date-selection-card {
    border: 2px solid var(--primary-color) !important;
    background: var(--bg-tertiary) !important;
}

.date-selected {
    background: rgba(19, 232, 233, 0.1) !important;
}

.input-group .form-control:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .form-control:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-left: none;
}

.input-group .form-control:focus {
    z-index: 3;
}

.discount-hint {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Responsive design */
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
    
    .btn-group {
        width: 100%;
        flex-direction: column;
    }
    
    .btn-group .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* Animation for adding/removing services */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.service-card {
    animation: slideInDown 0.3s ease-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let serviceIndex = 0;
    let selectedDate = '';
    
    // Elements
    const serviceDateInput = document.getElementById('service-date');
    const setTodayBtn = document.getElementById('set-today-btn');
    const servicesSection = document.getElementById('services-section');
    const datePrompt = document.getElementById('date-prompt');
    const selectedDateDisplay = document.getElementById('selected-date-display');
    const addServiceBtn = document.getElementById('add-service-btn');
    const servicesContainer = document.getElementById('services-container');
    const noServicesMessage = document.getElementById('no-services-message');
    const serviceCounter = document.getElementById('service-counter');
    const validateServicesBtn = document.getElementById('validate-services-btn');
    const submitBulkBtn = document.getElementById('submit-bulk-btn');

    // Event listeners
    serviceDateInput.addEventListener('change', handleDateChange);
    setTodayBtn.addEventListener('click', setToday);
    addServiceBtn.addEventListener('click', addService);
    validateServicesBtn.addEventListener('click', validateAllServices);

    // Initialize
    updateUI();

    function handleDateChange() {
        selectedDate = serviceDateInput.value;
        if (selectedDate) {
            // Show services section
            servicesSection.style.display = 'block';
            datePrompt.style.display = 'none';
            
            // Update date display
            const dateObj = new Date(selectedDate);
            selectedDateDisplay.textContent = dateObj.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            // Update all existing service dates
            document.querySelectorAll('.service-date-input').forEach(input => {
                input.value = selectedDate;
            });
            
            // If no services exist, show the no services message
            if (servicesContainer.children.length === 0) {
                noServicesMessage.style.display = 'block';
            }
        } else {
            // Hide services section
            servicesSection.style.display = 'none';
            datePrompt.style.display = 'block';
        }
        updateUI();
    }

    function setToday() {
        const today = new Date().toISOString().split('T')[0];
        serviceDateInput.value = today;
        handleDateChange();
    }

    function addService(duplicateData = null) {
        if (!selectedDate) {
            showAlert('warning', 'Please select a service date first.');
            return;
        }

        const template = document.getElementById('service-template');
        const serviceHtml = template.innerHTML.replace(/INDEX/g, serviceIndex);
        
        const serviceDiv = document.createElement('div');
        serviceDiv.innerHTML = serviceHtml;
        const serviceCard = serviceDiv.firstElementChild;
        
        // Update service number and title
        const serviceNumber = serviceCard.querySelector('.service-number');
        const serviceTitle = serviceCard.querySelector('.service-title');
        serviceNumber.textContent = serviceIndex + 1;
        serviceTitle.textContent = `Service ${serviceIndex + 1}`;
        
        // Set the service date
        const serviceDateInput = serviceCard.querySelector('.service-date-input');
        serviceDateInput.value = selectedDate;
        
        // Add to container
        servicesContainer.appendChild(serviceCard);
        
        // Set up event listeners for this service
        setupServiceEventListeners(serviceCard, serviceIndex);
        
        // Fill with duplicate data if provided
        if (duplicateData) {
            fillServiceWithData(serviceCard, duplicateData);
        }
        
        serviceIndex++;
        updateUI();
    }

    function setupServiceEventListeners(serviceCard, index) {
        // Remove service button
        const removeBtn = serviceCard.querySelector('.remove-service-btn');
        removeBtn.addEventListener('click', () => {
            serviceCard.remove();
            updateUI();
        });

        // Duplicate service button
        const duplicateBtn = serviceCard.querySelector('.duplicate-service-btn');
        duplicateBtn.addEventListener('click', () => {
            const serviceData = getServiceData(serviceCard);
            addService(serviceData);
        });

        // Service selection auto-fill amount
        const serviceSelect = serviceCard.querySelector('.service-select');
        const amountInput = serviceCard.querySelector('.amount-input');
        
        serviceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.price) {
                amountInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
            }
            
            // Handle bundled services
            handleBundledServices(serviceCard, selectedOption);
            
            updateServiceSummary(serviceCard);
        });

        // Discount type toggle
        const discountTypeSelect = serviceCard.querySelector('.discount-type-select');
        const discountInput = serviceCard.querySelector('.discount-input');
        const discountHint = serviceCard.querySelector('.discount-hint');
        
        discountTypeSelect.addEventListener('change', function() {
            if (this.value === 'none') {
                discountInput.disabled = true;
                discountInput.value = '';
                discountHint.textContent = 'No discount applied';
            } else {
                discountInput.disabled = false;
                if (this.value === 'percentage') {
                    discountInput.max = '100';
                    discountInput.placeholder = 'e.g. 10';
                    discountHint.textContent = 'Enter percentage (0-100)';
                } else if (this.value === 'fixed') {
                    discountInput.removeAttribute('max');
                    discountInput.placeholder = 'e.g. 100';
                    discountHint.textContent = 'Enter amount in KSh';
                }
            }
            updateServiceSummary(serviceCard);
        });

        // Notes toggle
        const notesToggle = serviceCard.querySelector('.notes-toggle');
        const notesSection = serviceCard.querySelector('.notes-section');
        
        notesToggle.addEventListener('change', function() {
            notesSection.style.display = this.checked ? 'block' : 'none';
        });

        // Update summary on any input change
        const inputs = serviceCard.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', () => updateServiceSummary(serviceCard));
            input.addEventListener('input', () => updateServiceSummary(serviceCard));
        });

        // Initial summary update
        updateServiceSummary(serviceCard);
    }

    function getServiceData(serviceCard) {
        const data = {};
        const inputs = serviceCard.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            const name = input.name;
            if (name) {
                const fieldName = name.match(/\[(\w+)\]$/)?.[1];
                if (fieldName && fieldName !== 'service_date') { // Don't duplicate date
                    data[fieldName] = input.value;
                }
            }
        });
        
        return data;
    }

    function fillServiceWithData(serviceCard, data) {
        Object.keys(data).forEach(key => {
            const input = serviceCard.querySelector(`[name*="[${key}]"]`);
            if (input && key !== 'service_date') { // Don't override date
                input.value = data[key];
                
                // Trigger change event for dependent fields
                if (key === 'service_id') {
                    input.dispatchEvent(new Event('change'));
                }
            }
        });
        
        updateServiceSummary(serviceCard);
    }

    function updateServiceSummary(serviceCard) {
        const serviceSelect = serviceCard.querySelector('.service-select');
        const amountInput = serviceCard.querySelector('.amount-input');
        const discountTypeSelect = serviceCard.querySelector('.discount-type-select');
        const discountInput = serviceCard.querySelector('.discount-input');
        
        const summaryText = serviceCard.querySelector('.summary-text');
        const totalAmount = serviceCard.querySelector('.total-amount');
        const validationStatus = serviceCard.querySelector('.validation-status');

        // Calculate amounts
        const baseAmount = parseFloat(amountInput.value) || 0;
        const discountType = discountTypeSelect.value;
        const discountValue = parseFloat(discountInput.value) || 0;
        
        let discountAmount = 0;
        let discountDisplay = '';
        
        if (discountType === 'percentage' && discountValue > 0) {
            discountAmount = (baseAmount * discountValue) / 100;
            discountDisplay = `${discountValue}% off`;
        } else if (discountType === 'fixed' && discountValue > 0) {
            discountAmount = Math.min(discountValue, baseAmount); // Don't exceed base amount
            discountDisplay = `KSh ${discountValue} off`;
        }
        
        const finalAmount = Math.max(0, baseAmount - discountAmount);

        // Update summary text
        const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
        if (selectedService.value) {
            const serviceName = selectedService.text.split(' - ')[0];
            summaryText.textContent = discountAmount > 0 ? 
                `${serviceName} (${discountDisplay})` : 
                serviceName;
        } else {
            summaryText.textContent = 'Select service to see details';
        }

        // Update total amount
        totalAmount.textContent = `KSh ${finalAmount.toFixed(2)}`;
        totalAmount.className = finalAmount > 0 ? 'total-amount text-success' : 'total-amount text-muted';

        // Update validation status
        const isValid = validateService(serviceCard);
        if (isValid) {
            validationStatus.textContent = 'Ready';
            validationStatus.className = 'validation-status badge bg-success';
            serviceCard.classList.remove('has-errors');
            serviceCard.classList.add('is-valid');
        } else {
            validationStatus.textContent = 'Incomplete';
            validationStatus.className = 'validation-status badge bg-warning';
            serviceCard.classList.remove('is-valid');
        }

        updateSubmitButton();
    }

    function validateService(serviceCard) {
        const requiredFields = serviceCard.querySelectorAll('input[required], select[required]');
        return Array.from(requiredFields).every(field => field.value.trim() !== '');
    }

    function validateAllServices() {
        const services = document.querySelectorAll('.service-card');
        let validCount = 0;
        let invalidServices = [];

        services.forEach((service, index) => {
            if (validateService(service)) {
                validCount++;
            } else {
                invalidServices.push(index + 1);
            }
        });

        // Show validation summary
        if (invalidServices.length === 0 && validCount > 0) {
            showAlert('success', `All ${validCount} services are valid and ready to submit!`);
        } else if (invalidServices.length > 0) {
            showAlert('warning', `${validCount} services are valid. Please complete services: ${invalidServices.join(', ')}`);
        } else {
            showAlert('info', 'Please add at least one service to validate.');
        }
    }

    function updateSubmitButton() {
        const services = document.querySelectorAll('.service-card');
        const validServices = Array.from(services).filter(service => validateService(service));
        
        submitBulkBtn.disabled = validServices.length === 0 || !selectedDate;
    }

    function updateUI() {
        const serviceCount = document.querySelectorAll('.service-card').length;
        
        // Update counter
        serviceCounter.textContent = `${serviceCount} ${serviceCount === 1 ? 'Service' : 'Services'}`;
        
        // Update service numbers
        document.querySelectorAll('.service-number').forEach((badge, index) => {
            badge.textContent = index + 1;
        });
        
        document.querySelectorAll('.service-title').forEach((title, index) => {
            title.textContent = `Service ${index + 1}`;
        });
        
        // Show/hide no services message
        if (serviceCount === 0 && selectedDate) {
            noServicesMessage.style.display = 'block';
        } else {
            noServicesMessage.style.display = 'none';
        }

        updateSubmitButton();
    }

    function showAlert(type, message) {
        // Remove existing alerts
        document.querySelectorAll('.dynamic-alert').forEach(alert => alert.remove());
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show dynamic-alert`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert after the sub-navigation
        const subNav = document.querySelector('.sub-navigation');
        subNav.parentNode.insertBefore(alertDiv, subNav.nextSibling);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Form submission
    document.getElementById('bulkBookingForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-bulk-btn');
        const submitText = submitBtn.querySelector('.submit-text');
        const spinner = submitBtn.querySelector('.spinner-border');
        const loadingText = submitBtn.querySelector('.loading-text');
        
        // Show loading state
        submitText.classList.add('d-none');
        spinner.classList.remove('d-none');
        loadingText.classList.remove('d-none');
        submitBtn.disabled = true;
        
        // Disable all form inputs
        const inputs = document.querySelectorAll('input, select, textarea, button');
        inputs.forEach(input => {
            if (input !== submitBtn) {
                input.disabled = true;
            }
        });
    });

    // Handle bundled services functionality
    function handleBundledServices(serviceCard, selectedOption) {
        const isBundleTrigger = selectedOption.dataset.bundleTrigger === 'true';
        
        if (!isBundleTrigger) {
            return; // Not a bundle trigger, nothing to do
        }
        
        let bundledServices = [];
        try {
            bundledServices = selectedOption.dataset.bundledServices ? JSON.parse(selectedOption.dataset.bundledServices) : [];
        } catch (e) {
            console.error('Error parsing bundled services:', e);
            bundledServices = [];
        }
        
        if (bundledServices.length === 0) {
            return; // No bundled services to handle
        }
        
        // Show notification about bundled services
        showBundledServicesNotification(selectedOption.dataset.name, bundledServices.length);
        
        // Note: In bulk creation, we don't automatically add bundled services as separate entries
        // because each service entry is independent. Instead, we show a notification
        // to inform the user that this service includes bundled services.
    }
    
    function showBundledServicesNotification(serviceName, bundledCount) {
        // Create and show a temporary notification
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        notification.innerHTML = `
            <small><strong>Bundle Service:</strong> ${serviceName} includes ${bundledCount} bundled service${bundledCount > 1 ? 's' : ''}. Consider adding them separately if needed.</small>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
</script>
@endsection
