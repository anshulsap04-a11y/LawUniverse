@extends('layouts.auth')

@section('content')
<div class="auth-card p-4 p-md-5 text-center">

    <div class="brand-logo-container mb-2">
        <img src="{{ asset('images/law-universe-logo.svg') }}" alt="Law Universe" class="brand-logo-img">
    </div>
    <div class="brand-tagline mb-3" style="font-size: 13px; color: #111;">
        Law Universe - Instant Authentication System
    </div>

    <div class="card border p-4 bg-light-subtle my-4">
        <h5 class="fw-bold text-dark mb-2">Instantly Login with OTP</h5>
        <p class="text-secondary small mb-3">we'll send to you via email.</p>

        <form action="{{ route('auth.otp.send') }}" method="POST">
            @csrf

            <div class="mb-3 text-start">
                <label for="email" class="form-label small fw-semibold text-secondary">Email</label>
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your registered email">
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
            <i class="bi bi-arrow-left me-1"></i> Back to Standard Login
        </a>
    </div>

</div>
@endsection

