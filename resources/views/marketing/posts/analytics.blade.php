@extends('layouts.dash')

@section('title', 'Post Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0" style="color: var(--text-primary);">Post Analytics</h1>
        <p class="text-muted mb-0">{{ Str::limit($post->title, 60) }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('marketing.posts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Posts
        </a>
        <a href="{{ route('marketing.posts.edit', $post) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Edit Post
        </a>
    </div>
</div>

<!-- Post Summary Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="mb-2" style="color: var(--text-secondary);">{{ Str::limit($post->content, 200) }}</p>
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @if($post->hashtags && is_array($post->hashtags))
                                @foreach($post->hashtags as $tag)
                                    @if($tag && trim($tag))
                                        <span class="badge bg-light text-primary">#{{ trim($tag, '#') }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <small style="color: var(--text-muted);">
                            <i class="fas fa-calendar me-1"></i>Created {{ $post->created_at->format('M j, Y \a\t g:i A') }}
                            @if($post->scheduled_at)
                                &nbsp;&bull;&nbsp;<i class="fas fa-clock me-1"></i>Scheduled for {{ $post->scheduled_at->format('M j, Y \a\t g:i A') }}
                            @endif
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @php
                            $statusMap = [
                                'published'           => ['bg-success', 'fa-check-circle', 'Published'],
                                'partially_published' => ['bg-warning text-dark', 'fa-exclamation-circle', 'Partially Published'],
                                'scheduled'           => ['bg-info', 'fa-clock', 'Scheduled'],
                                'draft'               => ['bg-secondary', 'fa-file-alt', 'Draft'],
                                'failed'              => ['bg-danger', 'fa-times-circle', 'Failed'],
                            ];
                            [$badgeClass, $icon, $label] = $statusMap[$post->status] ?? ['bg-secondary', 'fa-question', ucfirst($post->status)];
                        @endphp
                        <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                            <i class="fas {{ $icon }} me-1"></i>{{ $label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Engagement Totals -->
<div class="row mb-4">
    @php
        $metrics = [
            ['key' => 'reach',    'icon' => 'fa-eye',        'label' => 'Reach',    'color' => '#0d6efd'],
            ['key' => 'likes',    'icon' => 'fa-heart',      'label' => 'Likes',    'color' => '#dc3545'],
            ['key' => 'comments', 'icon' => 'fa-comment',    'label' => 'Comments', 'color' => '#198754'],
            ['key' => 'shares',   'icon' => 'fa-share-alt',  'label' => 'Shares',   'color' => '#fd7e14'],
        ];
    @endphp
    @foreach($metrics as $m)
        <div class="col-6 col-md-3 mb-3">
            <div class="card text-center shadow-sm h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-body py-3">
                    <i class="fas {{ $m['icon'] }} fa-2x mb-2" style="color: {{ $m['color'] }};"></i>
                    <div class="h3 mb-0 fw-bold" style="color: var(--text-primary);">
                        {{ number_format($totalEngagement[$m['key']]) }}
                    </div>
                    <small style="color: var(--text-muted);">{{ $m['label'] }}</small>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Per-Platform Breakdown -->
<div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
    <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-share-alt me-2"></i>Platform Breakdown
        </h6>
    </div>
    <div class="card-body p-0">
        @if($post->publications->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--bg-tertiary);">
                        <tr>
                            <th style="color: var(--text-muted); font-weight: 600;">Platform</th>
                            <th style="color: var(--text-muted); font-weight: 600;">Status</th>
                            <th style="color: var(--text-muted); font-weight: 600;">Published At</th>
                            <th style="color: var(--text-muted); font-weight: 600; text-align:right;">Reach</th>
                            <th style="color: var(--text-muted); font-weight: 600; text-align:right;">Likes</th>
                            <th style="color: var(--text-muted); font-weight: 600; text-align:right;">Comments</th>
                            <th style="color: var(--text-muted); font-weight: 600; text-align:right;">Shares</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                [$iconClass, $iconColor, $platformName] = $platformIcons[$platform] ?? ['fas fa-share-alt', '#6c757d', ucfirst($platform)];
                                $em = $pub->engagement_metrics ?? [];
                                $pubStatusMap = [
                                    'published' => ['bg-success', 'Published'],
                                    'failed'    => ['bg-danger',  'Failed'],
                                    'pending'   => ['bg-warning text-dark', 'Pending'],
                                ];
                                [$pubBadge, $pubLabel] = $pubStatusMap[$pub->status] ?? ['bg-secondary', ucfirst($pub->status ?? 'Unknown')];
                            @endphp
                            <tr>
                                <td style="color: var(--text-primary);">
                                    <i class="{{ $iconClass }} me-2" style="color: {{ $iconColor }};"></i>
                                    {{ $platformName }}
                                    @if($pub->socialMediaAccount->username ?? null)
                                        <small class="text-muted d-block">{{ $pub->socialMediaAccount->username }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $pubBadge }}">{{ $pubLabel }}</span>
                                    @if($pub->status === 'failed' && $pub->error_message)
                                        <i class="fas fa-info-circle text-danger ms-1"
                                           data-bs-toggle="tooltip"
                                           title="{{ $pub->error_message }}"></i>
                                    @endif
                                </td>
                                <td style="color: var(--text-muted);">
                                    {{ $pub->published_at ? $pub->published_at->format('M j, Y g:i A') : '—' }}
                                </td>
                                <td class="text-end" style="color: var(--text-primary);">{{ number_format($em['reach'] ?? 0) }}</td>
                                <td class="text-end" style="color: var(--text-primary);">{{ number_format($em['likes'] ?? 0) }}</td>
                                <td class="text-end" style="color: var(--text-primary);">{{ number_format($em['comments'] ?? 0) }}</td>
                                <td class="text-end" style="color: var(--text-primary);">{{ number_format($em['shares'] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5" style="color: var(--text-muted);">
                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                <p class="mb-0">No platform publications found for this post.</p>
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
                    $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                    // Convert absolute path to a storage URL if possible
                    $relativePath = null;
                    if (str_contains($file, 'public/')) {
                        $relativePath = 'storage/' . substr($file, strpos($file, 'public/') + 7);
                    }
                @endphp
                <div class="col-6 col-md-3">
                    @if($relativePath)
                        @if($isVideo)
                            <video class="w-100 rounded" style="max-height: 240px; object-fit: contain; background:#000;" controls>
                                <source src="{{ asset($relativePath) }}">
                            </video>
                        @else
                            <img src="{{ asset($relativePath) }}"
                                 class="w-100 rounded"
                                 style="display: block; max-width: 100%; width: auto; height: auto; max-height: 240px; margin: 0 auto;"
                                 alt="Post media"
                                 onerror="this.parentElement.innerHTML='<div class=\'text-center p-3 rounded\' style=\'background:var(--bg-tertiary);\'><i class=\'fas fa-image fa-2x text-muted\'></i><br><small class=\'text-muted\'>Image unavailable</small></div>'">
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

<!-- Target Platforms -->
@if($post->target_platforms && is_array($post->target_platforms) && count($post->target_platforms) > 0)
<div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
    <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-bullseye me-2"></i>Target Platforms
        </h6>
    </div>
    <div class="card-body d-flex flex-wrap gap-2">
        @foreach($post->target_platforms as $tp)
            @php
                $icons = [
                    'facebook'  => ['fab fa-facebook',  '#1877f2'],
                    'instagram' => ['fab fa-instagram', '#e4405f'],
                    'twitter'   => ['fab fa-x-twitter', '#000'],
                    'linkedin'  => ['fab fa-linkedin',  '#0077b5'],
                    'telegram'  => ['fab fa-telegram',  '#0088cc'],
                    'discord'   => ['fab fa-discord',   '#7289da'],
                ];
                [$tpIcon, $tpColor] = $icons[$tp] ?? ['fas fa-share-alt', '#6c757d'];
            @endphp
            <span class="badge fs-6 px-3 py-2" style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                <i class="{{ $tpIcon }} me-2" style="color: {{ $tpColor }};"></i>{{ ucfirst($tp) }}
            </span>
        @endforeach
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Init Bootstrap tooltips for error messages
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endsection
