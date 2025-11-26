{{-- Features Section Editor --}}
<div class="features-editor">
    <div class="mb-3">
        <label class="form-label fw-bold">Section Heading</label>
        <input type="text" 
               class="form-control" 
               data-content-field="heading"
               value="{{ $section->content['heading'] ?? '' }}"
               placeholder="e.g., Our Features">
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Subheading</label>
        <input type="text" 
               class="form-control" 
               data-content-field="subheading"
               value="{{ $section->content['subheading'] ?? '' }}"
               placeholder="e.g., What makes us special">
    </div>

    <div class="features-list">
        <label class="form-label fw-bold mb-3">Features</label>
        @php
            $features = $section->content['items'] ?? [];
        @endphp
        
        @foreach($features as $index => $feature)
        <div class="feature-item border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Feature {{ $index + 1 }}</h6>
                <button class="btn btn-sm btn-outline-danger remove-feature" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-2">
                <label class="form-label small">Icon (Font Awesome class)</label>
                <input type="text" 
                       class="form-control form-control-sm" 
                       data-content-field="items.{{ $index }}.icon"
                       value="{{ $feature['icon'] ?? '' }}"
                       placeholder="e.g., fa-rocket">
            </div>

            <div class="mb-2">
                <label class="form-label small">Title</label>
                <input type="text" 
                       class="form-control form-control-sm" 
                       data-content-field="items.{{ $index }}.title"
                       value="{{ $feature['title'] ?? '' }}"
                       placeholder="Feature title">
            </div>

            <div class="mb-2">
                <label class="form-label small">Description</label>
                <textarea class="form-control form-control-sm" 
                          data-content-field="items.{{ $index }}.description"
                          rows="2"
                          placeholder="Feature description">{{ $feature['description'] ?? '' }}</textarea>
            </div>
        </div>
        @endforeach

        <button class="btn btn-sm btn-outline-primary add-feature" type="button">
            <i class="fas fa-plus me-1"></i> Add Feature
        </button>
    </div>
</div>

@push('scripts')
<script>
$(document).on('click', '.add-feature', function() {
    const featuresContainer = $(this).closest('.features-list');
    const index = featuresContainer.find('.feature-item').length;
    
    const newFeature = `
        <div class="feature-item border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Feature ${index + 1}</h6>
                <button class="btn btn-sm btn-outline-danger remove-feature" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-2">
                <label class="form-label small">Icon (Font Awesome class)</label>
                <input type="text" 
                       class="form-control form-control-sm" 
                       data-content-field="items.${index}.icon"
                       placeholder="e.g., fa-rocket">
            </div>

            <div class="mb-2">
                <label class="form-label small">Title</label>
                <input type="text" 
                       class="form-control form-control-sm" 
                       data-content-field="items.${index}.title"
                       placeholder="Feature title">
            </div>

            <div class="mb-2">
                <label class="form-label small">Description</label>
                <textarea class="form-control form-control-sm" 
                          data-content-field="items.${index}.description"
                          rows="2"
                          placeholder="Feature description"></textarea>
            </div>
        </div>
    `;
    
    $(this).before(newFeature);
});

$(document).on('click', '.remove-feature', function() {
    $(this).closest('.feature-item').fadeOut(function() {
        $(this).remove();
        // Renumber remaining features
        $('.feature-item').each(function(i) {
            $(this).find('h6').text(`Feature ${i + 1}`);
        });
    });
});
</script>
@endpush
