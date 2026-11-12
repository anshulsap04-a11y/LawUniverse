@extends('layouts.auth')

@section('content')
<div class="auth-card p-4 p-md-5 text-center">

    <div class="brand-logo-container mb-2">
        <img src="{{ asset('images/law-universe-logo.svg') }}" alt="Law Universe" class="brand-logo-img">
    </div>
    <div class="brand-tagline mb-3" style="font-size: 13px; color: #111;">
        Law Universe Password Recovery Wizard
    </div>

    <div class="card border p-4 bg-light-subtle my-4">
        <h5 class="fw-bold text-dark mb-2">We'll forward reset instruction to your email</h5>
        <p class="text-secondary small mb-3">Please verify your registered email ID and date of birth.</p>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="mb-3 text-start">
                <label for="email" class="form-label small fw-semibold text-secondary">Email</label>
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter registered email">
            </div>

            <div class="mb-4 text-start">
                <label for="dob" class="form-label small fw-semibold text-secondary">Date of Birth</label>
                <input type="date" class="form-control form-control-lg @error('dob') is-invalid @enderror" id="dob" name="dob" value="{{ old('dob') }}" required placeholder="mm/dd/yyyy">
                <div class="form-text small">Required to verify account ownership.</div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-lu-primary py-2 fw-bold">
                    Send Me
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Login
        </a>
    </div>

</div>
@endsection

