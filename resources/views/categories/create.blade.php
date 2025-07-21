@extends('layouts.dash')

@section('title', 'Add Category - ' . $business->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('add_category') }}</h1>
                <a href="{{ route('categories.index', ['business_id' => $business->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ t('back_to_categories') }}
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ t('category_information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.store', ['business_id' => $business->id]) }}">
                        @csrf
                        <input type="hidden" name="business_id" value="{{ $business->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ t('category_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parent_id" class="form-label">{{ t('parent_category') }}</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                        <option value="">{{ t('no_parent_root_category') }}</option>
                                        @foreach($parentCategories as $parentCategory)
                                            <option value="{{ $parentCategory->id }}" {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
                                                {{ $parentCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ t('description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image_path" class="form-label">{{ t('image_url') }}</label>
                                    <input type="url" class="form-control @error('image_path') is-invalid @enderror" 
                                           id="image_path" name="image_path" value="{{ old('image_path') }}">
                                    @error('image_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ t('enter_image_url') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">{{ t('sort_order') }}</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ t('lower_numbers_appear_first') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Section -->
                        <hr>
                        <h6 class="mb-3">{{ t('seo_settings') }}</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">{{ t('meta_title') }}</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                           id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ t('seo_title_for_search_engines') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">{{ t('meta_description') }}</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                      id="meta_description" name="meta_description" rows="2">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ t('seo_description_for_search_engines') }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ t('active_category') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('categories.index', ['business_id' => $business->id]) }}" class="btn btn-secondary me-2">
                                {{ t('cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ t('save_category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
