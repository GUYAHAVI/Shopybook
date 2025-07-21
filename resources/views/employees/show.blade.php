@extends('layouts.dash')

@section('title', $employee->first_name . ' ' . $employee->last_name . ' - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('employee_details') }}</h1>
                <div>
                    <a href="{{ route('employees.edit', ['business_id' => $business->id, 'employee' => $employee->id]) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>{{ t('edit') }}
                    </a>
                    <a href="{{ route('employees.index', ['business_id' => $business->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ t('back_to_employees') }}
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ t('personal_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">{{ t('employee_id') }}</label>
                                        <p class="fw-bold">{{ $employee->employee_id }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">{{ t('status') }}</label>
                                        <p>
                                            <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                                {{ $employee->is_active ? t('active') : t('inactive') }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">{{ t('first_name') }}</label>
                                        <p class="fw-bold">{{ $employee->first_name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">{{ t('last_name') }}</label>
                                        <p class="fw-bold">{{ $employee->last_name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">{{ t('email') }}</label>
                                        <p>
                                            <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">{{ t('phone') }}</label>
                                        <p>
                                            @if($employee->phone)
                                                <a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a>
                                            @else
                                                <span class="text-muted">{{ t('not_provided') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($employee->address)
                                <div class="mb-3">
                                    <label class="form-label text-muted">{{ t('address') }}</label>
                                    <p>{{ $employee->address }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ t('employment_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">{{ t('position') }}</label>
                                <p class="fw-bold">{{ $employee->position }}</p>
                            </div>

                            @if($employee->department)
                                <div class="mb-3">
                                    <label class="form-label text-muted">{{ t('department') }}</label>
                                    <p>{{ $employee->department }}</p>
                                </div>
                            @endif

                            @if($employee->hire_date)
                                <div class="mb-3">
                                    <label class="form-label text-muted">{{ t('hire_date') }}</label>
                                    <p>{{ $employee->hire_date->format('M d, Y') }}</p>
                                    <small class="text-muted">
                                        {{ $employee->hire_date->diffForHumans() }}
                                    </small>
                                </div>
                            @endif

                            @if($employee->salary)
                                <div class="mb-3">
                                    <label class="form-label text-muted">{{ t('salary') }}</label>
                                    <p class="fw-bold">${{ number_format($employee->salary, 2) }}</p>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label text-muted">{{ t('created') }}</label>
                                <p>
                                    {{ $employee->created_at->format('M d, Y') }}
                                    <small class="text-muted d-block">
                                        {{ $employee->created_at->diffForHumans() }}
                                    </small>
                                </p>
                            </div>

                            @if($employee->updated_at != $employee->created_at)
                                <div class="mb-3">
                                    <label class="form-label text-muted">{{ t('last_updated') }}</label>
                                    <p>
                                        {{ $employee->updated_at->format('M d, Y') }}
                                        <small class="text-muted d-block">
                                            {{ $employee->updated_at->diffForHumans() }}
                                        </small>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ t('actions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('employees.edit', ['business_id' => $business->id, 'employee' => $employee->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>{{ t('edit_employee') }}
                                </a>
                                <form method="POST" 
                                      action="{{ route('employees.destroy', ['business_id' => $business->id, 'employee' => $employee->id]) }}"
                                      onsubmit="return confirm('{{ t('confirm_delete_employee') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i>{{ t('delete_employee') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
