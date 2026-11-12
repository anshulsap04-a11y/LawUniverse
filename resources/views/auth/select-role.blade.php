@extends('layouts.auth')

@section('content')
<div class="auth-card p-4 p-md-5 text-center">

    <div class="brand-logo-container mb-3">
        <img src="{{ asset('images/law-universe-logo.svg') }}" alt="Law Universe" class="brand-logo-img">
        <div>
            <h1 class="brand-name">LawUniverse</h1>
            <div class="brand-tagline">Role Selection Wizard</div>
        </div>
    </div>

    <!-- Illustrated 3D Team Icons from PDF -->
    <div class="my-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle rounded-circle p-3 shadow-sm" style="width: 100px; height: 100px;">
            <i class="bi bi-people-fill text-primary" style="font-size: 50px;"></i>
        </div>
        <div class="mt-2 text-muted small">
            Signed in via <strong>{{ ucfirst($oauthData['provider'] ?? 'Social') }}</strong> as <em>{{ $oauthData['email'] ?? '' }}</em>
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-4">Select User Role</h4>

    <form action="{{ route('auth.social.role.submit') }}" method="POST">
        @csrf

        <div class="border rounded p-4 mb-4 bg-light-subtle">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <input type="radio" class="btn-check" name="role" id="role_public" value="public" checked>
                <label class="btn btn-outline-secondary px-3 py-2 fw-semibold" for="role_public">
                    <i class="bi bi-person me-1"></i> Public
                </label>

                <input type="radio" class="btn-check" name="role" id="role_student" value="student">
                <label class="btn btn-outline-secondary px-3 py-2 fw-semibold" for="role_student">
                    <i class="bi bi-mortarboard me-1"></i> Student
                </label>

                <input type="radio" class="btn-check" name="role" id="role_advocate" value="advocate">
                <label class="btn btn-outline-secondary px-3 py-2 fw-semibold" for="role_advocate">
                    <i class="bi bi-briefcase me-1"></i> Advocate
                </label>

                <input type="radio" class="btn-check" name="role" id="role_judge" value="judge">
                <label class="btn btn-outline-secondary px-3 py-2 fw-semibold" for="role_judge">
                    <i class="bi bi-award me-1"></i> Judge
                </label>
            </div>
            <div class="small text-muted mt-3">
                <i class="bi bi-shield-check me-1"></i> Select your designation. Advocate and Judge accounts require admin approval.
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-lu-primary px-5 py-2 fw-bold shadow-sm">
                Next <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </form>

</div>
@endsection

