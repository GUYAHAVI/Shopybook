@extends('layouts.dash')

@section('title', 'Edit Page: ' . $page->title)

@section('content')
<style>
    .section-card {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
        cursor: move;
    }
    
    .section-card:hover {
        box-shadow: 0 4px 12px var(--shadow-color);
        transform: translateY(-2px);
    }
    
    .section-card.dragging {
        opacity: 0.5;
    }
    
    .section-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .section-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .section-actions .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .add-section-card {
        background: var(--bg-tertiary);
        border: 2px dashed var(--border-color);
        border-radius: 0.75rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .add-section-card:hover {
        border-color: var(--primary-color);
        background: rgba(2, 2, 88, 0.05);
    }
    
    .section-type-option {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .section-type-option:hover {
        border-color: var(--primary-color);
        box-shadow: 0 2px 8px var(--shadow-color);
    }
    
    .section-type-option.selected {
        border-color: var(--primary-color);
        background: rgba(2, 2, 88, 0.05);
    }
    
    @media (max-width: 768px) {
        .section-actions {
            flex-wrap: wrap;
        }
        
        .section-card {
            padding: 0.75rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <a href="{{ route('website.builder.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="h3 mb-0" style="color: var(--text-primary); font-weight: 700;">
                            <i class="fas fa-edit me-2" style="color: var(--primary-color);"></i>{{ $page->title }}
                        </h1>
                    </div>
                    <p class="text-muted mb-0 ms-5" style="font-size: 0.9rem;">
                        <i class="fas fa-link me-1"></i>{{ $page->website->url }}/{{ $page->slug }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($page->is_published)
                    <span class="badge" style="background: var(--success-color); padding: 0.5rem 1rem;">
                        <i class="fas fa-check-circle me-1"></i>Published
                    </span>
                    @else
                    <span class="badge bg-secondary" style="padding: 0.5rem 1rem;">
                        <i class="fas fa-clock me-1"></i>Draft
                    </span>
                    @endif
                    
                    <a href="{{ $page->website->url }}/{{ $page->slug }}" target="_blank" 
                       class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-eye me-1"></i>Preview
                    </a>
                    
                    <button onclick="togglePublish()" class="btn btn-primary btn-sm">
                        @if($page->is_published)
                            <i class="fas fa-eye-slash me-1"></i>Unpublish
                        @else
                            <i class="fas fa-rocket me-1"></i>Publish
                        @endif
                    </button>
                    
                    <button onclick="savePage()" class="btn btn-success btn-sm">
                        <i class="fas fa-save me-1"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Editor -->
        <div class="col-12 col-lg-8">
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 h6" style="color: var(--text-primary); font-weight: 600;">
                            <i class="fas fa-layer-group me-2"></i>Page Sections
                        </h2>
                        <button onclick="showAddSectionModal()" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Section
                        </button>
                    </div>
                </div>
                <div class="card-body p-3" id="sections-container">
                    @forelse($sections as $section)
                    <div class="section-card" data-section-id="{{ $section->id }}" draggable="true">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-grip-vertical text-muted" style="cursor: move;"></i>
                                    <span class="section-type-badge" style="background: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
                                        <i class="fas fa-{{ $section->type === 'hero' ? 'star' : ($section->type === 'features' ? 'list' : 'cube') }}"></i>
                                        {{ ucfirst($section->type) }}
                                    </span>
                                    @if(!$section->is_visible)
                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                        <i class="fas fa-eye-slash"></i> Hidden
                                    </span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.85rem;">
                                    @if(isset($section->content['heading']))
                                        <strong>{{ $section->content['heading'] }}</strong>
                                    @elseif(isset($section->content['title']))
                                        <strong>{{ $section->content['title'] }}</strong>
                                    @else
                                        <em>Section {{ $loop->iteration }}</em>
                                    @endif
                                </div>
                            </div>
                            <div class="section-actions">
                                <button onclick="editSection({{ $section->id }})" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                    <span class="d-none d-sm-inline ms-1">Edit</span>
                                </button>
                                <button onclick="toggleSectionVisibility({{ $section->id }})" 
                                        class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye{{ $section->is_visible ? '' : '-slash' }}"></i>
                                </button>
                                <button onclick="deleteSection({{ $section->id }})" 
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div style="font-size: 4rem;" class="mb-3">📄</div>
                        <h3 class="h5 mb-2" style="color: var(--text-primary); font-weight: 600;">
                            No sections yet
                        </h3>
                        <p class="text-muted mb-4" style="font-size: 0.9rem;">
                            Add your first section to start building this page
                        </p>
                        <button onclick="showAddSectionModal()" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Your First Section
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            <!-- Page Settings -->
            <div class="card mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <h3 class="mb-0 h6" style="color: var(--text-primary); font-weight: 600;">
                        <i class="fas fa-cog me-2"></i>Page Settings
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-medium" style="font-size: 0.9rem;">Page Title</label>
                        <input type="text" id="page-title" value="{{ $page->title }}" 
                               class="form-control" style="font-size: 0.9rem;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium" style="font-size: 0.9rem;">URL Slug</label>
                        <input type="text" id="page-slug" value="{{ $page->slug }}" 
                               class="form-control" style="font-size: 0.9rem;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium" style="font-size: 0.9rem;">Meta Description</label>
                        <textarea id="page-meta-description" rows="3" 
                                  class="form-control" style="font-size: 0.9rem;">{{ $page->meta_description }}</textarea>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" id="show-in-menu" class="form-check-input" 
                               {{ $page->show_in_menu ? 'checked' : '' }}>
                        <label class="form-check-label" for="show-in-menu" style="font-size: 0.9rem;">
                            Show in navigation menu
                        </label>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.05), rgba(19, 232, 233, 0.05)); border: 1px solid var(--border-color);">
                <div class="card-body p-3">
                    <h4 class="h6 mb-3" style="color: var(--text-primary); font-weight: 600;">
                        <i class="fas fa-lightbulb me-2" style="color: var(--warning-color);"></i>Quick Tips
                    </h4>
                    <ul class="mb-0 ps-3" style="font-size: 0.85rem; line-height: 1.8;">
                        <li class="mb-2">Drag sections to reorder them</li>
                        <li class="mb-2">Click "Edit" to customize section content</li>
                        <li class="mb-2">Use the eye icon to hide/show sections</li>
                        <li class="mb-2">Don't forget to save your changes!</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" style="color: var(--text-primary);">
                    <i class="fas fa-plus-circle me-2"></i>Add New Section
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4" style="font-size: 0.9rem;">Choose a section type to add to your page</p>
                <div class="row g-3">
                    @foreach($availableSectionTypes as $type)
                    <div class="col-12 col-md-6">
                        <div class="section-type-option" onclick="selectSectionType('{{ $type['type'] }}', this)">
                            <div class="d-flex align-items-start gap-3">
                                <div style="font-size: 2rem;">{{ $type['icon'] }}</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="color: var(--text-primary); font-weight: 600;">
                                        {{ $type['name'] }}
                                    </h6>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                        {{ $type['description'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;" onclick="generateSectionWithAI()">
                    <i class="fas fa-magic me-1"></i>Generate with AI
                </button>
                <button type="button" class="btn btn-primary" onclick="addSection()">
                    <i class="fas fa-plus me-1"></i>Add Section
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedSectionType = null;
let draggedElement = null;

// Drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
});

function initializeDragAndDrop() {
    const container = document.getElementById('sections-container');
    const sections = container.querySelectorAll('.section-card');
    
    sections.forEach(section => {
        section.addEventListener('dragstart', handleDragStart);
        section.addEventListener('dragend', handleDragEnd);
        section.addEventListener('dragover', handleDragOver);
        section.addEventListener('drop', handleDrop);
    });
}

function handleDragStart(e) {
    draggedElement = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    draggedElement = null;
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    if (draggedElement && draggedElement !== this) {
        const container = this.parentNode;
        const allSections = [...container.querySelectorAll('.section-card')];
        const draggedIndex = allSections.indexOf(draggedElement);
        const targetIndex = allSections.indexOf(this);
        
        if (draggedIndex < targetIndex) {
            this.parentNode.insertBefore(draggedElement, this.nextSibling);
        } else {
            this.parentNode.insertBefore(draggedElement, this);
        }
        
        // Update order on server
        updateSectionOrder();
    }
    
    return false;
}

function updateSectionOrder() {
    const sections = document.querySelectorAll('.section-card');
    const order = Array.from(sections).map(s => s.dataset.sectionId);
    
    fetch('{{ route("website.builder.sections.reorder", $page) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Section order updated', 'success');
        }
    });
}

function showAddSectionModal() {
    const modal = new bootstrap.Modal(document.getElementById('addSectionModal'));
    modal.show();
}

function selectSectionType(type, element) {
    // Remove selected class from all options
    document.querySelectorAll('.section-type-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    element.classList.add('selected');
    selectedSectionType = type;
}

function addSection() {
    if (!selectedSectionType) {
        showToast('Please select a section type', 'warning');
        return;
    }
    
    fetch('{{ route("website.builder.sections.store", $page) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: selectedSectionType,
            is_visible: true
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Section added successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to add section', 'danger');
        }
    })
    .catch(err => {
        showToast('An error occurred', 'danger');
    });
}

