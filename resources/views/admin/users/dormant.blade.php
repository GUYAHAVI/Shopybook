@extends('layouts.master')

@section('title', 'Dormant Users - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-user-slash text-secondary me-2"></i>Dormant Users</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-1"></i>
        <strong>{{ $dormantCount }}</strong> users have been inactive for {{ $days }}+ days (no page visits or business activity).
    </div>

    <div class="mb-3">
        <form method="GET" class="d-inline">
            <label class="me-2">Inactive for:</label>
            <select name="days" class="form-select form-select-sm d-inline-block" style="width:auto" onchange="this.form.submit()">
                @foreach([14, 30, 60, 90, 180] as $d)
                    <option value="{{ $d }}" {{ $days === $d ? 'selected' : '' }}>{{ $d }} days</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($dormantUsers->isNotEmpty())
    <form method="POST" action="{{ route('admin.users.dormant.delete') }}">
        @csrf
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="30"><input type="checkbox" id="select-all" class="form-check-input"></th>
                                <th>User</th>
                                <th>Business</th>
                                <th>Joined</th>
                                <th>Last Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dormantUsers as $user)
                            <tr>
                                <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox"></td>
                                <td>
                                    {{ $user->name }}
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </td>
                                <td class="small">
                                    @if($user->business)
                                        {{ $user->business->name }}
                                    @else
                                        <span class="text-muted">No business</span>
                                    @endif
                                </td>
                                <td class="small">{{ $user->created_at?->format('M j, Y') }}</td>
                                <td class="small text-muted">
                                    @php
                                        $lastVisit = \App\Models\PageVisit::where('user_id', $user->id)->max('created_at');
                                    @endphp
                                    {{ $lastVisit ? \Carbon\Carbon::parse($lastVisit)->diffForHumans() : 'Never logged in' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small" id="selected-count">0 selected</span>
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete selected dormant users? This cannot be undone.')">
                <i class="fas fa-trash me-1"></i>Delete Selected
            </button>
        </div>
    </form>

    <div class="mt-3">
        {{ $dormantUsers->links() }}
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5>No dormant users found</h5>
            <p>All users have been active in the last {{ $days }} days.</p>
        </div>
    </div>
    @endif
</div>

<script>
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
    updateCount();
});
document.querySelectorAll('.user-checkbox').forEach(cb => cb.addEventListener('change', updateCount));
function updateCount() {
    const count = document.querySelectorAll('.user-checkbox:checked').length;
    document.getElementById('selected-count').textContent = count + ' selected';
}
</script>
@endsection
