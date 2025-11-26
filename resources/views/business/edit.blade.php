@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4" style="background: #f8f9fa; min-height: 100vh; padding-top: 2rem;">
    <div class="row justify-content-start" style="padding-left: 2rem;">
        <!-- Logo Card -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100" style="min-height: 220px; border: 1px solid #13e8e9;">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3" style="color:#020258;">Business Logo</h5>
                    @if($business->logo_path)
                        <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Business Logo" class="rounded-full object-cover border-4 border-cyan-200 shadow mb-2" style="width:200px; max-width:100%; height:auto;">
                    @else
                        <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center text-3xl text-gray-400 mb-2" style="width:200px; height:200px;">
                            <i class="fas fa-store"></i>
                        </div>
                        <button type="button" class="btn btn-info btn-sm mt-2" onclick="openLogoGenerator()">
                            <i class="fas fa-magic"></i> Generate with AI
                        </button>
                    @endif
                    <p class="mt-1 small text-muted">Logo will be updated with the form</p>
                </div>
            </div>
        </div>
        <!-- Form Card -->
        <div class="col-md-8 col-lg-9">
            <div class="card" style="border: 1px solid #13e8e9;">
                <div class="card-body p-4">
                    <h2 class="text-2xl font-bold mb-6" style="color:#020258;">Edit Business Profile</h2>
                    
                    <!-- Display validation errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form id="businessEditForm" method="POST" action="{{ route('business.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Logo Upload Field -->
                        <div class="mb-4">
                            <label for="logo" class="form-label"><i class="fas fa-image me-1"></i> Update Business Logo</label>
                            <input id="logo" class="form-control" type="file" name="logo" accept="image/*">
                            <div class="form-text">Leave blank to keep current logo</div>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>
                        
                        <div class="row g-3">
                            <!-- Business Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label"><i class="fas fa-briefcase me-1"></i> Business Name *</label>
                                <input id="name" class="form-control" type="text" name="name" value="{{ old('name', $business->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <!-- Business Type -->
                            <div class="col-md-6">
                                <label for="business_type" class="form-label"><i class="fas fa-tags me-1"></i> Business Type *</label>
                                <select id="business_type" name="business_type" class="form-control" required>
                                    @foreach($businessTypes as $key => $type)
                                        <option value="{{ $key }}" {{ old('business_type', $business->business_type) == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                            </div>
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i> Phone Number *</label>
                                <input id="phone" class="form-control" type="text" name="phone" value="{{ old('phone', $business->phone) }}" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i> Email Address</label>
                                <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $business->email) }}" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <!-- Address -->
                            <div class="col-md-6">
                                <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i> Address *</label>
                                <input id="address" class="form-control" type="text" name="address" value="{{ old('address', $business->address) }}" required />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <!-- City -->
                            <div class="col-md-6">
                                <label for="city" class="form-label"><i class="fas fa-city me-1"></i> City *</label>
                                <input id="city" class="form-control" type="text" name="city" value="{{ old('city', $business->city) }}" required />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Business Description</label>
                            <x-ai-enhanced-textarea name="description" 
                                                   content-type="description" 
                                                   tone="professional" 
                                                   rows="3" 
                                                   placeholder="Describe your business...">
                                {{ old('description', $business->description) }}
                            </x-ai-enhanced-textarea>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" onclick="if(confirm('Are you sure you want to delete this business? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }" class="btn btn-outline-danger ms-auto" style="border:2px solid #dc3545; color:#dc3545;">
                                <i class="fas fa-trash-alt me-1"></i> Delete Business
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Business Profile
                            </button>
                        </div>
                    </form>

                    <!-- Inline OTP Modal for 2FA Verification -->
                    <div id="otpOverlay" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050;">
                        <div class="otp-modal" style="max-width: 520px; margin: 10vh auto; background: #fff; border-radius: 12px; border: 1px solid #13e8e9; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                            <div style="padding: 2rem 2rem 0.5rem 2rem; text-align:center;">
                                <div class="mx-auto" style="height:56px; width:56px; border-radius:50%; background:#e6fbfb; display:flex; align-items:center; justify-content:center; margin-bottom: 12px;">
                                    <i class="fas fa-shield-alt" style="color:#0bb; font-size: 20px;"></i>
                                </div>
                                <h5 style="color:#020258; font-weight:700; margin:0 0 6px 0;">Verify to Continue</h5>
                                <p class="text-muted" style="margin:0 0 10px 0;">Enter the 6-digit code sent to your email</p>
                            </div>
                            <div style="padding: 0 2rem 2rem 2rem;">
                                <form id="otpForm">
                                    @csrf
                                    <input type="hidden" name="action" value="business_edit">
                                    <div class="mb-3">
                                        <input id="otpCode" name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" class="form-control" placeholder="000000" style="text-align:center; font-size:24px; letter-spacing:8px; padding: 14px 12px;" required />
                                        <div class="form-text">Code expires in 10 minutes</div>
                                    </div>
                                    <div class="d-flex gap-2" style="gap:10px;">
                                        <button type="submit" class="btn btn-primary flex-fill" style="flex:1;">
                                            <i class="fas fa-check me-1"></i> Verify
                                        </button>
                                        <button type="button" id="otpResendBtn" class="btn btn-outline-secondary">
                                            <i class="fas fa-redo me-1"></i> Resend
                                        </button>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" id="otpCancelBtn" class="btn btn-link text-muted">Cancel</button>
                                    </div>
                                    <div id="otpStatus" class="mt-3" style="display:none;"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delete Business Form (Hidden) -->
                    <form id="delete-form" method="POST" action="{{ route('business.destroy', $business) }}" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let twofaVerified = false;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('businessEditForm');
    const overlay = document.getElementById('otpOverlay');
    const otpForm = document.getElementById('otpForm');
    const otpCode = document.getElementById('otpCode');
    const otpStatus = document.getElementById('otpStatus');
    const resendBtn = document.getElementById('otpResendBtn');
    const cancelBtn = document.getElementById('otpCancelBtn');

    form.addEventListener('submit', function(e) {
        if (!twofaVerified) {
            e.preventDefault();
            sendCode();
            overlay.style.display = 'block';
            setTimeout(() => otpCode && otpCode.focus(), 50);
        }
    });

    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        verifyCode(otpCode.value.trim());
    });

    resendBtn.addEventListener('click', function() {
        resendCode();
    });

    cancelBtn.addEventListener('click', function() {
        overlay.style.display = 'none';
    });

    function csrfToken() {
        const el = form.querySelector('input[name="_token"]');
        return el ? el.value : '';
    }

    function showOtpStatus(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        otpStatus.innerHTML = `<div class="alert ${alertClass}"><i class="fas ${icon} me-1"></i> ${message}</div>`;
        otpStatus.style.display = 'block';
    }

    function sendCode() {
        fetch('{{ route('2fa.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ action: 'business_edit' })
        }).catch(() => {});
    }

    function resendCode() {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
        fetch('{{ route('2fa.resend') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ action: 'business_edit' })
        }).then(r => r.json()).then(data => {
            showOtpStatus(data.message || 'Code sent.', data.success ? 'success' : 'error');
        }).catch(() => {
            showOtpStatus('Failed to send code. Please try again.', 'error');
        }).finally(() => {
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fas fa-redo me-1"></i> Resend';
        });
    }

    function verifyCode(code) {
        if (!code || code.length !== 6) {
            showOtpStatus('Please enter the 6-digit code.', 'error');
            return;
        }
        fetch('{{ route('2fa.verify') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ action: 'business_edit', code })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                showOtpStatus('Verified. Saving changes...', 'success');
                twofaVerified = true;
                setTimeout(() => {
                    overlay.style.display = 'none';
                    form.submit();
                }, 500);
            } else {
                showOtpStatus(data.message || 'Invalid code. Please try again.', 'error');
            }
        }).catch(() => {
            showOtpStatus('Verification failed. Please try again.', 'error');
        });
    }
});

