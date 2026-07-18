@extends('layouts.master')

@section('title', 'Manage Users - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-users text-success me-2"></i>Manage Users</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name or email...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Admin</label>
                    <select name="admin" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="yes" {{ request('admin') === 'yes' ? 'selected' : '' }}>Admins Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Verified</label>
                    <select name="verified" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified</option>
                        <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Unverified</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Business</th>
                            <th>Visits</th>
                            <th>Last Seen</th>
                            <th>Admin</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="text-decoration-none fw-semibold">{{ $user->name }}</a>
                                <div class="small text-muted">{{ $user->email }}</div>
                                @if(!$user->email_verified_at)<span class="badge bg-warning">Unverified</span>@endif
                            </td>
                            <td class="small">
                                @if($user->business)
                                    <a href="{{ route('admin.businesses.show', $user->business) }}" class="text-decoration-none">{{ $user->business->name }}</a>
                                @else
                                    <span class="text-muted">No business</span>
                                @endif
                            </td>
                            <td>{{ $userStats[$user->id]['visits'] ?? 0 }}</td>
                            <td class="small">{{ isset($userStats[$user->id]['last_seen']) ? \Carbon\Carbon::parse($userStats[$user->id]['last_seen'])->diffForHumans() : 'Never' }}</td>
                            <td>
                                @if($user->is_admin)
                                    <span class="badge bg-danger">Admin</span>
                                @endif
                            </td>
                            <td class="small">{{ $user->created_at?->format('M j, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No users found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
