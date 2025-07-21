@extends('layouts.dash')

@section('title', 'Edit Brand - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('edit_brand') }}</h1>
                <a href="{{ route('brands.index', ['business_id' => $business->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ t('back_to_brands') }}
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ t('edit_brand_information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('brands.update', ['business_id' => $business->id, 'brand' => $brand->id]) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="business_id" value="{{ $business->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ t('brand_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $brand->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">{{ t('website') }}</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website', $brand->website) }}">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ t('description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $brand->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo_path" class="form-label">{{ t('logo_url') }}</label>
                                    <input type="url" class="form-control @error('logo_path') is-invalid @enderror" 
                                           id="logo_path" name="logo_path" value="{{ old('logo_path', $brand->logo_path) }}">
                                    @error('logo_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">{{ t('sort_order') }}</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $brand->sort_order) }}" min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Social Links Section -->
                        <hr>
                        <h6 class="mb-3">{{ t('social_media_links') }}</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_links_facebook" class="form-label">{{ t('facebook') }}</label>
                                    <input type="url" class="form-control @error('social_links.facebook') is-invalid @enderror" 
                                           id="social_links_facebook" name="social_links[facebook]" 
                                           value="{{ old('social_links.facebook', $brand->social_links['facebook'] ?? '') }}">
                                    @error('social_links.facebook')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_links_instagram" class="form-label">{{ t('instagram') }}</label>
                                    <input type="url" class="form-control @error('social_links.instagram') is-invalid @enderror" 
                                           id="social_links_instagram" name="social_links[instagram]" 
                                           value="{{ old('social_links.instagram', $brand->social_links['instagram'] ?? '') }}">
                                    @error('social_links.instagram')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ t('active_brand') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('brands.index', ['business_id' => $business->id]) }}" class="btn btn-secondary me-2">
                                {{ t('cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ t('update_brand') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
