@extends('layouts.dash')

@section('title', 'Customer Statement')

@section('content')
<div class="container-fluid" style="max-width:960px;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Statement — {{ $customer->name }}</h4>
            <p class="text-muted mb-0">
                {{ $customer->phone ?? '' }}{{ $customer->phone && $customer->email ? ' · ' : '' }}{{ $customer->email ?? '' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.customers.show', ['type' => 'customer', 'id' => $customer->id]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Customer
            </a>
            <a href="{{ route('sales.customers.statement', ['customer' => $customer->id, 'pdf' => 1]) }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i>PDF
            </a>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card"><div class="card-body py-3">
            <div class="text-muted small">Total Invoiced</div>
            <div class="h5 mb-0">KSh {{ number_format($summary['invoiced'], 2) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body py-3">
            <div class="text-muted small">Total Paid</div>
            <div class="h5 mb-0 text-success">KSh {{ number_format($summary['paid'], 2) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card {{ $summary['balance'] > 0 ? 'border-warning' : '' }}"><div class="card-body py-3">
            <div class="text-muted small">Balance Due</div>
            <div class="h5 mb-0 {{ $summary['balance'] > 0 ? 'text-warning' : 'text-success' }}">KSh {{ number_format($summary['balance'], 2) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card {{ $summary['overdue'] > 0 ? 'border-danger' : '' }}"><div class="card-body py-3">
            <div class="text-muted small">Overdue</div>
            <div class="h5 mb-0 {{ $summary['overdue'] > 0 ? 'text-danger' : '' }}">KSh {{ number_format($summary['overdue'], 2) }}</div>
        </div></div></div>
    </div>

    <div class="card"><div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Date</th><th>Reference</th><th>Type</th>
                <th class="text-end">Debit (KSh)</th><th class="text-end">Credit (KSh)</th><th class="text-end">Balance (KSh)</th>
            </tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                    <td>
                        @if($row['link'])
                            <a href="{{ $row['link'] }}">{{ $row['ref'] }}</a>
                        @else
                            {{ $row['ref'] }}
                        @endif
                    </td>
                    <td><span class="badge {{ $row['credit'] > 0 ? 'bg-success' : 'bg-secondary' }}">{{ $row['type'] }}</span></td>
                    <td class="text-end">{{ $row['debit'] ? number_format($row['debit'], 2) : '—' }}</td>
                    <td class="text-end text-success">{{ $row['credit'] ? number_format($row['credit'], 2) : '—' }}</td>
                    <td class="text-end fw-semibold">{{ number_format($row['balance'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No transactions yet for this customer.</td></tr>
            @endforelse
            </tbody>
            @if($rows->isNotEmpty())
            <tfoot>
                <tr class="table-light">
                    <td colspan="5" class="text-end fw-bold">Closing balance</td>
                    <td class="text-end fw-bold">KSh {{ number_format($summary['balance'], 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div></div>
</div>
@endsection
