@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => '🧾 VAT Summary', 'blurb' => 'VAT you collected minus VAT you paid.'])

    <div class="card border">
        <div class="card-body">
            <div class="d-flex justify-content-between"><span>VAT on your sales (output)</span><span>KSh {{ number_format($report['output_vat'], 2) }}</span></div>
            <div class="d-flex justify-content-between"><span>VAT on your purchases (input)</span><span>KSh {{ number_format($report['input_vat'], 2) }}</span></div>
            <hr>
            <div class="d-flex justify-content-between fs-5 fw-bold">
                <span>{{ $report['payable'] >= 0 ? 'VAT to pay KRA' : 'VAT refund due' }}</span>
                <span>{{ number_format(abs($report['payable']), 2) }}</span>
            </div>
            <p class="text-muted text-sm mt-3 mb-0">Note: purchases currently don't capture input VAT on stock receipts — that arrives with the full VAT module. Sales VAT is always captured.</p>
        </div>
    </div>
</div>
@endsection
