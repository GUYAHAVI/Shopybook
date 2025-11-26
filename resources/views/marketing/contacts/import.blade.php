@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-2">
                <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contact Groups</a></li>
                <li class="breadcrumb-item"><a href="{{ route('contacts.show', $group->id) }}">{{ $group->name }}</a></li>
                <li class="breadcrumb-item active">Import Contacts</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0" style="color: var(--text-primary);">Import Contacts to {{ $group->name }}</h1>
        <p class="text-muted">Choose your preferred method to import contacts</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        <!-- Google Contacts Import -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fab fa-google fa-4x" style="color: #4285F4;"></i>
                    </div>
                    <h5 class="card-title" style="color: var(--text-primary);">Google Contacts</h5>
                    <p class="card-text text-muted">
                        Import contacts directly from your Google account. Sign in with Google to authorize access.
                    </p>
                    <a href="{{ route('contacts.google-import', $group->id) }}" class="btn btn-primary btn-block">
                        <i class="fab fa-google"></i> Import from Google
                    </a>
                </div>
                <div class="card-footer bg-transparent text-center" style="border-top: 1px solid var(--border-color);">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt"></i> Secure OAuth authentication
                    </small>
                </div>
            </div>
        </div>

        <!-- CSV/Excel Import -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-file-csv fa-4x" style="color: #28a745;"></i>
                    </div>
                    <h5 class="card-title" style="color: var(--text-primary);">CSV / Excel File</h5>
                    <p class="card-text text-muted">
                        Upload a CSV or Excel file with your contacts. Perfect for exporting from other apps.
                    </p>
                    <form id="csvForm" action="{{ route('contacts.import-csv', $group->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="csvFile" name="file" accept=".csv,.xlsx,.xls" style="display: none;" required>
                        <button type="button" class="btn btn-success btn-block" onclick="document.getElementById('csvFile').click()">
                            <i class="fas fa-upload"></i> Upload CSV/Excel
                        </button>
                    </form>
                    <a href="{{ route('contacts.download-template') }}" class="btn btn-link btn-sm mt-2">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>
                <div class="card-footer bg-transparent text-center" style="border-top: 1px solid var(--border-color);">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Max 10MB
                    </small>
                </div>
            </div>
        </div>

        <!-- VCF Import -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-address-card fa-4x" style="color: #dc3545;"></i>
                    </div>
                    <h5 class="card-title" style="color: var(--text-primary);">VCF (vCard) File</h5>
                    <p class="card-text text-muted">
                        Upload a VCF file exported from your phone's contacts app. Works with iPhone and Android.
                    </p>
                    <form id="vcfForm" action="{{ route('contacts.import-vcf', $group->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="vcfFile" name="file" accept=".vcf" style="display: none;" required>
                        <button type="button" class="btn btn-danger btn-block" onclick="document.getElementById('vcfFile').click()">
                            <i class="fas fa-mobile-alt"></i> Upload VCF File
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-transparent text-center" style="border-top: 1px solid var(--border-color);">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Max 10MB
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Instructions -->
    <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-body">
            <h5 style="color: var(--text-primary);">
                <i class="fas fa-question-circle"></i> How to Export Contacts from Your Phone
            </h5>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6 style="color: var(--text-primary);">
                        <i class="fab fa-apple"></i> iPhone
                    </h6>
                    <ol class="text-muted small">
                        <li>Open iCloud.com on your computer</li>
                        <li>Sign in and click on "Contacts"</li>
                        <li>Select contacts you want to export</li>
                        <li>Click the gear icon and select "Export vCard"</li>
                        <li>Upload the downloaded .vcf file here</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6 style="color: var(--text-primary);">
                        <i class="fab fa-android"></i> Android
                    </h6>
                    <ol class="text-muted small">
                        <li>Open the Contacts app</li>
                        <li>Tap the menu (⋮) and select "Settings"</li>
                        <li>Tap "Export" or "Import/Export"</li>
                        <li>Select "Export to .vcf file"</li>
                        <li>Upload the exported file here</li>
                    </ol>
                </div>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <strong><i class="fas fa-lightbulb"></i> Tip:</strong> For CSV imports, your file should include columns for: 
                <code>name</code>, <code>phone</code>, <code>email</code>, <code>company</code>, <code>position</code>, <code>address</code>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Handle CSV file selection
document.getElementById('csvFile').addEventListener('change', function() {
    if (this.files.length > 0) {
        if (confirm('Import ' + this.files[0].name + '?')) {
            document.getElementById('csvForm').submit();
        }
    }
});

// Handle VCF file selection
document.getElementById('vcfFile').addEventListener('change', function() {
    if (this.files.length > 0) {
        if (confirm('Import ' + this.files[0].name + '?')) {
            document.getElementById('vcfForm').submit();
        }
    }
});
</script>
@endpush
@endsection



