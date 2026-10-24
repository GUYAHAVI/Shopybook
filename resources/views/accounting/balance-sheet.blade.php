@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => '⚖️ Balance Sheet', 'blurb' => 'What the business owns and owes, as of this date.', 'asOf' => $asOf])

    <div class="card border">
        <div class="card-body">
            @php $row = fn($a) => '<div class="d-flex justify-content-between ps-3"><span>' . $a['account']->name . '</span><span>KSh ' . number_format($a['balance'], 2) . '</span></div>'; @endphp

            <h6 class="text-primary">WHAT YOU OWN</h6>
            @foreach($report['assets'] as $a) {!! $row($a) !!} @endforeach
            <hr><div class="d-flex justify-content-between fw-bold"><span>Total Owned</span><span>KSh {{ number_format($report['assets_total'], 2) }}</span></div>

            <h6 class="text-primary mt-4">WHAT YOU OWE</h6>
            @foreach($report['liabilities'] as $a) {!! $row($a) !!} @endforeach
            <hr><div class="d-flex justify-content-between fw-bold"><span>Total Owed</span><span>KSh {{ number_format($report['liabilities_total'], 2) }}</span></div>

            <h6 class="text-primary mt-4">YOUR STAKE</h6>
            @foreach($report['equity'] as $a) {!! $row($a) !!} @endforeach
            <div class="d-flex justify-content-between ps-3"><span>Profit to date</span><span>KSh {{ number_format($report['net_profit'], 2) }}</span></div>
            <hr><div class="d-flex justify-content-between fw-bold"><span>Total Stake</span><span>KSh {{ number_format($report['equity_total'], 2) }}</span></div>

            <hr>
            <div class="d-flex justify-content-between fs-5 fw-bold">
                <span>Owned = Owed + Stake</span>
                <span class="{{ abs($report['check']) < 0.01 ? 'text-success' : 'text-danger' }}">
                    {{ abs($report['check']) < 0.01 ? '✓ Balanced' : 'Off by ' . number_format($report['check'], 2) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
