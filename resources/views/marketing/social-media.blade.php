@extends('layouts.master')

@section('title', 'Social Media Marketing')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Social Media Marketing</h1>
            <p class="text-muted">Manage your social media accounts and create marketing posts</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#connectAccountModal">
                <i class="fas fa-plus me-2"></i>Connect Account
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
                <i class="fas fa-pen me-2"></i>Create Post
            </button>
        </div>
    </div>

    <!-- Premium Feature Notice -->
    @if(!auth()->user()->business->isPremium())
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="fas fa-crown me-3 text-warning" style="font-size: 1.5rem;"></i>
        <div class="flex-grow-1">
            <h5 class="alert-heading mb-1">Unlock Social Media Automation</h5>
            <p class="mb-2">Auto-post to multiple platforms, schedule content, and track engagement with our Premium plan.</p>
            <small class="text-muted">Starting from KSh 2,500/month</small>
        </div>
        <a href="{{ route('billing.upgrade') }}" class="btn btn-warning ms-3">
            <i class="fas fa-arrow-up me-2"></i>Upgrade Now
        </a>
    </div>
    @endif

    <!-- Connected Accounts -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Connected Accounts</h5>
                    <span class="badge bg-secondary">{{ $connectedAccounts->count() }} connected</span>
                </div>
                <div class="card-body">
                    @if($connectedAccounts->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-share-alt text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">No accounts connected yet</h5>
                            <p class="text-muted mb-3">Connect your social media accounts to start auto-posting</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#connectAccountModal">
                                <i class="fas fa-plus me-2"></i>Connect First Account
                            </button>
                        </div>
                    @else
                        <div class="row">
                            @foreach($connectedAccounts as $account)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="platform-icon me-3">
                                                <i class="fab fa-{{ $account->platform_icon }} fa-2x text-{{ $account->platform_color }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ ucfirst($account->platform) }}</h6>
                                                <small class="text-muted">@{{ $account->username ?? 'Unknown' }}</small>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-sync me-2"></i>Refresh Token</a></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-unlink me-2"></i>Disconnect</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $account->is_active ? 'success' : 'danger' }}">
                                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <small class="text-muted">
                                                Last connected {{ $account->last_connected_at?->diffForHumans() ?? 'Never' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Marketing Posts</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>All Status</option>
                            <option>Published</option>
                            <option>Scheduled</option>
                            <option>Draft</option>
                            <option>Failed</option>
                        </select>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>All Platforms</option>
                            <option>Facebook</option>
                            <option>Instagram</option>
                            <option>Twitter</option>
                            <option>LinkedIn</option>
                            <option>TikTok</option>
                            <option>YouTube</option>
                            <option>Pinterest</option>
                            <option>Telegram</option>
                            <option>Discord</option>
                            <option>Reddit</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentPosts->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">No posts created yet</h5>
                            <p class="text-muted mb-3">Create your first marketing post to reach your audience</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
                                <i class="fas fa-pen me-2"></i>Create First Post
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Post</th>
                                        <th>Platforms</th>
                                        <th>Status</th>
                                        <th>Scheduled</th>
                                        <th>Engagement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPosts as $post)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                @if($post->media_files)
                                                <img src="{{ $post->media_files[0] ?? '' }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-file-text text-muted"></i>
                                                </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">{{ $post->title }}</h6>
                                                    <p class="text-muted small mb-0">{{ Str::limit($post->content, 80) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @foreach($post->target_platforms as $platform)
                                                <i class="fab fa-{{ $platform }} text-muted" title="{{ ucfirst($platform) }}"></i>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $post->status_color }}">{{ ucfirst($post->status) }}</span>
                                        </td>
                                        <td>
                                            @if($post->scheduled_at)
                                                <small>{{ $post->scheduled_at->format('M j, Y g:i A') }}</small>
                                            @else
                                                <small class="text-muted">Immediate</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($post->engagement_data)
                                                <small>
                                                    <i class="fas fa-heart text-danger"></i> {{ $post->engagement_data['likes'] ?? 0 }}
                                                    <i class="fas fa-share text-primary ms-2"></i> {{ $post->engagement_data['shares'] ?? 0 }}
                                                </small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        {{ $recentPosts->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Connect Account Modal -->
<div class="modal fade" id="connectAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Connect Social Media Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Choose a platform to connect your business account</p>
                
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'facebook') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-facebook fa-2x mb-2"></i>
                            <span>Facebook</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'instagram') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-instagram fa-2x mb-2"></i>
                            <span>Instagram</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'twitter') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-twitter fa-2x mb-2"></i>
                            <span>Twitter</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'linkedin') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-linkedin fa-2x mb-2"></i>
                            <span>LinkedIn</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'tiktok') }}" class="btn btn-outline-dark w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-tiktok fa-2x mb-2"></i>
                            <span>TikTok</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'youtube') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-youtube fa-2x mb-2"></i>
                            <span>YouTube</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'pinterest') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-pinterest fa-2x mb-2"></i>
                            <span>Pinterest</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'telegram') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-telegram fa-2x mb-2"></i>
                            <span>Telegram</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'discord') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-discord fa-2x mb-2"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('social.connect', 'reddit') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fab fa-reddit fa-2x mb-2"></i>
                            <span>Reddit</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <button class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" disabled>
                            <i class="fab fa-whatsapp fa-2x mb-2"></i>
                            <span>WhatsApp</span>
                            <small class="text-muted">Coming Soon</small>
                        </button>
                    </div>
                    <div class="col-6 col-md-4">
                        <button class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" disabled>
                            <i class="fab fa-snapchat fa-2x mb-2"></i>
                            <span>Snapchat</span>
                            <small class="text-muted">Coming Soon</small>
                        </button>
                    </div>
                </div>

                @if(!auth()->user()->business->isPremium())
                <div class="alert alert-warning mt-4">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Premium Feature:</strong> Connect unlimited accounts and enable auto-posting with Premium.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('marketing.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Marketing Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Post Content -->
                            <div class="mb-3">
                                <label class="form-label">Post Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea class="form-control" name="content" rows="6" placeholder="Write your post content here..." required></textarea>
                                <div class="form-text">Character count: <span id="charCount">0</span>/2200</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hashtags</label>
                                <input type="text" class="form-control" name="hashtags" placeholder="#marketing #business #kenya">
                                <div class="form-text">Separate hashtags with spaces</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Media Files</label>
                                <input type="file" class="form-control" name="media[]" multiple accept="image/*,video/*">
                                <div class="form-text">Upload images or videos (max 10MB each)</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Publishing Options -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Publishing Options</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Post Type</label>
                                        <select class="form-select" name="post_type">
                                            <option value="immediate">Post Immediately</option>
                                            <option value="scheduled">Schedule for Later</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="scheduledFields" style="display: none;">
                                        <label class="form-label">Schedule Date & Time</label>
                                        <input type="datetime-local" class="form-control" name="scheduled_at">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Target Platforms</label>
                                        @if($connectedAccounts->isNotEmpty())
                                            @foreach($connectedAccounts->groupBy('platform') as $platform => $accounts)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="target_platforms[]" value="{{ $platform }}" id="platform_{{ $platform }}">
                                                <label class="form-check-label" for="platform_{{ $platform }}">
                                                    <i class="fab fa-{{ $platform }} me-2"></i>{{ ucfirst($platform) }}
                                                    <span class="badge bg-secondary ms-2">{{ $accounts->count() }}</span>
                                                </label>
                                            </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted small">No accounts connected. <a href="#" data-bs-toggle="modal" data-bs-target="#connectAccountModal">Connect accounts</a> to post.</p>
                                        @endif
                                    </div>

                                    @if(!auth()->user()->business->isPremium())
                                    <div class="alert alert-warning">
                                        <i class="fas fa-lock me-2"></i>
                                        <strong>Premium:</strong> Auto-posting requires Premium plan.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Create Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.platform-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.05);
}

.modal-lg {
    max-width: 900px;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const contentTextarea = document.querySelector('textarea[name="content"]');
    const charCount = document.getElementById('charCount');
    
    if (contentTextarea && charCount) {
        contentTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Show/hide scheduled fields
    const postTypeSelect = document.querySelector('select[name="post_type"]');
    const scheduledFields = document.getElementById('scheduledFields');
    
    if (postTypeSelect && scheduledFields) {
        postTypeSelect.addEventListener('change', function() {
            if (this.value === 'scheduled') {
                scheduledFields.style.display = 'block';
            } else {
                scheduledFields.style.display = 'none';
            }
        });
    }

    // File upload preview
    const fileInput = document.querySelector('input[name="media[]"]');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            // Add file preview logic here
            console.log('Files selected:', this.files.length);
        });
    }
});
</script>
@endpush
