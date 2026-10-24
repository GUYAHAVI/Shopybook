@extends('layouts.dash')
@section('content')
<div class="container py-4">
    @include('accounting.partials.head', ['title' => '⏳ Who Owes Who', 'blurb' => 'How old each debt is.', 'asOf' => $asOf])

    @foreach([['Customers who owe you', $receivables, $customers], ['Suppliers you owe', $payables, $suppliers]] as [$title, $rows, $names])
    <div class="card border mb-4">
        <div class="card-body">
            <h6 class="mb-3">{{ $title }}</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Name</th><th class="text-end">0–30 days</th><th class="text-end">31–60</th><th class="text-end">61–90</th><th class="text-end">90+</th><th class="text-end">Total</th><th></th></tr></thead>
                    <tbody>
                        @forelse($rows as $row)
                        <tr>
                            <td>{{ $names[$row['party']['id']] ?? $row['party']['memo'] ?? ucfirst($row['party']['type'] ?? 'Other') . ' #' . ($row['party']['id'] ?? '—') }}</td>
                            <td class="text-end">{{ number_format($row['current'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['d30'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['d60'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row['d90'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['total'], 2) }}</td>
                            <td class="text-end">
                                @if(in_array($row['party']['type'] ?? '', ['customer','supplier']) && $row['party']['id'])
                                <a href="{{ route('accounting.party', [$row['party']['type'], $row['party']['id']]) }}" class="btn btn-sm btn-outline-primary">Statement</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-muted">Nothing outstanding.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
