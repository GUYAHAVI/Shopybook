@extends('layouts.dash')

@section('title', 'Website Builder')

@section('content')
<style>
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--shadow-color);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .page-item {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }
    
    .page-item:hover {
        background: var(--bg-tertiary);
        box-shadow: 0 2px 8px var(--shadow-color);
    }
    
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .status-badge.published {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }
    
    .status-badge.draft {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }
    
    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px var(--shadow-color);
    }
    
    @media (max-width: 768px) {
        .stat-value {
            font-size: 1.5rem;
        }
        
        .page-item {
            padding: 1rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2" style="color: var(--text-primary); font-weight: 700;">
                        <i class="fas fa-globe me-2" style="color: var(--primary-color);"></i>Website Builder
                    </h1>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">
                        Manage your website for <strong>{{ $website->business->name }}</strong>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Publish Status -->
                    @if($website->is_active)
                    <span class="status-badge published">
                        <i class="fas fa-check-circle"></i>
                        Published
                    </span>
                    @else
                    <span class="status-badge draft">
                        <i class="fas fa-exclamation-circle"></i>
                        Draft
                    </span>
                    @endif

                    <!-- Actions -->
                    <a href="{{ route('website.builder.preview') }}" target="_blank" 
                       class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        <span class="d-none d-sm-inline">Preview</span>
                    </a>

                    @if($website->is_active)
                    <button onclick="unpublishWebsite()" 
                            class="btn btn-secondary btn-sm">
                        <i class="fas fa-eye-slash me-1"></i>
                        <span class="d-none d-sm-inline">Unpublish</span>
                    </button>
                    @else
                    <button onclick="publishWebsite()" 
                            class="btn btn-sm"
                            style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                   border: none; 
                                   color: white; 
                                   font-weight: 600;">
                        <i class="fas fa-rocket me-1"></i>
                        Publish Website
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- AI Auto-Build Banner for Enterprise Users -->
        @if($business->isEnterprise())
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; position: relative; overflow: hidden;">
                <!-- Decorative Elements -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                
                <div class="card-body p-4" style="position: relative; z-index: 1;">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-start gap-3">
                                <div style="font-size: 3rem; line-height: 1;">🤖</div>
                                <div>
                                    <h4 class="mb-2" style="color: white; font-weight: 700;">
                                        <i class="fas fa-crown me-2" style="color: #fbbf24;"></i>Want to Rebuild with AI?
                                    </h4>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 1rem;">
                                        Let Claude AI recreate your website from scratch with fresh content, new pages, and optimized SEO. 
                                        Your current website will be replaced.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <button type="button" class="btn btn-light btn-lg px-4" onclick="confirmAIRebuild()" 
                                    style="font-weight: 700; box-shadow: 0 8px 20px rgba(0,0,0,0.2);">
                                <i class="fas fa-magic me-2"></i>Rebuild with AI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Sidebar -->
        <div class="col-12 col-lg-4 col-xl-3">
            <!-- Quick Stats -->
            <div class="card mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <h3 class="mb-0 h6" style="color: var(--text-primary); font-weight: 600;">
                        <i class="fas fa-chart-line me-2" style="color: var(--primary-color);"></i>Quick Stats
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        <div class="stat-card">
                            <div class="stat-value" style="color: var(--primary-color);">
                                {{ $website->pages->count() }}
                            </div>
                            <div class="stat-label">Total Pages</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" style="color: var(--success-color);">
                                {{ $website->pages->where('is_published', true)->count() }}
                            </div>
                            <div class="stat-label">Published Pages</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" style="color: var(--info-color);">
                                {{ $website->theme->name ?? 'N/A' }}
                            </div>
                            <div class="stat-label">Current Theme</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Website URL -->
            <div class="card mb-4" style="background: linear-gradient(135deg, rgba(2, 2, 88, 0.05), rgba(19, 232, 233, 0.05)); border: 1px solid var(--border-color);">
                <div class="card-body p-3">
                    <h3 class="mb-2 h6" style="color: var(--text-primary); font-weight: 600;">
                        Your Website URL
                    </h3>
                    <div class="input-group input-group-sm">
                        <input type="text" value="{{ $website->url }}" readonly 
                               class="form-control" style="font-size: 0.85rem;">
                        <button onclick="copyUrl('{{ $website->url }}')" 
                                class="btn btn-primary">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- AI Guidance Panel -->
            <div class="card mb-3" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 1px solid #667eea;">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid #667eea;">
                    <h3 class="mb-0 h6" style="color: #667eea; font-weight: 600;">
                        <i class="fas fa-robot me-2"></i>AI Website Assistant
                    </h3>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted mb-3" style="font-size: 0.875rem;">
                        Get personalized guidance from Claude AI to build the perfect website for your business.
                    </p>
                    <button onclick="loadAIGuidance()" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="fas fa-magic me-2"></i>Get AI Guidance
                    </button>
                    <div id="aiGuidanceContent" class="mt-3 d-none">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0" style="font-size: 0.875rem;">Claude is analyzing your business...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <h3 class="mb-0 h6" style="color: var(--text-primary); font-weight: 600;">
                        <i class="fas fa-bolt me-2" style="color: var(--warning-color);"></i>Quick Actions
                    </h3>
                </div>
                <div class="card-body p-2">
                    <div class="d-flex flex-column gap-2">
                        <button onclick="openThemeSelector()" 
                                class="btn btn-outline-primary btn-sm text-start">
                            <i class="fas fa-palette me-2"></i>
                            Change Theme
                        </button>
                        <button onclick="openNewPageModal()" 
                                class="btn btn-outline-success btn-sm text-start">
                            <i class="fas fa-plus me-2"></i>
                            Add New Page
                        </button>
                        <a href="{{ route('website.builder.setup') }}" 
                           class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fas fa-cog me-2"></i>
                            Settings
                        </a>
                        <hr class="my-2">
                        <button onclick="confirmDeleteWebsite()" 
                                class="btn btn-outline-danger btn-sm text-start">
                            <i class="fas fa-trash-alt me-2"></i>
                            Delete Website
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-12 col-lg-8 col-xl-9">
            <!-- Pages List -->
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="mb-0 h5" style="color: var(--text-primary); font-weight: 600;">
                            <i class="fas fa-file-alt me-2"></i>Your Pages
                        </h2>
                        <button onclick="openNewPageModal()" 
                                class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            <span class="d-none d-sm-inline">New Page</span>
                        </button>
                    </div>
                </div>

                <div class="card-body p-3">
                    @forelse($website->pages as $page)
                    <div class="page-item mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <h3 class="mb-0 h6" style="color: var(--text-primary); font-weight: 600;">
                                        {{ $page->title }}
                                    </h3>
                                    @if($page->is_homepage)
                                    <span class="badge" style="background: var(--info-color); font-size: 0.7rem;">
                                        Homepage
                                    </span>
                                    @endif
                                    @if($page->is_published)
                                    <span class="badge" style="background: var(--success-color); font-size: 0.7rem;">
                                        Published
                                    </span>
                                    @else
                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                        Draft
                                    </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 0.85rem;">
                                    <span>
                                        <i class="fas fa-layer-group me-1"></i>
                                        {{ $page->sections->count() }} sections
                                    </span>
                                    <span>•</span>
                                    <span>
                                        <i class="fas fa-link me-1"></i>
                                        /{{ $page->slug }}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('website.builder.pages.edit', $page) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    <span class="d-none d-sm-inline">Edit</span>
                                </a>
                                @if(!$page->is_homepage)
                                <button onclick="deletePage({{ $page->id }})" 
                                        class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div style="font-size: 4rem;" class="mb-3">📄</div>
                        <h3 class="h5 mb-2" style="color: var(--text-primary); font-weight: 600;">
                            No pages yet
                        </h3>
                        <p class="text-muted mb-4" style="font-size: 0.9rem;">
                            Create your first page to get started with your website
                        </p>
                        <button onclick="openNewPageModal()" 
                                class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Page
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<script>
setTimeout(() => {
    const toast = document.querySelector('.alert-success');
    if (toast) {
        const bsAlert = new bootstrap.Alert(toast);
        bsAlert.close();
    }
}, 3000);
</script>
@endif

@push('scripts')
<script>
function openThemeSelector() {
    const modal = new bootstrap.Modal(document.getElementById('themeSelectorModal'));
    modal.show();
}

function confirmAIRebuild() {
    const confirmed = confirm(
        '🤖 AI Website Rebuild\n\n' +
        'Claude AI will:\n' +
        '• Delete your current website\n' +
        '• Build a brand new website from scratch\n' +
        '• Generate 5-7 fresh pages with new content\n' +
        '• Apply professional design and SEO\n\n' +
        'Your current website will be REPLACED.\n\n' +
        'Continue with AI rebuild?'
    );

    if (!confirmed) {
        return;
    }

    // Redirect to setup page with rebuild flag
    window.location.href = '{{ route("website.builder.setup") }}?rebuild=ai';
}

function selectTheme(themeId, themeName) {
    // Visual feedback
    document.querySelectorAll('.theme-card').forEach(card => {
        card.classList.remove('active');
    });
    document.querySelector(`[data-theme-id="${themeId}"]`).classList.add('active');
}

function previewTheme(themeId) {
    window.open(`/website-builder/preview-theme/${themeId}`, '_blank');
}

function applyTheme(themeId, themeName) {
    if (!confirm(`Switch to "${themeName}" theme? This will change the look of your website.`)) {
        return;
    }
    
    fetch('/website-builder/change-theme', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ theme_id: themeId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(`✨ Theme changed to "${themeName}"!`, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Failed to change theme: ' + (data.message || 'Unknown error'), 'danger');
        }
    })
    .catch(err => {
        showToast('An error occurred while changing theme', 'danger');
    });
}

function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    const container = document.body;
    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = container.lastElementChild;
    const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

function publishWebsite() {
    if (confirm('Ready to make your website live?')) {
        fetch('{{ route("website.builder.publish") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Website published successfully! 🎉', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to publish: ' + data.message, 'danger');
            }
        })
        .catch(err => {
            showToast('An error occurred', 'danger');
        });
    }
}

