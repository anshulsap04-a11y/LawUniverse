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
        <h5 class="fw-bold text-dark mb-2">Enter 6-Digit Verification Code</h5>
        <p class="text-secondary small mb-3">
            Code has been sent to: <strong>{{ $email }}</strong>
        </p>

        <form action="{{ route('auth.otp.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-4">
                <input type="text" maxlength="6" class="form-control form-control-lg text-center fs-2 fw-bold tracking-widest @error('otp') is-invalid @enderror" id="otp" name="otp" required autofocus placeholder="------" style="letter-spacing: 10px;">
                @error('otp')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-lu-primary py-2 fw-bold">
                    Verify & Login
                </button>
            </div>
        </form>

        <form action="{{ route('auth.otp.send') }}" method="POST" class="mt-2">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="btn btn-link btn-sm text-decoration-none">
                <i class="bi bi-arrow-repeat me-1"></i> Resend OTP code
            </button>
        </form>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Standard Login
        </a>
    </div>

</div>
@endsection

