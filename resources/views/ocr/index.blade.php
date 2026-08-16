@extends('layouts.dash')
@section('title', 'OCR Data Capture')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="fas fa-camera"></i> OCR Data Capture
            </h1>
            <p class="text-muted">Scan handwritten or printed records to automatically add them to your system</p>
        </div>
    </div>

    <!-- Record Type Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-tasks"></i> What would you like to scan?
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="record-type-card" data-type="inventory" onclick="selectRecordType('inventory')">
                                <div class="record-icon">
                                    <i class="fas fa-boxes fa-3x"></i>
                                </div>
                                <h5>Product Inventory</h5>
                                <p class="text-muted small">Scan inventory lists, stock counts, product details</p>
                                <div class="record-badge">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="record-type-card" data-type="sales" onclick="selectRecordType('sales')">
                                <div class="record-icon">
                                    <i class="fas fa-receipt fa-3x"></i>
                                </div>
                                <h5>Sales Records</h5>
                                <p class="text-muted small">Scan receipts, sales logs, transaction records</p>
                                <div class="record-badge">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="record-type-card" data-type="services" onclick="selectRecordType('services')">
                                <div class="record-icon">
                                    <i class="fas fa-calendar-check fa-3x"></i>
                                </div>
                                <h5>Service Bookings</h5>
                                <p class="text-muted small">Scan appointment books, booking records</p>
                                <div class="record-badge">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Upload Section -->
    <div class="row mb-4" id="uploadSection" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-cloud-upload-alt"></i> Upload Image
                    </h5>

                    <div class="upload-zone" id="uploadZone">
                        <input type="file" id="imageInput" accept="image/*" capture="camera" style="display: none;">
                        <div class="upload-placeholder">
                            <i class="fas fa-camera fa-4x mb-3 text-primary"></i>
                            <h5>Take a photo or upload an image</h5>
                            <p class="text-muted">Supports: JPG, PNG, HEIC - Max 10MB</p>
                            <button type="button" class="btn btn-primary btn-lg" onclick="document.getElementById('imageInput').click()">
                                <i class="fas fa-camera"></i> Choose Image
                            </button>
                        </div>
                        <div id="imagePreview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded">
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-lg" onclick="processImage()">
                                    <i class="fas fa-magic"></i> Extract Data
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetUpload()">
                                    <i class="fas fa-redo"></i> Choose Different Image
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="processingStatus" style="display: none;" class="mt-4">
                        <div class="alert alert-info">
                            <i class="fas fa-spinner fa-spin"></i> 
                            <span id="statusMessage">Processing image... This may take 15-30 seconds...</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Extracted Data Review Section -->
    <div class="row" id="reviewSection" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table"></i> Review Extracted Data
                        </h5>
                        <span class="badge bg-success" id="recordCount">0 records</span>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Please review carefully!</strong> AI extraction may not be 100% accurate. Edit any incorrect data before saving.
                    </div>

                    <div id="extractedDataTable"></div>

                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary" onclick="resetAll()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-success btn-lg" onclick="saveRecords()">
                            <i class="fas fa-save"></i> Save All Records
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.record-type-card {
    padding: 2rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    background: white;
}

.record-type-card:hover {
    border-color: #7b2e2e;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
    transform: translateY(-2px);
}

.record-type-card.active {
    border-color: #7b2e2e;
    background: linear-gradient(135deg, #7b2e2e 0%, #ff511a 100%);
    color: white;
}

.record-type-card.active .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

.record-type-card.active .record-badge {
    display: flex;
}

.record-icon {
    color: #7b2e2e;
    margin-bottom: 1rem;
}

.record-type-card.active .record-icon {
    color: white;
}

.record-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    color: white;
    font-size: 1.5rem;
    display: none;
}

.upload-zone {
    border: 3px dashed #cbd5e0;
    border-radius: 12px;
    padding: 3rem;
    text-align: center;
    background: #f9fafb;
}

.upload-placeholder {
    color: #6b7280;
}

#imagePreview img {
    max-height: 500px;
    object-fit: contain;
    border: 2px solid #e5e7eb;
}

.data-table {
    overflow-x: auto;
}

.data-table table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th, .data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.data-table th {
    background: #f9fafb;
    font-weight: 600;
}

.data-table input, .data-table select {
    width: 100%;
    padding: 6px;
    border: 1px solid #cbd5e0;
    border-radius: 4px;
}

.btn-remove-row {
    padding: 4px 8px;
    font-size: 0.875rem;
}
</style>

<script>
let selectedRecordType = null;
let extractedData = null;
let uploadedFile = null;