function editSection(sectionId) {
    // For now, redirect to a simple edit page
    // In the future, this could open a modal with a visual editor
    showToast('Section editing coming soon! For now, you can edit via the database or API.', 'info');
}

function toggleSectionVisibility(sectionId) {
    fetch(`/website-builder/sections/${sectionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            toggle_visibility: true
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Section visibility updated', 'success');
            setTimeout(() => location.reload(), 800);
        }
    });
}

function deleteSection(sectionId) {
    if (!confirm('Are you sure you want to delete this section?')) {
        return;
    }
    
    fetch(`/website-builder/sections/${sectionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Section deleted', 'success');
            setTimeout(() => location.reload(), 800);
        }
    });
}

function togglePublish() {
    const isPublished = {{ $page->is_published ? 'true' : 'false' }};
    
    fetch(`{{ route('website.builder.pages.update', $page) }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            is_published: !isPublished
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(isPublished ? 'Page unpublished' : 'Page published!', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    });
}

function savePage() {
    const title = document.getElementById('page-title').value;
    const slug = document.getElementById('page-slug').value;
    const metaDescription = document.getElementById('page-meta-description').value;
    const showInMenu = document.getElementById('show-in-menu').checked;
    
    fetch(`{{ route('website.builder.pages.update', $page) }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            title,
            slug,
            meta_description: metaDescription,
            show_in_menu: showInMenu
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Page saved successfully!', 'success');
        } else {
            showToast('Failed to save page', 'danger');
        }
    })
    .catch(err => {
        showToast('An error occurred', 'danger');
    });
}

