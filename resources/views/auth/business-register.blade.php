@extends('layouts.master')
@section('title', 'Register Business')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-4">Register Your Business</h2>

    <form method="POST" action="{{ route('register.business') }}">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold">Business Name</label>
            <input type="text" name="business_name" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Category</label>
            <select name="category" class="w-full border p-2 rounded" required>
                <option value="">Select a category</option>
                <option value="food">Food</option>
                <option value="drinks">Drinks</option>
                <option value="juakali">Juakali</option>
                <option value="furniture">Furniture</option>
                <option value="metal works">Metal Works</option>
                <!-- Add more as needed -->
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Email</label>
            <input type="email" name="email" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Password</label>
            <input type="password" name="password" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full border p-2 rounded" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Register Business</button>
    </form>
</div>
@endsection
