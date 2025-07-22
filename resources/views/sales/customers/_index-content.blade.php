<<style>
.hover-shadow {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0 !important;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    border: 1px solid #5a5c69 !important;
}

.customer-details .row {
    margin-bottom: 0.5rem;
}

.customer-details small {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.card-actions .btn-group .btn {
    border-radius: 0;
    font-size: 0.875rem;
}

.card-actions .btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.card-actions .btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

.badge {
    font-size: 0.75rem;
}
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customer Management</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.customers.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Add Individual Customer
            </a>
            <a href="{{ route('sales.organization-customers.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-building me-2"></i>Add Company/Organization Customer
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Individual Customers</h6>
                </div>
                <div class="card-body">
                    @if($customers && $customers->count() > 0)
                        <!-- Beautiful Card View for All Devices -->
                        <div class="row">
                            @foreach($customers as $customer)
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                    <div class="card h-100 shadow-sm border-0 hover-shadow">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0 text-primary">
                                                    <i class="fas fa-user me-2"></i>{{ $customer->name ?? 'Unknown' }}
                                                </h5>
                                                <span class="badge bg-info rounded-pill">
                                                    {{ $customer->orders_count ?? 0 }} orders
                                                </span>
                                            </div>
                                            
                                            <div class="customer-details flex-grow-1">
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-envelope me-1"></i>Email
                                                        </small>
                                                        <span class="fw-medium">{{ $customer->email ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-phone me-1"></i>Phone
                                                        </small>
                                                        <span class="fw-medium">{{ $customer->phone ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card-actions mt-auto">
                                                <div class="btn-group w-100" role="group">
                                                    <a href="{{ route('sales.customers.show', $customer) }}" 
                                                       class="btn btn-outline-info btn-sm"
                                                       title="View Customer">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ route('sales.customers.edit', $customer) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Edit Customer">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('sales.customers.destroy', $customer) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                onclick="return confirm('Are you sure you want to delete this customer?')"
                                                                title="Delete Customer">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>No Individual Customers Found</h5>
                            <p class="text-muted">Add your first customer to get started.</p>
                            <a href="{{ route('sales.customers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Customer
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Organization Customers</h6>
                </div>
                <div class="card-body">
                    @if($organizationCustomers && $organizationCustomers->count() > 0)
                        <!-- Beautiful Card View for All Devices -->
                        <div class="row">
                            @foreach($organizationCustomers as $orgCustomer)
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                    <div class="card h-100 shadow-sm border-0 hover-shadow">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0 text-success">
                                                    <i class="fas fa-building me-2"></i>{{ $orgCustomer->organization_name ?? 'Unknown' }}
                                                </h5>
                                                <span class="badge bg-success rounded-pill">
                                                    {{ $orgCustomer->orders_count ?? 0 }} orders
                                                </span>
                                            </div>
                                            
                                            <div class="customer-details flex-grow-1">
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-user-tie me-1"></i>Contact Person
                                                        </small>
                                                        <span class="fw-medium">{{ $orgCustomer->contact_person ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-envelope me-1"></i>Email
                                                        </small>
                                                        <span class="fw-medium">{{ $orgCustomer->email ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-phone me-1"></i>Phone
                                                        </small>
                                                        <span class="fw-medium">{{ $orgCustomer->phone ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card-actions mt-auto">
                                                <div class="btn-group w-100" role="group">
                                                    <a href="{{ route('sales.organization-customers.show', $orgCustomer) }}" 
                                                       class="btn btn-outline-info btn-sm"
                                                       title="View Organization">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ route('sales.organization-customers.edit', $orgCustomer) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Edit Organization">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('sales.organization-customers.destroy', $orgCustomer) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                onclick="return confirm('Are you sure you want to delete this organization?')"
                                                                title="Delete Organization">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5>No Organization Customers Found</h5>
                            <p class="text-muted">Add your first organization customer to get started.</p>
                            <a href="{{ route('sales.organization-customers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Organization
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mobile-card-item {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.mobile-card-title {
    font-weight: 600;
    margin: 0;
    flex: 1;
}

.mobile-card-badge {
    font-size: 0.75rem;
    margin-left: 0.5rem;
}

.mobile-card-content {
    margin-bottom: 0.75rem;
}

.mobile-card-field {
    display: flex;
    justify-content: space-between;
    padding: 0.25rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.mobile-card-field:last-child {
    border-bottom: none;
}

.mobile-card-field label {
    font-weight: 500;
    color: #6c757d;
    margin: 0;
    width: 40%;
}

.mobile-card-field span {
    color: #212529;
    width: 60%;
    text-align: right;
}

.mobile-card-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.mobile-card-actions .btn {
    font-size: 0.875rem;
}
</style>
                        <x-slot name="tableContent">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Orders</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($customers as $customer)
                                            <tr>
                                                <td>{{ $customer->name }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->phone }}</td>
                                                <td>{{ $customer->orders_count ?? $customer->orders->count() }}</td>
                                                <td>
                                                    <a href="{{ route('sales.customers.show', ['type' => 'individual', 'id' => $customer->id]) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted">No individual customers found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </x-slot>
                        
                        <x-slot name="mobileData">
                            @php
                                $mobileCustomers = collect($customers ?? [])->map(function($customer) {
                                    $ordersCount = $customer->orders_count ?? ($customer->orders ? $customer->orders->count() : 0);
                                    return [
                                        'title' => $customer->name ?? 'Unknown Customer',
                                        'badge' => [
                                            'text' => $ordersCount . ' Orders',
                                            'variant' => 'info'
                                        ],
                                        'fields' => [
                                            ['label' => 'Email', 'value' => $customer->email ?? '-'],
                                            ['label' => 'Phone', 'value' => $customer->phone ?? '-'],
                                            ['label' => 'Total Orders', 'value' => $ordersCount]
                                        ],
                                        'actions' => [
                                            [
                                                'type' => 'link',
                                                'url' => route('sales.customers.show', ['type' => 'individual', 'id' => $customer->id]),
                                                'text' => 'View Details',
                                                'class' => 'btn btn-sm btn-outline-info'
                                            ]
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>No Individual Customers Found</h5>
                            <p class="text-muted">Add your first customer to get started.</p>
                            <a href="{{ route('sales.customers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Customer
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Organization Customers</h6>
                </div>
                <div class="card-body">
                    @if($organizationCustomers && $organizationCustomers->count() > 0)
                        <!-- Beautiful Card View for All Devices -->
                        <div class="row">
                            @foreach($organizationCustomers as $orgCustomer)
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                    <div class="card h-100 shadow-sm border-0 hover-shadow">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0 text-success">
                                                    <i class="fas fa-building me-2"></i>{{ $orgCustomer->organization_name ?? 'Unknown' }}
                                                </h5>
                                                <span class="badge bg-success rounded-pill">
                                                    {{ $orgCustomer->orders_count ?? 0 }} orders
                                                </span>
                                            </div>
                                            
                                            <div class="customer-details flex-grow-1">
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-user-tie me-1"></i>Contact Person
                                                        </small>
                                                        <span class="fw-medium">{{ $orgCustomer->contact_person ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-envelope me-1"></i>Email
                                                        </small>
                                                        <span class="fw-medium">{{ $orgCustomer->email ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-phone me-1"></i>Phone
                                                        </small>
                                                        <span class="fw-medium">{{ $orgCustomer->phone ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card-actions mt-auto">
                                                <div class="btn-group w-100" role="group">
                                                    <a href="{{ route('sales.organization-customers.show', $orgCustomer) }}" 
                                                       class="btn btn-outline-info btn-sm"
                                                       title="View Organization">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ route('sales.organization-customers.edit', $orgCustomer) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Edit Organization">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('sales.organization-customers.destroy', $orgCustomer) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                onclick="return confirm('Are you sure you want to delete this organization?')"
                                                                title="Delete Organization">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5>No Organization Customers Found</h5>
                            <p class="text-muted">Add your first organization customer to get started.</p>
                            <a href="{{ route('sales.organization-customers.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> Add Organization Customer
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal for redirect with query string -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="successModalMessage">
        <!-- Message will be set by JS -->
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to get query parameter by name
    function getQueryParam(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }
    const successMsg = getQueryParam('success');
    if (successMsg) {
        document.getElementById('successModalMessage').textContent = successMsg;
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        setTimeout(function () {
            successModal.hide();
            // Remove the query string from the URL without reloading
            if (window.history.replaceState) {
                const url = window.location.protocol + '//' + window.location.host + window.location.pathname;
                window.history.replaceState({}, document.title, url);
            }
        }, 2500);
    }
});
</script> 