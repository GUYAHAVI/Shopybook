@extends('layouts.master')

@section('title', 'Social Media Marketing')

@section('content')
<head>
    
<style>
        /* Main Modal Styling */
    #createPostModal .modal-content {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header.bg-gradient-primary {
        background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
        border-bottom: none;
        padding: 1.5rem;
    }
    
    /* Form Elements - Fixed text color */
    .form-control, .form-select, .form-control-lg {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e0e0e0;
        background-color: #f9f9f9;
        transition: all 0.2s ease;
        color: #111 !important; /* Added explicit text color */
    }
    
    /* Placeholder text color */
    .form-control::placeholder,
    .form-control-lg::placeholder {
        color: #111 !important;
        opacity: 1;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #020258;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.15);
        background-color: #fff;
        color: #111 !important; /* Ensure text stays visible on focus */
    }
    
    /* Textarea specific styling */
    textarea.form-control {
        color: #111 !important;
        background-color: #fff;
        min-height: 200px;
    }
    
    /* Rest of your existing styles... */
    .platform-card {
        transition: all 0.2s ease;
        background: white;
        border: 1px solid #e0e0e0;
    }
    /* Main Modal Styling */
    #createPostModal .modal-content {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header.bg-gradient-primary {
        background: linear-gradient(135deg, #020258 0%, #13e8e9 100%);
        border-bottom: none;
        padding: 1.5rem;
    }
    
    /* Form Elements */
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e0e0e0;
        background-color: #f9f9f9;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #020258;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.15);
        background-color: #fff;
    }
    
    /* Platform Cards */
    .platform-card {
        transition: all 0.2s ease;
        background: white;
        border: 1px solid #e0e0e0;
    }
    
    .platform-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .platform-card input[type="checkbox"]:checked + label {
        background-color: rgba(78, 115, 223, 0.08);
        border-color: #020258;
    }
    
    .platform-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0.03);
    }
    
    /* Platform Colors */
    .text-facebook { color: #3b5998; }
    .text-twitter { color: #1da1f2; }
    .text-instagram { color: #e1306c; }
    .text-linkedin { color: #0077b5; }
    
    /* Media Upload */
    #mediaDropzone {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px dashed #e0e0e0;
        background-color: #f9f9f9;
    }
    
    #mediaDropzone:hover {
        border-color: #020258;
        background-color: rgba(78, 115, 223, 0.03);
    }
    
    #mediaDropzone.dragover {
        background-color: rgba(78, 115, 223, 0.08);
        border-color: #020258;
    }
    
    .media-preview-item {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .media-preview-item img, 
    .media-preview-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .media-preview-item .remove-media {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(0,0,0,0.5);
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
    }
    
    /* Character Counter */
    #charCount {
        font-weight: 600;
        color: #020258;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .modal-xl {
            max-width: 95%;
        }
        
        .bg-light {
            background-color: white !important;
        }
    }
</style>

</head>

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
                                            <div class="flex-grow-1 d-flex align-items-center">
                                                @if($account->platform === 'linkedin' && !empty($account->platform_data['picture']))
                                                    <img src="{{ $account->platform_data['picture'] }}" alt="Profile Picture" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ ucfirst($account->platform) }}</h6>
                                                    
                                                    @if($account->platform === 'linkedin')
                                                        @if(!empty($account->platform_data['name']))
                                                            <div class="text-muted small">{{ $account->platform_data['name'] }}</div>
                                                        @endif
                                                        @if(!empty($account->platform_data['locale']))
                                                            <div class="text-muted small">
                                                                {{ $account->platform_data['locale']['country'] ?? '' }}
                                                                @if(!empty($account->platform_data['locale']['language']))
                                                                    | {{ $account->platform_data['locale']['language'] }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
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
<div class="modal fade" id="createPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <form action="{{ route('marketing.posts.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
                @csrf
                <div class="modal-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-pen-fancy fs-4 me-3"></i>
                        <div>
                            <h5 class="modal-title mb-0">Create New Post</h5>
                            <small class="text-white-50">Reach your audience across multiple platforms</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Left Column - Content Creation -->
                        <div class="col-lg-8 p-4">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Post Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg border-2" name="title" placeholder="Catchy headline that grabs attention" required>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                                    <div class="text-muted small"><span id="charCount">0</span>/2200 characters</div>
                                </div>
                                <textarea class="form-control border-2" name="content" rows="8" placeholder="What's on your mind? Share valuable content with your audience..." 
                                          style="min-height: 200px;" required></textarea>
                                <div class="d-flex justify-content-end mt-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="tooltip" title="Add emoji">
                                        <i class="far fa-smile"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Format text">
                                        <i class="fas fa-text-height"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Hashtags</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" class="form-control" name="hashtags" placeholder="marketing, business, kenya">
                                </div>
                                <div class="form-text">Start typing to see trending hashtag suggestions</div>
                                <div class="hashtag-suggestions mt-2 d-none">
                                    <span class="badge bg-light text-dark me-1 mb-1 cursor-pointer">#digitalmarketing</span>
                                    <span class="badge bg-light text-dark me-1 mb-1 cursor-pointer">#socialmedia</span>
                                    <span class="badge bg-light text-dark me-1 mb-1 cursor-pointer">#entrepreneur</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Media Files</label>
                                <div class="border-2 rounded-3 p-3 text-center position-relative" id="mediaDropzone" style="min-height: 120px; border-style: dashed;">
                                    <input type="file" class="d-none" name="media[]" multiple accept="image/*,video/*" id="mediaInput">
                                    <div class="dropzone-content">
                                        <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-2"></i>
                                        <h6 class="mb-1">Drag & drop files here</h6>
                                        <p class="text-muted small mb-2">or click to browse</p>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('mediaInput').click()">
                                            <i class="fas fa-plus me-1"></i> Add Files
                                        </button>
                                    </div>
                                    <div class="media-preview d-flex flex-wrap gap-2 mt-3"></div>
                                </div>
                                <div class="form-text">Supports JPG, PNG, GIF, MP4 (max 10MB each)</div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Publishing Options -->
                        <div class="col-lg-4 bg-light p-4">
                            <div class="sticky-top" style="top: 20px;">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-white border-0 py-3">
                                        <h6 class="mb-0 fw-semibold"><i class="fas fa-paper-plane me-2"></i> Publishing Options</h6>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Post Type</label>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="post_type" value="immediate" id="postTypeImmediate" autocomplete="off" checked>
                                                <label class="btn btn-outline-primary" for="postTypeImmediate">
                                                    <i class="fas fa-bolt me-1"></i> Post Now
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="post_type" value="scheduled" id="postTypeScheduled" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="postTypeScheduled">
                                                    <i class="fas fa-clock me-1"></i> Schedule
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3" id="scheduledFields" style="display: none;">
                                            <label class="form-label fw-semibold">Schedule Date & Time</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="datetime-local" class="form-control" name="scheduled_at">
                                            </div>
                                            <div class="form-text">Your local timezone: {{ config('app.timezone') }}</div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label fw-semibold mb-0">Target Platforms</label>
                                                <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#connectAccountModal">
                                                    <small>Manage accounts</small>
                                                </button>
                                            </div>
                                            
                                            @if($connectedAccounts->isNotEmpty())
                                                <div class="platform-selection">
                                                    @foreach($connectedAccounts->groupBy('platform') as $platform => $accounts)
                                                    <div class="platform-card mb-2 rounded-3 p-2 border cursor-pointer" data-platform="{{ $platform }}">
                                                        <input type="checkbox" class="platform-checkbox" name="target_platforms[]" value="{{ $platform }}" id="platform_{{ $platform }}">
                                                        <label for="platform_{{ $platform }}" class="d-flex align-items-center mb-0 w-100">
                                                            <div class="platform-icon me-3 bg-white">
                                                                <i class="fab fa-{{ $platform }} fs-4 text-{{ $platform }}"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="fw-semibold">{{ ucfirst($platform) }}</div>
                                                                <div class="small text-muted">{{ $accounts->count() }} connected account(s)</div>
                                                            </div>
                                                            <div class="form-check">
                                                                <i class="fas fa-check-circle text-success platform-check-icon" style="display: none;"></i>
                                                                <i class="far fa-circle text-muted platform-uncheck-icon"></i>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-circle me-2"></i>
                                                    <small>No accounts connected. <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#connectAccountModal">Connect accounts</a> to post.</small>
                                                </div>
                                            @endif
                                        </div>

                                        @if(!auth()->user()->business->isPremium())
                                        <div class="alert alert-warning border-warning mb-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-crown me-2"></i>
                                                <div>
                                                    <strong class="d-block">Premium Feature</strong>
                                                    <small class="d-block">Upgrade to enable auto-posting to all platforms</small>
                                                </div>
                                            </div>
                                        </div>
                                @endif
                                    </div>
                                </div>
                                
                                <!-- Post Summary Preview -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-white border-0 py-3">
                                        <h6 class="mb-0 fw-semibold"><i class="fas fa-eye me-2"></i> Post Preview</h6>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="post-summary">
                                            <!-- Title Preview -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">TITLE</label>
                                                <div class="preview-title text-dark" id="previewTitle">
                                                    <em class="text-muted">Enter a title...</em>
                                                </div>
                                            </div>
                                            
                                            <!-- Content Preview -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">CONTENT</label>
                                                <div class="preview-content text-dark" id="previewContent" style="font-size: 0.9rem; line-height: 1.4;">
                                                    <em class="text-muted">Start typing your content...</em>
                                                </div>
                                            </div>
                                            
                                            <!-- Media Preview -->
                                            <div class="mb-3" id="previewMediaSection" style="display: none;">
                                                <label class="form-label small fw-semibold text-muted">MEDIA</label>
                                                <div class="preview-media d-flex flex-wrap gap-1" id="previewMedia">
                                                    <!-- Media thumbnails will be inserted here -->
                                                </div>
                                            </div>
                                            
                                            <!-- Platforms Preview -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">PLATFORMS</label>
                                                <div class="preview-platforms" id="previewPlatforms">
                                                    <em class="text-muted">Select platforms to post to...</em>
                                                </div>
                                            </div>
                                            
                                            <!-- Schedule Preview -->
                                            <div class="mb-0">
                                                <label class="form-label small fw-semibold text-muted">SCHEDULE</label>
                                                <div class="preview-schedule text-dark" id="previewSchedule">
                                                    <i class="fas fa-bolt text-warning me-1"></i> Post immediately
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                        <i class="fas fa-paper-plane me-2"></i>Publish Post
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        Save as Draft
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips (Bootstrap 5 native JS)
    if (window.bootstrap && typeof bootstrap.Tooltip === 'function') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }

    // Character counter
    const contentTextarea = document.querySelector('textarea[name="content"]');
    const charCount = document.getElementById('charCount');
    
    if (contentTextarea && charCount) {
        // Set initial count
        charCount.textContent = contentTextarea.value.length;
        
        contentTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Post type toggle (Immediate/Scheduled)
    const postTypeRadios = document.querySelectorAll('input[name="post_type"]');
    const scheduledFields = document.getElementById('scheduledFields');
    
    if (postTypeRadios.length && scheduledFields) {
        postTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                scheduledFields.style.display = 
                    this.value === 'scheduled' ? 'block' : 'none';
            });
        });
    }

    // Media upload handling
    const mediaDropzone = document.getElementById('mediaDropzone');
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreview = document.querySelector('.media-preview');
    
    if (mediaDropzone && mediaInput) {
        // Click handler
        mediaDropzone.addEventListener('click', () => mediaInput.click());
        
        // Drag and drop handlers
        mediaDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            mediaDropzone.classList.add('dragover');
        });
        
        mediaDropzone.addEventListener('dragleave', () => {
            mediaDropzone.classList.remove('dragover');
        });
        
        mediaDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            mediaDropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                handleFiles(e.dataTransfer.files);
            }
        });
        
        // File input change handler
        mediaInput.addEventListener('change', () => {
            if (mediaInput.files.length) {
                handleFiles(mediaInput.files);
            }
        });
        
        // Handle uploaded files
        function handleFiles(files) {
            if (!mediaPreview) return;
            
            mediaPreview.innerHTML = '';
            
            Array.from(files).forEach(file => {
                if (!file.type.match('image.*|video.*')) return;
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'media-preview-item';
                    
                    if (file.type.startsWith('image/')) {
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="${file.name}">
                            <div class="remove-media" data-filename="${file.name}">&times;</div>
                        `;
                    } else if (file.type.startsWith('video/')) {
                        previewItem.innerHTML = `
                            <video src="${e.target.result}"></video>
                            <div class="remove-media" data-filename="${file.name}">&times;</div>
                        `;
                    }
                    
                    mediaPreview.appendChild(previewItem);
                    
                    // Add remove event listener
                    previewItem.querySelector('.remove-media').addEventListener('click', function() {
                        removeMedia(this);
                    });
                };
                reader.readAsDataURL(file);
            });
        }
        
        // Remove media item
        function removeMedia(element) {
            const item = element.closest('.media-preview-item');
            if (item) {
                item.remove();
                // Additional logic to update file input if needed
            }
        }
    }

    // Platform card selection - Fixed to handle proper checkbox selection
    document.querySelectorAll('.platform-card').forEach(card => {
        const checkbox = card.querySelector('.platform-checkbox');
        const checkIcon = card.querySelector('.platform-check-icon');
        const uncheckIcon = card.querySelector('.platform-uncheck-icon');
        
        function updateVisualState() {
            if (checkbox.checked) {
                card.classList.add('selected');
                card.style.backgroundColor = 'rgba(78, 115, 223, 0.08)';
                card.style.borderColor = '#020258';
                if (checkIcon) checkIcon.style.display = 'inline';
                if (uncheckIcon) uncheckIcon.style.display = 'none';
            } else {
                card.classList.remove('selected');
                card.style.backgroundColor = '';
                card.style.borderColor = '';
                if (checkIcon) checkIcon.style.display = 'none';
                if (uncheckIcon) uncheckIcon.style.display = 'inline';
            }
        }
        
        // Initialize visual state
        updateVisualState();
        
        // Handle card click (toggle checkbox)
        card.addEventListener('click', function(e) {
            // Don't handle if clicking directly on checkbox or label
            if (e.target.type === 'checkbox' || e.target.tagName === 'LABEL') {
                return;
            }
            
            e.preventDefault();
            checkbox.checked = !checkbox.checked;
            updateVisualState();
        });
        
        // Handle checkbox change (for direct clicks on checkbox)
        checkbox.addEventListener('change', function() {
            updateVisualState();
        });
    });
    
    // Ensure at least one platform is selected before submission
    document.getElementById('postForm').addEventListener('submit', function(e) {
        const selectedPlatforms = document.querySelectorAll('.platform-checkbox:checked');
        if (selectedPlatforms.length === 0) {
            e.preventDefault();
            alert('Please select at least one platform to post to.');
            return false;
        }
    });
});
</script>
@endsection




