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
            <a href="{{ route('salary-advances.index') }}" class="nav-tab active">
                <i class="fas fa-money-bill-wave me-1"></i> Salary Advances
            </a>
            <a href="{{ route('staff.salary-calculations') }}" class="nav-tab">
                <i class="fas fa-calculator me-1"></i> Salary Calculations
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--text-primary);">Salary Advances</h2>
                <div>
                    <a href="{{ route('salary-advances.staff-summary') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-chart-line me-1"></i> Staff Summary
                    </a>
                    <a href="{{ route('salary-advances.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Advance Request
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">All Salary Advances</h5>
                </div>
                <div class="card-body">
                    @if($salaryAdvances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background-color: var(--bg-tertiary);">
                                    <tr>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Staff Member</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Amount</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Date</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Status</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Deduction Status</th>
                                        <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Remaining Balance</th>
                                        <th class="text-center" style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salaryAdvances as $advance)
                                    <tr>
                                        <td style="color: var(--text-primary);">
                                            <div>
                                                <strong>{{ $advance->staff->name }}</strong>
                                                <br><small style="color: var(--text-muted);">{{ $advance->staff->role }}</small>
                                            </div>
                                        </td>
                                        <td style="color: var(--text-primary);">
                                            <strong>{{ $advance->formatted_amount }}</strong>
                                        </td>
                                        <td style="color: var(--text-primary);">
                                            {{ $advance->advance_date->format('M d, Y') }}
                                        </td>
                                        <td>
                                            {!! $advance->status_badge !!}
                                        </td>
                                        <td>
                                            @if($advance->status === 'paid')
                                                {!! $advance->deduction_status_badge !!}
                                            @else
                                                <span style="color: var(--text-muted);">-</span>
                                            @endif
                                        </td>
                                        <td style="color: var(--text-primary);">
                                            @if($advance->status === 'paid')
                                                {{ $advance->formatted_remaining_balance }}
                                            @else
                                                <span style="color: var(--text-muted);">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('salary-advances.show', $advance) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($advance->status === 'pending')
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $advance->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $salaryAdvances->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x" style="color: var(--text-muted);" class="mb-3"></i>
                            <h5 style="color: var(--text-primary);">No Salary Advances Found</h5>
                            <p style="color: var(--text-muted);">Create your first salary advance request to get started.</p>
                            <a href="{{ route('salary-advances.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create Advance Request
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Verification Modals -->
@foreach($salaryAdvances as $advance)
    @if($advance->status === 'pending')
        <div class="modal fade" id="approveModal{{ $advance->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $advance->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel{{ $advance->id }}">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            Approve Salary Advance
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('salary-advances.approve', $advance) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Security Verification Required</strong><br>
                                Please enter your login password to approve this salary advance for <strong>{{ $advance->staff->name }}</strong>.
                            </div>
                            
                            <div class="mb-3">
                                <label for="password{{ $advance->id }}" class="form-label">Your Password</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password{{ $advance->id }}" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Enter your login password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Amount:</strong> {{ $advance->formatted_amount }}<br>
                                <strong>Date:</strong> {{ $advance->advance_date->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Approve Advance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
// Clear password fields when modals are closed
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const passwordInputs = this.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                input.value = '';
            });
        });
    });
});
</script>

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
@endsection