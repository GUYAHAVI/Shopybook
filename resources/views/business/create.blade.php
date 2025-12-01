@extends('layouts.master')

@section('content')
<style>

/* Form Container */
.form-container {
    width: 100%;
    max-width: 600px;
    margin: 30px auto;
    font-family: 'Poppins', sans-serif;
    color: #2C3E50;
}

/* Progress Bar */
#progressbar {
    margin-bottom: 40px;
    overflow: hidden;
    counter-reset: step;
    display: flex;
    padding-left: 0;
}

#progressbar li {
    list-style-type: none;
    color: #9ca3af;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: 500;
    width: 33.33%;
    position: relative;
    text-align: center;
}

#progressbar li:before {
    content: counter(step);
    counter-increment: step;
    width: 30px;
    height: 30px;
    line-height: 30px;
    display: block;
    font-size: 12px;
    color: #fff;
    background: #d1d5db;
    border-radius: 50%;
    margin: 0 auto 10px;
}

#progressbar li:after {
    content: '';
    width: 100%;
    height: 2px;
    background: #d1d5db;
    position: absolute;
    left: -50%;
    top: 15px;
    z-index: -1;
}

#progressbar li:first-child:after {
    content: none;
}

#progressbar li.active {
    color: #2563eb;
}

#progressbar li.active:before {
    background: #2563eb;
}

#progressbar li.active:after {
    background: #2563eb;
}

/* Fieldsets */
#businessRegistrationForm fieldset {
    background: #fff;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 30px;
    width: 80%;
    margin: 0 auto;
    position: relative;
}

#businessRegistrationForm fieldset:not(:first-of-type) {
    display: none;
}

/* Form Elements */
.fs-title {
    font-size: 18px;
    color: #1f2937;
    margin-bottom: 15px;
    font-weight: 600;
    text-align: center;
}

.fs-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 25px;
    font-weight: 400;
    text-align: center;
}

.form-group {
    margin-bottom: 20px;
    position: relative;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.form-error {
    display: block;
    color: #ef4444;
    font-size: 12px;
    margin-top: 5px;
}

/* Buttons */
.action-button {
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 12px 25px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-block;
    margin: 10px 5px 0;
}

.action-button:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.action-button:active {
    transform: translateY(0);
}

.action-button.previous {
    background: #9ca3af;
}

.action-button.previous:hover {
    background: #6b7280;
}

/* Summary Box */
.summary-box {
    background: #f9fafb;
    border-radius: 6px;
    padding: 20px;
    margin-bottom: 25px;
}

.summary-box h4 {
    color: #2563eb;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-box h4 i {
    font-size: 20px;
}

/* File Upload */
.file-upload {
    margin-bottom: 25px;
}

/* Selected Type Indicator */
.selected-type-indicator {
    background: #f0f9ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.selected-type-indicator span {
    display: flex;
    align-items: center;
}

.selected-type-indicator a {
    text-decoration: none;
    font-size: 12px;
    color: #6b7280;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.selected-type-indicator a:hover {
    background: #e5e7eb;
    color: #374151;
}

.file-upload label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    color: #374151;
}

.file-upload .upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 6px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.file-upload .upload-area:hover {
    border-color: #2563eb;
    background: #f8fafc;
}

.file-upload .upload-area i {
    font-size: 40px;
    color: #9ca3af;
    margin-bottom: 10px;
}

.file-upload .upload-area p {
    color: #6b7280;
    margin: 0;
}

.file-upload input[type="file"] {
    display: none;
}

