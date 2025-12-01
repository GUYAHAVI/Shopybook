@extends('layouts.dash')

@section('title', 'Create Your Website - Step 2')

@section('content')
<style>
    :root {
        --primary-purple: #8b5cf6;
        --dark-bg: #1e293b;
        --darker-bg: #0f172a;
    }
    
    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
    }
    
    .alert-error,
    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }
    
    .alert-icon {
        font-size: 1.2rem;
    }
    
    body {
        background: var(--darker-bg);
        color: #e2e8f0;
    }
    
    .configurator-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 3rem 1.5rem;
    }
    
    .step-indicator {
        text-align: center;
        margin-bottom: 3rem;
        color: #94a3b8;
        font-size: 0.9rem;
    }
    
    .hero-section {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .hero-title {
        font-size: 3rem;
        font-weight: 300;
        color: #fff;
        margin-bottom: 0.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
        color: #94a3b8;
    }
    
    .input-container {
        margin-bottom: 2rem;
    }
    
    .input-label {
        font-size: 1.1rem;
        color: #94a3b8;
        margin-bottom: 1rem;
        display: block;
    }
    
    .business-input {
        width: 100%;
        background: var(--dark-bg);
        border: 2px solid #334155;
        border-radius: 8px;
        padding: 1.5rem;
        font-size: 1.5rem;
        color: #fff;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .business-input:focus {
        outline: none;
        border-color: var(--primary-purple);
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }
    
    .business-input::placeholder {
        color: #475569;
        font-style: italic;
    }
    
    .description-input {
        width: 100%;
        background: var(--dark-bg);
        border: 2px solid #334155;
        border-radius: 8px;
        padding: 1.5rem;
        font-size: 1.1rem;
        color: #fff;
        transition: all 0.3s ease;
        resize: vertical;
        min-height: 120px;
    }
    
    .description-input:focus {
        outline: none;
        border-color: var(--primary-purple);
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }
    
    .char-count {
        text-align: right;
        color: #64748b;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 3rem;
        gap: 1rem;
    }
    
    .btn {
        padding: 1rem 2.5rem;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-back {
        background: transparent;
        color: #94a3b8;
        border: 2px solid #334155;
    }
    
    .btn-back:hover {
        border-color: #475569;
        color: #e2e8f0;
    }
    
    .btn-next {
        background: var(--primary-purple);
        color: white;
        flex: 1;
    }
    
    .btn-next:hover {
        background: #7c3aed;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
    }
    
    .btn-generate-ai {
        background: transparent;
        border: 2px solid var(--primary-purple);
        color: var(--primary-purple);
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-generate-ai:hover {
        background: var(--primary-purple);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    
    .btn-generate-ai:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .business-input {
            font-size: 1.2rem;
        }
        
        .action-buttons {
            flex-direction: column-reverse;
        }
        
        .btn-next {
            width: 100%;
        }
        
        .btn-generate-ai {
            font-size: 0.85rem;
            padding: 0.6rem 1rem;
        }
    }
</style>

<div class="configurator-container">
    <!-- Error/Success Messages -->
    @if(session('success'))
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error">
        <span class="alert-icon">⚠</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <span class="alert-icon">⚠</span>
        <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Step Indicator -->
    <div class="step-indicator">
        Step 2 of 4
    </div>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">I want a {{ ucfirst(str_replace('_', ' ', $websiteType)) }} for my</h1>
    </div>

    <!-- Form -->
    <form action="{{ route('website-configurator.step2.submit') }}" method="POST">
        @csrf
        
        <!-- Business Name Input -->
        <div class="input-container">
            <input type="text" 
                   name="business_name" 
                   class="business-input" 
                   placeholder="{{ $business->name }}"
                   value="{{ old('business_name', $business->name) }}"
                   required>
            <p class="input-label mt-2" style="text-align: center;">business</p>
        </div>

        <!-- Business Description -->
        <div class="input-container mt-4">
            <label class="input-label">Tell us more about your business (optional)</label>
            <textarea name="business_description" 
                      id="descriptionInput"
                      class="description-input" 
                      placeholder="e.g., We provide high-quality greenhouse materials and expert gardening advice..."
                      maxlength="500">{{ old('business_description', $business->description) }}</textarea>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                <button type="button" id="generateDescriptionBtn" class="btn-generate-ai">
                    <i class="fas fa-magic"></i> Generate Website Description
                </button>
                <div class="char-count">
                    <span id="charCount">0</span> / 500 characters
                </div>
            </div>
            <div id="aiGenerationProgress" style="display: none; margin-top: 10px; padding: 12px; background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 6px; font-size: 14px; color: #a78bfa;">
                <i class="fas fa-spinner fa-spin"></i> Claude AI is optimizing your description for the web...
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('website-configurator.step1') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            
            <button type="submit" class="btn btn-next">
                Continue <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const descInput = document.getElementById('descriptionInput');
    const charCount = document.getElementById('charCount');
    const generateBtn = document.getElementById('generateDescriptionBtn');
    const aiProgress = document.getElementById('aiGenerationProgress');
    const businessNameInput = document.querySelector('[name="business_name"]');
    
    function updateCharCount() {
        const count = descInput.value.length;
        charCount.textContent = count;
        
        if (count > 450) {
            charCount.style.color = '#f59e0b';
        } else {
            charCount.style.color = '#64748b';
        }
    }
    
    descInput.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count
    
    // AI Description Generation
    if (generateBtn) {
        generateBtn.addEventListener('click', async function() {
            const originalDescription = descInput.value.trim();
            const businessName = businessNameInput.value.trim();
            const websiteType = '{{ $websiteType }}';
            const businessType = '{{ $business->business_type ?? "business" }}';
            
            // Validation
            if (!businessName) {
                alert('Please enter your business name first.');
                businessNameInput.focus();
                return;
            }
            
            // Show loading state
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            aiProgress.style.display = 'block';
            
            try {
                const response = await fetch('{{ route('website-configurator.generate-website-description') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        original_description: originalDescription,
                        business_name: businessName,
                        business_type: businessType,
                        website_type: websiteType
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.website_description) {
                    // Update textarea with optimized description
                    descInput.value = data.website_description;
                    updateCharCount();
                    
                    // Visual feedback
                    descInput.style.borderColor = 'var(--primary-purple)';
                    descInput.style.boxShadow = '0 0 0 4px rgba(139, 92, 246, 0.2)';
                    
                    setTimeout(() => {
                        descInput.style.borderColor = '#334155';
                        descInput.style.boxShadow = 'none';
                    }, 2000);
                    
                    // Show success message
                    aiProgress.innerHTML = '<i class="fas fa-check-circle"></i> Website description generated successfully! Perfect for your website.';
                    aiProgress.style.background = 'rgba(16, 185, 129, 0.1)';
                    aiProgress.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                    aiProgress.style.color = '#10b981';
                    
                    // Hide success message after 3 seconds
                    setTimeout(() => {
                        aiProgress.style.display = 'none';
                        aiProgress.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Claude AI is optimizing your description for the web...';
                        aiProgress.style.background = 'rgba(139, 92, 246, 0.1)';
                        aiProgress.style.borderColor = 'rgba(139, 92, 246, 0.3)';
                        aiProgress.style.color = '#a78bfa';
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Generation failed');
                }
            } catch (error) {
                console.error('AI generation error:', error);
                
                // Show error message
                aiProgress.innerHTML = '<i class="fas fa-exclamation-circle"></i> Generation failed. Please try again or write your own description.';
                aiProgress.style.background = 'rgba(239, 68, 68, 0.1)';
                aiProgress.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                aiProgress.style.color = '#ef4444';
                
                setTimeout(() => {
                    aiProgress.style.display = 'none';
                    aiProgress.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Claude AI is optimizing your description for the web...';
                    aiProgress.style.background = 'rgba(139, 92, 246, 0.1)';
                    aiProgress.style.borderColor = 'rgba(139, 92, 246, 0.3)';
                    aiProgress.style.color = '#a78bfa';
                }, 4000);
            } finally {
                // Reset button state
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<i class="fas fa-magic"></i> Generate Website Description';
            }
        });
    }
});
</script>
@endsection