// Override AI enhancement for business description
if (typeof enhanceTextarea === 'function') {
    const originalEnhanceTextarea = enhanceTextarea;
    
    enhanceTextarea = function(textareaId) {
        // Check if this is the business description field
        if (textareaId === 'description') {
            const textarea = document.getElementById(textareaId);
            const businessNameInput = document.getElementById('name');
            const businessTypeInput = document.getElementById('business_type');
            
            if (!textarea || !businessNameInput || !businessTypeInput) {
                console.error('Required fields not found');
                return;
            }
            
            const description = textarea.value || '';
            const businessName = businessNameInput.value || '';
            const businessType = businessTypeInput.value || '';
            
            if (!description.trim()) {
                alert('Please enter some content to enhance.');
                return;
            }
            
            if (!businessName.trim()) {
                alert('Please enter your business name first.');
                return;
            }
            
            showAIStatus(textareaId);
            
            fetch('{{ route('business.enhance-description') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    description: description,
                    business_name: businessName,
                    business_type: businessType
                })
            })
            .then(response => response.json())
            .then(data => {
                hideAIStatus(textareaId);
                if (data.success) {
                    textarea.value = data.enhanced_description;
                    showSuccessMessage(textareaId, 'Business description enhanced successfully!');
                } else {
                    showErrorMessage(textareaId, data.message || 'Enhancement failed');
                }
            })
            .catch(error => {
                hideAIStatus(textareaId);
                console.error('AI Enhancement Error:', error);
                showErrorMessage(textareaId, 'Error: ' + error.message);
            });
        } else {
            // Use original function for other textareas
            originalEnhanceTextarea(textareaId);
        }
    };
}
</script>

