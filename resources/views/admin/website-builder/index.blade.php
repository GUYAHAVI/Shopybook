@extends('layouts.master')

@section('title', 'Website Builder Management - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-globe text-primary me-2"></i>Website Builder Management</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">With Website</div>
                <div class="fs-3 fw-bold text-success">{{ number_format($totalWithWebsite) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Without Website</div>
                <div class="fs-3 fw-bold text-warning">{{ number_format($totalWithoutWebsite) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Adoption Rate</div>
                <div class="fs-3 fw-bold">{{ ($totalWithWebsite + $totalWithoutWebsite) > 0 ? round($totalWithWebsite / ($totalWithWebsite + $totalWithoutWebsite) * 100) : 0 }}%</div>
            </div></div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Business name or owner email...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Website Status</label>
                    <select name="has_website" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="yes" {{ request('has_website') === 'yes' ? 'selected' : '' }}>Has Website</option>
                        <option value="no" {{ request('has_website') === 'no' ? 'selected' : '' }}>No Website</option>
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
                            <td class="small">{{ $biz->user?->email }}</td>
                            <td><span class="badge bg-{{ $biz->plan === 'free' ? 'secondary' : 'primary' }} text-capitalize">{{ $biz->plan }}</span></td>
                            <td>
                                @if($biz->website)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Active</span>
                                    <div class="small text-muted">{{ $biz->website->created_at?->diffForHumans() }}</div>
                                @else
                                    <span class="badge bg-warning"><i class="fas fa-times me-1"></i>None</span>
                                @endif
                            </td>
                            <td class="small">{{ $biz->created_at?->format('M j, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.businesses.show', $biz) }}" class="btn btn-outline-primary" title="View Details"><i class="fas fa-eye"></i></a>
                                    @if($biz->website)
                                    <a href="{{ route('public.website', $biz->slug) }}" target="_blank" class="btn btn-outline-success" title="View Website"><i class="fas fa-external-link-alt"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No businesses found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $businesses->links() }}</div>
</div>
@endsection
