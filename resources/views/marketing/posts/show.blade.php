@extends('layouts.dash')

@section('title', 'View Post')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0" style="color: var(--text-primary);">{{ $post->title }}</h1>
        <p class="text-muted mb-0">Created {{ $post->created_at->format('M j, Y \a\t g:i A') }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('marketing.posts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Posts
        </a>
        <a href="{{ route('marketing.posts.edit', $post) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('marketing.posts.analytics', $post) }}" class="btn btn-outline-info">
            <i class="fas fa-chart-bar me-2"></i>Analytics
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Post Content -->
    <div class="col-lg-8">

        <!-- Content Card -->
        <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                    <i class="fas fa-file-alt me-2"></i>Post Content
                </h6>
                @php
                    $statusMap = [
                        'published'           => ['bg-success',          'fa-check-circle',      'Published'],
                        'partially_published' => ['bg-warning text-dark','fa-exclamation-circle','Partially Published'],
                        'scheduled'           => ['bg-info',             'fa-clock',             'Scheduled'],
                        'draft'               => ['bg-secondary',        'fa-file-alt',          'Draft'],
                        'failed'              => ['bg-danger',           'fa-times-circle',      'Failed'],
                    ];
                    [$badgeClass, $icon, $label] = $statusMap[$post->status] ?? ['bg-secondary','fa-question',ucfirst($post->status)];
                @endphp
                <span class="badge {{ $badgeClass }} px-3 py-2">
                    <i class="fas {{ $icon }} me-1"></i>{{ $label }}
                </span>
            </div>
            <div class="card-body">
                <p style="color: var(--text-primary); white-space: pre-line; line-height: 1.7;">{{ $post->content }}</p>

                @if($post->hashtags && is_array($post->hashtags) && count($post->hashtags) > 0)
                    <div class="mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                        @foreach($post->hashtags as $tag)
                            @if($tag && trim($tag))
                                <span class="badge bg-light text-primary me-1 mb-1 px-2 py-1">#{{ trim($tag, '#') }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Media Files -->
        @if($post->media_files && is_array($post->media_files) && count($post->media_files) > 0)
        <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                    <i class="fas fa-images me-2"></i>Attached Media
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($post->media_files as $file)
                        @php
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                            $relativePath = null;
                            if (str_contains($file, 'public/')) {
                                $relativePath = 'storage/' . substr($file, strpos($file, 'public/') + 7);
                            }
                        @endphp
                        <div class="col-6 col-md-4">
                            @if($relativePath)
                                @if($isVideo)
                                    <video class="w-100 rounded" style="max-height: 300px; object-fit: contain; background:#000;" controls>
                                        <source src="{{ asset($relativePath) }}">
                                    </video>
                                @else
                                    <a href="{{ asset($relativePath) }}" target="_blank">
                                        <img src="{{ asset($relativePath) }}"
                                             class="w-100 rounded"
                                             style="display: block; max-width: 100%; width: auto; height: auto; max-height: 300px; margin: 0 auto;"
                                             alt="Post media"
                                             onerror="this.parentElement.innerHTML='<div style=\'padding:2rem;text-align:center;background:var(--bg-tertiary);border-radius:0.375rem;\'><i class=\'fas fa-image fa-2x text-muted\'></i><br><small class=\'text-muted\'>Image unavailable</small></div>'">
                                    </a>
                                @endif
                            @else
                                <div class="text-center p-3 rounded" style="background: var(--bg-tertiary);">
                                    <i class="fas fa-{{ $isVideo ? 'video' : 'image' }} fa-2x text-muted"></i>
                                    <br><small class="text-muted">{{ basename($file) }}</small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Publications -->
        <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                    <i class="fas fa-share-alt me-2"></i>Platform Publications
                </h6>
            </div>
            <div class="card-body p-0">
                @if($post->publications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($post->publications as $pub)
                            @php
                                $platform = $pub->socialMediaAccount->platform ?? 'unknown';
                                $platformIcons = [
                                    'facebook'  => ['fab fa-facebook',  '#1877f2', 'Facebook'],
                                    'instagram' => ['fab fa-instagram', '#e4405f', 'Instagram'],
                                    'twitter'   => ['fab fa-x-twitter', '#000',    'X (Twitter)'],
                                    'linkedin'  => ['fab fa-linkedin',  '#0077b5', 'LinkedIn'],
                                    'telegram'  => ['fab fa-telegram',  '#0088cc', 'Telegram'],
                                    'discord'   => ['fab fa-discord',   '#7289da', 'Discord'],
                                ];
                                [$iconClass, $iconColor, $platformName] = $platformIcons[$platform] ?? ['fas fa-share-alt','#6c757d',ucfirst($platform)];
                                $pubStatusMap = [
                                    'published' => ['bg-success',          'Published'],
                                    'failed'    => ['bg-danger',           'Failed'],
                                    'pending'   => ['bg-warning text-dark','Pending'],
                                ];
                                [$pubBadge, $pubLabel] = $pubStatusMap[$pub->status] ?? ['bg-secondary', ucfirst($pub->status ?? 'Unknown')];
                            @endphp
                            <div class="list-group-item" style="background: transparent; border-color: var(--border-color);">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="fw-semibold" style="color: var(--text-primary);">
                                            <i class="{{ $iconClass }} me-2" style="color: {{ $iconColor }};"></i>{{ $platformName }}
                                        </span>
                                        @if($pub->socialMediaAccount->username ?? null)
                                            <small class="text-muted ms-2">@{{ $pub->socialMediaAccount->username }}</small>
                                        @endif
                                        @if($pub->published_at)
                                            <div><small class="text-muted">Published {{ $pub->published_at->format('M j, Y \a\t g:i A') }}</small></div>
                                        @endif
                                        @if($pub->status === 'failed' && $pub->error_message)
                                            <div class="mt-1">
                                                <small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>{{ $pub->error_message }}</small>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="badge {{ $pubBadge }}">{{ $pubLabel }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4" style="color: var(--text-muted);">
                        <i class="fas fa-share-alt fa-2x mb-2"></i>
                        <p class="mb-0">This post hasn't been published to any platform yet.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Right: Sidebar -->
    <div class="col-lg-4">

        <!-- Post Details -->
        <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                    <i class="fas fa-info-circle me-2"></i>Post Details
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="row-gap: 0.5rem;">
                    <dt class="col-5" style="color: var(--text-muted); font-weight: 500;">Type</dt>
                    <dd class="col-7 mb-0" style="color: var(--text-primary);">{{ ucfirst($post->post_type ?? 'standard') }}</dd>

                    <dt class="col-5" style="color: var(--text-muted); font-weight: 500;">Status</dt>
                    <dd class="col-7 mb-0"><span class="badge {{ $badgeClass }}">{{ $label }}</span></dd>

                    @if($post->scheduled_at)
                    <dt class="col-5" style="color: var(--text-muted); font-weight: 500;">Scheduled</dt>
                    <dd class="col-7 mb-0" style="color: var(--text-primary);">{{ $post->scheduled_at->format('M j, Y g:i A') }}</dd>
                    @endif

                    <dt class="col-5" style="color: var(--text-muted); font-weight: 500;">Created</dt>
                    <dd class="col-7 mb-0" style="color: var(--text-primary);">{{ $post->created_at->format('M j, Y') }}</dd>

                    <dt class="col-5" style="color: var(--text-muted); font-weight: 500;">Updated</dt>
                    <dd class="col-7 mb-0" style="color: var(--text-primary);">{{ $post->updated_at->diffForHumans() }}</dd>

                    <dt class="col-5" style="color: var(--text-muted); font-weight: 500;">Platforms</dt>
                    <dd class="col-7 mb-0" style="color: var(--text-primary);">{{ $post->publications->count() }} published</dd>
                </dl>
            </div>
        </div>

        <!-- Target Platforms -->
        @if($post->target_platforms && is_array($post->target_platforms) && count($post->target_platforms) > 0)
        <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                    <i class="fas fa-bullseye me-2"></i>Target Platforms
                </h6>
            </div>
            <div class="card-body d-flex flex-wrap gap-2">
                @php
                    $tpIcons = [
                        'facebook'  => ['fab fa-facebook',  '#1877f2'],
                        'instagram' => ['fab fa-instagram', '#e4405f'],
                        'twitter'   => ['fab fa-x-twitter', '#000'],
                        'linkedin'  => ['fab fa-linkedin',  '#0077b5'],
                        'telegram'  => ['fab fa-telegram',  '#0088cc'],
                        'discord'   => ['fab fa-discord',   '#7289da'],
                    ];
                @endphp
                @foreach($post->target_platforms as $tp)
                    @php [$tpIcon, $tpColor] = $tpIcons[$tp] ?? ['fas fa-share-alt','#6c757d']; @endphp
                    <span class="badge px-3 py-2" style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <i class="{{ $tpIcon }} me-1" style="color: {{ $tpColor }};"></i>{{ ucfirst($tp) }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                    <i class="fas fa-cog me-2"></i>Actions
                </h6>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('marketing.posts.edit', $post) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>Edit Post
                </a>
                <a href="{{ route('marketing.posts.analytics', $post) }}" class="btn btn-outline-info">
                    <i class="fas fa-chart-bar me-2"></i>View Analytics
                </a>
                @if($post->publications->where('status', 'published')->count() === 0)
                    <button type="button"
                            class="btn btn-outline-success publish-btn"
                            data-url="{{ route('marketing.posts.publish', $post) }}"
                            data-csrf="{{ csrf_token() }}">
                        <i class="fas fa-share me-2"></i>Publish Now
                    </button>
                @endif
                <form action="{{ route('marketing.posts.destroy', $post) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this post?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Post
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11000;">
    <div id="publishToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body" id="publishToastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function showPublishToast(message, isSuccess) {
        const toastEl   = document.getElementById('publishToast');
        const toastBody = document.getElementById('publishToastBody');
        toastEl.classList.remove('bg-success', 'bg-danger', 'text-white');
        toastEl.classList.add(isSuccess ? 'bg-success' : 'bg-danger', 'text-white');
        toastBody.innerHTML = `<i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>${message}`;
        new bootstrap.Toast(toastEl).show();
    }

    document.querySelectorAll('.publish-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url      = this.dataset.url;
            const csrf     = this.dataset.csrf;
            const original = this.innerHTML;
            this.disabled  = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Publishing...';

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
            .then(({ ok, data }) => {
                if (ok && data.message) {
                    showPublishToast(data.message, true);
                    this.remove();
                } else {
                    showPublishToast(data.error || data.message || 'Something went wrong.', false);
                    this.disabled  = false;
                    this.innerHTML = original;
                }
            })
            .catch(() => {
                showPublishToast('Network error — please try again.', false);
                this.disabled  = false;
                this.innerHTML = original;
            });
        });
    });
});
</script>
@endsection
