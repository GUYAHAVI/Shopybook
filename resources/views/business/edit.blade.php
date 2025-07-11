@extends('layouts.dash')

@section('content')
<div class="py-12" style="background: #f8f9fa; min-height: 100vh;">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Logo Card -->
            <div class="col-span-1 flex flex-col items-center justify-start">
                <div class="bg-white shadow-lg rounded-lg p-6 w-full flex flex-col items-center border border-cyan-200" style="min-height: 220px;">
                    <h3 class="text-lg font-semibold mb-4 text-center" style="color:#020258;">Business Logo</h3>
                    @if($business->logo_path)
                        <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Business Logo" class="rounded-full object-cover border-4 border-cyan-200 shadow mb-2" style="width:200px; max-width:100%; height:auto;">
                    @else
                        <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center text-3xl text-gray-400 mb-2" style="width:200px; height:200px;">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                    <label for="logo" class="block mt-2 text-sm text-gray-500">Update Logo</label>
                    <input id="logo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none mt-1" type="file" name="logo" accept="image/*">
                    <p class="mt-1 text-xs text-gray-400">Leave blank to keep current logo</p>
                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                </div>
            </div>
            <!-- Form Card -->
            <div class="col-span-2">
                <div class="bg-white shadow-lg rounded-lg p-8 border border-cyan-200">
                    <h2 class="text-2xl font-bold mb-6" style="color:#020258;">Edit Business Profile</h2>
                    <form method="POST" action="{{ route('business.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Business Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-cyan-700 mb-1"><i class="fas fa-briefcase mr-1"></i> Business Name *</label>
                                <input id="name" class="form-control block w-full" type="text" name="name" value="{{ old('name', $business->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <!-- Business Type -->
                            <div>
                                <label for="business_type" class="block text-sm font-medium text-cyan-700 mb-1"><i class="fas fa-tags mr-1"></i> Business Type *</label>
                                <select id="business_type" name="business_type" class="form-control block w-full" required>
                                    @foreach($businessTypes as $key => $type)
                                        <option value="{{ $key }}" {{ old('business_type', $business->business_type) == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                            </div>
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-cyan-700 mb-1"><i class="fas fa-phone mr-1"></i> Phone Number *</label>
                                <input id="phone" class="form-control block w-full" type="text" name="phone" value="{{ old('phone', $business->phone) }}" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-cyan-700 mb-1"><i class="fas fa-envelope mr-1"></i> Email Address</label>
                                <input id="email" class="form-control block w-full" type="email" name="email" value="{{ old('email', $business->email) }}" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-cyan-700 mb-1"><i class="fas fa-map-marker-alt mr-1"></i> Address *</label>
                                <input id="address" class="form-control block w-full" type="text" name="address" value="{{ old('address', $business->address) }}" required />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-cyan-700 mb-1"><i class="fas fa-city mr-1"></i> City *</label>
                                <input id="city" class="form-control block w-full" type="text" name="city" value="{{ old('city', $business->city) }}" required />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-cyan-700 mb-1"><i class="fas fa-align-left mr-1"></i> Business Description</label>
                            <textarea id="description" name="description" rows="3" class="form-control block w-full">{{ old('description', $business->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <hr class="my-6 border-cyan-200">
                        <div class="flex items-center justify-between mt-6">
                            <button type="button" onclick="window.openDeleteBusinessModal(function(password) { document.getElementById('delete-form').submit(); });" class="btn btn-outline-danger ms-auto" style="border:2px solid #dc3545; color:#dc3545;">
                                <i class="fas fa-trash-alt me-1"></i> Delete Business
                            </button>
                            <x-primary-button>
                                <i class="fas fa-save me-1"></i> {{ __('Update Business Profile') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Remove the old confirmDelete() function
</script>

<style>
body, .container-fluid, .card, .main-content, .content {
    background: #fff !important;
    color: #020258 !important;
}
.btn-primary {
    background: #020258 !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
}
.btn-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #020258 !important;
}
.form-control {
    background: #f8f9fa !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
    margin-bottom: 1rem !important;
    max-width: 400px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}
@media (max-width: 640px) {
    .form-control {
        max-width: 100%;
    }
}
.form-control:focus {
    border-color: #020258 !important;
    box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
}
.card-header {
    background: #f8f9fa !important;
    color: #020258 !important;
    border-bottom: 1px solid #13e8e9 !important;
}
</style>
@endsection