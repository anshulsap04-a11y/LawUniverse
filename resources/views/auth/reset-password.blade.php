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
        <h5 class="fw-bold text-dark mb-3">Set New Password</h5>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3 text-start">
                <label for="password" class="form-label small fw-semibold text-secondary">New Password</label>
                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required autofocus placeholder="Enter new password">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 text-start">
                <label for="password_confirmation" class="form-label small fw-semibold text-secondary">Confirm Password</label>
                <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required placeholder="Confirm new password">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-lu-primary py-2 fw-bold">
                    Submit
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