<style>
/* Enhanced Business Edit Form Styles */
.container-fluid {
    background: #f8f9fa !important;
    padding-left: 3rem !important;
}

.card {
    background: #fff !important;
    border: 1px solid #13e8e9 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.card-body {
    padding: 2rem !important;
}

.card-title {
    color: #020258 !important;
    font-weight: 600 !important;
    margin-bottom: 1rem !important;
}

.form-label {
    color: #020258 !important;
    font-weight: 500 !important;
    margin-bottom: 0.5rem !important;
}

.form-control {
    background: #f8f9fa !important;
    color: #020258 !important;
    border: 1px solid #13e8e9 !important;
    border-radius: 6px !important;
    padding: 0.75rem !important;
    margin-bottom: 1rem !important;
}

.form-control:focus {
    border-color: #020258 !important;
    box-shadow: 0 0 0 0.2rem rgba(19, 232, 233, 0.25) !important;
    background: #fff !important;
}

.btn-primary {
    background: #020258 !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    padding: 0.75rem 1.5rem !important;
}

.btn-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #020258 !important;
}

.btn-outline-danger {
    border: 2px solid #dc3545 !important;
    color: #dc3545 !important;
    background: transparent !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
}

.btn-outline-danger:hover {
    background: #dc3545 !important;
    color: #fff !important;
}

.form-text {
    color: #6c757d !important;
    font-size: 0.875rem !important;
}

.alert-danger {
    background: rgba(220, 53, 69, 0.1) !important;
    border: 1px solid #dc3545 !important;
    color: #dc3545 !important;
    border-radius: 6px !important;
}

/* Enhanced left padding for mobile */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1.5rem !important;
        padding-right: 1rem !important;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}

/* Logo styling */
.rounded-full {
    border-radius: 50% !important;
}

.text-center {
    text-align: center !important;
}

/* Business logo placeholder */
.bg-gray-200 {
    background-color: #e9ecef !important;
    color: #6c757d !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto !important;
    border-radius: 50% !important;
}
</style>

<!-- AI Logo Generation Modal -->
<div id="logoGeneratorModal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: 2px solid #13e8e9; border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #020258, #13e8e9); color: white; border-radius: 13px 13px 0 0;">
                <h5 class="modal-title"><i class="fas fa-magic"></i> Generate Business Logo with AI</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <p class="text-muted mb-3">Choose a logo style and let AI create a unique logo for your business.</p>
                
                <div class="mb-3">
                    <label for="logoStyle" class="form-label"><i class="fas fa-palette"></i> Logo Style</label>
                    <select id="logoStyle" class="form-select">
                        <option value="modern">Modern & Minimalist</option>
                        <option value="professional">Professional & Corporate</option>
                        <option value="creative">Creative & Artistic</option>
                        <option value="playful">Playful & Fun</option>
                        <option value="elegant">Elegant & Luxurious</option>
                        <option value="bold">Bold & Vibrant</option>
                    </select>
                </div>
                
                <div id="generationStatus" class="alert alert-info" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> <span id="statusMessage">Generating your logo...</span>
                </div>
                
                <div id="generatedLogoContainer" style="display: none; text-align: center; margin-top: 20px;">
                    <img id="generatedLogoPreview" src="" alt="Generated Logo" style="max-width: 300px; max-height: 300px; border-radius: 8px; border: 2px solid #e5e7eb; background: #f9fafb; padding: 20px;" />
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="regenerateLogoFromModal()">
                            <i class="fas fa-redo"></i> Regenerate
                        </button>
                        <button type="button" class="btn btn-sm btn-success" onclick="useGeneratedLogoInForm()">
                            <i class="fas fa-check"></i> Use This Logo
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="generateLogoModalBtn" class="btn btn-primary" onclick="generateLogoFromModal()">
                    <i class="fas fa-magic"></i> Generate Logo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let generatedLogoPath = null;
