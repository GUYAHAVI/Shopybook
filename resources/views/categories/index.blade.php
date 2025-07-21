@extends('layouts.dash')

@section('title', 'Categories - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('categories') }}</h1>
                <a href="{{ route('categories.create', ['business_id' => $business->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ t('add_category') }}
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
                            <h5 class="card-title mb-0">{{ t('all_categories') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="hidden" name="business_id" value="{{ $business->id }}">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="{{ t('search_categories') }}" 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ t('name') }}</th>
                                        <th>{{ t('description') }}</th>
                                        <th>{{ t('parent_category') }}</th>
                                        <th>{{ t('products_count') }}</th>
                                        <th>{{ t('status') }}</th>
                                        <th>{{ t('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($category->image_path)
                                                        <img src="{{ $category->image_path }}" alt="{{ $category->name }}" 
                                                             class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="avatar-sm me-3">
                                                            <div class="avatar-title bg-primary rounded">
                                                                <i class="fas fa-tag"></i>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $category->name }}</h6>
                                                        <small class="text-muted">{{ $category->slug }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 200px;">
                                                    {{ Str::limit($category->description, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($category->parent)
                                                    <span class="badge bg-info">{{ $category->parent->name }}</span>
                                                @else
                                                    <span class="text-muted">{{ t('root_category') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $category->products()->count() }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                    {{ $category->is_active ? t('active') : t('inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('categories.show', ['business_id' => $business->id, 'category' => $category->id]) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('categories.edit', ['business_id' => $business->id, 'category' => $category->id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('categories.destroy', ['business_id' => $business->id, 'category' => $category->id]) }}" 
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
                            {{ $categories->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ t('no_categories_found') }}</h5>
                            <p class="text-muted">{{ t('start_by_adding_category') }}</p>
                            <a href="{{ route('categories.create', ['business_id' => $business->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>{{ t('add_first_category') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
