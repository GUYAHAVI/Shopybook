@extends('layouts.dash')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Edit Business Profile</h2>
                    @if($business->logo_path)
                        <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Business Logo" class="h-16 w-16 rounded-full object-cover">
                    @endif
                </div>
                
                <form method="POST" action="{{ route('business.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Name -->
                        <div>
                            <x-input-label for="name" :value="__('Business Name *')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $business->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Business Type -->
                        <div>
                            <x-input-label for="business_type" :value="__('Business Type *')" />
                            <select id="business_type" name="business_type" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                @foreach($businessTypes as $key => $type)
                                    <option value="{{ $key }}" {{ old('business_type', $business->business_type) == $key ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                        </div>

                        <!-- Contact Information -->
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number *')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $business->phone)" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $business->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="address" :value="__('Address *')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $business->address)" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="city" :value="__('City *')" />
                            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city', $business->city)" required />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        <!-- Business Logo -->
                        <div class="md:col-span-2">
                            <x-input-label for="logo" :value="__('Update Logo')" />
                            <input id="logo" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="logo" accept="image/*">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Leave blank to keep current logo</p>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Business Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $business->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <button type="button" onclick="confirmDelete()" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                            Delete Business
                        </button>
                        
                        <x-primary-button>
                            {{ __('Update Business Profile') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Delete Form (hidden) -->
                <form id="delete-form" method="POST" action="{{ route('business.destroy') }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete your business? This action cannot be undone and will remove all your business data.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection