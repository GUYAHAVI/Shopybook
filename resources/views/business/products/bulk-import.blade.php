@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Bulk Import Products</h1>
            <p class="text-muted">Import multiple products from a CSV/Excel file or handwritten images</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    <!-- Error Messages -->
    @if(session('failures'))
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Import Errors</h6>
            <p>The following rows failed to import:</p>
            <ul class="mb-0">
                @foreach(session('failures') as $failure)
                    <li>Row {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Import Method Tabs -->
            <ul class="nav nav-tabs mb-3" id="importTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-import" type="button" role="tab">
                        <i class="fas fa-file-csv me-2"></i>File Import
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ocr-tab" data-bs-toggle="tab" data-bs-target="#ocr-import" type="button" role="tab">
                        <i class="fas fa-camera me-2"></i>Image OCR
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="importTabContent">
                <!-- File Import Tab -->
                <div class="tab-pane fade show active" id="file-import" role="tabpanel">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Upload File</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('products.bulk-import.process') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="csv_file" class="form-label">Select File *</label>
                                    <input type="file" class="form-control @error('csv_file') is-invalid @enderror" 
                                           id="csv_file" name="csv_file" accept=".csv,.txt,.xlsx" required>
                                    <div class="form-text">Maximum file size: 2MB. Supported formats: CSV, TXT, Excel (.xlsx)</div>
                                    @error('csv_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_header" name="has_header" value="1" checked>
                                        <label class="form-check-label" for="has_header">
                                            File has header row
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                                        <label class="form-check-label" for="skip_duplicates">
                                            Skip duplicate products (based on SKU)
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-2"></i>Import Products
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- OCR Import Tab -->
                <div class="tab-pane fade" id="ocr-import" role="tabpanel">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Upload Handwritten Images</h6>
                        </div>
                        <div class="card-body">
                            <form id="ocrForm" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="ocr_images" class="form-label">Select Images *</label>
                                    <input type="file" class="form-control @error('ocr_images') is-invalid @enderror" 
                                           id="ocr_images" name="images[]" accept="image/*" multiple required>
                                    <div class="form-text">Maximum file size: 5MB per image. Supported formats: JPEG, PNG, JPG</div>
                                    @error('ocr_images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ocr_skip_duplicates" name="skip_duplicates" value="1" checked>
                                        <label class="form-check-label" for="ocr_skip_duplicates">
                                            Skip duplicate products (based on SKU)
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </button>
                                    <button type="button" class="btn btn-info" id="previewBtn">
                                        <i class="fas fa-eye me-2"></i>Preview Results
                                    </button>
                                    <button type="button" class="btn btn-primary" id="importBtn" style="display: none;">
                                        <i class="fas fa-upload me-2"></i>Import Products
                                    </button>
                                </div>
                            </form>

                            <!-- Preview Results -->
                            <div id="previewResults" class="mt-4" style="display: none;">
                                <h6 class="font-weight-bold text-primary">Preview Results</h6>
                                <div id="previewContent"></div>
                            </div>

                            <!-- Loading Spinner -->
                            <div id="loadingSpinner" class="text-center mt-4" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Processing...</span>
                                </div>
                                <p class="mt-2">Processing images with OCR...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Download Template -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-download me-2"></i>Download Template
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Download our CSV template to ensure your data is formatted correctly.</p>
                    <a href="{{ route('products.bulk-import.template') }}" class="btn btn-success w-100">
                        <i class="fas fa-file-csv me-2"></i>Download CSV Template
                    </a>
                </div>
            </div>

            <!-- Import Instructions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Import Instructions
                    </h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Download the CSV template</li>
                        <li class="mb-2">Fill in your product data</li>
                        <li class="mb-2">Save as CSV or Excel format</li>
                        <li class="mb-2">Upload the file here</li>
                        <li class="mb-2">Review and confirm import</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Fields -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">File Column Requirements</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Column Name</th>
                                    <th>Required</th>
                                    <th>Description</th>
                                    <th>Example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>name</strong></td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                    <td>Product name</td>
                                    <td>iPhone 13 Pro</td>
                                </tr>
                                <tr>
                                    <td><strong>price</strong></td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                    <td>Selling price (numbers only)</td>
                                    <td>129999.00</td>
                                </tr>
                                <tr>
                                    <td><strong>stock_quantity</strong></td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                    <td>Initial stock quantity</td>
                                    <td>50</td>
                                </tr>
                                <tr>
                                    <td><strong>description</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Product description</td>
                                    <td>Latest iPhone model</td>
                                </tr>
                                <tr>
                                    <td><strong>sku</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Stock keeping unit</td>
                                    <td>IPH13PRO-128</td>
                                </tr>
                                <tr>
                                    <td><strong>category</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Product category</td>
                                    <td>Electronics</td>
                                </tr>
                                <tr>
                                    <td><strong>brand</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Product brand</td>
                                    <td>Apple</td>
                                </tr>
                                <tr>
                                    <td><strong>cost_price</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Cost price (numbers only)</td>
                                    <td>100000.00</td>
                                </tr>
                                <tr>
                                    <td><strong>low_stock_threshold</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Low stock alert threshold</td>
                                    <td>10</td>
                                </tr>
                                <tr>
                                    <td><strong>is_active</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Active status (1 or 0)</td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td><strong>is_featured</strong></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Featured status (1 or 0)</td>
                                    <td>0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    background-color: #f8f9fc;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
}

.preview-item {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.preview-item.error {
    border-color: #e74a3b;
    background-color: #f8f9fa;
}

.preview-item.success {
    border-color: #1cc88a;
    background-color: #f8f9fa;
}

.field-value {
    font-weight: 600;
    color: #5a5c69;
}

.field-missing {
    color: #e74a3b;
    font-style: italic;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let ocrData = null;

    // Preview OCR Results
    document.getElementById('previewBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('ocrForm'));
        const files = document.getElementById('ocr_images').files;

        if (files.length === 0) {
            alert('Please select at least one image file.');
            return;
        }

        // Show loading spinner
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('previewResults').style.display = 'none';
        document.getElementById('importBtn').style.display = 'none';

        fetch('{{ route("products.ocr.preview") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';
            
            if (data.success) {
                ocrData = data.data;
                displayPreviewResults(data.data);
                document.getElementById('previewResults').style.display = 'block';
                document.getElementById('importBtn').style.display = 'inline-block';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            document.getElementById('loadingSpinner').style.display = 'none';
            console.error('Error:', error);
            alert('An error occurred while processing the images.');
        });
    });

    // Import OCR Results
    document.getElementById('importBtn').addEventListener('click', function() {
        if (!ocrData) {
            alert('Please preview the results first.');
            return;
        }

        const formData = new FormData(document.getElementById('ocrForm'));

        // Show loading spinner
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('importBtn').disabled = true;

        fetch('{{ route("products.ocr.process") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('importBtn').disabled = false;
            
            if (data.success) {
                alert(data.message);
                window.location.href = '{{ route("products.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('importBtn').disabled = false;
            console.error('Error:', error);
            alert('An error occurred while importing the products.');
        });
    });

    function displayPreviewResults(data) {
        const container = document.getElementById('previewContent');
        container.innerHTML = '';

        data.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = `preview-item ${item.error ? 'error' : 'success'}`;
            
            if (item.error) {
                div.innerHTML = `
                    <h6 class="text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Error Processing Image ${index + 1}
                    </h6>
                    <p class="mb-0">${item.error}</p>
                `;
            } else {
                const fields = [
                    { key: 'name', label: 'Product Name' },
                    { key: 'price', label: 'Price' },
                    { key: 'stock_quantity', label: 'Stock Quantity' },
                    { key: 'sku', label: 'SKU' },
                    { key: 'category', label: 'Category' },
                    { key: 'brand', label: 'Brand' },
                    { key: 'cost_price', label: 'Cost Price' },
                    { key: 'description', label: 'Description' }
                ];

                let fieldsHtml = '';
                fields.forEach(field => {
                    const value = item[field.key];
                    if (value !== null && value !== '') {
                        fieldsHtml += `
                            <div class="row mb-2">
                                <div class="col-md-3"><strong>${field.label}:</strong></div>
                                <div class="col-md-9">
                                    <span class="field-value">${value}</span>
                                </div>
                            </div>
                        `;
                    } else {
                        fieldsHtml += `
                            <div class="row mb-2">
                                <div class="col-md-3"><strong>${field.label}:</strong></div>
                                <div class="col-md-9">
                                    <span class="field-missing">Not detected</span>
                                </div>
                            </div>
                        `;
                    }
                });

                div.innerHTML = `
                    <h6 class="text-success">
                        <i class="fas fa-check-circle me-2"></i>Image ${index + 1} - ${item.name || 'Unnamed Product'}
                    </h6>
                    ${fieldsHtml}
                    <div class="mt-2">
                        <small class="text-muted">
                            <strong>Extracted Text:</strong><br>
                            <pre style="font-size: 0.8rem; background: #f8f9fa; padding: 0.5rem; border-radius: 0.25rem; margin-top: 0.5rem;">${item.ocr_text || 'No text detected'}</pre>
                        </small>
                    </div>
                `;
            }
            
            container.appendChild(div);
        });
    }
});
</script>
@endsection 