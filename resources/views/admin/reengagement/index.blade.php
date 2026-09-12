@extends('layouts.admin')

@section('title', 'Re-engage users — Super Admin')
@section('page-title', 'Re-engage users')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-1"><i class="fas fa-paper-plane me-2"></i>Re-engage inactive users</h2>
        <p class="text-muted">Send a personal email from Elvis to users who have gone quiet. AI drafts a message based on each user's activity — you review and edit before sending.</p>
    </div>

    {{-- Summary + filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <label class="text-muted small mb-0">Inactive for:</label>
                        <select name="days" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                            @foreach([7, 14, 30, 60, 90, 180] as $d)
                                <option value="{{ $d }}" {{ $days === $d ? 'selected' : '' }}>{{ $d }} days</option>
                            @endforeach
                        </select>
                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" name="hide_contacted" value="1" id="hideContacted" {{ $hideContacted ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label small text-muted" for="hideContacted">Hide already contacted</label>
                        </div>
                        @if(!$hideContacted)
                            <input type="hidden" name="hide_contacted" value="0">
                        @endif
                    </form>
                </div>
                <div class="d-flex gap-3 small">
                    <span class="text-muted"><i class="fas fa-users me-1"></i>{{ $totalInactive }} inactive</span>
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>{{ $alreadyContacted }} contacted</span>
                    <span class="text-primary"><i class="fas fa-clock me-1"></i>{{ $users->total() }} to contact</span>
                </div>
            </div>
        </div>
    </div>

    @if($users->isNotEmpty())
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-primary" id="select-all-btn" onclick="selectAllPage()">
                <i class="fas fa-check-square me-1"></i>Select all on page
            </button>
            <button class="btn btn-sm btn-primary" id="select-all-matching-btn" onclick="selectAllMatching()">
                <i class="fas fa-check-double me-1"></i>Select all {{ $users->total() }} users
            </button>
            <span class="text-muted small" id="selection-info"></span>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="select-all" class="form-check-input"></th>
                            <th>User</th>
                            <th>Business</th>
                            <th>Last seen</th>
                            <th>Visits</th>
                            <th>Last page</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr data-user-id="{{ $user->id }}">
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
                            <td class="small text-muted">
                                {{ $user->last_visit_at ? $user->last_visit_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="small">{{ $user->visit_count }}</td>
                            <td class="small text-muted">{{ $user->last_visit_page ?? '—' }}</td>
                            <td>
                                @if($user->last_reengaged_at)
                                    <span class="badge bg-success" title="{{ $user->last_reengaged_at->format('M j, Y g:i a') }}">
                                        <i class="fas fa-paper-plane me-1"></i>Contacted
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Not contacted</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="draftEmail({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    <i class="fas fa-magic me-1"></i>Draft
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>

    <!-- Bulk send -->
    <div class="card mt-4" id="bulk-section" style="display:none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Personalized bulk send to <span id="bulk-count">0</span> users</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary" id="generate-drafts-btn" onclick="generateBulkDrafts()">
                    <i class="fas fa-magic me-1"></i>Generate personalized drafts
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="closeBulk()">Cancel</button>
            </div>
        </div>
        <div class="card-body">
            <p class="text-muted small">Click "Generate personalized drafts" — AI will write a unique email for each user based on their activity. Review and edit each one, then send all at once. For large batches this may take a minute.</p>

            <div id="bulk-loading" class="text-center py-4" style="display:none;">
                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                <p class="text-muted">Generating personalized drafts... <span id="bulk-progress">0 / 0</span></p>
                <p class="text-muted small">This takes about 2-3 seconds per user. Please wait.</p>
            </div>

            <div id="bulk-drafts" style="display:none;"></div>

            <form id="bulk-send-form" method="POST" action="{{ route('admin.reengagement.send-bulk') }}" style="display:none;">
                @csrf
                <div id="bulk-emails-container"></div>
                <button type="submit" class="btn btn-primary mt-3" onclick="return confirm('Send all personalized emails?')">
                    <i class="fas fa-paper-plane me-1"></i>Send all personalized emails
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5>All caught up!</h5>
            <p>No users left to contact in the last {{ $days }} days. @if($alreadyContacted > 0) {{ $alreadyContacted }} have already been contacted this period. @endif</p>
        </div>
    </div>
    @endif
</div>

<!-- Draft modal -->
<div class="modal fade" id="draftModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-magic me-2"></i>Email draft for <span id="draft-user-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="draft-loading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                    <p class="text-muted">AI is drafting a personal message...</p>
                </div>
                <div id="draft-content" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Subject</label>
                        <input type="text" id="draft-subject" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">Message (edit before sending)</label>
                        <textarea id="draft-body" class="form-control" rows="14" style="font-family: 'Poppins', sans-serif; line-height: 1.7;"></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span id="draft-source" class="small text-muted"></span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="redraft()">
                                <i class="fas fa-redo me-1"></i>Regenerate
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="sendDraft()">
                                <i class="fas fa-paper-plane me-1"></i>Send email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="send-form" method="POST" action="" style="display:none;">
    @csrf
    <input type="hidden" name="subject" id="send-subject">
    <input type="hidden" name="body" id="send-body">
</form>

@push('scripts')
<script>
let currentUserId = null;
let selectedUserIds = new Set();
const draftModal = new bootstrap.Modal(document.getElementById('draftModal'));

document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.checked = this.checked;
        if (this.checked) selectedUserIds.add(cb.value); else selectedUserIds.delete(cb.value);
    });
    updateSelectionInfo();
});

