@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-magic text-primary"></i>
                        AI Content Enhancer
                    </h4>
                    <p class="text-muted">Enhance your content with AI-powered improvements</p>
                </div>
                <div class="card-body">
                    <!-- AI Service Status -->
                    <div class="alert alert-info" id="aiStatus">
                        <i class="fas fa-spinner fa-spin"></i> Checking AI service status...
                    </div>

                    <!-- Content Enhancement Tabs -->
                    <ul class="nav nav-tabs" id="contentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="enhance-tab" data-bs-toggle="tab" data-bs-target="#enhance" type="button" role="tab">
                                <i class="fas fa-edit"></i> Enhance Content
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="generate-tab" data-bs-toggle="tab" data-bs-target="#generate" type="button" role="tab">
                                <i class="fas fa-plus"></i> Generate Content
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                                <i class="fas fa-search"></i> SEO Optimization
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="variations-tab" data-bs-toggle="tab" data-bs-target="#variations" type="button" role="tab">
                                <i class="fas fa-copy"></i> Content Variations
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="contentTabsContent">
                        <!-- Enhance Content Tab -->
                        <div class="tab-pane fade show active" id="enhance" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Original Content</h5>
                                    <div class="form-group">
                                        <textarea class="form-control" id="originalContent" rows="8" placeholder="Enter your content to enhance..."></textarea>
                                    </div>
                                    <div class="mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Content Type</label>
                                                <select class="form-control" id="enhanceContentType">
                                                    @foreach($contentTypes as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Tone</label>
                                                <select class="form-control" id="enhanceTone">
                                                    @foreach($tones as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary mt-3" onclick="enhanceContent()">
                                            <i class="fas fa-magic"></i> Enhance Content
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Enhanced Content</h5>
                                    <div class="form-group">
                                        <textarea class="form-control" id="enhancedContent" rows="8" readonly placeholder="Enhanced content will appear here..."></textarea>
                                    </div>
                                    <div class="mt-3">
                                        <div id="enhancementStats" class="d-none">
                                            <small class="text-muted">
                                                <span id="wordCount"></span> words | 
                                                <span id="improvementPercent"></span> improvement
                                            </small>
                                        </div>
                                        <div id="enhancementImprovements" class="mt-2"></div>
                                        <button class="btn btn-success mt-2 d-none" id="copyEnhancedBtn" onclick="copyToClipboard('enhancedContent')">
                                            <i class="fas fa-copy"></i> Copy Enhanced Content
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Generate Content Tab -->
                        <div class="tab-pane fade" id="generate" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Content Generation</h5>
                                    <div class="form-group">
                                        <label>Keywords or Brief Description</label>
                                        <textarea class="form-control" id="generateKeywords" rows="4" placeholder="Enter keywords separated by commas (e.g., consulting, business, strategy)"></textarea>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <label>Content Type</label>
                                            <select class="form-control" id="generateContentType">
                                                @foreach($contentTypes as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Tone</label>
                                            <select class="form-control" id="generateTone">
                                                @foreach($tones as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Length</label>
                                            <select class="form-control" id="generateLength">
                                                @foreach($lengths as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="generateContent()">
                                        <i class="fas fa-plus"></i> Generate Content
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <h5>Generated Content</h5>
                                    <div class="form-group">
                                        <textarea class="form-control" id="generatedContent" rows="8" readonly placeholder="Generated content will appear here..."></textarea>
                                    </div>
                                    <div class="mt-3">
                                        <div id="generationStats" class="d-none">
                                            <small class="text-muted">
                                                <span id="generatedWordCount"></span> words | 
                                                Keywords used: <span id="usedKeywords"></span>
                                            </small>
                                        </div>
                                        <button class="btn btn-success mt-2 d-none" id="copyGeneratedBtn" onclick="copyToClipboard('generatedContent')">
                                            <i class="fas fa-copy"></i> Copy Generated Content
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Optimization Tab -->
                        <div class="tab-pane fade" id="seo" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>SEO Optimization</h5>
                                    <div class="form-group">
                                        <label>Content to Optimize</label>
                                        <textarea class="form-control" id="seoContent" rows="6" placeholder="Enter content to optimize for SEO..."></textarea>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Target Keywords</label>
                                        <input type="text" class="form-control" id="seoKeywords" placeholder="Enter keywords separated by commas">
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="optimizeSEO()">
                                        <i class="fas fa-search"></i> Optimize for SEO
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <h5>Optimized Content</h5>
                                    <div class="form-group">
                                        <textarea class="form-control" id="optimizedContent" rows="6" readonly placeholder="Optimized content will appear here..."></textarea>
                                    </div>
                                    <div class="mt-3">
                                        <div id="seoStats" class="d-none">
                                            <div class="alert alert-info">
                                                <strong>SEO Score:</strong> <span id="seoScore"></span>/100
                                            </div>
                                            <div id="keywordDensity"></div>
                                            <div id="seoSuggestions"></div>
                                        </div>
                                        <button class="btn btn-success mt-2 d-none" id="copyOptimizedBtn" onclick="copyToClipboard('optimizedContent')">
                                            <i class="fas fa-copy"></i> Copy Optimized Content
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content Variations Tab -->
                        <div class="tab-pane fade" id="variations" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Content Variations</h5>
                                    <div class="form-group">
                                        <label>Base Content</label>
                                        <textarea class="form-control" id="variationContent" rows="6" placeholder="Enter base content to create variations..."></textarea>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label>Content Type</label>
                                            <select class="form-control" id="variationContentType">
                                                @foreach($contentTypes as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Number of Variations</label>
                                            <select class="form-control" id="variationCount">
                                                <option value="3">3 Variations</option>
                                                <option value="4">4 Variations</option>
                                                <option value="5">5 Variations</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="generateVariations()">
                                        <i class="fas fa-copy"></i> Generate Variations
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <h5>Content Variations</h5>
                                    <div id="variationsContainer">
                                        <p class="text-muted">Content variations will appear here...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">AI is processing your content...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Check AI service status on page load
document.addEventListener('DOMContentLoaded', function() {
    checkAIStatus();
});

function checkAIStatus() {
    fetch('/ai-content/status')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('aiStatus');
            if (data.python_available) {
                statusDiv.className = 'alert alert-success';
                statusDiv.innerHTML = '<i class="fas fa-check"></i> AI Content Enhancement service is ready!';
            } else {
                statusDiv.className = 'alert alert-warning';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> AI service is not available. Some features may not work.';
            }
        })
        .catch(error => {
            document.getElementById('aiStatus').className = 'alert alert-danger';
            document.getElementById('aiStatus').innerHTML = '<i class="fas fa-times"></i> Failed to check AI service status.';
        });
}

function showLoading() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoading() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) modal.hide();
}

function enhanceContent() {
    const content = document.getElementById('originalContent').value;
    const contentType = document.getElementById('enhanceContentType').value;
    const tone = document.getElementById('enhanceTone').value;

    if (!content.trim()) {
        alert('Please enter content to enhance.');
        return;
    }

    showLoading();

    fetch('/ai-content/enhance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            content: content,
            content_type: contentType,
            tone: tone
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            document.getElementById('enhancedContent').value = data.enhanced_content;
            document.getElementById('wordCount').textContent = data.word_count;
            document.getElementById('improvementPercent').textContent = data.improvement_percentage.toFixed(1) + '%';
            document.getElementById('enhancementStats').classList.remove('d-none');
            document.getElementById('copyEnhancedBtn').classList.remove('d-none');
            
            // Show improvements
            const improvementsDiv = document.getElementById('enhancementImprovements');
            if (data.improvements && data.improvements.length > 0) {
                improvementsDiv.innerHTML = '<strong>Improvements:</strong><br>' + 
                    data.improvements.map(imp => `<span class="badge bg-success me-1">${imp}</span>`).join('');
            }
        } else {
            alert('Enhancement failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoading();
        alert('Error: ' + error.message);
    });
}

function generateContent() {
    const keywords = document.getElementById('generateKeywords').value;
    const contentType = document.getElementById('generateContentType').value;
    const tone = document.getElementById('generateTone').value;
    const length = document.getElementById('generateLength').value;

    if (!keywords.trim()) {
        alert('Please enter keywords for content generation.');
        return;
    }

    showLoading();

    fetch('/ai-content/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            keywords: keywords,
            content_type: contentType,
            tone: tone,
            length: length
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            document.getElementById('generatedContent').value = data.generated_content;
            document.getElementById('generatedWordCount').textContent = data.word_count;
            document.getElementById('usedKeywords').textContent = data.keywords_used.join(', ');
            document.getElementById('generationStats').classList.remove('d-none');
            document.getElementById('copyGeneratedBtn').classList.remove('d-none');
        } else {
            alert('Generation failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoading();
        alert('Error: ' + error.message);
    });
}

function optimizeSEO() {
    const content = document.getElementById('seoContent').value;
    const keywords = document.getElementById('seoKeywords').value.split(',').map(k => k.trim()).filter(k => k);

    if (!content.trim()) {
        alert('Please enter content to optimize.');
        return;
    }

    if (keywords.length === 0) {
        alert('Please enter target keywords.');
        return;
    }

    showLoading();

    fetch('/ai-content/seo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            content: content,
            keywords: keywords
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            document.getElementById('optimizedContent').value = data.optimized_content;
            document.getElementById('seoScore').textContent = data.seo_score;
            
            // Show keyword density
            const densityDiv = document.getElementById('keywordDensity');
            densityDiv.innerHTML = '<strong>Keyword Density:</strong><br>' + 
                Object.entries(data.keyword_density).map(([keyword, density]) => 
                    `<span class="badge bg-info me-1">${keyword}: ${(density * 100).toFixed(1)}%</span>`
                ).join('');
            
            // Show suggestions
            const suggestionsDiv = document.getElementById('seoSuggestions');
            if (data.suggestions && data.suggestions.length > 0) {
                suggestionsDiv.innerHTML = '<strong>Suggestions:</strong><br>' + 
                    data.suggestions.map(suggestion => `<span class="badge bg-warning me-1">${suggestion}</span>`).join('');
            }
            
            document.getElementById('seoStats').classList.remove('d-none');
            document.getElementById('copyOptimizedBtn').classList.remove('d-none');
        } else {
            alert('SEO optimization failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoading();
        alert('Error: ' + error.message);
    });
}

function generateVariations() {
    const content = document.getElementById('variationContent').value;
    const contentType = document.getElementById('variationContentType').value;
    const count = document.getElementById('variationCount').value;

    if (!content.trim()) {
        alert('Please enter base content for variations.');
        return;
    }

    showLoading();

    fetch('/ai-content/variations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            content: content,
            content_type: contentType,
            count: parseInt(count)
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            const container = document.getElementById('variationsContainer');
            container.innerHTML = '';
            
            data.variations.forEach((variation, index) => {
                const variationDiv = document.createElement('div');
                variationDiv.className = 'mb-3';
                variationDiv.innerHTML = `
                    <div class="card">
                        <div class="card-header">
                            <strong>Variation ${variation.id}</strong> (${variation.style}) - ${variation.word_count} words
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" rows="4" readonly>${variation.content}</textarea>
                            <button class="btn btn-sm btn-success mt-2" onclick="copyToClipboard(this.previousElementSibling)">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(variationDiv);
            });
        } else {
            alert('Variation generation failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoading();
        alert('Error: ' + error.message);
    });
}

function copyToClipboard(elementId) {
    const element = typeof elementId === 'string' ? document.getElementById(elementId) : elementId;
    element.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copied!';
    button.className = 'btn btn-sm btn-success mt-2';
    
    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}
</script>
@endpush

