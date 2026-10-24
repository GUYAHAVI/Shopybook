@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => "📖 {$account->name}", 'blurb' => 'Every movement on this account, in order.'])

    <div class="card border">
        <div class="card-body table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Date</th><th>Entry</th><th>Description</th><th>Party</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
                <tbody>
                    <tr class="text-muted"><td colspan="6">Opening balance</td><td class="text-end">{{ number_format($opening, 2) }}</td></tr>
                    @foreach($lines as $line)
                    <tr class="{{ $line->reverses_id ? 'text-muted' : '' }}">
                        <td>{{ \Carbon\Carbon::parse($line->posting_date)->format('d M Y') }}</td>
                        <td>{{ $line->entry_number }}{{ $line->reverses_id ? ' (reversal)' : '' }}{{ $line->reversed_by_id ? ' (reversed)' : '' }}</td>
                        <td>{{ $line->description }}</td>
                        <td>{{ $line->party_type ? ucfirst($line->party_type) . ' #' . $line->party_id : '' }}</td>
                        <td class="text-end">{{ $line->debit ? number_format((float) $line->debit, 2) : '' }}</td>
                        <td class="text-end">{{ $line->credit ? number_format((float) $line->credit, 2) : '' }}</td>
                        <td class="text-end">{{ number_format($line->running_balance, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot><tr class="fw-bold"><td colspan="6">Closing balance</td><td class="text-end">{{ number_format($closing, 2) }}</td></tr></tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