function unpublishWebsite() {
    if (confirm('Are you sure you want to unpublish your website?')) {
        fetch('{{ route("website.builder.unpublish") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Website unpublished', 'warning');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to unpublish', 'danger');
            }
        })
        .catch(err => {
            showToast('An error occurred', 'danger');
        });
    }
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        showToast('URL copied to clipboard!', 'success');
    }).catch(() => {
        showToast('Failed to copy URL', 'danger');
    });
}

function deletePage(pageId) {
    if (confirm('Are you sure you want to delete this page? This action cannot be undone.')) {
        fetch(`/website-builder/pages/${pageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Page deleted successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to delete page', 'danger');
            }
        })
        .catch(err => {
            showToast('An error occurred', 'danger');
        });
    }
}

function openNewPageModal() {
    const title = prompt('Enter page title:');
    if (title && title.trim()) {
        fetch('{{ route("website.builder.pages.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ 
                title: title.trim(), 
                is_published: false 
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Page created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = `/website-builder/pages/${data.page.id}/edit`;
                }, 1000);
            } else {
                showToast('Failed to create page: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(err => {
            showToast('An error occurred', 'danger');
        });
    }
}
</script>
@endpush

<!-- Theme Selector Modal -->
<div class="modal fade" id="themeSelectorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" style="color: var(--text-primary);">
                    <i class="fas fa-palette me-2" style="color: var(--primary-color);"></i>
                    Choose Your Theme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    @foreach($themes as $theme)
                    <div class="col-md-6 col-lg-4">
                        <div class="theme-card {{ $website->theme_id == $theme->id ? 'active' : '' }}" 
                             data-theme-id="{{ $theme->id }}"
                             onclick="selectTheme({{ $theme->id }}, '{{ $theme->name }}')">
                            <!-- Theme Preview Image -->
                            <div class="theme-preview-img">
                                @if($theme->preview_image)
                                    <img src="{{ $theme->preview_image }}" alt="{{ $theme->name }}" loading="lazy">
                                @else
                                    <div class="theme-placeholder" style="background: linear-gradient(135deg, {{ $theme->default_colors['primary'] ?? '#4F46E5' }} 0%, {{ $theme->default_colors['secondary'] ?? '#6366F1' }} 100%)">
                                        <i class="fas fa-paint-brush fa-3x" style="color: white; opacity: 0.5;"></i>
                                    </div>
                                @endif
                                
                                <!-- Active Badge -->
                                @if($website->theme_id == $theme->id)
                                <div class="active-badge">
                                    <i class="fas fa-check-circle"></i> Active
                                </div>
                                @endif
                            </div>
                            
                            <!-- Theme Info -->
                            <div class="theme-info">
                                <h6 class="theme-name">{{ $theme->name }}</h6>
                                <p class="theme-description">{{ $theme->description }}</p>
                                
                                <!-- Theme Meta -->
                                <div class="theme-meta">
                                    <span class="badge" style="background: {{ $theme->default_colors['primary'] }}20; color: {{ $theme->default_colors['primary'] }};">
                                        {{ ucfirst($theme->style) }}
                                    </span>
                                    @if(!$theme->is_free)
                                    <span class="badge bg-warning">Premium</span>
                                    @endif
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="theme-actions mt-3">
                                    <button class="btn btn-sm btn-outline-primary w-100 mb-2" 
                                            onclick="event.stopPropagation(); previewTheme({{ $theme->id }})">
                                        <i class="fas fa-eye me-1"></i> Preview
                                    </button>
                                    @if($website->theme_id != $theme->id)
                                    <button class="btn btn-sm btn-primary w-100" 
                                            onclick="event.stopPropagation(); applyTheme({{ $theme->id }}, '{{ $theme->name }}')">
                                        <i class="fas fa-check me-1"></i> Select Theme
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.theme-card {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    height: 100%;
}

.theme-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    border-color: var(--primary-color);
}

.theme-card.active {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.theme-preview-img {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f5f5f5;
}

@media (min-width: 768px) {
    .theme-preview-img {
        height: 250px;
    }
}

@media (min-width: 1200px) {
    .theme-preview-img {
        height: 280px;
    }
}

.theme-preview-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.theme-card:hover .theme-preview-img img {
    transform: scale(1.05);
}

.theme-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.active-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--success-color);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.theme-info {
    padding: 1rem;
}

.theme-name {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.theme-description {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

    .theme-meta {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
</style>

@push('scripts')
<script>
// AI Guidance Function
function loadAIGuidance() {
    const guidanceContent = document.getElementById('aiGuidanceContent');
    guidanceContent.classList.remove('d-none');
    guidanceContent.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 mb-0" style="font-size: 0.875rem;">Claude is analyzing your business...</p>
        </div>
    `;

    fetch('{{ route("website.builder.ai.guidance") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.guidance) {
            // Convert markdown-style text to HTML
            let guidanceHTML = data.guidance
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n/g, '<br>')
                .replace(/(\d+\.\s)/g, '<br><strong>$1</strong>');
            
            guidanceContent.innerHTML = `
                <div class="alert alert-light" style="font-size: 0.875rem; max-height: 400px; overflow-y: auto;">
                    ${guidanceHTML}
                </div>
                <button onclick="document.getElementById('aiGuidanceContent').classList.add('d-none')" 
                        class="btn btn-sm btn-outline-secondary w-100">
                    Hide Guidance
                </button>
            `;
        } else {
            guidanceContent.innerHTML = `
                <p class="text-danger mb-0" style="font-size: 0.875rem;">
                    Unable to load guidance. Please try again later.
                </p>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        guidanceContent.innerHTML = `
            <p class="text-danger mb-0" style="font-size: 0.875rem;">
                Failed to load AI guidance. Please try again.
            </p>
        `;
    });
}

// Delete Website Function
function confirmDeleteWebsite() {
    const confirmed = confirm(
        '⚠️ WARNING: This will permanently delete your ENTIRE website!\n\n' +
        'This includes:\n' +
        '• All pages\n' +
        '• All sections and content\n' +
        '• All settings and customizations\n\n' +
        'This action CANNOT be undone.\n\n' +
        'Are you absolutely sure you want to delete your website?'
    );

    if (!confirmed) {
        return;
    }

    // Second confirmation for safety
    const finalConfirm = confirm(
        '🛑 FINAL CONFIRMATION\n\n' +
        'This is your last chance to cancel.\n\n' +
        'Type YES in your mind and click OK to proceed with deletion.'
    );

    if (!finalConfirm) {
        return;
    }

    // Show loading state
    const loadingModal = document.createElement('div');
    loadingModal.className = 'modal fade show';
    loadingModal.style.display = 'block';
    loadingModal.style.backgroundColor = 'rgba(0,0,0,0.8)';
    loadingModal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #dc3545; color: white; border: none;">
                <div class="modal-body text-center p-5">
                    <i class="fas fa-trash-alt fa-3x mb-4"></i>
                    <h4 class="mb-3">Deleting Website...</h4>
                    <p class="mb-0">Please wait while we remove all data.</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingModal);

    // Call delete endpoint
    const deleteUrl = '{{ route("website.builder.delete") }}';
    console.log('Delete URL:', deleteUrl);
    
    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json().then(data => ({
            status: response.status,
            ok: response.ok,
            data: data
        }));
    })
    .then(result => {
        loadingModal.remove();
        console.log('Full response:', result);

        if (result.ok && result.data.success) {
            // Show success message
            const successModal = document.createElement('div');
            successModal.className = 'modal fade show';
            successModal.style.display = 'block';
            successModal.style.backgroundColor = 'rgba(0,0,0,0.8)';
            successModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-check-circle me-2"></i>Website Deleted
                            </h5>
                        </div>
                        <div class="modal-body text-center p-4">
                            <p class="mb-3">Your website has been successfully deleted.</p>
                            <p class="mb-0">Redirecting to setup page...</p>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(successModal);

            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = result.data.redirect_url || '{{ route("website-configurator.step1") }}';
            }, 2000);
        } else {
            const errorMsg = result.data.error || result.data.message || 'Failed to delete website';
            console.error('Delete failed:', errorMsg);
            alert('❌ Error: ' + errorMsg);
        }
    })
    .catch(error => {
        loadingModal.remove();
        console.error('Delete error:', error);
        console.error('Error stack:', error.stack);
        alert('❌ An error occurred while deleting the website. Check console for details.');
    });
}
</script>
@endpush

@endsection
