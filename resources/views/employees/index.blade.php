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

            <div class="card">
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
@endsection
