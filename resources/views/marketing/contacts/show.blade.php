@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contact Groups</a></li>
                    <li class="breadcrumb-item active">{{ $group->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: var(--text-primary);">{{ $group->name }}</h1>
            <span class="badge badge-{{ $group->type === 'customers' ? 'primary' : ($group->type === 'staff' ? 'success' : 'secondary') }}">
                {{ ucfirst($group->type) }}
            </span>
        </div>
        <div>
            <a href="{{ route('contacts.import', $group->id) }}" class="btn btn-primary">
                <i class="fas fa-upload"></i> Import Contacts
            </a>
            <a href="{{ route('marketing.bulk-sms') }}" class="btn btn-success">
                <i class="fas fa-sms"></i> Send SMS
            </a>
        </div>
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

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-body">
                    <h6 style="color: var(--text-primary);" class="mb-3">Quick Sync</h6>
                    <div class="btn-group" role="group">
                        <form action="{{ route('contacts.sync-customers', $group->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-users"></i> Sync Existing Customers
                            </button>
                        </form>
                        <form action="{{ route('contacts.sync-employees', $group->id) }}" method="POST" class="d-inline ml-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-user-tie"></i> Sync Existing Employees
                            </button>
                        </form>
                    </div>
                    <small class="form-text text-muted mt-2">
                        Import contacts from your existing customer or employee database
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">
                        Contacts ({{ number_format($group->contacts->count()) }})
                    </h6>
                </div>
                <div class="col-auto">
                    <input type="text" id="searchContact" class="form-control form-control-sm" placeholder="Search contacts..." style="width: 250px;">
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($group->contacts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="contactsTable">
                        <thead>
                            <tr>
                                <th style="color: var(--text-primary);">Name</th>
                                <th style="color: var(--text-primary);">Phone</th>
                                <th style="color: var(--text-primary);">Email</th>
                                <th style="color: var(--text-primary);">Company</th>
                                <th style="color: var(--text-primary);">Source</th>
                                <th style="color: var(--text-primary);">Added</th>
                                <th style="color: var(--text-primary);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group->contacts as $contact)
                                <tr>
                                    <td style="color: var(--text-primary);">
                                        <strong>{{ $contact->name }}</strong>
                                        @if($contact->position)
                                            <br><small class="text-muted">{{ $contact->position }}</small>
                                        @endif
                                    </td>
                                    <td style="color: var(--text-primary);">{{ $contact->phone }}</td>
                                    <td style="color: var(--text-primary);">
                                        {{ $contact->email ?? '-' }}
                                    </td>
                                    <td style="color: var(--text-primary);">
                                        {{ $contact->company ?? '-' }}
                                    </td>
                                    <td>
                                        @if($contact->source === 'google')
                                            <span class="badge badge-info"><i class="fab fa-google"></i> Google</span>
                                        @elseif($contact->source === 'csv')
                                            <span class="badge badge-primary"><i class="fas fa-file-csv"></i> CSV</span>
                                        @elseif($contact->source === 'vcf')
                                            <span class="badge badge-success"><i class="fas fa-address-card"></i> VCF</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-hand-pointer"></i> Manual</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $contact->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <form action="{{ route('contacts.destroy-contact', [$group->id, $contact->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-address-book fa-3x mb-3" style="color: var(--text-muted);"></i>
                    <h5 style="color: var(--text-primary);">No Contacts Yet</h5>
                    <p class="text-muted mb-4">Start by importing contacts into this group.</p>
                    <a href="{{ route('contacts.import', $group->id) }}" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import Contacts
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchContact');
    const table = document.getElementById('contactsTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });
    }
});
</script>
@endpush
@endsection




