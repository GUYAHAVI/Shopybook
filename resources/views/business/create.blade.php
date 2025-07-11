@extends('layouts.master')

@section('content')<style>
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
<head>

</head>
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
            <h2 class="fs-title">Business Information</h2>
            <h3 class="fs-subtitle">Tell us about your business</h3>
            
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
                <textarea name="description" placeholder="Business Description">{{ old('description') }}</textarea>
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
            
            <div class="form-group file-upload">
                <label for="logo">Business Logo</label>
                <input type="file" id="logo" name="logo" accept="image/*" />
                <div class="upload-area">
                    <i class='bx bxs-cloud-upload'></i>
                    <p>Click to upload logo</p>
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
});
</script>
