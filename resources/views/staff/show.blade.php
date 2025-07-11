@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Staff Details</h2>
                <dl class="row">
                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $staff->name }}</dd>
                    <dt class="col-sm-4">Role</dt>
                    <dd class="col-sm-8">{{ $staff->role }}</dd>
                    <dt class="col-sm-4">Commission Rate</dt>
                    <dd class="col-sm-8">{{ $staff->commission_rate ?? '-' }}%</dd>
                    <dt class="col-sm-4">Contact</dt>
                    <dd class="col-sm-8">{{ $staff->contact }}</dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('staff.edit', $staff) }}" class="btn btn-primary me-2">Edit</a>
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 