document.querySelectorAll('.user-checkbox').forEach(cb => cb.addEventListener('change', function() {
    if (this.checked) selectedUserIds.add(this.value); else selectedUserIds.delete(this.value);
    updateSelectionInfo();
}));

function updateSelectionInfo() {
    const count = selectedUserIds.size;
    const info = document.getElementById('selection-info');
    if (count > 0) {
        info.textContent = count + ' selected';
        document.getElementById('bulk-section').style.display = '';
        document.getElementById('bulk-count').textContent = count;
    } else {
        info.textContent = '';
        document.getElementById('bulk-section').style.display = 'none';
    }
}

function selectAllPage() {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.checked = true;
        selectedUserIds.add(cb.value);
    });
    document.getElementById('select-all').checked = true;
    updateSelectionInfo();
}

function selectAllMatching() {
    const btn = document.getElementById('select-all-matching-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';

    const params = new URLSearchParams(window.location.search);

    fetch('{{ route("admin.reengagement.all-ids") }}?' + params, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        selectedUserIds = new Set(data.user_ids.map(String));
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = selectedUserIds.has(cb.value);
        });
        updateSelectionInfo();
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-double me-1"></i>Select all ' + data.count + ' users';
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-double me-1"></i>Select all users';
    });
}

function closeBulk() {
    document.getElementById('bulk-section').style.display = 'none';
    document.getElementById('bulk-loading').style.display = 'none';
    document.getElementById('bulk-drafts').style.display = 'none';
    document.getElementById('bulk-drafts').innerHTML = '';
    document.getElementById('bulk-send-form').style.display = 'none';
    document.getElementById('bulk-emails-container').innerHTML = '';
    document.getElementById('generate-drafts-btn').style.display = '';
}