/* Logo Options Cards */
.logo-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.logo-option-card {
    position: relative;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.logo-option-card:hover {
    border-color: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
}

.logo-option-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.logo-option-card input[type="radio"]:checked + label {
    color: #2563eb;
}

.logo-option-card input[type="radio"]:checked ~ label::before {
    background: #2563eb;
    border-color: #2563eb;
}

.logo-option-card.active {
    border-color: #2563eb;
    background: #f0f9ff;
}

.logo-option-card label {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin: 0;
}

.logo-option-card label i {
    font-size: 32px;
    color: #6b7280;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.logo-option-card.active label i {
    color: #2563eb;
}

.logo-option-card .option-title {
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 5px;
    display: block;
}

.logo-option-card .option-description {
    font-size: 12px;
    color: #6b7280;
    display: block;
}

.logo-section {
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fafafa;
}

.btn-remove-logo,
.btn-regenerate-logo {
    background: transparent;
    border: 1px solid #e5e7eb;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    color: #6b7280;
    margin: 5px;
    transition: all 0.2s ease;
}

.btn-remove-logo:hover {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

.btn-regenerate-logo:hover {
    background: #f0f9ff;
    border-color: #bfdbfe;
    color: #2563eb;
}

/* Responsive design for logo options */
@media (max-width: 768px) {
    .logo-options {
        grid-template-columns: 1fr;
    }
}

/* Terms Agreement */
.terms-agreement {
    display: flex;
    align-items: center;
    margin: 20px 0;
}

.terms-agreement input {
    margin-right: 10px;
}

.terms-agreement label {
    font-size: 14px;
    color: #4b5563;
}

.terms-agreement a {
    color: #2563eb;
    text-decoration: none;
}

.terms-agreement a:hover {
    text-decoration: underline;
}

body, .container-fluid, .card, .main-content, .content {
    background: #fff !important;
    color: #020258 !important;
}
.btn-primary {
    background: #020258 !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
}
.btn-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #020258 !important;
}
.form-control {
    background: #f8f9fa !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}
.form-control:focus {
    border-color: #020258 !important;
    box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
}
.card-header {
    background: #f8f9fa !important;
    color: #020258 !important;
    border-bottom: 1px solid #13e8e9 !important;
}
</style>

<!-- Multi-step Business Registration Form -->
<div class="form-container">
    <form id="businessRegistrationForm" method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data">
        @csrf
        
        <!-- Progress Bar -->
        <ul id="progressbar">
            <li class="active">Business Info</li>
            <li>Contact Details</li>
            <li>Finalize</li>
        </ul>
        
        <!-- Fieldset 1: Business Information -->
        <fieldset>
            <h2 class="fs-title">{{ $typeTitle ?? 'Business Information' }}</h2>
            <h3 class="fs-subtitle">Tell us about your {{ strtolower($typeTitle ?? 'business') }}</h3>
            
            @if(isset($selectedType))
            <div class="form-group">
                <div class="selected-type-indicator">
                    <i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i>
                    <span style="color: #374151; font-weight: 500;">
                        Business Type: 
                        @if($selectedType == 'product')
                            <strong>Product-Based Business</strong>
                        @elseif($selectedType == 'service')
                            <strong>Service-Based Business</strong>
                        @else
                            <strong>Hybrid Business (Products & Services)</strong>
                        @endif
                    </span>
                    <a href="{{ route('business.choose-type') }}" style="margin-left: 10px; color: #6b7280; font-size: 12px;">Change</a>
                </div>
            </div>
            @endif
            
            <div class="form-group">
                <input type="text" name="name" placeholder="Business Name*" value="{{ old('name') }}" required />
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <select name="business_type" required>
                    <option value="">Select Business Type*</option>
                    @foreach($businessTypes as $key => $type)
                        <option value="{{ $key }}" {{ old('business_type') == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                @error('business_type')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; color: #374151; font-size: 14px;">
                    Business Description
                    <span style="color: #6b7280; font-size: 12px;">(Tell us about your business)</span>
                </label>
                <textarea id="businessDescription" name="description" placeholder="Describe your business, products, or services..." style="min-height: 120px;">{{ old('description') }}</textarea>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                    <button type="button" id="enhanceDescriptionBtn" class="btn btn-sm" style="background: #13e8e9; color: #020258; border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px; transition: all 0.3s;">
                        <i class="fas fa-magic"></i>
                        <span>Enhance with AI</span>
                    </button>
                    <small id="descriptionCharCount" style="color: #6b7280; font-size: 12px;">0 characters</small>
                </div>
                <div id="enhancementLoading" style="display: none; margin-top: 10px; padding: 10px; background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 13px; color: #1e40af;">
                    <i class="fas fa-spinner fa-spin"></i> Claude AI is enhancing your description...
                </div>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <input type="button" name="next" class="next action-button" value="Continue" />
        </fieldset>
        
        <!-- Fieldset 2: Contact Details -->
        <fieldset>
            <h2 class="fs-title">Contact Details</h2>
            <h3 class="fs-subtitle">How can customers reach you?</h3>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" />
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <input type="tel" name="phone" placeholder="Phone Number*" value="{{ old('phone') }}" required />
                @error('phone')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <input type="text" name="address" placeholder="Business Address" value="{{ old('address') }}" />
                @error('address')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <input type="text" name="city" placeholder="City*" value="{{ old('city') }}" required />
                @error('city')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <input type="button" name="previous" class="previous action-button" value="Back" />
            <input type="button" name="next" class="next action-button" value="Continue" />
        </fieldset>
        
        <!-- Fieldset 3: Finalization -->
        <fieldset>
            <h2 class="fs-title">Complete Registration</h2>
            <h3 class="fs-subtitle">Review your information</h3>
            
            <div class="summary-box">
                <h4><i class='bx bxs-check-circle'></i> Business Summary</h4>
                <div id="businessSummary"></div>
            </div>
            
            <div class="form-group">
                <label>Business Logo (Optional)</label>
                <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px;">
                    Choose how to add your business logo - you can upload, generate with AI, or skip this step.
                </p>
                
                <!-- Logo Options -->
                <div class="logo-options">
                    <div class="logo-option-card" id="uploadLogoOption">
                        <input type="radio" name="logo_option" id="uploadLogoRadio" value="upload" checked />
                        <label for="uploadLogoRadio">
                            <i class='bx bxs-cloud-upload'></i>
                            <span class="option-title">Upload Logo</span>
                            <span class="option-description">Upload an existing logo file</span>
                        </label>
                    </div>
                    
                    <div class="logo-option-card" id="generateLogoOption">
                        <input type="radio" name="logo_option" id="generateLogoRadio" value="generate" />
                        <label for="generateLogoRadio">
                            <i class='bx bx-magic-wand'></i>
                            <span class="option-title">Generate with AI</span>
                            <span class="option-description">Create a unique logo using AI</span>
                        </label>
                    </div>
                    
                    <div class="logo-option-card" id="skipLogoOption">
                        <input type="radio" name="logo_option" id="skipLogoRadio" value="skip" />
                        <label for="skipLogoRadio">
                            <i class='bx bx-skip-next'></i>
                            <span class="option-title">Skip for Now</span>
                            <span class="option-description">Add your logo later</span>
                        </label>
                    </div>
                </div>
                
                <!-- Upload Logo Section -->
                <div id="uploadLogoSection" class="logo-section" style="display: block;">
                    <div class="file-upload">
                        <input type="file" id="logo" name="logo" accept="image/*" />
                        <div class="upload-area" onclick="document.getElementById('logo').click();">
                            <i class='bx bxs-cloud-upload'></i>
                            <p>Click to upload logo</p>
                            <small style="color: #9ca3af; font-size: 12px;">PNG, JPG, or GIF (Max 2MB)</small>
                        </div>
                        <div id="uploadPreview" style="display: none; margin-top: 15px; text-align: center;">
                            <img id="uploadPreviewImg" src="" alt="Logo Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e5e7eb;" />
                            <button type="button" class="btn-remove-logo" onclick="removeUploadedLogo()">
                                <i class='bx bx-trash'></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Generate Logo Section -->
                <div id="generateLogoSection" class="logo-section" style="display: none;">
                    <div class="ai-logo-generator">
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="logoStyle" style="font-size: 14px; color: #374151; margin-bottom: 8px; display: block;">
                                Logo Style
                            </label>
                            <select id="logoStyle" name="logo_style" class="form-control" style="width: 100%;">
                                <option value="modern">Modern - Clean and contemporary</option>
                                <option value="classic">Classic - Timeless and elegant</option>
                                <option value="minimal">Minimal - Simple and essential</option>
                                <option value="bold">Bold - Strong and vibrant</option>
                                <option value="playful">Playful - Fun and creative</option>
                                <option value="corporate">Corporate - Professional and formal</option>
                            </select>
                        </div>
                        
                        <button type="button" id="generateLogoBtn" class="action-button" style="width: 100%; margin-bottom: 15px;">
                            <i class='bx bx-magic-wand'></i> Generate Logo with AI
                        </button>
                        
                        <div id="logoGenerationProgress" style="display: none; padding: 15px; background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 6px; text-align: center; margin-bottom: 15px;">
                            <i class='bx bx-loader-alt bx-spin' style="font-size: 24px; color: #2563eb;"></i>
                            <p style="margin: 10px 0 0 0; color: #1e40af; font-size: 14px;">Creating your logo... This may take 15-30 seconds.</p>
                        </div>
                        
                        <div id="generatedLogoPreview" style="display: none; text-align: center;">
                            <img id="generatedLogoImg" src="" alt="Generated Logo" style="max-width: 300px; max-height: 300px; border-radius: 8px; border: 2px solid #e5e7eb; background: #f9fafb; padding: 20px;" />
                            <input type="hidden" id="generatedLogoPath" name="generated_logo_path" />
                            <div style="margin-top: 15px;">
                                <button type="button" class="btn-regenerate-logo" onclick="regenerateLogo()">
                                    <i class='bx bx-refresh'></i> Regenerate
                                </button>
                                <button type="button" class="btn-remove-logo" onclick="removeGeneratedLogo()">
                                    <i class='bx bx-trash'></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Skip Logo Section -->
                <div id="skipLogoSection" class="logo-section" style="display: none;">
                    <div style="padding: 20px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center;">
                        <i class='bx bx-info-circle' style="font-size: 36px; color: #6b7280; margin-bottom: 10px;"></i>
                        <p style="color: #4b5563; margin: 0; font-size: 14px;">
                            You can add your business logo later from your dashboard settings.
                        </p>
                    </div>
                </div>
                
                @error('logo_path')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="terms-agreement">
                <input type="checkbox" id="terms" name="terms" required />
                <label for="terms">I agree to Shopybook's <a href="#">Terms of Service</a></label>
                @error('terms')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <input type="button" name="previous" class="previous action-button" value="Back" />
            <button type="submit" name="submit" class="action-button submit">Complete Registration</button>
        </fieldset>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('businessRegistrationForm');
    const fieldsets = form.querySelectorAll('fieldset');
    const progressBarItems = form.querySelectorAll('#progressbar li');
    let currentStep = 0;

    // Next button click handler
    form.querySelectorAll('.next').forEach(button => {
        button.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                fieldsets[currentStep].style.display = 'none';
                currentStep++;
                fieldsets[currentStep].style.display = 'block';
                updateProgressBar();
                
                // Update summary on last step
                if (currentStep === fieldsets.length - 1) {
                    updateSummary();
                }
            }
        });
    });

    // Previous button click handler
    form.querySelectorAll('.previous').forEach(button => {
        button.addEventListener('click', function() {
            fieldsets[currentStep].style.display = 'none';
            currentStep--;
            fieldsets[currentStep].style.display = 'block';
            updateProgressBar();
        });
    });

    // Update progress bar
    function updateProgressBar() {
        progressBarItems.forEach((item, index) => {
            if (index <= currentStep) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Simple step validation
    function validateStep(step) {
        const inputs = fieldsets[step].querySelectorAll('[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = '#ef4444';
                isValid = false;
            } else {
                input.style.borderColor = '#e0e0e0';
            }
        });
        
        return isValid;
    }

    // Update summary
    function updateSummary() {
        const summary = document.getElementById('businessSummary');
        const formData = new FormData(form);
        
        let html = `
            <div class="summary-item">
                <strong>Business Name:</strong> ${formData.get('name')}
            </div>
            <div class="summary-item">
                <strong>Business Type:</strong> ${form.querySelector('[name="business_type"] option:checked').textContent}
            </div>
            <div class="summary-item">
                <strong>Email:</strong> ${formData.get('email') || 'Not provided'}
            </div>
            <div class="summary-item">
                <strong>Phone:</strong> ${formData.get('phone')}
            </div>
            <div class="summary-item">
                <strong>Location:</strong> ${formData.get('city')}${formData.get('address') ? ', ' + formData.get('address') : ''}
            </div>
        `;
        
        summary.innerHTML = html;
    }

    // File upload preview
    const fileUpload = form.querySelector('.file-upload');
    const uploadArea = fileUpload.querySelector('.upload-area');
    const fileInput = fileUpload.querySelector('input[type="file"]');
    
    uploadArea.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            uploadArea.innerHTML = `
                <i class='bx bxs-check-circle'></i>
                <p>${this.files[0].name}</p>
            `;
            uploadArea.querySelector('i').style.color = '#10b981';
        }
    });

    // Initialize form
    fieldsets.forEach((fieldset, index) => {
        if (index !== 0) fieldset.style.display = 'none';
    });

    // Character counter for description
    const descriptionTextarea = document.getElementById('businessDescription');
    const charCount = document.getElementById('descriptionCharCount');
    
    if (descriptionTextarea && charCount) {
        descriptionTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length + ' characters';
        });
        // Initialize count
        charCount.textContent = descriptionTextarea.value.length + ' characters';
    }

    // AI Description Enhancement
    const enhanceBtn = document.getElementById('enhanceDescriptionBtn');
    const enhancementLoading = document.getElementById('enhancementLoading');
    
    if (enhanceBtn) {
        enhanceBtn.addEventListener('click', async function() {
            const description = descriptionTextarea.value.trim();
            const businessName = form.querySelector('[name="name"]').value.trim();
            const businessType = form.querySelector('[name="business_type"]').value;
            
            // Validation
            if (!description || description.length < 10) {
                alert('Please enter at least 10 characters in your business description before enhancing.');
                descriptionTextarea.focus();
                return;
            }
            
            if (!businessName) {
                alert('Please enter your business name first.');
                form.querySelector('[name="name"]').focus();
                return;
            }
            
            if (!businessType) {
                alert('Please select your business type first.');
                form.querySelector('[name="business_type"]').focus();
                return;
            }
            
            // Show loading state
            enhanceBtn.disabled = true;
            enhanceBtn.style.opacity = '0.6';
            enhanceBtn.style.cursor = 'not-allowed';
            enhancementLoading.style.display = 'block';
            
            try {
                const response = await fetch('{{ route('business.enhance-description') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        description: description,
                        business_name: businessName,
                        business_type: businessType
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.enhanced_description) {
                    // Store original for comparison
                    const originalDescription = description;
                    
                    // Update textarea with enhanced description
                    descriptionTextarea.value = data.enhanced_description;
                    
                    // Update character count
                    charCount.textContent = data.enhanced_description.length + ' characters';
                    
                    // Add visual feedback
                    descriptionTextarea.style.borderColor = '#10b981';
                    descriptionTextarea.style.boxShadow = '0 0 0 2px rgba(16, 185, 129, 0.1)';
                    
                    setTimeout(() => {
                        descriptionTextarea.style.borderColor = '';
                        descriptionTextarea.style.boxShadow = '';
                    }, 2000);
                    
                    // Show success message
                    enhancementLoading.innerHTML = '<i class="fas fa-check-circle"></i> Description enhanced successfully! Your description is now more SEO-friendly.';
                    enhancementLoading.style.background = '#f0fdf4';
                    enhancementLoading.style.borderColor = '#86efac';
                    enhancementLoading.style.color = '#166534';
                    
                    // Hide success message after 3 seconds
                    setTimeout(() => {
                        enhancementLoading.style.display = 'none';
                        enhancementLoading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Claude AI is enhancing your description...';
                        enhancementLoading.style.background = '#f0f9ff';
                        enhancementLoading.style.borderColor = '#bfdbfe';
                        enhancementLoading.style.color = '#1e40af';
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Enhancement failed');
                }
            } catch (error) {
                console.error('Enhancement error:', error);
                
                // Show error message
                enhancementLoading.innerHTML = '<i class="fas fa-exclamation-circle"></i> Enhancement failed. Please try again.';
                enhancementLoading.style.background = '#fef2f2';
                enhancementLoading.style.borderColor = '#fecaca';
                enhancementLoading.style.color = '#991b1b';
                
                setTimeout(() => {
                    enhancementLoading.style.display = 'none';
                    enhancementLoading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Claude AI is enhancing your description...';
                    enhancementLoading.style.background = '#f0f9ff';
                    enhancementLoading.style.borderColor = '#bfdbfe';
                    enhancementLoading.style.color = '#1e40af';
                }, 3000);
            } finally {
                // Reset button state
                enhanceBtn.disabled = false;
                enhanceBtn.style.opacity = '1';
                enhanceBtn.style.cursor = 'pointer';
            }
        });
        
        // Hover effect for enhance button
        enhanceBtn.addEventListener('mouseenter', function() {
            if (!this.disabled) {
                this.style.background = '#020258';
                this.style.color = '#13e8e9';
                this.style.transform = 'translateY(-1px)';
                this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            }
        });
        
        enhanceBtn.addEventListener('mouseleave', function() {
            if (!this.disabled) {
                this.style.background = '#13e8e9';
                this.style.color = '#020258';
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            }
        });
    }

    // Logo Option Handlers
    const logoOptions = document.querySelectorAll('.logo-option-card');
    const uploadSection = document.getElementById('uploadLogoSection');
    const generateSection = document.getElementById('generateLogoSection');
    const skipSection = document.getElementById('skipLogoSection');
    
    logoOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            logoOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to clicked option
            this.classList.add('active');
            
            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Show/hide appropriate sections
            const value = radio.value;
            uploadSection.style.display = value === 'upload' ? 'block' : 'none';
            generateSection.style.display = value === 'generate' ? 'block' : 'none';
            skipSection.style.display = value === 'skip' ? 'block' : 'none';
        });
    });
    
    // Initialize with upload option active
    document.getElementById('uploadLogoOption').classList.add('active');
    
    // File upload preview
    const logoInput = document.getElementById('logo');
    const uploadPreview = document.getElementById('uploadPreview');
    const uploadPreviewImg = document.getElementById('uploadPreviewImg');
    
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    logoInput.value = '';
                    return;
                }
                
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    logoInput.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadPreviewImg.src = e.target.result;
                    uploadPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // AI Logo Generation
    const generateLogoBtn = document.getElementById('generateLogoBtn');
    const logoGenerationProgress = document.getElementById('logoGenerationProgress');
    const generatedLogoPreview = document.getElementById('generatedLogoPreview');
    const generatedLogoImg = document.getElementById('generatedLogoImg');
    const generatedLogoPath = document.getElementById('generatedLogoPath');
    
    if (generateLogoBtn) {
        generateLogoBtn.addEventListener('click', async function() {
            const businessName = form.querySelector('[name="name"]').value.trim();
            const businessDescription = form.querySelector('[name="description"]').value.trim();
            const businessType = form.querySelector('[name="business_type"]').value;
            const logoStyle = document.getElementById('logoStyle').value;
            
            // Validation
            if (!businessName) {
                alert('Please enter your business name first.');
                // Go back to first step
                fieldsets[currentStep].style.display = 'none';
                currentStep = 0;
                fieldsets[currentStep].style.display = 'block';
                updateProgressBar();
                form.querySelector('[name="name"]').focus();
                return;
            }
            
            if (!businessDescription || businessDescription.length < 10) {
                alert('Please provide a business description (at least 10 characters) before generating a logo.');
                // Go back to first step
                fieldsets[currentStep].style.display = 'none';
                currentStep = 0;
                fieldsets[currentStep].style.display = 'block';
                updateProgressBar();
                form.querySelector('[name="description"]').focus();
                return;
            }
            
            if (!businessType) {
                alert('Please select your business type first.');
                // Go back to first step
                fieldsets[currentStep].style.display = 'none';
                currentStep = 0;
                fieldsets[currentStep].style.display = 'block';
                updateProgressBar();
                form.querySelector('[name="business_type"]').focus();
                return;
            }
            
            // Show loading state
            generateLogoBtn.disabled = true;
            generateLogoBtn.style.opacity = '0.6';
            generateLogoBtn.style.cursor = 'not-allowed';
            logoGenerationProgress.style.display = 'block';
            generatedLogoPreview.style.display = 'none';
            
            try {
                const response = await fetch('{{ route('business.generate-logo') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        business_name: businessName,
                        business_description: businessDescription,
                        business_type: businessType,
                        logo_style: logoStyle
                    }),
                    credentials: 'same-origin'
                });
                
                // Check for redirect (authentication failure)
                if (response.redirected) {
                    console.error('Request was redirected to:', response.url);
                    throw new Error('Your session has expired. Please refresh the page and try again.');
                }
                
                // Check if response is ok before parsing
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error:', response.status, errorText);
                    throw new Error(`Server error: ${response.status}. Please try again.`);
                }
                
                // Get response text first to debug
                const responseText = await response.text();
                console.log('=== LOGO GENERATION RESPONSE DEBUG ===');
                console.log('Response length:', responseText.length);
                console.log('First 500 chars:', responseText.substring(0, 500));
                console.log('Last 200 chars:', responseText.substring(Math.max(0, responseText.length - 200)));
                console.log('Is HTML?', responseText.trim().startsWith('<!DOCTYPE') || responseText.trim().startsWith('<html'));
                console.log('======================================');
                
                let data;
                try {
                    data = JSON.parse(responseText);
                    console.log('✓ JSON parsed successfully:', data);
                } catch (parseError) {
                    console.error('✗ JSON PARSE ERROR:', parseError.message);
                    console.error('Full response text:', responseText);
                    
                    // Try to identify the issue
                    if (responseText.trim().startsWith('<!DOCTYPE') || responseText.trim().startsWith('<html')) {
                        throw new Error('Server returned HTML instead of JSON. Check server logs for errors.');
                    } else if (responseText.trim() === '') {
                        throw new Error('Server returned empty response. Check server logs.');
                    } else if (!responseText.trim().startsWith('{')) {
                        throw new Error(`Server response doesn't start with JSON. Starts with: "${responseText.substring(0, 50)}"`);
                    } else {
                        throw new Error('Invalid JSON format from server. Check console for details.');
                    }
                }
                
                if (data.success && data.logo_url) {
                    // Show the generated logo
                    generatedLogoImg.src = data.logo_url;
                    generatedLogoPath.value = data.logo_path;
                    generatedLogoPreview.style.display = 'block';
                    
                    // Hide progress
                    logoGenerationProgress.style.display = 'none';
                    
                    // Show success feedback
                    logoGenerationProgress.innerHTML = '<i class="bx bx-check-circle" style="font-size: 24px; color: #10b981;"></i><p style="margin: 10px 0 0 0; color: #059669; font-size: 14px;">Logo generated successfully!</p>';
                    logoGenerationProgress.style.display = 'block';
                    logoGenerationProgress.style.background = '#f0fdf4';
                    logoGenerationProgress.style.borderColor = '#86efac';
                    
                    setTimeout(() => {
                        logoGenerationProgress.style.display = 'none';
                        logoGenerationProgress.innerHTML = '<i class="bx bx-loader-alt bx-spin" style="font-size: 24px; color: #2563eb;"></i><p style="margin: 10px 0 0 0; color: #1e40af; font-size: 14px;">Creating your logo... This may take 15-30 seconds.</p>';
                        logoGenerationProgress.style.background = '#f0f9ff';
                        logoGenerationProgress.style.borderColor = '#bfdbfe';
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Logo generation failed');
                }
            } catch (error) {
                console.error('Logo generation error:', error);
                
                // Show error message
                logoGenerationProgress.innerHTML = '<i class="bx bx-error-circle" style="font-size: 24px; color: #ef4444;"></i><p style="margin: 10px 0 0 0; color: #dc2626; font-size: 14px;">Failed to generate logo. Please try again or upload your own.</p>';
                logoGenerationProgress.style.background = '#fef2f2';
                logoGenerationProgress.style.borderColor = '#fecaca';
                
                setTimeout(() => {
                    logoGenerationProgress.style.display = 'none';
                    logoGenerationProgress.innerHTML = '<i class="bx bx-loader-alt bx-spin" style="font-size: 24px; color: #2563eb;"></i><p style="margin: 10px 0 0 0; color: #1e40af; font-size: 14px;">Creating your logo... This may take 15-30 seconds.</p>';
                    logoGenerationProgress.style.background = '#f0f9ff';
                    logoGenerationProgress.style.borderColor = '#bfdbfe';
                }, 5000);
            } finally {
                // Reset button state
                generateLogoBtn.disabled = false;
                generateLogoBtn.style.opacity = '1';
                generateLogoBtn.style.cursor = 'pointer';
            }
        });
    }
});

// Global functions for logo management
function removeUploadedLogo() {
    const logoInput = document.getElementById('logo');
    const uploadPreview = document.getElementById('uploadPreview');
    
    logoInput.value = '';
    uploadPreview.style.display = 'none';
}

function removeGeneratedLogo() {
    const generatedLogoPreview = document.getElementById('generatedLogoPreview');
    const generatedLogoPath = document.getElementById('generatedLogoPath');
    
    generatedLogoPreview.style.display = 'none';
    generatedLogoPath.value = '';
}

function regenerateLogo() {
    removeGeneratedLogo();
    document.getElementById('generateLogoBtn').click();
}
</script>
