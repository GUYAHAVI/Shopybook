@extends('layouts.master')

@section('title', 'Manage Businesses - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-store text-primary me-2"></i>Manage Businesses</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name, email, slug...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Plan</label>
                    <select name="plan" class="form-select form-select-sm">
                        <option value="">All Plans</option>
                        <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Trial</label>
                    <select name="trial" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active" {{ request('trial') === 'active' ? 'selected' : '' }}>Active Trial</option>
                        <option value="expired" {{ request('trial') === 'expired' ? 'selected' : '' }}>Expired Trial</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Website</label>
                    <select name="website" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="yes" {{ request('website') === 'yes' ? 'selected' : '' }}>Has Website</option>
                        <option value="no" {{ request('website') === 'no' ? 'selected' : '' }}>No Website</option>
                    </select>
                </div>
                <div class="col-md-2">
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
                            <th>Business</th>
                            <th>Owner</th>
                            <th>Plan</th>
                            <th>Trial</th>
                            <th>Website</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businesses as $biz)
                        <tr>
                            <td>
                                <a href="{{ route('admin.businesses.show', $biz) }}" class="text-decoration-none fw-semibold">{{ $biz->name }}</a>
                                <div class="small text-muted">{{ $biz->slug }}</div>
                            </td>
                            <td>
                                {{ $biz->user?->name ?? 'N/A' }}
                                <div class="small text-muted">{{ $biz->user?->email ?? '' }}</div>
                            </td>
                            <td><span class="badge bg-{{ $biz->plan === 'free' ? 'secondary' : ($biz->plan === 'premium' ? 'primary' : 'success') }} text-capitalize">{{ $biz->plan }}</span></td>
                            <td>
                                @if($biz->on_trial)
                                    @if($biz->trial_ends_at && $biz->trial_ends_at->isFuture())
                                        <span class="badge bg-warning">{{ $biz->trial_ends_at->diffInDays(now()) }}d left</span>
                                    @else
                                        <span class="badge bg-danger">Expired</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($biz->website)
                                    <i class="fas fa-check text-success"></i>
                                @else
                                    <i class="fas fa-times text-muted"></i>
                                @endif
                            </td>
                            <td class="small">{{ $biz->created_at?->format('M j, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.businesses.show', $biz) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No businesses found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $businesses->links() }}
    </div>
</div>
@endsection
