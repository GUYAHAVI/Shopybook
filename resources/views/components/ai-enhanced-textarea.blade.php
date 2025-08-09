@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'rows' => 4,
    'contentType' => 'description',
    'tone' => 'professional',
    'required' => false,
    'disabled' => false
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    
    <div class="ai-enhanced-textarea-container">
        <textarea 
            name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-control @error($name) is-invalid @enderror"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            data-content-type="{{ $contentType }}"
            data-tone="{{ $tone }}"
        >{{ old($name, $slot ?? '') }}</textarea>
        
        <div class="ai-enhancement-buttons">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="enhanceTextarea('{{ $name }}')" title="Enhance with AI">
                <i class="fas fa-magic"></i> Enhance
            </button>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="generateContent('{{ $name }}')" title="Generate content">
                <i class="fas fa-plus"></i> Generate
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="optimizeSEO('{{ $name }}')" title="Optimize for SEO">
                <i class="fas fa-search"></i> SEO
            </button>
        </div>
    </div>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    <div id="{{ $name }}-ai-status" class="ai-status mt-2 d-none">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <small class="text-muted ms-2">AI is processing...</small>
    </div>
</div>

<style>
.ai-enhanced-textarea-container {
    position: relative;
}

.ai-enhancement-buttons {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.ai-enhanced-textarea-container:hover .ai-enhancement-buttons {
    opacity: 1;
}

.ai-enhancement-buttons .btn {
    padding: 4px 8px;
    font-size: 0.75rem;
    border-radius: 4px;
}

.ai-status {
    display: flex;
    align-items: center;
}
</style>

<script>
function enhanceTextarea(textareaId) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) {
        console.error('Textarea not found:', textareaId);
        return;
    }
    
    const content = textarea.value || '';
    const contentType = textarea.dataset.contentType || 'description';
    const tone = textarea.dataset.tone || 'professional';
    
    if (!content.trim()) {
        alert('Please enter some content to enhance.');
        return;
    }
    
    showAIStatus(textareaId);
    
    const requestData = {
        content: content,
        content_type: contentType,
        tone: tone
    };
    console.log('AI Enhancement Request:', requestData); // Debug logging
    console.log('Content:', content); // Debug content
    console.log('Content Type:', contentType); // Debug content type
    console.log('Tone:', tone); // Debug tone
    
    fetch('/ai-content/enhance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug response status
        return response.json();
    })
    .then(data => {
        hideAIStatus(textareaId);
        console.log('AI Enhancement Response:', data); // Debug logging
        console.log('Response keys:', Object.keys(data)); // Debug all response keys
        if (data.success) {
            textarea.value = data.enhanced_content;
            const wordCount = data.word_count || 0;
            const improvementPercentage = data.improvement_percentage || 0;
            console.log('Word Count:', wordCount); // Debug word count
            console.log('Improvement Percentage:', improvementPercentage); // Debug improvement percentage
            console.log('Improvement Percentage Type:', typeof improvementPercentage); // Debug type
            showSuccessMessage(textareaId, `Enhanced! ${wordCount} words, ${improvementPercentage.toFixed(1)}% improvement`);
        } else {
            showErrorMessage(textareaId, data.error || 'Enhancement failed');
        }
    })
    .catch(error => {
        hideAIStatus(textareaId);
        console.error('AI Enhancement Error:', error); // Debug logging
        showErrorMessage(textareaId, 'Error: ' + error.message);
    });
}

function generateContent(textareaId) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) {
        console.error('Textarea not found:', textareaId);
        return;
    }
    
    const keywords = prompt('Enter keywords separated by commas:');
    
    if (!keywords || !keywords.trim()) {
        return;
    }
    
    const contentType = textarea.dataset.contentType || 'description';
    const tone = textarea.dataset.tone || 'professional';
    
    showAIStatus(textareaId);
    
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
            length: 'medium'
        })
    })
    .then(response => response.json())
    .then(data => {
        hideAIStatus(textareaId);
        if (data.success) {
            textarea.value = data.generated_content;
            const wordCount = data.word_count || 0;
            showSuccessMessage(textareaId, `Generated! ${wordCount} words`);
        } else {
            showErrorMessage(textareaId, data.error || 'Generation failed');
        }
    })
    .catch(error => {
        hideAIStatus(textareaId);
        showErrorMessage(textareaId, 'Error: ' + error.message);
    });
}

function optimizeSEO(textareaId) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) {
        console.error('Textarea not found:', textareaId);
        return;
    }
    
    const content = textarea.value || '';
    const keywords = prompt('Enter target keywords separated by commas:');
    
    if (!content.trim()) {
        alert('Please enter content to optimize.');
        return;
    }
    
    if (!keywords || !keywords.trim()) {
        alert('Please enter target keywords.');
        return;
    }
    
    const keywordArray = keywords.split(',').map(k => k.trim()).filter(k => k);
    
    showAIStatus(textareaId);
    
    fetch('/ai-content/seo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            content: content,
            keywords: keywordArray
        })
    })
    .then(response => response.json())
    .then(data => {
        hideAIStatus(textareaId);
        if (data.success) {
            textarea.value = data.optimized_content;
            const seoScore = data.seo_score || 0;
            showSuccessMessage(textareaId, `SEO optimized! Score: ${seoScore}/100`);
        } else {
            showErrorMessage(textareaId, data.error || 'SEO optimization failed');
        }
    })
    .catch(error => {
        hideAIStatus(textareaId);
        showErrorMessage(textareaId, 'Error: ' + error.message);
    });
}

function showAIStatus(textareaId) {
    const statusDiv = document.getElementById(textareaId + '-ai-status');
    if (statusDiv) {
        statusDiv.classList.remove('d-none');
    }
}

function hideAIStatus(textareaId) {
    const statusDiv = document.getElementById(textareaId + '-ai-status');
    if (statusDiv) {
        statusDiv.classList.add('d-none');
    }
}

function showSuccessMessage(textareaId, message) {
    const statusDiv = document.getElementById(textareaId + '-ai-status');
    if (statusDiv) {
        statusDiv.innerHTML = `<i class="fas fa-check text-success"></i><small class="text-success ms-2">${message}</small>`;
        statusDiv.classList.remove('d-none');
        
        setTimeout(() => {
            if (statusDiv) {
                statusDiv.classList.add('d-none');
            }
        }, 3000);
    }
}

function showErrorMessage(textareaId, message) {
    const statusDiv = document.getElementById(textareaId + '-ai-status');
    if (statusDiv) {
        statusDiv.innerHTML = `<i class="fas fa-times text-danger"></i><small class="text-danger ms-2">${message}</small>`;
        statusDiv.classList.remove('d-none');
        
        setTimeout(() => {
            if (statusDiv) {
                statusDiv.classList.add('d-none');
            }
        }, 5000);
    }
}
</script>

