@extends('layouts.dash')
@section('title', 'Customer Management')

@section('content')
<div class="container-fluid">
    <!-- Sub-navigation for Sales -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('sales.customers') }}" class="nav-tab active">
                <i class="fas fa-users me-1"></i> Customers
            </a>
            <a href="{{ route('sales.orders') }}" class="nav-tab">
                <i class="fas fa-shopping-cart me-1"></i> Orders
            </a>
            <a href="{{ route('sales.pos') }}" class="nav-tab">
                <i class="fas fa-cash-register me-1"></i> POS
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0" style="color: var(--text-primary);">Customer Management</h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sales.customers.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i><span class="d-none d-sm-inline">Add Individual Customer</span><span class="d-sm-none">Add Customer</span>
            </a>
            <a href="{{ route('sales.organization-customers.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-building me-2"></i><span class="d-none d-md-inline">Add Company/Organization Customer</span><span class="d-md-none">Add Organization</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Individual Customers</h6>
                    <span class="badge" style="background-color: var(--primary-color); color: var(--white);">{{ $customers->total() }}</span>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Table -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: var(--bg-tertiary);">
                                <tr>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Name</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Email</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Phone</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Orders</th>
                                    <th style="color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td style="color: var(--text-primary);">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-initial rounded-circle" style="background-color: var(--primary-color); color: var(--white);">
                                                        {{ substr($customer->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0" style="color: var(--text-primary);">{{ $customer->name }}</h6>
                                                    <small style="color: var(--text-muted);">{{ $customer->city ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color: var(--text-primary);">{{ $customer->email ?? 'N/A' }}</td>
                                        <td style="color: var(--text-primary);">{{ $customer->phone }}</td>
                                        <td>
                                            <span class="badge" style="background-color: var(--success-color); color: var(--white);">{{ $customer->orders_count ?? $customer->orders->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales.customers.show', ['type' => 'individual', 'id' => $customer->id]) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4" style="color: var(--text-muted);">
                                            <i class="fas fa-users fa-2x mb-3" style="color: var(--text-muted);"></i>
                                            <p style="color: var(--text-muted);">No individual customers found.</p>
                                            <a href="{{ route('sales.customers.create') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus me-2"></i>Add First Customer
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile Cards -->
                    <div class="d-lg-none">
                        @forelse($customers as $customer)
                            <div class="customer-card" style="background: var(--card-bg); border: 1px solid var(--border-color); box-shadow: 0 1px 3px var(--shadow-color);">
                                <div class="customer-header" style="border-bottom: 1px solid var(--border-color);">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle" style="background-color: var(--primary-color); color: var(--white);">
                                                {{ substr($customer->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0" style="color: var(--text-primary);">{{ $customer->name }}</h6>
                                            <small style="color: var(--text-muted);">{{ $customer->city ?? 'N/A' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge" style="background-color: var(--success-color); color: var(--white);">{{ $customer->orders_count ?? $customer->orders->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="customer-body">
                                    <div class="detail-item">
                                        <span style="color: var(--text-secondary);">Email:</span>
                                        <span style="color: var(--text-primary);">{{ $customer->email ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span style="color: var(--text-secondary);">Phone:</span>
                                        <span style="color: var(--text-primary);">{{ $customer->phone }}</span>
                                    </div>
                                </div>
                                <div class="card-footer" style="background-color: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('sales.customers.show', ['type' => 'individual', 'id' => $customer->id]) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4" style="color: var(--text-muted);">
                                <i class="fas fa-users fa-2x mb-3" style="color: var(--text-muted);"></i>
                                <p style="color: var(--text-muted);">No individual customers found.</p>
                                <a href="{{ route('sales.customers.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add First Customer
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Company/Organization Customers</h6>
                    <span class="badge" style="background-color: #020258;">{{ $organizations->total() }}</span>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Table -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>KRA PIN</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organizations as $org)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-initial rounded-circle" style="background-color: #13e8e9; color: #020258;">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $org->name }}</h6>
                                                    <small class="text-muted">{{ $org->city ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $org->email ?? 'N/A' }}</td>
                                        <td>{{ $org->phone }}</td>
                                        <td>
                                            <span class="badge" style="background-color: #64748b;">{{ $org->kra_pin ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales.customers.show', ['type' => 'organization', 'id' => $org->id]) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-building fa-2x mb-3 text-muted"></i>
                                            <p>No company/organization customers found.</p>
                                            <a href="{{ route('sales.organization-customers.create') }}" class="btn btn-sm" style="background-color: #020258; border-color: #020258; color: white;">
                                                <i class="fas fa-plus me-2"></i>Add First Organization
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile Cards -->
                    <div class="d-lg-none">
                        @forelse($organizations as $org)
                            <div class="customer-card">
                                <div class="customer-header">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle" style="background-color: #13e8e9; color: #020258;">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $org->name }}</h6>
                                            <small class="text-muted">{{ $org->city ?? 'N/A' }}</small>
                                        </div>
                                        <span class="badge" style="background-color: #64748b;">{{ $org->kra_pin ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="customer-details">
                                    <div class="detail-item">
                                        <i class="fas fa-envelope text-muted"></i>
                                        <span>{{ $org->email ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-phone text-muted"></i>
                                        <span>{{ $org->phone }}</span>
                                    </div>
                                </div>
                                <div class="customer-actions">
                                    <a href="{{ route('sales.customers.show', ['type' => 'organization', 'id' => $org->id]) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-building fa-2x mb-3 text-muted"></i>
                                <p>No company/organization customers found.</p>
                                <a href="{{ route('sales.organization-customers.create') }}" class="btn btn-sm" style="background-color: #020258; border-color: #020258; color: white;">
                                    <i class="fas fa-plus me-2"></i>Add First Organization
                                </a>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($organizations->hasPages())
                        <div class="card-footer">
                            {{ $organizations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.nav-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.nav-tab {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
    border-color: var(--border-color);
}

.nav-tab.active {
    color: var(--white);
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.customer-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px var(--shadow-color);
}

.customer-card:hover {
    box-shadow: 0 4px 6px var(--shadow-color);
}

.customer-header {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.customer-body {
    padding: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
}

.card-footer {
    background-color: var(--bg-tertiary);
    border-top: 1px solid var(--border-color);
    padding: 0.75rem;
}

.avatar-sm {
    width: 40px;
    height: 40px;
}

.avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tab {
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection 