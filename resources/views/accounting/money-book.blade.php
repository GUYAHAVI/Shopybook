@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => '💵 Money Accounts', 'blurb' => 'Cash, M-Pesa and bank — what came in and went out this period.'])

    <div class="card border">
        <div class="card-body table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Account</th><th class="text-end">Opening</th><th class="text-end">In</th><th class="text-end">Out</th><th class="text-end">Closing</th><th></th></tr></thead>
                <tbody>
                    @foreach($report['rows'] as $row)
                    <tr>
                        <td>{{ $row['account']->name }}</td>
                        <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                        <td class="text-end text-success">{{ number_format($row['in'], 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($row['out'], 2) }}</td>
                        <td class="text-end fw-bold {{ $row['closing'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['closing'], 2) }}</td>
                        <td class="text-end"><a href="{{ route('accounting.statement', ['account' => $row['account']->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot><tr class="fw-bold"><td>Total</td><td></td><td class="text-end">{{ number_format($report['in_total'], 2) }}</td><td class="text-end">{{ number_format($report['out_total'], 2) }}</td><td class="text-end">{{ number_format($report['in_total'] - $report['out_total'], 2) }}</td><td></td></tr></tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
