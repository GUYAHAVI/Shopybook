@extends('layouts.dash')
@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h5 class="mb-0">Financial Reports</h5>
            <p class="text-sm mb-0">Every figure comes straight from your books — sales, payments, stock and expenses all in one place.</p>
        </div>
    </div>

    @if(!$setupDone)
    <div class="alert alert-primary d-flex justify-content-between align-items-center">
        <div><strong>Set up your books</strong> — import your history or enter opening balances so these reports have correct figures.</div>
        <a href="{{ route('accounting.setup') }}" class="btn btn-primary btn-sm">Set up now</a>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-3">
            <div class="card p-3">
                <p class="text-sm mb-0 text-uppercase font-weight-bold">Cash in hand (this month)</p>
                <h5 class="font-weight-bolder">KSh {{ number_format($cash['in_total'] - $cash['out_total'], 2) }}</h5>
                <p class="text-xs text-muted mb-0">In: {{ number_format($cash['in_total'], 0) }} · Out: {{ number_format($cash['out_total'], 0) }}</p>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3">
            <div class="card p-3">
                <p class="text-sm mb-0 text-uppercase font-weight-bold">Customers owe you</p>
                <h5 class="font-weight-bolder">KSh {{ number_format($receivables, 2) }}</h5>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3">
            <div class="card p-3">
                <p class="text-sm mb-0 text-uppercase font-weight-bold">You owe suppliers</p>
                <h5 class="font-weight-bolder">KSh {{ number_format($payables, 2) }}</h5>
            </div>
        </div>
    </div>

    @php $links = [
        ['accounting.profit-loss', 'Profit & Loss', 'Did the business make money this period?', 'fas fa-chart-line'],
        ['accounting.balance-sheet', 'Balance Sheet', 'What you own, what you owe, what\'s yours.', 'fas fa-balance-scale'],
        ['accounting.money-book', 'Money Accounts', 'Cash, M-Pesa and bank — in, out, balance.', 'fas fa-money-bill-wave'],
        ['accounting.aging', 'Who Owes Who', 'Customers who owe you and suppliers you owe, by age.', 'fas fa-hourglass-half'],
        ['accounting.stock-valuation', 'Stock Value', 'What your inventory is worth right now.', 'fas fa-boxes'],
        ['accounting.vat', 'VAT Summary', 'VAT collected minus VAT paid — what you owe KRA.', 'fas fa-file-invoice-dollar'],
        ['accounting.trial-balance', 'Trial Balance', 'Every account, for your accountant.', 'fas fa-list-ol'],
        ['accounting.setup', 'Books Setup', 'Import history or set opening balances.', 'fas fa-tools'],
    ]; @endphp
    <div class="row">
        @foreach($links as [$route, $name, $desc, $icon])
        <div class="col-md-6 col-xl-4 mb-3">
            <a href="{{ route($route) }}" class="card p-3 h-100 text-decoration-none" style="color:inherit">
                <div class="d-flex align-items-center gap-3">
                    <i class="{{ $icon }} fa-lg text-primary"></i>
                    <div>
                        <div class="fw-bold">{{ $name }}</div>
                        <div class="text-muted text-sm">{{ $desc }}</div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