let logoGeneratorModal = null;

document.addEventListener('DOMContentLoaded', function() {
    logoGeneratorModal = new bootstrap.Modal(document.getElementById('logoGeneratorModal'));
});

function openLogoGenerator() {
    logoGeneratorModal.show();
    document.getElementById('generatedLogoContainer').style.display = 'none';
    document.getElementById('generationStatus').style.display = 'none';
}

async function generateLogoFromModal() {
    const style = document.getElementById('logoStyle').value;
    const statusDiv = document.getElementById('generationStatus');
    const statusMessage = document.getElementById('statusMessage');
    const generateBtn = document.getElementById('generateLogoModalBtn');
    const logoContainer = document.getElementById('generatedLogoContainer');
    
    statusDiv.style.display = 'block';
    statusMessage.textContent = 'Generating your logo... This may take 30-60 seconds.';
    generateBtn.disabled = true;
    logoContainer.style.display = 'none';
    
    try {
        const businessName = {!! json_encode($business->name ?? '') !!};
        const businessDescription = {!! json_encode($business->description ?? '') !!};
        const businessType = {!! json_encode($business->business_type ?? '') !!};
        
        const response = await fetch('{{ route("business.generate-logo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                business_name: businessName,
                business_description: businessDescription || 'A professional ' + businessType + ' business providing quality services',
                business_type: businessType,
                logo_style: style
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            statusDiv.style.display = 'none';
            document.getElementById('generatedLogoPreview').src = data.logo_url;
            generatedLogoPath = data.logo_path;
            logoContainer.style.display = 'block';
        } else {
            statusDiv.className = 'alert alert-danger';
            statusMessage.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + (data.message || 'Failed to generate logo');
        }
    } catch (error) {
        statusDiv.className = 'alert alert-danger';
        statusMessage.textContent = 'Error: ' + error.message;
    } finally {
        generateBtn.disabled = false;
    }
}

function regenerateLogoFromModal() {
    generateLogoFromModal();
}

function useGeneratedLogoInForm() {
    if (!generatedLogoPath) {
        alert('No logo generated yet');
        return;
    }
    
    // Create a hidden input to submit the generated logo path
    const form = document.getElementById('businessEditForm');
    let hiddenInput = document.getElementById('generated_logo_path_input');
    
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'generated_logo_path';
        hiddenInput.id = 'generated_logo_path_input';
        form.appendChild(hiddenInput);
    }
    
    hiddenInput.value = generatedLogoPath;
    
    // Update the logo preview in the card
    const logoCard = document.querySelector('.card-body .rounded-full, .card-body img[alt="Business Logo"]');
    if (logoCard) {
        const newImg = document.createElement('img');
        newImg.src = document.getElementById('generatedLogoPreview').src;
        newImg.alt = 'Business Logo';
        newImg.className = 'rounded-full object-cover border-4 border-cyan-200 shadow mb-2';
        newImg.style = 'width:200px; max-width:100%; height:auto;';
        logoCard.parentNode.replaceChild(newImg, logoCard);
    }
    
    // Close modal
    logoGeneratorModal.hide();
    
    // Show success message
    const successAlert = document.createElement('div');
    successAlert.className = 'alert alert-success alert-dismissible fade show';
    successAlert.innerHTML = '<i class="fas fa-check-circle"></i> Logo generated! Click "Update Business Profile" to save it. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.querySelector('.card-body form').insertBefore(successAlert, document.querySelector('.card-body form').firstChild);
}
</script>
@endsection