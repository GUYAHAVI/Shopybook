@extends('layouts.dash')

@section('title', 'Employees - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('employees') }}</h1>
                <a href="{{ route('employees.create', ['business_id' => $business->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ t('add_employee') }}
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @if($employees && $employees->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ t('employee_id') }}</th>
                                            <th>{{ t('name') }}</th>
                                            <th>{{ t('email') }}</th>
                                            <th>{{ t('position') }}</th>
                                            <th>{{ t('hire_date') }}</th>
                                            <th>{{ t('salary') }}</th>
                                            <th>{{ t('actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->employee_id ?? '-' }}</td>
                                            <td>{{ ($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '') }}</td>
                                            <td>{{ $employee->email ?? '-' }}</td>
                                            <td>{{ $employee->position ?? '-' }}</td>
                                            <td>{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '-' }}</td>
                                            <td>{{ $employee->salary ? 'KSh ' . number_format($employee->salary, 2) : '-' }}</td>
                                            <td>
                                                <a href="{{ route('employees.show', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                   class="btn btn-sm btn-outline-info me-1"
                                                   title="View Employee">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('employees.edit', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                   class="btn btn-sm btn-outline-primary me-1"
                                                   title="Edit Employee">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('employees.destroy', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('{{ t('confirm_delete_employee') }}')"
                                                            title="Delete Employee">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-block d-md-none">
                            @foreach ($employees as $employee)
                                <div class="mobile-card card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <!-- Header Section -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-1 text-primary mobile-card-title">
                                                    {{ ($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '') }}
                                                </h6>
                                            </div>
                                            <span class="badge bg-primary mobile-card-badge">
                                                {{ $employee->position ?? 'Employee' }}
                                            </span>
                                        </div>

                                        <!-- Details Section - Vertical Layout -->
                                        <div class="mobile-card-details">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="detail-item">
                                                        <small class="text-muted fw-medium">{{ t('employee_id') }}</small>
                                                        <div class="detail-value">{{ $employee->employee_id ?? '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="detail-item">
                                                        <small class="text-muted fw-medium">{{ t('hire_date') }}</small>
                                                        <div class="detail-value">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="detail-item">
                                                        <small class="text-muted fw-medium">{{ t('email') }}</small>
                                                        <div class="detail-value text-break">{{ $employee->email ?? '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="detail-item">
                                                        <small class="text-muted fw-medium">{{ t('salary') }}</small>
                                                        <div class="detail-value">{{ $employee->salary ? 'KSh ' . number_format($employee->salary, 2) : '-' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions Section -->
                                        <div class="mobile-card-actions mt-3 pt-3 border-top">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('employees.show', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                   class="btn btn-outline-info btn-sm mobile-action-btn">
                                                    <i class="fas fa-eye me-1"></i> {{ t('view') }}
                                                </a>
                                                <a href="{{ route('employees.edit', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                   class="btn btn-outline-primary btn-sm mobile-action-btn">
                                                    <i class="fas fa-edit me-1"></i> {{ t('edit') }}
                                                </a>
                                                <form action="{{ route('employees.destroy', ['business_id' => $business->id, 'employee' => $employee->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm mobile-action-btn"
                                                            onclick="return confirm('{{ t('delete_confirmation') }}')">
                                                        <i class="fas fa-trash me-1"></i> {{ t('delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            'class' => 'btn-outline-danger',
                                            'icon' => 'fas fa-trash',
                                            'confirm' => t('confirm_delete_employee')
                                        ]
                                    ]"
                                />
                            @endforeach
                        </div>

                        @if($employees->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $employees->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>{{ t('no_employees_found') }}</h5>
                            <p class="text-muted">{{ t('add_first_employee_message') }}</p>
                            <a href="{{ route('employees.create', ['business_id' => $business->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> {{ t('add_employee') }}
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
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">{{ t('all_employees') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="hidden" name="business_id" value="{{ $business->id }}">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="{{ t('search_employees') }}" 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($employees->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ t('name') }}</th>
                                            <th>{{ t('email') }}</th>
                                            <th>{{ t('phone') }}</th>
                                                <th>{{ t('position') }}</th>
                                                <th>{{ t('status') }}</th>
                                                <th>{{ t('actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employees as $employee)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-3">
                                                                <div class="avatar-title bg-primary rounded-circle">
                                                                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $employee->first_name }} {{ $employee->last_name }}</h6>
                                                                <small class="text-muted">{{ $employee->employee_id }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $employee->email }}</td>
                                                    <td>{{ $employee->phone }}</td>
                                                    <td>{{ $employee->position }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                                            {{ $employee->is_active ? t('active') : t('inactive') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('employees.show', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                               class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('employees.edit', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form method="POST" 
                                                                  action="{{ route('employees.destroy', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('{{ t('confirm_delete') }}')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Mobile Card View -->
                            <div class="d-block d-md-none">
                                @foreach($employees as $employee)
                                    <div class="mobile-card card border-0 shadow-sm mb-3">
                                        <div class="card-body p-3">
                                            <!-- Header Section -->
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-1 text-primary mobile-card-title">
                                                        {{ ($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '') }}
                                                    </h6>
                                                </div>
                                                <span class="badge {{ ($employee->is_active ?? false) ? 'bg-success' : 'bg-secondary' }} mobile-card-badge">
                                                    {{ ($employee->is_active ?? false) ? t('active') : t('inactive') }}
                                                </span>
                                            </div>

                                            <!-- Details Section - Vertical Layout -->
                                            <div class="mobile-card-details">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="detail-item">
                                                            <small class="text-muted fw-medium">{{ t('employee_id') }}</small>
                                                            <div class="detail-value">{{ $employee->employee_id ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="detail-item">
                                                            <small class="text-muted fw-medium">{{ t('position') }}</small>
                                                            <div class="detail-value">{{ $employee->position ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="detail-item">
                                                            <small class="text-muted fw-medium">{{ t('email') }}</small>
                                                            <div class="detail-value text-break">{{ $employee->email ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="detail-item">
                                                            <small class="text-muted fw-medium">{{ t('phone') }}</small>
                                                            <div class="detail-value">{{ $employee->phone ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Actions Section -->
                                            <div class="mobile-card-actions mt-3 pt-3 border-top">
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('employees.show', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                       class="btn btn-outline-info btn-sm mobile-action-btn">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </a>
                                                    <a href="{{ route('employees.edit', ['business_id' => $business->id, 'employee' => $employee->id]) }}" 
                                                       class="btn btn-outline-primary btn-sm mobile-action-btn">
                                                        <i class="fas fa-edit me-1"></i> Edit
                                                    </a>
                                                    <form action="{{ route('employees.destroy', ['business_id' => $business->id, 'employee' => $employee->id]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm mobile-action-btn"
                                                                onclick="return confirm('{{ t('confirm_delete') }}')">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ t('no_employees_found') }}</h5>
                            <p class="text-muted">{{ t('start_by_adding_employee') }}</p>
                            <a href="{{ route('employees.create', ['business_id' => $business->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>{{ t('add_first_employee') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Mobile Card Styles */
.mobile-card {
    transition: all 0.2s ease;
    border-radius: 12px;
    background: #fff;
}

.mobile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.mobile-card-title {
    font-size: 1rem;
    color: #495057;
    margin-bottom: 0.25rem;
}

.mobile-card-badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
}

.mobile-card-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    margin: 0.75rem 0;
}

.detail-item {
    margin-bottom: 0.5rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item small {
    display: block;
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 0.875rem;
    color: #212529;
    font-weight: 500;
    line-height: 1.4;
    word-break: break-word;
}

.mobile-card-actions {
    border-top: 1px solid #e9ecef;
}

.mobile-action-btn {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.mobile-action-btn:hover {
    transform: translateY(-1px);
}

/* Mobile Responsive Enhancements */
@media (max-width: 576px) {
    .mobile-card {
        margin: 0 -0.5rem 0.75rem -0.5rem;
        border-radius: 8px;
    }
    
    .mobile-card-title {
        font-size: 0.9rem;
    }
    
    .mobile-card-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .detail-value {
        font-size: 0.8rem;
    }
    
    .mobile-action-btn {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
}

@media (max-width: 375px) {
    .mobile-card-title {
        font-size: 0.85rem;
    }
    
    .mobile-card-badge {
        font-size: 0.7rem;
    }
    
    .detail-value {
        font-size: 0.75rem;
    }
    
    .mobile-action-btn {
        font-size: 0.7rem;
    }
}
</style>

@endsection