function generateBulkDrafts() {
    const userIds = Array.from(selectedUserIds);
    if (userIds.length === 0) return;

    const loading = document.getElementById('bulk-loading');
    const draftsDiv = document.getElementById('bulk-drafts');
    const sendForm = document.getElementById('bulk-send-form');
    const generateBtn = document.getElementById('generate-drafts-btn');

    loading.style.display = '';
    draftsDiv.style.display = 'none';
    draftsDiv.innerHTML = '';
    sendForm.style.display = 'none';
    generateBtn.style.display = 'none';
    document.getElementById('bulk-progress').textContent = `0 / ${userIds.length}`;

    fetch('{{ route("admin.reengagement.draft-bulk") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_ids: userIds })
    })
    .then(r => r.json())
    .then(data => {
        loading.style.display = 'none';
        if (!data.drafts || data.drafts.length === 0) {
            draftsDiv.innerHTML = '<p class="text-danger">No drafts were generated. Please try again.</p>';
            draftsDiv.style.display = '';
            generateBtn.style.display = '';
            return;
        }

        const emailsContainer = document.getElementById('bulk-emails-container');
        emailsContainer.innerHTML = '';

        data.drafts.forEach((draft, i) => {
            const card = document.createElement('div');
            card.className = 'border rounded p-3 mb-3';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong><i class="fas fa-user me-1"></i>${draft.user_name}</strong>
                    <span class="text-muted small">${draft.user_email} &middot; ${draft.source === 'ai' ? 'AI-drafted' : 'Template'}</span>
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted">Subject</label>
                    <input type="text" class="form-control form-control-sm bulk-subject" data-idx="${i}" value="${(draft.subject || '').replace(/"/g, '"')}">
                </div>
                <div>
                    <label class="form-label small text-muted">Message</label>
                    <textarea class="form-control form-control-sm bulk-body" data-idx="${i}" rows="8" style="font-family: 'Poppins', sans-serif; line-height: 1.7;">${draft.body}</textarea>
                </div>
            `;
            draftsDiv.appendChild(card);

            const subjInput = document.createElement('input');
            subjInput.type = 'hidden';
            subjInput.name = `emails[${i}][user_id]`;
            subjInput.value = draft.user_id;
            emailsContainer.appendChild(subjInput);

            const subjField = document.createElement('input');
            subjField.type = 'hidden';
            subjField.name = `emails[${i}][subject]`;
            subjField.className = `bulk-subject-hidden-${i}`;
            emailsContainer.appendChild(subjField);

            const bodyField = document.createElement('input');
            bodyField.type = 'hidden';
            bodyField.name = `emails[${i}][body]`;
            bodyField.className = `bulk-body-hidden-${i}`;
            emailsContainer.appendChild(bodyField);
        });

        sendForm.onsubmit = function() {
            data.drafts.forEach((draft, i) => {
                document.querySelector(`.bulk-subject-hidden-${i}`).value = document.querySelector(`.bulk-subject[data-idx="${i}"]`).value;
                document.querySelector(`.bulk-body-hidden-${i}`).value = document.querySelector(`.bulk-body[data-idx="${i}"]`).value;
            });
        };

        draftsDiv.style.display = '';
        sendForm.style.display = '';
        document.getElementById('bulk-progress').textContent = `${data.drafts.length} / ${userIds.length}`;
    })
    .catch(() => {
        loading.style.display = 'none';
        draftsDiv.innerHTML = '<p class="text-danger">Failed to generate drafts. Please try again.</p>';
        draftsDiv.style.display = '';
        generateBtn.style.display = '';
    });
}

function draftEmail(userId, userName) {
    currentUserId = userId;
    document.getElementById('draft-user-name').textContent = userName;
    document.getElementById('draft-loading').style.display = '';
    document.getElementById('draft-content').style.display = 'none';
    draftModal.show();
    loadDraft(userId);
}

function loadDraft(userId) {
    fetch(`{{ route('admin.reengagement.draft', '__USER__') }}`.replace('__USER__', userId), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('draft-loading').style.display = 'none';
        document.getElementById('draft-content').style.display = '';
        document.getElementById('draft-subject').value = data.subject || '';
        document.getElementById('draft-body').value = data.body || '';
        document.getElementById('draft-source').textContent = data.source === 'ai' ? 'Drafted by AI' : 'Using template (AI unavailable)';
    })
    .catch(() => {
        document.getElementById('draft-loading').innerHTML = '<p class="text-danger">Failed to generate draft. Please try again.</p>';
    });
}

function redraft() {
    if (!currentUserId) return;
    document.getElementById('draft-loading').style.display = '';
    document.getElementById('draft-content').style.display = 'none';
    loadDraft(currentUserId);
}

function sendDraft() {
    if (!currentUserId) return;
    const subject = document.getElementById('draft-subject').value;
    const body = document.getElementById('draft-body').value;
    const form = document.getElementById('send-form');
    form.action = `{{ route('admin.reengagement.send', '__USER__') }}`.replace('__USER__', currentUserId);
    document.getElementById('send-subject').value = subject;
    document.getElementById('send-body').value = body;
    form.submit();
}
</script>
@endpush
@endsection
