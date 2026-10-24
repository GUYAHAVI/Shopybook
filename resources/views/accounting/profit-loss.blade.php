@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => '💰 Profit & Loss', 'blurb' => 'Income minus costs for the period.'])

    <div class="card border">
        <div class="card-body">
            @php $row = fn($label, $amount, $bold = false, $id = null) =>
                '<div class="d-flex justify-content-between ' . ($bold ? 'fw-bold' : 'ps-3') . '">' .
                '<span>' . $label . '</span><span>KSh ' . number_format($amount, 2) . '</span></div>'; @endphp

            <h6 class="text-primary">REVENUE</h6>
            @foreach($report['revenue'] as $r)
                {!! $row($r['account']->name, $r['amount']) !!}
            @endforeach
            @foreach($report['returns'] as $r)
                {!! $row('Less: ' . $r['account']->name, -$r['amount']) !!}
            @endforeach
            <hr>{!! $row('Net Sales', $report['net_sales'], true) !!}

            <h6 class="text-primary mt-4">COST OF GOODS SOLD</h6>
            @foreach($report['cogs'] as $r)
                {!! $row($r['account']->name, -$r['amount']) !!}
            @endforeach
            <hr>{!! $row('Gross Profit', $report['gross_profit'], true) !!}

            <h6 class="text-primary mt-4">EXPENSES</h6>
            @foreach($report['expenses'] as $r)
                {!! $row($r['account']->name, -$r['amount']) !!}
            @endforeach
            <hr>{!! $row('Total Expenses', $report['expenses_total'], true) !!}

            <hr>
            <div class="d-flex justify-content-between fs-5 fw-bold">
                <span>NET {{ $report['net_profit'] >= 0 ? 'PROFIT' : 'LOSS' }}</span>
                <span class="{{ $report['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">KSh {{ number_format($report['net_profit'], 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
