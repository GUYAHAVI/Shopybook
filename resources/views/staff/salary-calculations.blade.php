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
            <a href="{{ route('staff.salary-calculations') }}" class="nav-tab active">
                <i class="fas fa-calculator me-1"></i> Salary Calculations
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--text-primary);">Staff Salary Calculations</h2>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-success" onclick="exportSalaryData()">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <a href="{{ route('commission-reports') }}" class="btn btn-outline-info">
                <i class="fas fa-chart-bar me-1"></i> Commission Reports
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.salary-calculations') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="month" class="form-label" style="color: var(--text-primary);">Month</label>
                    <input type="month" class="form-control" id="month" name="month" value="{{ $selectedMonth }}"
                           style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                </div>
                <div class="col-md-4">
                    <label for="staff_filter" class="form-label" style="color: var(--text-primary);">Staff Member</label>
                    <select class="form-control" id="staff_filter" name="staff_id"
                            style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                        <option value="">All Staff</option>
                        @foreach($allStaff as $staff)
                            <option value="{{ $staff->id }}" @if($selectedStaffId == $staff->id) selected @endif>
                                {{ $staff->name }} ({{ $staff->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card" style="background: var(--primary-color); color: var(--white);">
                <div class="card-body">
                    <h5 class="card-title">Total Net Salaries</h5>
                    <h3 class="mb-0">KSh {{ number_format($totalNetSalaries, 2) }}</h3>
                    <small>After advances & commissions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: var(--info-color); color: var(--white);">
                <div class="card-body">
                    <h5 class="card-title">Total Commissions</h5>
                    <h3 class="mb-0">KSh {{ number_format($totalCommissions, 2) }}</h3>
                    <small>Earned this month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: var(--warning-color); color: var(--white);">
                <div class="card-body">
                    <h5 class="card-title">Total Advances</h5>
                    <h3 class="mb-0">KSh {{ number_format($totalAdvances, 2) }}</h3>
                    <small>Deducted this month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: var(--success-color); color: var(--white);">
                <div class="card-body">
                    <h5 class="card-title">Active Staff</h5>
                    <h3 class="mb-0">{{ $salaryCalculations->count() }}</h3>
                    <small>With salary calculations</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Salary Calculations -->
    <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <h5 class="mb-0" style="color: var(--text-primary);">Monthly Salary Breakdown</h5>
        </div>
        <div class="card-body">
            @if($salaryCalculations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: var(--bg-tertiary);">
                            <tr>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Staff Member</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Base Salary</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Commissions</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Advances</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Net Salary</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaryCalculations as $calculation)
                                <tr style="color: var(--text-primary);">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-initial rounded-circle" style="background-color: var(--primary-color); color: var(--white);">
                                                    {{ substr($calculation->staff->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0" style="color: var(--text-primary);">{{ $calculation->staff->name }}</h6>
                                                <small style="color: var(--text-muted);">{{ $calculation->staff->role }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color: var(--text-primary);">
                                        <strong>KSh {{ number_format($calculation->base_salary, 2) }}</strong>
                                    </td>
                                    <td style="color: var(--text-primary);">
                                        <span class="text-success">+ KSh {{ number_format($calculation->commissions, 2) }}</span>
                                        <br><small style="color: var(--text-muted);">{{ $calculation->services_count }} services</small>
                                    </td>
                                    <td style="color: var(--text-primary);">
                                        <span class="text-warning">- KSh {{ number_format($calculation->advances, 2) }}</span>
                                        <br><small style="color: var(--text-muted);">{{ $calculation->advances_count }} advances</small>
                                    </td>
                                    <td style="color: var(--text-primary);">
                                        <strong class="text-primary">KSh {{ number_format($calculation->net_salary, 2) }}</strong>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="viewSalaryDetails({{ $calculation->staff->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('staff.salary-details', ['staff' => $calculation->staff->id, 'month' => $selectedMonth]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calculator fa-3x" style="color: var(--text-muted);" class="mb-3"></i>
                    <h5 style="color: var(--text-primary);">No Salary Calculations Found</h5>
                    <p style="color: var(--text-muted);">No staff members have salary calculations for the selected period.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Salary Details Modal -->
<div class="modal fade" id="salaryDetailsModal" tabindex="-1" aria-labelledby="salaryDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="salaryDetailsModalLabel" style="color: var(--text-primary);">Salary Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="salaryDetailsContent">
                <!-- Salary details will be loaded here -->
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

.avatar-sm {
    width: 40px;
    height: 40px;
}

.avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.modal-content {
    box-shadow: 0 10px 30px var(--shadow-color);
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
function viewSalaryDetails(staffId) {
    // Load salary details via AJAX
    fetch(`/staff/${staffId}/salary-details?month={{ $selectedMonth }}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('salaryDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('salaryDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading salary details:', error);
        });
}

function exportSalaryData() {
    const month = document.getElementById('month').value;
    const staffId = document.getElementById('staff_filter').value;
    
    let url = '{{ route("staff.salary-calculations") }}?export=1&month=' + month;
    if (staffId) {
        url += '&staff_id=' + staffId;
    }
    
    window.open(url, '_blank');
}
</script>
@endsection 