function showToast(message, type = 'info') {
    const colors = {
        success: 'var(--success-color)',
        danger: 'var(--danger-color)',
        warning: 'var(--warning-color)',
        info: 'var(--info-color)'
    };
    
    const icons = {
        success: 'fa-check-circle',
        danger: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = 'alert alert-dismissible fade show';
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-left: 4px solid ${colors[type]};
        box-shadow: 0 4px 12px var(--shadow-color);
    `;
    toast.innerHTML = `
        <i class="fas ${icons[type]} me-2" style="color: ${colors[type]};"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(toast);
        bsAlert.close();
    }, 3000);
}

// AI Content Generation
function generateSectionWithAI() {
    if (!selectedSectionType) {
        showToast('Please select a section type first', 'warning');
        return;
    }

    const generateBtn = event.target;
    const originalText = generateBtn.innerHTML;
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating...';

    fetch('{{ route("website.builder.ai.generate-content") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            section_type: selectedSectionType,
            website_id: {{ $website->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.content) {
            // Add section with AI-generated content
            fetch('{{ route("website.builder.sections.store", $page) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: selectedSectionType,
                    content: data.content,
                    is_visible: true
                })
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    showToast('AI-generated section added successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('Failed to add section', 'danger');
                }
            });
        } else {
            showToast('Failed to generate content. Please try manually.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'danger');
    })
    .finally(() => {
        generateBtn.disabled = false;
        generateBtn.innerHTML = originalText;
    });
}
</script>
@endpush
@endsection


