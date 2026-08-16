@extends('layouts.dash')
@section('title', 'Team Management')

@section('content')
<style>
/* ── Page Layout ── */
.tm-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
.tm-header h2 { font-size: 1.6rem; font-weight: 700; color: var(--text-primary); margin: 0; }
.tm-header p  { margin: 0; color: var(--text-secondary); font-size: .9rem; }

/* ── Member Cards ── */
.member-grid  { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-bottom: 32px; }
.member-card  { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 20px; position: relative; transition: box-shadow .2s; }
.member-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.1); }

.member-avatar { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; color: #fff; flex-shrink: 0; }
.member-info { flex: 1; min-width: 0; }
.member-name { font-weight: 600; font-size: .95rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.member-email { color: var(--text-secondary); font-size: .8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.role-badge   { display: inline-flex; align-items: center; gap: 4px; font-size: .72rem; font-weight: 600; padding: 2px 9px; border-radius: 20px; }
.role-admin   { background: #fef3c7; color: #92400e; }
.role-manager { background: #dbeafe; color: #1e40af; }
.role-cashier { background: #d1fae5; color: #065f46; }
.role-staff   { background: #ede9fe; color: #5b21b6; }
.role-viewer  { background: #f3f4f6; color: #374151; }
.role-owner   { background: linear-gradient(135deg,#7b2e2e,#1a1a7e); color: #fff; }

.status-dot   { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.status-active    { background: #10b981; }
.status-pending   { background: #f59e0b; }
.status-suspended { background: #ef4444; }

.perm-chips   { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 10px; }
.perm-chip    { font-size: .68rem; padding: 2px 7px; border-radius: 20px; background: var(--hover-bg,#f0f4ff); color: var(--text-secondary); border: 1px solid var(--border-color); }

.member-actions { display: flex; gap: 6px; margin-top: 12px; }

/* ── Stats bar ── */
.team-stats { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.team-stat  { flex: 1; min-width: 120px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; text-align: center; }
.team-stat .val { font-size: 1.8rem; font-weight: 700; color: var(--primary-color,#7b2e2e); }
.team-stat .lbl { font-size: .8rem; color: var(--text-secondary); }

/* ── Activity Log ── */
.log-table th { font-size: .75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); white-space: nowrap; }
.log-table td { font-size: .83rem; vertical-align: middle; }
.log-module { font-size: .7rem; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: var(--hover-bg,#f0f4ff); color: var(--text-secondary); }

/* ── Permissions grid in modal ── */
.perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 8px; }
.perm-item { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border: 1.5px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: border-color .15s, background .15s; }
.perm-item:hover { border-color: var(--primary-light,#ff511a); background: var(--hover-bg,#f0f4ff); }
.perm-item input[type=checkbox]:checked ~ span { color: var(--primary-color,#7b2e2e); font-weight: 600; }
.perm-item input[type=checkbox]:checked { accent-color: var(--primary-color,#7b2e2e); }
.perm-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: .75rem; color: #fff; flex-shrink: 0; }
</style>

{{-- ── Flash Messages ── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('success_html'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{!! session('success_html') !!}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- ── Page Header ── --}}
<div class="tm-header">
    <div>
        <h2><i class="fas fa-users-cog me-2" style="color:var(--primary-light,#ff511a)"></i>Team Management</h2>
        <p>Manage who has access to <strong>{{ $business->name }}</strong> and what they can do</p>
    </div>
    @if($isOwner)
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
        <i class="fas fa-user-plus me-2"></i>Add Team Member
    </button>
    @endif
</div>

{{-- ── Stats ── --}}
@php
    $active    = $members->where('status','active')->count();
    $pending   = $members->where('status','pending')->count();
    $suspended = $members->where('status','suspended')->count();
@endphp
<div class="team-stats">
    <div class="team-stat">
        <div class="val">{{ $members->count() + 1 }}</div>
        <div class="lbl">Total Members</div>
    </div>
    <div class="team-stat">
        <div class="val" style="color:#10b981">{{ $active }}</div>
        <div class="lbl">Active</div>
    </div>
    <div class="team-stat">
        <div class="val" style="color:#f59e0b">{{ $pending }}</div>
        <div class="lbl">Pending Invite</div>
    </div>
    <div class="team-stat">
        <div class="val" style="color:#ef4444">{{ $suspended }}</div>
        <div class="lbl">Suspended</div>
    </div>
</div>

{{-- ── Member Cards ── --}}
<div class="member-grid">
    {{-- Owner Card --}}
    <div class="member-card" style="border-color: rgba(2,2,88,.25); background: linear-gradient(135deg, rgba(2,2,88,.04) 0%, rgba(19,232,233,.04) 100%);">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="member-avatar" style="background: linear-gradient(135deg,#7b2e2e,#1a1a7e)">
                {{ strtoupper(substr($business->owner->name ?? auth()->user()->name, 0, 1)) }}
            </div>
            <div class="member-info">
                <div class="member-name">{{ $business->owner->name ?? auth()->user()->name }}</div>
                <div class="member-email">{{ $business->owner->email ?? auth()->user()->email }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="role-badge role-owner"><i class="fas fa-crown"></i> Owner</span>
            <span class="status-dot status-active ms-1" title="Active"></span>
            <small class="text-muted ms-1">All permissions</small>
        </div>
    </div>

    {{-- Team Member Cards --}}
    @forelse($members as $member)
    @php
        $avatarColors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899'];
        $color = $avatarColors[$loop->index % count($avatarColors)];
        $permCount = count($member->permissions ?? []);
    @endphp
    <div class="member-card">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="member-avatar" style="background: {{ $color }}">
                {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
            <div class="member-info">
                <div class="member-name">{{ $member->name }}</div>
                <div class="member-email">{{ $member->invited_email }}</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="role-badge role-{{ $member->role }}">{{ $member->role_label }}</span>
            <span class="status-dot status-{{ $member->status }}" title="{{ ucfirst($member->status) }}"></span>
            <small class="text-muted">{{ ucfirst($member->status) }}</small>
        </div>

        <div class="perm-chips">
            @foreach(array_slice($member->permissions ?? [], 0, 4) as $perm)
                <span class="perm-chip">{{ config("rbac.modules.{$perm}.label", $perm) }}</span>
            @endforeach
            @if($permCount > 4)
                <span class="perm-chip">+{{ $permCount - 4 }} more</span>
            @endif
            @if($permCount === 0)
                <span class="perm-chip text-danger" style="border-color:#ef4444;color:#ef4444">No permissions</span>
            @endif
        </div>

        @if($isOwner)
        <div class="member-actions mt-3">
            <button class="btn btn-sm btn-outline-primary flex-grow-1"
                onclick="openEditModal({{ $member->id }}, '{{ $member->name }}', '{{ $member->role }}', {{ json_encode($member->permissions ?? []) }})">
                <i class="fas fa-edit me-1"></i>Edit
            </button>
            <form action="{{ route('team.toggleStatus', $member) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm {{ $member->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $member->status === 'active' ? 'Suspend' : 'Activate' }}">
                    <i class="fas fa-{{ $member->status === 'active' ? 'ban' : 'check' }}"></i>
                </button>
            </form>
            <form action="{{ route('team.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove {{ addslashes($member->name) }} from the team?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
        <div class="mt-2">
            <small class="text-muted"><i class="fas fa-clock me-1"></i>
                @if($member->joined_at)
                    Joined {{ $member->joined_at->diffForHumans() }}
                @else
                    Invited {{ $member->invited_at?->diffForHumans() }}
                @endif
            </small>
        </div>
        @endif
    </div>
    @empty
    <div class="member-card d-flex flex-column align-items-center justify-content-center" style="min-height:160px; border-style:dashed; grid-column: 1 / -1;">
        <i class="fas fa-user-plus text-muted mb-2" style="font-size:2rem;opacity:.4"></i>
        <p class="text-muted mb-3">No team members yet</p>
        @if($isOwner)
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
            <i class="fas fa-user-plus me-1"></i>Add First Member</button>
        @endif
    </div>
    @endforelse
</div>

{{-- ── Activity Log ── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between py-3" style="background:var(--card-bg); border-bottom:1px solid var(--border-color);">
        <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Activity Log</h6>
        <div class="d-flex gap-2 flex-wrap">
            <select id="logUserFilter" class="form-select form-select-sm" style="max-width:160px">
                <option value="">All Members</option>
                @foreach($members->where('user_id','!=',null) as $m)
                    <option value="{{ $m->user_id }}">{{ $m->name }}</option>
                @endforeach
                <option value="{{ $business->user_id }}">{{ auth()->user()->name }} (Owner)</option>
            </select>
            <select id="logModuleFilter" class="form-select form-select-sm" style="max-width:140px">
                <option value="">All Modules</option>
                @foreach($modules as $slug => $mod)
                    <option value="{{ $slug }}">{{ $mod['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 log-table">
                <thead>
                    <tr>
                        <th class="ps-3">Member</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP</th>
                        <th class="pe-3">Time</th>
                    </tr>
                </thead>
                <tbody id="logTableBody">
                    @forelse($logs as $log)
                    <tr data-user="{{ $log->user_id }}" data-module="{{ $log->module }}">
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="member-avatar" style="width:28px;height:28px;font-size:.7rem;background:#7b2e2e">
                                    {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <span>{{ $log->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td><span class="log-module">{{ ucfirst($log->module) }}</span></td>
                        <td><span class="badge bg-{{ $log->action_color }}">{{ ucfirst($log->action) }}</span></td>
                        <td>{{ Str::limit($log->description, 70) }}</td>
                        <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                        <td class="pe-3"><small class="text-muted" title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No activity recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════
     INVITE MODAL
═══════════════════════════════════════════════════════════════ --}}
@if($isOwner)
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>Add Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('team.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Jane Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="jane@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" id="inviteRole" class="form-select" required onchange="loadRoleDefaults(this.value, 'invite')">
                                @foreach($roles as $slug => $label)
                                    <option value="{{ $slug }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="invitePassword" class="form-control"
                                    placeholder="Set login password" minlength="6" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('invitePassword', this)" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Min 6 characters. Share this securely with the new member.</div>
                        </div>
                    </div>

                    <label class="form-label fw-semibold mb-2">Module Permissions</label>
                    <div class="perm-grid" id="invitePermGrid">
                        @foreach($modules as $slug => $mod)
                        <label class="perm-item">
                            <input type="checkbox" name="permissions[]" value="{{ $slug }}"
                                class="invite-perm"
                                {{ in_array($slug, $defaults['staff'] ?? []) ? 'checked' : '' }}>
                            <div class="perm-icon" style="background:{{ $mod['color'] }}">
                                <i class="{{ $mod['icon'] }}"></i>
                            </div>
                            <span style="font-size:.8rem">{{ $mod['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus me-1"></i>Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     EDIT MEMBER MODAL
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Edit Member: <span id="editMemberName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" id="editRole" class="form-select" onchange="loadRoleDefaults(this.value, 'edit')">
                                @foreach($roles as $slug => $label)
                                    <option value="{{ $slug }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reset Password <span class="text-muted fw-normal">(optional)</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="editPassword" class="form-control"
                                    placeholder="Leave blank to keep current" minlength="6">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('editPassword', this)" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Only fill this to change the member's password.</div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllPerms('edit', true)">
                                <i class="fas fa-check-double me-1"></i>Select All
                            </button>
                        </div>
                    </div>
                    <label class="form-label fw-semibold mb-2">Module Permissions</label>
                    <div class="perm-grid" id="editPermGrid">
                        @foreach($modules as $slug => $mod)
                        <label class="perm-item">
                            <input type="checkbox" name="permissions[]" value="{{ $slug }}" class="edit-perm">
                            <div class="perm-icon" style="background:{{ $mod['color'] }}">
                                <i class="{{ $mod['icon'] }}"></i>
                            </div>
                            <span style="font-size:.8rem">{{ $mod['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// Populate edit modal
function openEditModal(id, name, role, permissions) {
    document.getElementById('editMemberName').textContent = name;
    document.getElementById('editForm').action = '/team/' + id;
    document.getElementById('editRole').value = role;

    // Tick the right checkboxes
    document.querySelectorAll('.edit-perm').forEach(cb => {
        cb.checked = Array.isArray(permissions) && permissions.includes(cb.value);
    });

    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Load role defaults via fetch
async function loadRoleDefaults(role, context) {
    const res  = await fetch('/team/role-defaults/' + role);
    const data = await res.json();
    const perms = data.permissions || [];

    const cls = context === 'invite' ? '.invite-perm' : '.edit-perm';
    document.querySelectorAll(cls).forEach(cb => {
        cb.checked = perms.includes(cb.value);
    });
}

// Select / deselect all
function selectAllPerms(context, state) {
    const cls = context === 'invite' ? '.invite-perm' : '.edit-perm';
    document.querySelectorAll(cls).forEach(cb => cb.checked = state);
}

// Activity log filters
document.getElementById('logUserFilter')?.addEventListener('change', filterLogs);
document.getElementById('logModuleFilter')?.addEventListener('change', filterLogs);

function filterLogs() {
    const user   = document.getElementById('logUserFilter').value;
    const module = document.getElementById('logModuleFilter').value;
    document.querySelectorAll('#logTableBody tr[data-user]').forEach(row => {
        const uMatch = !user   || row.dataset.user   === user;
        const mMatch = !module || row.dataset.module === module;
        row.style.display = (uMatch && mMatch) ? '' : 'none';
    });
}

// Init invite modal defaults
document.getElementById('inviteModal')?.addEventListener('show.bs.modal', () => {
    loadRoleDefaults(document.getElementById('inviteRole').value, 'invite');
});

// Toggle password visibility
function togglePwd(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

@endsection
