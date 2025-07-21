@extends('layouts.dash')

@section('title', $brand->name . ' - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('brand_details') }}</h1>
                <div>
                    <a href="{{ route('brands.edit', ['business_id' => $business->id, 'brand' => $brand->id]) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>{{ t('edit') }}
                    </a>
                    <a href="{{ route('brands.index', ['business_id' => $business->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ t('back_to_brands') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h2>{{ $brand->name }}</h2>
                            @if($brand->description)
                                <p class="text-muted">{{ $brand->description }}</p>
                            @endif
                            
                            @if($brand->website)
                                <p><strong>{{ t('website') }}:</strong> <a href="{{ $brand->website }}" target="_blank">{{ $brand->website }}</a></p>
                            @endif
                            
                            <p><strong>{{ t('status') }}:</strong> 
                                <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                    {{ $brand->is_active ? t('active') : t('inactive') }}
                                </span>
                            </p>

                            @if($brand->social_links)
                                <div class="mt-3">
                                    <h6>{{ t('social_media') }}</h6>
                                    @foreach($brand->social_links as $platform => $url)
                                        <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fab fa-{{ $platform }}"></i> {{ ucfirst($platform) }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if($brand->logo_path)
                                <img src="{{ $brand->logo_path }}" alt="{{ $brand->name }}" class="img-fluid rounded">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
