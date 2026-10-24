@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => '📒 Trial Balance', 'blurb' => 'All accounts and their movements — for your accountant.'])

    <div class="card border">
        <div class="card-body table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Code</th><th>Account</th><th class="text-end">Debits</th><th class="text-end">Credits</th><th class="text-end">Balance</th><th></th></tr></thead>
                <tbody>
                    @foreach($report['rows'] as $row)
                    @if($row['debit'] || $row['credit'])
                    <tr>
                        <td>{{ $row['account']->code }}</td>
                        <td>{{ $row['account']->name }}</td>
                        <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                        <td class="text-end"><a href="{{ route('accounting.statement', ['account' => $row['account']->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="2">Totals</td>
                        <td class="text-end">{{ number_format($report['total_debits'], 2) }}</td>
                        <td class="text-end">{{ number_format($report['total_credits'], 2) }}</td>
                        <td colspan="2" class="text-end {{ abs($report['total_debits'] - $report['total_credits']) < 0.01 ? 'text-success' : 'text-danger' }}">
                            {{ abs($report['total_debits'] - $report['total_credits']) < 0.01 ? '✓ Balanced' : 'Not balanced' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
