@extends('layouts.master')

@section('title', $user->name . ' - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-user text-success me-2"></i>{{ $user->name }}</h2>
            <div class="text-muted small">{{ $user->email }}</div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>

    {{-- User info --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-info-circle me-2"></i>User Info</div>
                <div class="card-body small">
                    <div class="mb-2"><strong>Email:</strong> {{ $user->email }}</div>
                    <div class="mb-2"><strong>Verified:</strong> {{ $user->email_verified_at ? 'Yes' : 'No' }}</div>
                    <div class="mb-2"><strong>Admin:</strong> {{ $user->is_admin ? 'Yes' : 'No' }}</div>
                    <div class="mb-2"><strong>Joined:</strong> {{ $user->created_at?->format('M j, Y H:i') }}</div>
                    <div class="mb-2"><strong>Last Seen:</strong> {{ $lastSeen ? \Carbon\Carbon::parse($lastSeen)->diffForHumans() : 'Never' }}</div>
                    <div class="mb-2"><strong>Total Visits:</strong> {{ number_format($totalVisits) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-store me-2"></i>Business</div>
                <div class="card-body">
                    @if($user->business)
                        <div class="mb-2"><strong>{{ $user->business->name }}</strong></div>
                        <div class="small text-muted mb-2">{{ $user->business->business_type }}</div>
                        <span class="badge bg-{{ $user->business->plan === 'free' ? 'secondary' : 'primary' }} text-capitalize">{{ $user->business->plan }}</span>
                        <div class="mt-3">
                            <a href="{{ route('admin.businesses.show', $user->business) }}" class="btn btn-sm btn-outline-primary">View Business</a>
                        </div>
                    @else
                        <span class="text-muted">No business registered</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-cog me-2"></i>Admin Actions</div>
                <div class="card-body">
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                                <i class="fas fa-shield-alt me-1"></i>{{ $user->is_admin ? 'Revoke' : 'Grant' }} Admin
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete user {{ $user->email }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i>Delete User
                            </button>
                        </form>
                    @else
                        <span class="text-muted">This is your account</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Top pages --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Top Pages (Last 30 Days)</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Page</th><th>Visits</th><th>Avg Duration</th></tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $page)
                                <tr>
                                    <td class="small">{{ $page->page }}</td>
                                    <td>{{ $page->visits }}</td>
                                    <td class="small">{{ $page->avg_duration ? round($page->avg_duration) . 'ms' : '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No visits</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-list me-2"></i>Recent Activity</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @forelse($activityLogs as $log)
                            <div class="list-group-item small">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $log->description }}</span>
                                    <span class="text-muted" style="font-size: 11px;">{{ $log->created_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted text-center py-3">No activity</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