function selectRecordType(type) {
    selectedRecordType = type;
    
    // Update UI
    document.querySelectorAll('.record-type-card').forEach(card => {
        card.classList.remove('active');
    });
    document.querySelector(`[data-type="${type}"]`).classList.add('active');
    
    // Show upload section
    document.getElementById('uploadSection').style.display = 'block';
    document.getElementById('uploadSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        uploadedFile = file;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.querySelector('.upload-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

async function processImage() {
    if (!uploadedFile || !selectedRecordType) {
        alert('Please select a record type and upload an image');
        return;
    }

    const formData = new FormData();
    formData.append('image', uploadedFile);
    formData.append('record_type', selectedRecordType);

    document.getElementById('processingStatus').style.display = 'block';
    document.getElementById('statusMessage').textContent = 'Processing image... This may take 15-30 seconds...';

    try {
        const response = await fetch('{{ route("ocr.extract") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            extractedData = result.data;
            displayExtractedData();
            document.getElementById('processingStatus').style.display = 'none';
            document.getElementById('reviewSection').style.display = 'block';
            document.getElementById('reviewSection').scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Error: ' + result.message);
            document.getElementById('processingStatus').style.display = 'none';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to process image: ' + error.message);
        document.getElementById('processingStatus').style.display = 'none';
    }
}

function displayExtractedData() {
    const records = extractedData.records || [];
    document.getElementById('recordCount').textContent = `${records.length} records found`;

    let tableHTML = '<div class="data-table"><table class="table" id="dataTable"><thead><tr>';

    // Table headers based on record type
    if (selectedRecordType === 'inventory') {
        tableHTML += '<th>Product Name</th><th>SKU</th><th>Quantity</th><th>Price</th><th>Category</th><th>Notes</th><th>Action</th>';
    } else if (selectedRecordType === 'sales') {
        tableHTML += '<th>Product</th><th>Customer</th><th>Quantity</th><th>Price</th><th>Total</th><th>Date</th><th>Payment</th><th>Action</th>';
    } else if (selectedRecordType === 'services') {
        tableHTML += '<th>Customer</th><th>Service</th><th>Date</th><th>Time</th><th>Price</th><th>Phone</th><th>Action</th>';
    }

    tableHTML += '</tr></thead><tbody>';

    // Table rows
    records.forEach((record, index) => {
        tableHTML += `<tr data-index="${index}">`;
        
        if (selectedRecordType === 'inventory') {
            tableHTML += `
                <td><input type="text" value="${escapeHtml(record.name || '')}" data-field="name"></td>
                <td><input type="text" value="${escapeHtml(record.sku || '')}" data-field="sku"></td>
                <td><input type="number" value="${record.quantity || 0}" data-field="quantity" min="0"></td>
                <td><input type="number" value="${record.unit_price || 0}" data-field="unit_price" step="0.01" min="0"></td>
                <td><input type="text" value="${escapeHtml(record.category || '')}" data-field="category"></td>
                <td><input type="text" value="${escapeHtml(record.notes || '')}" data-field="notes"></td>
            `;
        } else if (selectedRecordType === 'sales') {
            tableHTML += `
                <td><input type="text" value="${escapeHtml(record.product_name || '')}" data-field="product_name"></td>
                <td><input type="text" value="${escapeHtml(record.customer_name || '')}" data-field="customer_name"></td>
                <td><input type="number" value="${record.quantity || 1}" data-field="quantity" min="1"></td>
                <td><input type="number" value="${record.unit_price || 0}" data-field="unit_price" step="0.01" min="0"></td>
                <td><input type="number" value="${record.total || 0}" data-field="total" step="0.01" min="0"></td>
                <td><input type="date" value="${record.date || ''}" data-field="date"></td>
                <td><select data-field="payment_method">
                    <option value="cash" ${record.payment_method === 'cash' ? 'selected' : ''}>Cash</option>
                    <option value="mpesa" ${record.payment_method === 'mpesa' ? 'selected' : ''}>M-PESA</option>
                    <option value="card" ${record.payment_method === 'card' ? 'selected' : ''}>Card</option>
                </select></td>
            `;
        } else if (selectedRecordType === 'services') {
            tableHTML += `
                <td><input type="text" value="${escapeHtml(record.customer_name || '')}" data-field="customer_name"></td>
                <td><input type="text" value="${escapeHtml(record.service_name || '')}" data-field="service_name"></td>
                <td><input type="date" value="${record.date || ''}" data-field="date"></td>
                <td><input type="time" value="${record.time || ''}" data-field="time"></td>
                <td><input type="number" value="${record.price || 0}" data-field="price" step="0.01" min="0"></td>
                <td><input type="tel" value="${escapeHtml(record.phone || '')}" data-field="phone"></td>
            `;
        }

        tableHTML += `<td><button class="btn btn-sm btn-danger btn-remove-row" onclick="removeRow(${index})"><i class="fas fa-trash"></i></button></td>`;
        tableHTML += '</tr>';
    });

    tableHTML += '</tbody></table></div>';
    document.getElementById('extractedDataTable').innerHTML = tableHTML;
}

function removeRow(index) {
    if (confirm('Are you sure you want to remove this record?')) {
        extractedData.records.splice(index, 1);
        displayExtractedData();
    }
}

function collectTableData() {
    const rows = document.querySelectorAll('#dataTable tbody tr');
    const records = [];

    rows.forEach(row => {
        const inputs = row.querySelectorAll('input, select');
        const record = {};
        
        inputs.forEach(input => {
            const field = input.getAttribute('data-field');
            if (field) {
                record[field] = input.value;
            }
        });

        records.push(record);
    });

    return records;
}

async function saveRecords() {
    const records = collectTableData();

    if (records.length === 0) {
        alert('No records to save');
        return;
    }

    if (!confirm(`Save ${records.length} records to the database?`)) {
        return;
    }

    try {
        const response = await fetch('{{ route("ocr.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                record_type: selectedRecordType,
                records: records,
                total_amount: extractedData.total_amount || 0
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            
            // Redirect based on record type
            if (selectedRecordType === 'inventory') {
                window.location.href = '{{ route("products.index") }}';
            } else if (selectedRecordType === 'sales') {
                window.location.href = '{{ route("sales.orders") }}';
            } else if (selectedRecordType === 'services') {
                window.location.href = '{{ route("service-bookings.index") }}';
            }
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save records: ' + error.message);
    }
}

function resetUpload() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.querySelector('.upload-placeholder').style.display = 'block';
    uploadedFile = null;
}

function resetAll() {
    if (confirm('Discard all extracted data and start over?')) {
        location.reload();
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}
</script>
@endsection
