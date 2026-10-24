@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => "🧾 {$title}", 'blurb' => 'Every invoice, payment and credit for this party.'])

    <div class="card border">
        <div class="card-body table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Date</th><th>Entry</th><th>Description</th><th class="text-end">Owed</th><th class="text-end">Paid/Credited</th><th class="text-end">Balance</th></tr></thead>
                <tbody>
                    <tr class="text-muted"><td colspan="5">Opening balance</td><td class="text-end">{{ number_format($opening, 2) }}</td></tr>
                    @forelse($lines as $line)
                    <tr class="{{ $line->reverses_id ? 'text-muted' : '' }}">
                        <td>{{ \Carbon\Carbon::parse($line->posting_date)->format('d M Y') }}</td>
                        <td>{{ $line->entry_number }}{{ $line->reverses_id ? ' (reversal)' : '' }}</td>
                        <td>{{ $line->description }}</td>
                        <td class="text-end">{{ $line->debit ? number_format((float) $line->debit, 2) : '' }}</td>
                        <td class="text-end">{{ $line->credit ? number_format((float) $line->credit, 2) : '' }}</td>
                        <td class="text-end">{{ number_format($line->running_balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-muted">No movements in this period.</td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr class="fw-bold"><td colspan="5">Balance</td><td class="text-end">KSh {{ number_format($closing, 2) }}</td></tr></tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
