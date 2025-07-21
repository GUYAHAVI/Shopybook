@extends('layouts.dash')

@section('title', 'Brands - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('brands') }}</h1>
                <a href="{{ route('brands.create', ['business_id' => $business->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ t('add_brand') }}
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
                            <h5 class="card-title mb-0">{{ t('all_brands') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="hidden" name="business_id" value="{{ $business->id }}">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="{{ t('search_brands') }}" 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($brands->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ t('brand_name') }}</th>
                                        <th>{{ t('description') }}</th>
                                        <th>{{ t('website') }}</th>
                                        <th>{{ t('products_count') }}</th>
                                        <th>{{ t('status') }}</th>
                                        <th>{{ t('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($brands as $brand)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($brand->logo_path)
                                                        <img src="{{ $brand->logo_path }}" alt="{{ $brand->name }}" 
                                                             class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="avatar-sm me-3">
                                                            <div class="avatar-title bg-info rounded">
                                                                <i class="fas fa-award"></i>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $brand->name }}</h6>
                                                        <small class="text-muted">{{ $brand->slug }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 200px;">
                                                    {{ Str::limit($brand->description, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($brand->website)
                                                    <a href="{{ $brand->website }}" target="_blank" class="text-decoration-none">
                                                        <i class="fas fa-external-link-alt me-1"></i>
                                                        {{ Str::limit(parse_url($brand->website, PHP_URL_HOST), 20) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ t('no_website') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $brand->products()->count() }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                                    {{ $brand->is_active ? t('active') : t('inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('brands.show', ['business_id' => $business->id, 'brand' => $brand->id]) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('brands.edit', ['business_id' => $business->id, 'brand' => $brand->id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('brands.destroy', ['business_id' => $business->id, 'brand' => $brand->id]) }}" 
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
                            {{ $brands->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-award fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ t('no_brands_found') }}</h5>
                            <p class="text-muted">{{ t('start_by_adding_brand') }}</p>
                            <a href="{{ route('brands.create', ['business_id' => $business->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>{{ t('add_first_brand') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
