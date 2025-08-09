@extends('layouts.dash')

@section('title', 'Bulk SMS')

@section('content')
<!-- Sub-navigation for Marketing -->
<div class="sub-navigation mb-4">
    <div class="nav-tabs">
        <a href="{{ route('marketing.social-media') }}" class="nav-tab">
            <i class="fas fa-share-alt me-1"></i> Social Media
        </a>
        <a href="{{ route('marketing.promotions') }}" class="nav-tab">
            <i class="fas fa-bullhorn me-1"></i> Promotions
        </a>
        <a href="{{ route('marketing.advertising') }}" class="nav-tab">
            <i class="fas fa-ad me-1"></i> Advertising
        </a>
        <a href="{{ route('marketing.bulk-sms') }}" class="nav-tab active">
            <i class="fas fa-sms me-1"></i> Bulk SMS
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-primary);">Bulk SMS</h1>
    <div>
        <span class="badge" style="background: var(--info-color); color: var(--white);">API Integration Pending</span>
    </div>
</div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Send Bulk SMS</h6>
                </div>
                <div class="card-body">
                    <form id="smsForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="template" style="color: var(--text-primary);">Message Template</label>
                            <select class="form-control" id="template" name="template" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                <option value="">Select a template or write custom message</option>
                                @foreach($templates as $key => $template)
                                <option value="{{ $key }}">{{ ucfirst($key) }} Template</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <x-ai-enhanced-textarea name="message" 
                                                   content-type="sms" 
                                                   tone="friendly" 
                                                   rows="4" 
                                                   placeholder="Enter your message...">
                            </x-ai-enhanced-textarea>
                        </div>

                        <div class="form-group">
                            <label for="scheduled_at" style="color: var(--text-primary);">Schedule (Optional)</label>
                            <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                            <small class="form-text" style="color: var(--text-muted);">Leave empty to send immediately</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label style="color: var(--text-primary);">Select Recipients</label>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAll()">
                                        <i class="fas fa-check-square"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">
                                        <i class="fas fa-square"></i> Deselect All
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <span class="badge" id="selectedCount" style="background: var(--info-color); color: var(--white);">0 selected</span>
                                </div>
                            </div>
                            
                            <div class="row">
                                @foreach($customers as $customer)
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input customer-checkbox" 
                                               id="customer_{{ $customer->id }}" name="customer_ids[]" 
                                               value="{{ $customer->id }}" data-phone="{{ $customer->phone }}">
                                        <label class="custom-control-label" for="customer_{{ $customer->id }}" style="color: var(--text-primary);">
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $customer->name }}</span>
                                                <small style="color: var(--text-muted);">{{ $customer->phone }}</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                <i class="fas fa-paper-plane"></i> Send SMS
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="previewMessage()">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">SMS Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right" style="border-right: 1px solid var(--border-color);">
                                <div class="h4" id="totalRecipients" style="color: var(--primary-color);">0</div>
                                <small style="color: var(--text-muted);">Recipients</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4" id="estimatedCost" style="color: var(--success-color);">KSh 0</div>
                            <small style="color: var(--text-muted);">Estimated Cost</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Message Preview</h6>
                </div>
                <div class="card-body">
                    <div id="messagePreview" class="border rounded p-3" style="border: 1px solid var(--border-color) !important; background: var(--bg-tertiary);">
                        <p class="mb-2" style="color: var(--text-primary);"><strong>To:</strong> <span id="previewRecipients" style="color: var(--text-secondary);">No recipients selected</span></p>
                        <p class="mb-2" style="color: var(--text-primary);"><strong>Message:</strong></p>
                        <div id="previewMessage" style="color: var(--text-muted);">Your message will appear here...</div>
                        <hr style="border-color: var(--border-color);">
                        <small style="color: var(--text-muted);">
                            <i class="fas fa-info-circle"></i> 
                            SMS API integration is pending. Messages will be logged for later processing.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SMS Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Recipients ({{ count($customers) }})</h6>
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            @foreach($customers as $customer)
                            <div class="d-flex justify-content-between py-1">
                                <span>{{ $customer->name }}</span>
                                <small class="text-muted">{{ $customer->phone }}</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Message</h6>
                        <div class="border rounded p-3 bg-light">
                            <div id="modalMessage">Your message will appear here...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const message = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const template = document.getElementById('template');
    const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const totalRecipients = document.getElementById('totalRecipients');
    const estimatedCost = document.getElementById('estimatedCost');
    const previewRecipients = document.getElementById('previewRecipients');
    const previewMessage = document.getElementById('previewMessage');
    const modalMessage = document.getElementById('modalMessage');

    const templates = @json($templates);

    // Character count
    message.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        charCount.className = count > 160 ? 'text-danger' : 'text-muted';
        updatePreview();
    });

    // Template selection
    template.addEventListener('change', function() {
        if (this.value && templates[this.value]) {
            message.value = templates[this.value];
            message.dispatchEvent(new Event('input'));
        }
    });

    // Customer selection
    customerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateStats);
    });

    function updateStats() {
        const selected = document.querySelectorAll('.customer-checkbox:checked');
        const count = selected.length;
        
        selectedCount.textContent = `${count} selected`;
        totalRecipients.textContent = count;
        estimatedCost.textContent = `KSh ${(count * 5).toFixed(2)}`; // Assuming KSh 5 per SMS
        
        updatePreview();
    }

    function updatePreview() {
        const selected = document.querySelectorAll('.customer-checkbox:checked');
        const count = selected.length;
        
        if (count === 0) {
            previewRecipients.textContent = 'No recipients selected';
        } else if (count === 1) {
            previewRecipients.textContent = selected[0].nextElementSibling.querySelector('span').textContent;
        } else {
            previewRecipients.textContent = `${count} recipients selected`;
        }
        
        previewMessage.textContent = message.value || 'Your message will appear here...';
        modalMessage.textContent = message.value || 'Your message will appear here...';
    }

    // Form submission
    document.getElementById('smsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selected = document.querySelectorAll('.customer-checkbox:checked');
        if (selected.length === 0) {
            alert('Please select at least one recipient');
            return;
        }
        
        if (!message.value.trim()) {
            alert('Please enter a message');
            return;
        }
        
        if (confirm(`Send SMS to ${selected.length} recipients?`)) {
            const formData = new FormData(this);
            
            fetch('{{ route("marketing.sms.send") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message + '\n\n' + data.note);
                    this.reset();
                    updateStats();
                } else {
                    alert('Error sending SMS');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending SMS');
            });
        }
    });

    function selectAll() {
        customerCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateStats();
    }

    function deselectAll() {
        customerCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateStats();
    }

    function previewMessage() {
        $('#previewModal').modal('show');
    }

    updateStats();
});
</script>

<style>
.sub-navigation {
    background: var(--card-bg);
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
}

.sub-navigation .nav-tabs {
    display: flex;
    gap: 1rem;
    list-style: none;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
}

.sub-navigation .nav-tab {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-muted);
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.sub-navigation .nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
}

.sub-navigation .nav-tab.active {
    color: var(--primary-color);
    background: var(--primary-color);
    color: var(--white);
}

@media (max-width: 768px) {
    .sub-navigation .nav-tabs {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .sub-navigation .nav-tab {
        text-align: center;
        padding: 0.75rem 1rem;
    }
}
</style>
@endpush 