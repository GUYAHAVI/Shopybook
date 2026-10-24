@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="card mb-4"><div class="card-header pb-0">
        <h5>📦 Stock Value</h5>
        <p class="text-sm mb-0">What your inventory is worth right now, at moving-average cost. Matches the Stock account in your books.</p>
    </div></div>

    <div class="card border">
        <div class="card-body table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Product</th><th>SKU</th><th class="text-end">In stock</th><th class="text-end">Avg. cost</th><th class="text-end">Value</th></tr></thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->sku }}</td>
                        <td class="text-end">{{ number_format($row->qty_after, 3) }} {{ $row->measurement_unit }}</td>
                        <td class="text-end">{{ number_format($row->valuation_rate, 2) }}</td>
                        <td class="text-end">{{ number_format($row->value_after, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-muted">No stock movements recorded yet.</td></tr>
                    @endforelse
                </tbody>
                @if($rows->isNotEmpty())
                <tfoot><tr class="fw-bold"><td colspan="4">Total stock value</td><td class="text-end">KSh {{ number_format($rows->sum('value_after'), 2) }}</td></tr></tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
