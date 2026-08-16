@extends('layouts.public')
@section('title', 'Register Your Business - Shopybook')

@section('content')

<section class="hero-section text-light">
    <div class="container text-center">
        <h1>Register Your Business</h1>
        <p class="mb-0">Add your business to the Shopybook directory.</p>
    </div>
</section>

<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="sb-form-card">

                    <form method="POST" action="{{ route('register.business') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#7b2e2e;">Business Name</label>
                            <input type="text" name="business_name" class="form-control" required placeholder="Enter your business name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#7b2e2e;">Category</label>
                            <select name="category" class="form-control" required>
                                <option value="">Select a category</option>
                                <option value="food">Food</option>
                                <option value="drinks">Drinks</option>
                                <option value="juakali">Juakali</option>
                                <option value="furniture">Furniture</option>
                                <option value="metal works">Metal Works</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#7b2e2e;">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="Enter your business email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#7b2e2e;">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Create a password">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color:#7b2e2e;">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm your password">
                        </div>

                        <button type="submit" class="btn1 w-100">Register Business</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
