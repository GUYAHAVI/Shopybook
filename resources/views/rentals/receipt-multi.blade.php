<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rent Receipt RCP-{{ str_pad($payments->first()->id, 6, '0', STR_PAD_LEFT) }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Arial', sans-serif; background: #e8e6e3; padding: 24px; color: #222; }
    .receipt {
        max-width: 480px; margin: 0 auto; background: #fff; padding: 32px 36px;
        border-radius: 4px; box-shadow: 0 2px 12px rgba(0,0,0,.15); position: relative;
    }
    .biz { text-align: center; border-bottom: 2px solid #7b2e2e; padding-bottom: 14px; margin-bottom: 18px; }
    .biz h1 { font-size: 20px; color: #7b2e2e; margin-bottom: 2px; }
    .biz p { font-size: 12px; color: #555; }
    .title { text-align: center; font-size: 14px; letter-spacing: 3px; font-weight: bold; color: #444; margin-bottom: 16px; }
    .paid-stamp {
        position: absolute; top: 38px; right: 42px; transform: rotate(-12deg);
        border: 3px solid #1a9850; color: #1a9850; padding: 4px 14px;
        font-weight: bold; font-size: 16px; letter-spacing: 2px; border-radius: 4px; opacity: .85;
    }
    table.details { width: 100%; font-size: 13px; border-collapse: collapse; }
    table.details td { padding: 6px 0; vertical-align: top; }
    table.details td:first-child { color: #777; width: 42%; }
    table.details td:last-child { font-weight: 600; }
    table.items { width: 100%; font-size: 13px; border-collapse: collapse; margin-top: 6px; }
    table.items th { text-align: left; font-size: 11px; color: #777; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e5d9d5; padding: 4px 0; }
    table.items td { padding: 6px 0; border-bottom: 1px dashed #eee; }
    table.items td:last-child { text-align: right; font-weight: 600; }
    .amount-box {
        background: #f6f1ef; border: 1px solid #e5d9d5; border-radius: 6px;
        text-align: center; padding: 14px; margin: 18px 0;
    }
    .amount-box .amt { font-size: 28px; font-weight: bold; color: #7b2e2e; }
    .amount-box .lbl { font-size: 11px; color: #777; letter-spacing: 1px; text-transform: uppercase; }
    .sign-row { display: flex; justify-content: space-between; margin-top: 44px; font-size: 12px; color: #555; }
    .sign-row .line { border-top: 1px solid #999; width: 40%; padding-top: 4px; text-align: center; }
    .foot { text-align: center; font-size: 11px; color: #999; margin-top: 20px; border-top: 1px dashed #ccc; padding-top: 12px; }
    .actions { text-align: center; margin: 18px 0; }
    .actions button { padding: 9px 22px; border: 0; border-radius: 6px; cursor: pointer; font-size: 14px; margin: 0 5px; }
    .btn-print { background: #7b2e2e; color: #fff; }
    .btn-close { background: #ddd; color: #333; }
    @media print {
        body { background: #fff; padding: 0; }
        .receipt { box-shadow: none; max-width: 100%; }
        .actions { display: none; }
        @page { margin: 10mm; }
    }
</style>
</head>
<body>

@php $first = $payments->first(); @endphp

<div class="actions no-print">
    <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    <button class="btn-close" onclick="window.close()">Close</button>
</div>

<div class="receipt">
    <div class="paid-stamp">PAID</div>

    <div class="biz">
        <h1>{{ $business->name }}</h1>
        <p>
            {{ $business->phone }}@if($business->email) · {{ $business->email }}@endif
            @if($business->address) · {{ $business->address }}@endif
        </p>
    </div>

    <div class="title">RENT RECEIPT</div>

    <table class="details">
        <tr><td>Receipt No.</td><td>RCP-{{ str_pad($first->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td>Date</td><td>{{ ($first->paid_at ?? $first->created_at)->format('d M Y') }}</td></tr>
        <tr><td>Received From</td><td>{{ $first->tenant?->name ?? '—' }}</td></tr>
        @if($first->tenant?->phone)
        <tr><td>Phone</td><td>{{ $first->tenant->phone }}</td></tr>
        @endif
        <tr><td>For</td><td>{{ $first->unit?->label ?? '—' }}@if($first->unit?->property) — {{ $first->unit->property->name }}@endif</td></tr>
        @if($first->unit?->location_text)
        <tr><td>Location</td><td>{{ $first->unit->location_text }}</td></tr>
        @endif
        <tr><td>Payment Method</td><td>{{ strtoupper(str_replace('_', ' ', $first->method)) }}</td></tr>
        @if($first->transaction_reference)
        <tr><td>Reference</td><td>{{ $first->transaction_reference }}</td></tr>
        @endif
    </table>

    <table class="items">
        <tr><th>Period</th><th style="text-align:right">Amount</th></tr>
        @foreach($payments as $p)
        <tr>
            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $p->period)->format('F Y') }}</td>
            <td>KES {{ number_format($p->amount, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <div class="amount-box">
        <div class="lbl">Total Received — {{ $payments->count() }} month{{ $payments->count() === 1 ? '' : 's' }}</div>
        <div class="amt">KES {{ number_format($payments->sum('amount'), 2) }}</div>
    </div>

    @if($first->notes)
    <table class="details"><tr><td>Notes</td><td>{{ $first->notes }}</td></tr></table>
    @endif

    <div class="sign-row">
        <div class="line">Received by</div>
        <div class="line">Signature / Stamp</div>
    </div>

    <div class="foot">
        Generated by {{ $business->name }} via Shopybook · {{ now()->format('d M Y H:i') }}
    </div>
</div>

@if(request('print'))
<script>window.addEventListener('load', () => setTimeout(() => window.print(), 300));</script>
@endif
</body>
</html>
