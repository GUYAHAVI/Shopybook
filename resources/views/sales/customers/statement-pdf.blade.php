@extends('reports.pdf')

@section('report-content')
<table class="sum-table" style="margin-bottom: 10px;">
    <tr>
        <td><div class="sum-label">Customer</div><div class="sum-val">{{ $customer->name }}</div>
            <div class="sum-label" style="text-transform:none;">{{ collect([$customer->phone, $customer->email])->filter()->implode(' · ') }}</div></td>
        <td><div class="sum-label">Invoiced</div><div class="sum-val">KSh {{ number_format($summary['invoiced'], 2) }}</div></td>
        <td><div class="sum-label">Paid</div><div class="sum-val ok">KSh {{ number_format($summary['paid'], 2) }}</div></td>
        <td><div class="sum-label">Balance</div><div class="sum-val {{ $summary['balance'] > 0 ? 'warn' : 'ok' }}">KSh {{ number_format($summary['balance'], 2) }}</div></td>
        @if($summary['overdue'] > 0)
        <td><div class="sum-label">Overdue</div><div class="sum-val bad">KSh {{ number_format($summary['overdue'], 2) }}</div></td>
        @endif
    </tr>
</table>

<table class="data">
    <thead><tr>
        <th>Date</th><th>Reference</th><th>Type</th>
        <th class="num">Debit (KSh)</th><th class="num">Credit (KSh)</th><th class="num">Balance (KSh)</th>
    </tr></thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
            <td>{{ $row['ref'] }}</td>
            <td>{{ $row['type'] }}</td>
            <td class="num">{{ $row['debit'] ? number_format($row['debit'], 2) : '—' }}</td>
            <td class="num ok">{{ $row['credit'] ? number_format($row['credit'], 2) : '—' }}</td>
            <td class="num"><strong>{{ number_format($row['balance'], 2) }}</strong></td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr class="totals">
            <td colspan="5" class="num">Closing balance</td>
            <td class="num">KSh {{ number_format($summary['balance'], 2) }}</td>
        </tr>
    </tfoot>
</table>
@endsection
