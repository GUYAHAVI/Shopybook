@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Advertising Campaign</h1>
        <a href="{{ route('marketing.advertising') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Campaigns
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('marketing.advertising.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Campaign Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="platform">Platform *</label>
                                    <select class="form-control @error('platform') is-invalid @enderror" 
                                            id="platform" name="platform" required>
                                        <option value="">Select Platform</option>
                                        <option value="facebook" {{ old('platform') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                        <option value="instagram" {{ old('platform') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                        <option value="google" {{ old('platform') == 'google' ? 'selected' : '' }}>Google Ads</option>
                                        <option value="email" {{ old('platform') == 'email' ? 'selected' : '' }}>Email Marketing</option>
                                        <option value="sms" {{ old('platform') == 'sms' ? 'selected' : '' }}>SMS Marketing</option>
                                    </select>
                                    @error('platform')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="budget">Budget (₦) *</label>
                                    <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                           id="budget" name="budget" value="{{ old('budget') }}" 
                                           step="0.01" min="0" required>
                                    @error('budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="paused" {{ old('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="target_audience">Target Audience</label>
                            <textarea class="form-control @error('target_audience') is-invalid @enderror" 
                                      id="target_audience" name="target_audience" rows="2" 
                                      placeholder="e.g., Age 25-45, interested in technology, located in Lagos">{{ old('target_audience') }}</textarea>
                            @error('target_audience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Campaign
                            </button>
                            <a href="{{ route('marketing.advertising') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Preview</h6>
                </div>
                <div class="card-body">
                    <div id="preview-card" class="border rounded p-3 bg-light">
                        <h6 id="preview-name">Campaign Name</h6>
                        <p id="preview-description" class="text-muted">Description will appear here</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge badge-primary" id="preview-platform">Platform</span>
                            <span class="badge badge-secondary" id="preview-status">Status</span>
                        </div>
                        <div class="text-sm">
                            <div><strong>Budget:</strong> <span id="preview-budget">₦0</span></div>
                            <div><strong>Duration:</strong> <span id="preview-duration">Start - End</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Platform Tips</h6>
                </div>
                <div class="card-body">
                    <div id="platform-tips">
                        <p class="text-muted">Select a platform to see optimization tips</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const name = document.getElementById('name');
    const description = document.getElementById('description');
    const platform = document.getElementById('platform');
    const status = document.getElementById('status');
    const budget = document.getElementById('budget');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    const platformTips = {
        facebook: 'Use engaging visuals and clear call-to-actions. Target specific demographics and interests.',
        instagram: 'Focus on high-quality images and stories. Use relevant hashtags and engage with your audience.',
        google: 'Use relevant keywords and compelling ad copy. Optimize landing pages for conversions.',
        email: 'Personalize content and use compelling subject lines. Segment your audience for better results.',
        sms: 'Keep messages short and include clear call-to-actions. Respect opt-out requests.'
    };

    function updatePreview() {
        document.getElementById('preview-name').textContent = name.value || 'Campaign Name';
        document.getElementById('preview-description').textContent = description.value || 'Description will appear here';
        document.getElementById('preview-platform').textContent = platform.value ? platform.value.charAt(0).toUpperCase() + platform.value.slice(1) : 'Platform';
        document.getElementById('preview-status').textContent = status.value ? status.value.charAt(0).toUpperCase() + status.value.slice(1) : 'Status';
        document.getElementById('preview-budget').textContent = budget.value ? `₦${parseFloat(budget.value).toLocaleString()}` : '₦0';
        
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            const end = new Date(endDate.value).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('preview-duration').textContent = `${start} - ${end}`;
        }

        // Update platform tips
        const tipsDiv = document.getElementById('platform-tips');
        if (platform.value && platformTips[platform.value]) {
            tipsDiv.innerHTML = `<p class="text-info"><i class="fas fa-lightbulb"></i> ${platformTips[platform.value]}</p>`;
        } else {
            tipsDiv.innerHTML = '<p class="text-muted">Select a platform to see optimization tips</p>';
        }
    }

    // Add event listeners
    [name, description, platform, status, budget, startDate, endDate].forEach(element => {
        element.addEventListener('input', updatePreview);
        element.addEventListener('change', updatePreview);
    });

    // Set default dates
    if (!startDate.value) {
        startDate.value = new Date().toISOString().split('T')[0];
    }
    if (!endDate.value) {
        const endDateDefault = new Date();
        endDateDefault.setMonth(endDateDefault.getMonth() + 1);
        endDate.value = endDateDefault.toISOString().split('T')[0];
    }

    updatePreview();
});
</script>
@endpush 