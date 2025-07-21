@extends('layouts.dash')

@section('title', $category->name . ' - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('category_details') }}</h1>
                <div>
                    <a href="{{ route('categories.edit', ['business_id' => $business->id, 'category' => $category->id]) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>{{ t('edit') }}
                    </a>
                    <a href="{{ route('categories.index', ['business_id' => $business->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ t('back_to_categories') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h2>{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="text-muted">{{ $category->description }}</p>
                            @endif
                            
                            @if($category->parent)
                                <p><strong>{{ t('parent_category') }}:</strong> {{ $category->parent->name }}</p>
                            @endif
                            
                            <p><strong>{{ t('status') }}:</strong> 
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? t('active') : t('inactive') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            @if($category->image_path)
                                <img src="{{ $category->image_path }}" alt="{{ $category->name }}" class="img-fluid rounded">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
