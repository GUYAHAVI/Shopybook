@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color:#7b2e2e;">Commission Reports</h2>
                <div class="btn-group">
                    <a href="{{ route('service-bookings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Booking
                    </a>
                    <button type="button" class="btn btn-outline-success" onclick="exportCommissionData()">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('commission-reports') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
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
            <div class="row mb-4 summary-cards">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Earnings</h5>
                            <h3 class="mb-0">KSh {{ number_format($totalEarnings ?? 0, 2) }}</h3>
                            <small>Salary + Commission</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Commissions Only</h5>
                            <h3 class="mb-0">KSh {{ number_format($totalCommissions, 2) }}</h3>
                            <small>{{ \Carbon\Carbon::parse($startDate)->format('M j') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Staff</h5>
                            <h3 class="mb-0">{{ $staffCommissions->where('service_count', '>', 0)->count() }}</h3>
                            <small>Staff with services in period</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Services</h5>
                            <h3 class="mb-0">{{ $staffCommissions->sum('service_count') }}</h3>
                            <small>Services completed in period</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Commission Details -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Staff Commission Summary</h5>
                </div>
                <div class="card-body">
                    @if($staffCommissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Staff Member</th>
                                        <th class="d-none d-md-table-cell">Role</th>
                                        <th class="text-center d-none d-lg-table-cell">Salary</th>
                                        <th class="text-center">Services</th>
                                        <th class="text-center d-none d-lg-table-cell">Commission</th>
                                        <th class="text-center">Total Earnings</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffCommissions as $index => $staffData)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ strtoupper(substr($staffData['staff']->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $staffData['staff']->name }}</strong>
                                                        <div class="d-md-none">
                                                            <small class="text-muted">{{ $staffData['staff']->role }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="badge bg-secondary">{{ $staffData['staff']->role }}</span>
                                            </td>
                                            <td class="text-center d-none d-lg-table-cell">
                                                <strong class="text-info">KSh {{ number_format($staffData['salary'] ?? 0, 2) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $staffData['service_count'] }}</span>
                                            </td>
                                            <td class="text-center d-none d-lg-table-cell">
                                                <strong class="text-primary">KSh {{ number_format($staffData['total_commission'], 2) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <strong class="text-success">KSh {{ number_format($staffData['total_earnings'] ?? 0, 2) }}</strong>
                                                <div class="d-lg-none">
                                                    <small class="text-muted d-block">Salary: KSh {{ number_format($staffData['salary'] ?? 0, 2) }}</small>
                                                    <small class="text-muted">Commission: KSh {{ number_format($staffData['total_commission'], 2) }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($staffData['service_count'] > 0)
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#staff-details-{{ $index }}" 
                                                            aria-expanded="false">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        
                                        @if($staffData['service_count'] > 0)
                                            <tr>
                                                <td colspan="7" class="p-0">
                                                    <div class="collapse" id="staff-details-{{ $index }}">
                                                        <div class="card m-3 border-0 bg-light">
                                                            <div class="card-header bg-primary text-white">
                                                                <h6 class="mb-0">
                                                                    <i class="fas fa-user me-2"></i>{{ $staffData['staff']->name }} - Detailed Commission Breakdown
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-striped">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Date</th>
                                                                                <th>Service</th>
                                                                                <th>Customer</th>
                                                                                <th>Service Amount</th>
                                                                                <th>Commission Rate</th>
                                                                                <th>Commission Earned</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($staffData['service_items'] as $item)
                                                                                <tr>
                                                                                    <td>{{ $item->serviceBooking->created_at->format('M j, Y H:i') }}</td>
                                                                                    <td>
                                                                                        <span class="fw-bold">{{ $item->service->name }}</span>
                                                                                        @if($item->notes === 'Complimentary service')
                                                                                            <span class="badge bg-success ms-1">Complimentary</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        <i class="fas fa-user me-1"></i>
                                                                                        {{ $item->serviceBooking->customer ? $item->serviceBooking->customer->name : 'Walk-in' }}
                                                                                    </td>
                                                                                    <td>
                                                                                        <span class="text-muted">KSh</span> {{ number_format($item->amount, 2) }}
                                                                                    </td>
                                                                                    <td>
                                                                                        <span class="badge bg-warning">{{ $item->commission_rate }}%</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <strong class="text-success">KSh {{ number_format($item->commission_amount, 2) }}</strong>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr class="table-primary">
                                                                                <th colspan="5" class="text-end">Total Commission for {{ $staffData['staff']->name }}:</th>
                                                                                <th>
                                                                                    <strong class="text-primary">KSh {{ number_format($staffData['total_commission'], 2) }}</strong>
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                                
                                                                <!-- Additional Stats -->
                                                                <div class="row mt-3">
                                                                    <div class="col-md-3">
                                                                        <div class="text-center p-2 bg-white rounded">
                                                                            <small class="text-muted d-block">Total Services</small>
                                                                            <strong class="h5 text-info">{{ $staffData['service_count'] }}</strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-center p-2 bg-white rounded">
                                                                            <small class="text-muted d-block">Avg per Service</small>
                                                                            <strong class="h5 text-warning">KSh {{ number_format($staffData['total_commission'] / max($staffData['service_count'], 1), 2) }}</strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-center p-2 bg-white rounded">
                                                                            <small class="text-muted d-block">Complimentary Services</small>
                                                                            <strong class="h5 text-success">{{ $staffData['service_items']->where('notes', 'Complimentary service')->count() }}</strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-center p-2 bg-white rounded">
                                                                            <small class="text-muted d-block">Regular Services</small>
                                                                            <strong class="h5 text-primary">{{ $staffData['service_items']->where('notes', '!=', 'Complimentary service')->count() }}</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th colspan="3" class="text-end d-none d-lg-table-cell">Grand Total Commissions:</th>
                                        <th colspan="1" class="text-end d-lg-none">Total:</th>
                                        <th class="text-center d-none d-lg-table-cell"></th>
                                        <th class="text-center d-none d-lg-table-cell"></th>
                                        <th class="text-center">
                                            <strong class="h5 text-warning">KSh {{ number_format($totalCommissions, 2) }}</strong>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No Staff Found</h4>
                            <p class="text-muted mb-4">Add staff members to track their commissions and performance.</p>
                            <a href="{{ route('staff.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i> Add Your First Staff Member
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.9em;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-primary th {
    background-color: rgba(74, 92, 255, 0.1);
    color: #7b2e2e;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4a5cff, #6c63ff);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    flex-shrink: 0;
}

.table > tbody > tr > td {
    vertical-align: middle;
}

.btn-outline-primary:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}

.collapse .card {
    border: 1px solid #dee2e6;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-striped > tbody > tr:nth-of-type(odd) > td {
    background-color: rgba(74, 92, 255, 0.05);
}

.bg-light .bg-white {
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

/* Mobile responsive improvements */
@media (max-width: 768px) {
    .card-body .table-responsive {
        font-size: 0.9rem;
    }
    
    .avatar-circle {
        width: 35px;
        height: 35px;
        font-size: 12px;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-group .btn {
        width: 100%;
    }
    
    .summary-cards .col-md-4 {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .table td, .table th {
        padding: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animation to view details buttons
    const viewButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    viewButtons.forEach(button => {
        const icon = button.querySelector('i');
        const targetId = button.getAttribute('data-bs-target');
        const targetElement = document.querySelector(targetId);
        
        button.addEventListener('click', function() {
            if (targetElement.classList.contains('show')) {
                icon.className = 'fas fa-eye';
                button.setAttribute('title', 'View Details');
            } else {
                icon.className = 'fas fa-eye-slash';
                button.setAttribute('title', 'Hide Details');
            }
        });
        
        // Set initial title
        button.setAttribute('title', 'View Details');
    });
    
    // Add hover effects to commission amounts
    const commissionCells = document.querySelectorAll('td strong, th strong');
    commissionCells.forEach(cell => {
        if (cell.textContent.includes('KSh')) {
            cell.style.cursor = 'pointer';
            cell.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
                this.style.transition = 'transform 0.2s';
            });
            cell.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        }
    });
    
    // Add loading animation to filter form
    const filterForm = document.querySelector('form[method="GET"]');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            const icon = submitButton.querySelector('i');
            
            submitButton.disabled = true;
            icon.className = 'fas fa-spinner fa-spin me-1';
            submitButton.innerHTML = icon.outerHTML + ' Loading...';
        });
    }
    
    // Initialize tooltips for better UX
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    if (window.bootstrap && window.bootstrap.Tooltip) {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Add quick stats animation on page load
    const statCards = document.querySelectorAll('.bg-white.rounded');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Export commission data function
function exportCommissionData() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    // Collect data from the table
    const exportData = [];
    const rows = document.querySelectorAll('tbody tr:not([id^="staff-details"])');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 6) {
            const staffName = cells[0].querySelector('strong').textContent;
            const role = cells[1].textContent.trim();
            const services = cells[2].textContent.trim();
            const todayCommission = cells[3].textContent.replace('KSh ', '').replace(',', '');
            const monthCommission = cells[4].textContent.replace('KSh ', '').replace(',', '');
            const periodTotal = cells[5].textContent.replace('KSh ', '').replace(',', '');
            
            exportData.push({
                'Staff Name': staffName,
                'Role': role,
                'Services Count': services,
                'Today Commission': todayCommission,
                'Month Commission': monthCommission,
                'Period Total': periodTotal
            });
        }
    });
    
    // Convert to CSV
    if (exportData.length === 0) {
        alert('No data to export');
        return;
    }
    
    const csv = convertToCSV(exportData);
    const filename = `commission_report_${startDate}_to_${endDate}.csv`;
    downloadCSV(csv, filename);
}

function convertToCSV(data) {
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
    ].join('\n');
    
    return csvContent;
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', filename);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    // Show success message
    const exportBtn = document.querySelector('button[onclick="exportCommissionData()"]');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-check me-1"></i> Exported!';
    exportBtn.classList.remove('btn-outline-success');
    exportBtn.classList.add('btn-success');
    
    setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.classList.remove('btn-success');
        exportBtn.classList.add('btn-outline-success');
    }, 2000);
}
</script>
@endsection
