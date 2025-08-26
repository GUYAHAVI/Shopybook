@extends('layouts.dash')

@section('title', 'Marketing Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0" style="color: var(--text-primary);">All Marketing Posts</h1>
        <p class="text-muted mb-0">Manage and track your social media posts</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('marketing.social-media') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Social Media
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <i class="fas fa-plus me-2"></i>Create Post
        </button>
    </div>
</div>

<!-- Posts Grid -->
<div class="row">
    @forelse($posts as $post)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100 shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <!-- Post Header -->
                <div class="card-header d-flex justify-content-between align-items-center" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h6 class="mb-0" style="color: var(--text-primary);">{{ Str::limit($post->title, 40) }}</h6>
                        <small style="color: var(--text-muted);">{{ $post->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('marketing.posts.show', $post) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketing.posts.edit', $post) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketing.posts.analytics', $post) }}"><i class="fas fa-chart-bar me-2"></i>Analytics</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('marketing.posts.destroy', $post) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this post?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Post Content -->
                <div class="card-body">
                    <p class="card-text" style="color: var(--text-secondary);">
                        {{ Str::limit($post->content, 150) }}
                    </p>
                    
                    @if($post->hashtags && is_array($post->hashtags) && count($post->hashtags) > 0)
                        <div class="mb-3">
                            @foreach($post->hashtags as $hashtag)
                                @if($hashtag && trim($hashtag))
                                    <span class="badge bg-light text-primary me-1">#{{ trim($hashtag, '#') }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Post Status -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            @if($post->scheduled_at && $post->scheduled_at->isFuture())
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Scheduled
                                </span>
                            @elseif($post->publications->where('status', 'published')->count() > 0)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Published
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-draft2digital me-1"></i>Draft
                                </span>
                            @endif
                        </div>
                        <small style="color: var(--text-muted);">
                            {{ $post->publications->count() }} platform(s)
                        </small>
                    </div>

                    <!-- Platform Icons -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="platform-icons">
                            @foreach($post->publications as $publication)
                                @switch($publication->socialMediaAccount->platform)
                                    @case('facebook')
                                        <i class="fab fa-facebook text-primary me-1" title="Facebook"></i>
                                        @break
                                    @case('instagram')
                                        <i class="fab fa-instagram text-danger me-1" title="Instagram"></i>
                                        @break
                                    @case('twitter')
                                        <i class="fab fa-x-twitter text-dark me-1" title="X (Twitter)"></i>
                                        @break
                                    @case('linkedin')
                                        <i class="fab fa-linkedin text-info me-1" title="LinkedIn"></i>
                                        @break
                                @endswitch
                            @endforeach
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="btn-group btn-group-sm">
                            @if($post->publications->where('status', 'published')->count() === 0 && (!$post->scheduled_at || $post->scheduled_at->isPast()))
                                <form action="{{ route('marketing.posts.publish', $post) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Publish Now">
                                        <i class="fas fa-share"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('marketing.posts.analytics', $post) }}" class="btn btn-sm btn-outline-info" title="View Analytics">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Info -->
                @if($post->scheduled_at)
                    <div class="card-footer" style="background: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                        <small style="color: var(--text-muted);">
                            <i class="fas fa-clock me-1"></i>
                            @if($post->scheduled_at->isFuture())
                                Scheduled for {{ $post->scheduled_at->format('M j, Y \a\t g:i A') }}
                            @else
                                Was scheduled for {{ $post->scheduled_at->format('M j, Y \a\t g:i A') }}
                            @endif
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-share-alt fa-4x text-muted"></i>
                </div>
                <h4 style="color: var(--text-primary);">No posts yet</h4>
                <p class="text-muted mb-4">Start creating engaging content for your social media platforms.</p>
                <a href="{{ route('marketing.social-media') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Your First Post
                </a>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($posts->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>
@endif

<style>
.platform-icons i {
    font-size: 1.1rem;
}

.card {
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px var(--shadow-color);
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
