{{-- Hero Section Editor --}}
<div class="hero-editor">
    <div class="mb-3">
        <label class="form-label fw-bold">Heading</label>
        <input type="text" 
               class="form-control form-control-lg" 
               data-content-field="heading"
               value="{{ $section->content['heading'] ?? '' }}"
               placeholder="Enter main heading">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Subheading</label>
        <input type="text" 
               class="form-control" 
               data-content-field="subheading"
               value="{{ $section->content['subheading'] ?? '' }}"
               placeholder="Enter subheading">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea class="form-control" 
                  data-content-field="description"
                  rows="3"
                  placeholder="Optional description text">{{ $section->content['description'] ?? '' }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Primary Button Text</label>
            <input type="text" 
                   class="form-control" 
                   data-content-field="cta_primary"
                   value="{{ $section->content['cta_primary'] ?? '' }}"
                   placeholder="e.g., Get Started">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Primary Button Link</label>
            <input type="text" 
                   class="form-control" 
                   data-content-field="cta_primary_link"
                   value="{{ $section->content['cta_primary_link'] ?? '#' }}"
                   placeholder="e.g., /contact">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Secondary Button Text</label>
            <input type="text" 
                   class="form-control" 
                   data-content-field="cta_secondary"
                   value="{{ $section->content['cta_secondary'] ?? '' }}"
                   placeholder="e.g., Learn More">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Secondary Button Link</label>
            <input type="text" 
                   class="form-control" 
                   data-content-field="cta_secondary_link"
                   value="{{ $section->content['cta_secondary_link'] ?? '#' }}"
                   placeholder="e.g., /about">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Background Image URL</label>
        <input type="text" 
               class="form-control" 
               data-content-field="image"
               value="{{ $section->content['image'] ?? '' }}"
               placeholder="Optional image URL">
        <small class="text-muted">Leave empty for solid color background</small>
    </div>
</div>
