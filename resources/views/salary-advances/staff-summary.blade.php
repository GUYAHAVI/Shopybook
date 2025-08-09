@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color:#020258;">Staff Salary Advance Summary</h2>
                <div>
                    <a href="{{ route('salary-advances.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-list me-1"></i> All Advances
                    </a>
                    <a href="{{ route('salary-advances.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Advance Request
                    </a>
                </div>
            </div>

            @if($staff->count() > 0)
                <div class="row">
                    @foreach($staff as $member)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">{{ $member->name }}</h5>
                                        <small class="text-muted">{{ $member->role }}</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Monthly Salary</small>
                                        <div class="fw-bold">KSh {{ number_format($member->salary, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Salary Status -->
                                <div class="row mb-3">
                                    <div class="col-4 text-center">
                                        <small class="text-muted">Total Advances</small>
                                        <div class="fw-bold text-info">KSh {{ number_format($member->total_advances, 2) }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <small class="text-muted">Pending Deductions</small>
                                        <div class="fw-bold text-warning">KSh {{ number_format($member->pending_advance_deductions, 2) }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <small class="text-muted">Available</small>
                                        <div class="fw-bold text-success">KSh {{ number_format($member->available_advance_amount, 2) }}</div>
                                    </div>
                                </div>

                                <!-- Recent Advances -->
                                @if($member->salaryAdvances->count() > 0)
                                    <h6 class="fw-bold mb-2">Recent Advances</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($member->salaryAdvances->take(3) as $advance)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('salary-advances.show', $advance) }}" class="text-decoration-none">
                                                            {{ $advance->advance_date->format('M d') }}
                                                        </a>
                                                    </td>
                                                    <td>KSh {{ number_format($advance->amount, 0) }}</td>
                                                    <td>
                                                        @if($advance->status === 'pending')
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @elseif($advance->status === 'approved')
                                                            <span class="badge bg-info">Approved</span>
                                                        @elseif($advance->status === 'paid')
                                                            <span class="badge bg-success">Paid</span>
                                                        @else
                                                            <span class="badge bg-danger">{{ ucfirst($advance->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($advance->status === 'paid')
                                                            KSh {{ number_format($advance->remaining_balance, 0) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($member->salaryAdvances->count() > 3)
                                        <div class="text-center">
                                            <small class="text-muted">
                                                +{{ $member->salaryAdvances->count() - 3 }} more advances
                                            </small>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-money-bill-wave fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No advances taken</p>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    @if($member->available_advance_amount > 0)
                                        <a href="{{ route('salary-advances.create') }}?staff={{ $member->id }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus me-1"></i> New Advance
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-exclamation-triangle me-1"></i> No Available Advance
                                        </button>
                                    @endif
                                    
                                    @if($member->salaryAdvances->count() > 0)
                                        <a href="{{ route('salary-advances.index') }}?staff={{ $member->id }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-history me-1"></i> View History
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Staff with Salary Found</h5>
                        <p class="text-muted">Add staff members with salaries to enable salary advance requests.</p>
                        <a href="{{ route('staff.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Add Staff Member
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-select staff if coming from a specific staff member
    const urlParams = new URLSearchParams(window.location.search);
    const staffId = urlParams.get('staff');
    
    if (staffId) {
        // Highlight the specific staff member's card
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            const advanceLink = card.querySelector(`a[href*="staff=${staffId}"]`);
            if (advanceLink) {
                card.classList.add('border-primary');
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
</script>

<style>
.card.border-primary {
    border-color: #0d6efd !important;
    border-width: 2px !important;
}
</style>
@endsection