<div class="salary-details">
    <div class="row">
        <div class="col-12">
            <h6 class="fw-bold mb-3" style="color: var(--text-primary);">{{ $staff->name }} - {{ \Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }}</h6>
        </div>
    </div>

    <!-- Salary Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                <div class="card-body text-center">
                    <h6 style="color: var(--text-primary);">Base Salary</h6>
                    <h4 class="mb-0" style="color: var(--text-primary);">KSh {{ number_format($baseSalary, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                <div class="card-body text-center">
                    <h6 style="color: var(--text-primary);">Commissions</h6>
                    <h4 class="mb-0 text-success">+ KSh {{ number_format($totalCommissions, 2) }}</h4>
                    <small style="color: var(--text-muted);">{{ $commissionDetails->count() }} services</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                <div class="card-body text-center">
                    <h6 style="color: var(--text-primary);">Advances</h6>
                    <h4 class="mb-0 text-warning">- KSh {{ number_format($totalAdvances, 2) }}</h4>
                    <small style="color: var(--text-muted);">{{ $advanceDetails->count() }} advances</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: var(--primary-color); color: var(--white);">
                <div class="card-body text-center">
                    <h6>Net Salary</h6>
                    <h4 class="mb-0">KSh {{ number_format($netSalary, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Details -->
    @if($commissionDetails->count() > 0)
        <div class="card mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0" style="color: var(--text-primary);">Commission Details</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead style="background-color: var(--bg-tertiary);">
                            <tr>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Date</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Service</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Customer</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commissionDetails as $item)
                                <tr style="color: var(--text-primary);">
                                    <td>{{ $item->created_at->format('M d, Y') }}</td>
                                    <td>{{ $item->service->name ?? 'Unknown Service' }}</td>
                                    <td>{{ $item->serviceBooking->customer->name ?? 'Walk-in Customer' }}</td>
                                    <td class="text-success">KSh {{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Advance Details -->
    @if($advanceDetails->count() > 0)
        <div class="card mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0" style="color: var(--text-primary);">Salary Advance Details</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead style="background-color: var(--bg-tertiary);">
                            <tr>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Date</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Amount</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Reason</th>
                                <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advanceDetails as $advance)
                                <tr style="color: var(--text-primary);">
                                    <td>{{ $advance->advance_date->format('M d, Y') }}</td>
                                    <td class="text-warning">KSh {{ number_format($advance->amount, 2) }}</td>
                                    <td>{{ Str::limit($advance->reason, 50) }}</td>
                                    <td>
                                        <span class="badge bg-success">Paid</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- No Data Message -->
    @if($commissionDetails->count() == 0 && $advanceDetails->count() == 0)
        <div class="text-center py-4">
            <i class="fas fa-info-circle fa-2x" style="color: var(--text-muted);" class="mb-3"></i>
            <p style="color: var(--text-muted);">No commission or advance data found for this month.</p>
        </div>
    @endif
</div>

<style>
.table th {
    background-color: var(--bg-tertiary);
    border-bottom: 2px solid var(--border-color);
    color: var(--text-secondary);
}

.table td {
    color: var(--text-primary);
    border-color: var(--border-color);
}

.card {
    box-shadow: 0 2px 4px var(--shadow-color);
}
</style> 