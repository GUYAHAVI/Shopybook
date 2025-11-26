@extends('layouts.app')

@section('title', 'Edit Content - ' . $page->title)

@section('content')
<div class="content-editor">
    <div class="editor-header">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <div class="col-md-6">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editing: {{ $page->title }}
                    </h4>
                    <small class="text-muted">{{ $website->business->name }}</small>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('website.builder.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="button" class="btn btn-success" id="saveContent">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                    <a href="{{ $page->url }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i> Preview
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="editor-body">
        <div class="container-fluid py-4">
            <div class="row">
                <!-- Content Sections -->
                <div class="col-lg-9">
                    <div id="sectionsContainer">
                        @foreach($page->sections()->orderBy('order')->get() as $section)
                        <div class="section-card mb-4" data-section-id="{{ $section->id }}">
                            <div class="section-header d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-grip-vertical text-muted me-3" style="cursor: move;"></i>
                                    <h5 class="mb-0">
                                        {{ ucwords(str_replace('-', ' ', $section->type)) }}
                                    </h5>
                                </div>
                                <div class="section-actions">
                                    <button class="btn btn-sm btn-outline-secondary toggle-section" type="button">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-section" type="button">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="section-content p-4">
                                @include('website-builder.sections.editor.' . str_replace('-', '_', $section->type), ['section' => $section])
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-outline-primary" id="addSection">
                            <i class="fas fa-plus me-2"></i>
                            Add Section
                        </button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="sticky-top" style="top: 20px;">
                        <!-- Page Settings -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Page Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Page Title</label>
                                    <input type="text" class="form-control" id="pageTitle" value="{{ $page->title }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">URL Slug</label>
                                    <input type="text" class="form-control" id="pageSlug" value="{{ $page->slug }}">
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="showInMenu" {{ $page->show_in_menu ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showInMenu">Show in Menu</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isPublished" {{ $page->is_published ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isPublished">Published</label>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-search me-2"></i>SEO</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="metaTitle" value="{{ $page->meta_title }}">
                                    <small class="text-muted">{{ strlen($page->meta_title ?? '') }}/60</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <textarea class="form-control" id="metaDescription" rows="3">{{ $page->meta_description }}</textarea>
                                    <small class="text-muted">{{ strlen($page->meta_description ?? '') }}/160</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="section-type-card" data-type="hero">
                            <i class="fas fa-image fa-2x mb-2"></i>
                            <h6>Hero</h6>
                            <small>Large banner with heading and CTA</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-type-card" data-type="features">
                            <i class="fas fa-th fa-2x mb-2"></i>
                            <h6>Features</h6>
                            <small>Grid of features/benefits</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-type-card" data-type="services">
                            <i class="fas fa-briefcase fa-2x mb-2"></i>
                            <h6>Services</h6>
                            <small>List of your services</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-type-card" data-type="testimonials">
                            <i class="fas fa-quote-right fa-2x mb-2"></i>
                            <h6>Testimonials</h6>
                            <small>Customer reviews</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-type-card" data-type="cta">
                            <i class="fas fa-bullhorn fa-2x mb-2"></i>
                            <h6>Call to Action</h6>
                            <small>Encourage visitor action</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-type-card" data-type="team">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h6>Team</h6>
                            <small>Team member profiles</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content-editor {
    min-height: 100vh;
    background: #f8f9fa;
}

.editor-header {
    background: white;
    border-bottom: 2px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 100;
}

.section-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}

.section-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.section-type-card {
    padding: 20px;
    text-align: center;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.section-type-card:hover {
    border-color: #0d6efd;
    background: #f0f8ff;
    transform: translateY(-2px);
}

.section-type-card i {
    color: #0d6efd;
}
</style>

@push('scripts')
<script>
// Make sections sortable
$(document).ready(function() {
    $('#sectionsContainer').sortable({
        handle: '.fa-grip-vertical',
        update: function() {
            // Update order
            updateSectionOrder();
        }
    });
});

// Toggle section visibility
$('.toggle-section').click(function() {
    const card = $(this).closest('.section-card');
    card.find('.section-content').slideToggle();
    $(this).find('i').toggleClass('fa-chevron-up fa-chevron-down');
});

// Add section
$('#addSection').click(function() {
    $('#addSectionModal').modal('show');
});

$('.section-type-card').click(function() {
    const type = $(this).data('type');
    addNewSection(type);
    $('#addSectionModal').modal('hide');
});

// Delete section
$(document).on('click', '.delete-section', function() {
    if (confirm('Are you sure you want to delete this section?')) {
        const card = $(this).closest('.section-card');
        const sectionId = card.data('section-id');
        
        $.ajax({
            url: `/website-builder/sections/${sectionId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                card.fadeOut(function() {
                    $(this).remove();
                });
            }
        });
    }
});

// Save all changes
$('#saveContent').click(function() {
    const pageData = {
        title: $('#pageTitle').val(),
        slug: $('#pageSlug').val(),
        show_in_menu: $('#showInMenu').is(':checked'),
        is_published: $('#isPublished').is(':checked'),
        meta_title: $('#metaTitle').val(),
        meta_description: $('#metaDescription').val(),
    };
    
    // Collect all section data
    const sections = [];
    $('.section-card').each(function(index) {
        const sectionId = $(this).data('section-id');
        const sectionData = collectSectionData($(this));
        sections.push({
            id: sectionId,
            order: index + 1,
            ...sectionData
        });
    });
    
    $.ajax({
        url: '{{ route("website.builder.pages.update", $page->id) }}',
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: {
            page: pageData,
            sections: sections
        },
        success: function(response) {
            alert('Changes saved successfully!');
        },
        error: function() {
            alert('Failed to save changes');
        }
    });
});

function collectSectionData(sectionCard) {
    const content = {};
    sectionCard.find('[data-content-field]').each(function() {
        const field = $(this).data('content-field');
        content[field] = $(this).val();
    });
    return { content };
}

function updateSectionOrder() {
    const order = [];
    $('.section-card').each(function(index) {
        order.push({
            id: $(this).data('section-id'),
            order: index + 1
        });
    });
    
    $.ajax({
        url: '{{ route("website.builder.sections.reorder") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: { sections: order }
    });
}

function addNewSection(type) {
    $.ajax({
        url: '{{ route("website.builder.sections.create") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: {
            page_id: {{ $page->id }},
            type: type
        },
        success: function(response) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection
