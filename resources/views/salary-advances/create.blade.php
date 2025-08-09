@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Staff -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('staff.index') }}" class="nav-tab">
                <i class="fas fa-users me-1"></i> All Staff
            </a>
            <a href="{{ route('staff.create') }}" class="nav-tab">
                <i class="fas fa-plus me-1"></i> Add Staff
            </a>
            <a href="{{ route('salary-advances.index') }}" class="nav-tab">
                <i class="fas fa-money-bill-wave me-1"></i> Salary Advances
            </a>
            <a href="{{ route('salary-advances.create') }}" class="nav-tab active">
                <i class="fas fa-plus-circle me-1"></i> New Advance
            </a>
            <a href="{{ route('staff.salary-calculations') }}" class="nav-tab">
                <i class="fas fa-calculator me-1"></i> Salary Calculations
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <h2 class="fw-bold mb-4" style="color: var(--text-primary);">Request Salary Advance</h2>
                
                <div class="alert alert-info mb-4" style="background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> Salary advances will be deducted from the staff member's monthly salary. 
                    The advance amount cannot exceed their remaining available salary after existing advance deductions.
                </div>

                <form method="POST" action="{{ route('salary-advances.store') }}" id="advanceForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="staff_id" class="form-label" style="color: var(--text-primary);">Staff Member *</label>
                        <select class="form-control" id="staff_id" name="staff_id" required
                                style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                            <option value="">Select Staff Member</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" 
                                    data-salary="{{ $member->salary }}"
                                    data-pending-deductions="{{ $member->pending_advance_deductions }}"
                                    data-available="{{ $member->available_advance_amount }}"
                                    @if(old('staff_id') == $member->id) selected @endif>
                                    {{ $member->name }} ({{ $member->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <!-- Staff Salary Information Card -->
                    <div class="card mb-3" id="salary-info" style="display: none; background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2" style="color: var(--text-primary);"><i class="fas fa-info-circle me-2"></i>Salary Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small style="color: var(--text-muted);">Monthly Salary</small>
                                    <div class="fw-bold" id="monthly-salary" style="color: var(--text-primary);">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small style="color: var(--text-muted);">Pending Deductions</small>
                                    <div class="fw-bold text-warning" id="pending-deductions">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small style="color: var(--text-muted);">Available for Advance</small>
                                    <div class="fw-bold text-success" id="available-amount">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label" style="color: var(--text-primary);">Advance Amount (KSh) *</label>
                        <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" 
                               value="{{ old('amount') }}" required placeholder="Enter advance amount"
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                        <div class="form-text" id="amount-validation" style="display: none; color: var(--text-muted);"></div>
                    </div>

                    <div class="mb-3">
                        <label for="advance_date" class="form-label" style="color: var(--text-primary);">Advance Date *</label>
                        <input type="date" class="form-control" id="advance_date" name="advance_date" 
                               value="{{ old('advance_date', now()->format('Y-m-d')) }}" 
                               max="{{ now()->format('Y-m-d') }}" required
                               style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        @error('advance_date')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label" style="color: var(--text-primary);">Reason for Advance *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required 
                                  placeholder="Please provide the reason for this salary advance request"
                                  style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">{{ old('reason') }}</textarea>
                        @error('reason')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label" style="color: var(--text-primary);">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Any additional notes or comments (optional)"
                                  style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">{{ old('notes') }}</textarea>
                        @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('salary-advances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Advances
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="fas fa-paper-plane me-1"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const staffSelect = document.getElementById('staff_id');
    const salaryInfo = document.getElementById('salary-info');
    const monthlySalary = document.getElementById('monthly-salary');
    const pendingDeductions = document.getElementById('pending-deductions');
    const availableAmount = document.getElementById('available-amount');
    const amountInput = document.getElementById('amount');
    const amountValidation = document.getElementById('amount-validation');
    const submitBtn = document.getElementById('submit-btn');

    let maxAvailable = 0;

    staffSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const salary = parseFloat(selectedOption.dataset.salary || 0);
            const pending = parseFloat(selectedOption.dataset.pendingDeductions || 0);
            const available = parseFloat(selectedOption.dataset.available || 0);
            
            maxAvailable = available;
            
            monthlySalary.textContent = 'KSh ' + salary.toLocaleString();
            pendingDeductions.textContent = 'KSh ' + pending.toLocaleString();
            availableAmount.textContent = 'KSh ' + available.toLocaleString();
            
            salaryInfo.style.display = 'block';
            amountInput.max = available;
            
            validateAmount();
        } else {
            salaryInfo.style.display = 'none';
            maxAvailable = 0;
            amountInput.max = '';
            amountValidation.style.display = 'none';
        }
    });

    amountInput.addEventListener('input', validateAmount);

    function validateAmount() {
        const amount = parseFloat(amountInput.value || 0);
        
        if (amount > maxAvailable && maxAvailable > 0) {
            amountValidation.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i>Amount exceeds available limit of KSh ' + maxAvailable.toLocaleString();
            amountValidation.className = 'form-text text-danger';
            amountValidation.style.display = 'block';
            submitBtn.disabled = true;
        } else if (amount > 0 && maxAvailable > 0) {
            amountValidation.innerHTML = '<i class="fas fa-check text-success me-1"></i>Amount is within available limit';
            amountValidation.className = 'form-text text-success';
            amountValidation.style.display = 'block';
            submitBtn.disabled = false;
        } else {
            amountValidation.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    // Trigger change event if there's an old value
    if (staffSelect.value) {
        staffSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection

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