@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bulk SMS</h1>
        <div>
            <span class="badge badge-info">API Integration Pending</span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Send Bulk SMS</h6>
                </div>
                <div class="card-body">
                    <form id="smsForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="template">Message Template</label>
                            <select class="form-control" id="template" name="template">
                                <option value="">Select a template or write custom message</option>
                                @foreach($templates as $key => $template)
                                <option value="{{ $key }}">{{ ucfirst($key) }} Template</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="4" 
                                      maxlength="160" placeholder="Enter your message here..." required></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Maximum 160 characters</small>
                                <small class="text-muted"><span id="charCount">0</span>/160</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="scheduled_at">Schedule (Optional)</label>
                            <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at">
                            <small class="form-text text-muted">Leave empty to send immediately</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Select Recipients</label>
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
                                    <span class="badge badge-info" id="selectedCount">0 selected</span>
                                </div>
                            </div>
                            
                            <div class="row">
                                @foreach($customers as $customer)
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input customer-checkbox" 
                                               id="customer_{{ $customer->id }}" name="customer_ids[]" 
                                               value="{{ $customer->id }}" data-phone="{{ $customer->phone }}">
                                        <label class="custom-control-label" for="customer_{{ $customer->id }}">
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $customer->name }}</span>
                                                <small class="text-muted">{{ $customer->phone }}</small>
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
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">SMS Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <div class="h4 text-primary" id="totalRecipients">0</div>
                                <small class="text-muted">Recipients</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success" id="estimatedCost">₦0</div>
                            <small class="text-muted">Estimated Cost</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Message Preview</h6>
                </div>
                <div class="card-body">
                    <div id="messagePreview" class="border rounded p-3 bg-light">
                        <p class="mb-2"><strong>To:</strong> <span id="previewRecipients">No recipients selected</span></p>
                        <p class="mb-2"><strong>Message:</strong></p>
                        <div id="previewMessage" class="text-muted">Your message will appear here...</div>
                        <hr>
                        <small class="text-muted">
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
        estimatedCost.textContent = `₦${(count * 5).toFixed(2)}`; // Assuming ₦5 per SMS
        
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
@endpush 