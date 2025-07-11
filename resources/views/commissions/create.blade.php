@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Add Commission Payout</h2>
                <form method="POST" action="{{ route('commissions.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Staff *</label>
                        <select class="form-control" id="staff_id" name="staff_id" required>
                            <option value="">Select Staff</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @if(old('staff_id') == $member->id) selected @endif>{{ $member->name }}</option>
                            @endforeach
                        </select>
                        @error('staff_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (KSh) *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" required>
                        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="paid_at" class="form-label">Paid At</label>
                        <input type="datetime-local" class="form-control" id="paid_at" name="paid_at" value="{{ old('paid_at') }}">
                        @error('paid_at')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes">{{ old('notes') }}</textarea>
                        @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Payout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 