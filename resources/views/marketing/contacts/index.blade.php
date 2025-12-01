@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: var(--text-primary);">Contact Groups</h1>
            <p class="text-muted">Manage and organize your contacts for bulk SMS campaigns</p>
        </div>
        <a href="{{ route('contacts.create-group') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Contact Group
        </a>
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

    <!-- Contact Groups Grid -->
    <div class="row">
        @forelse($contactGroups as $group)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1" style="color: var(--text-primary);">
                                    {{ $group->name }}
                                </h5>
                                <span class="badge badge-{{ $group->type === 'customers' ? 'primary' : ($group->type === 'staff' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($group->type) }}
                                </span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link" type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('contacts.show', $group->id) }}">
                                        <i class="fas fa-eye"></i> View Contacts
                                    </a>
                                    <a class="dropdown-item" href="{{ route('contacts.import', $group->id) }}">
                                        <i class="fas fa-upload"></i> Import Contacts
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('contacts.destroy-group', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> Delete Group
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <p class="card-text text-muted small mb-3">
                            {{ $group->description ?? 'No description' }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0" style="color: var(--text-primary);">
                                    {{ number_format($group->contact_count) }}
                                </h3>
                                <small class="text-muted">Contacts</small>
                            </div>
                            <div>
                                <a href="{{ route('contacts.show', $group->id) }}" class="btn btn-sm btn-outline-primary">
                                    View <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent" style="border-top: 1px solid var(--border-color);">
                        <small class="text-muted">
                            <i class="far fa-clock"></i> Updated {{ $group->updated_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-address-book fa-4x mb-3" style="color: var(--text-muted);"></i>
                        <h4 style="color: var(--text-primary);">No Contact Groups Yet</h4>
                        <p class="text-muted mb-4">Create your first contact group to start organizing your contacts for bulk SMS campaigns.</p>
                        <a href="{{ route('contacts.create-group') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Group
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Quick Info -->
    <div class="card shadow-sm mt-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-body">
            <h5 style="color: var(--text-primary);">
                <i class="fas fa-lightbulb"></i> How to Use Contact Groups
            </h5>
            <div class="row mt-3">
                <div class="col-md-4">
                    <h6 style="color: var(--text-primary);">1. Create a Group</h6>
                    <p class="text-muted small">Create groups for different audience segments like customers, staff, or custom groups.</p>
                </div>
                <div class="col-md-4">
                    <h6 style="color: var(--text-primary);">2. Import Contacts</h6>
                    <p class="text-muted small">Import from Google Contacts, upload CSV/Excel files, or VCF files from your phone.</p>
                </div>
                <div class="col-md-4">
                    <h6 style="color: var(--text-primary);">3. Send Bulk SMS</h6>
                    <p class="text-muted small">Select groups in the Bulk SMS page to send messages to all contacts in that group.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




