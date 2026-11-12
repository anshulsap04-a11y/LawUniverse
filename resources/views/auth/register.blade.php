@extends('layouts.auth', ['cardWidth' => 'col-md-9 col-lg-8'])

@section('content')
<div class="auth-card p-4 p-md-5">

    <!-- Brand Header -->
    <div class="brand-logo-container mb-3">
        <img src="{{ asset('images/law-universe-logo.svg') }}" alt="Law Universe" class="brand-logo-img">
        <div>
            <h1 class="brand-name">LawUniverse</h1>
            <div class="brand-tagline">Legal Ecosystem & Academy</div>
        </div>
    </div>

    <!-- Navigation toggle -->
    <div class="d-flex justify-content-end mb-3">
        <div class="btn-group">
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
            <a href="{{ route('register') }}" class="btn btn-sm btn-dark active"><i class="bi bi-person-plus me-1"></i> Create Account</a>
        </div>
    </div>

    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark">Create account within a minute by filling simple form</h4>
        <p class="text-muted small">Join advocates, judicial officers, students, and legal scholars.</p>
    </div>

    <!-- Third Party Quick Signup -->
    <div class="card bg-light-subtle border p-3 mb-4 text-center">
        <span class="small fw-semibold text-secondary mb-2 d-block">Start your lawful journey with LawUniverse account — One account all services</span>
        <div class="row g-2 justify-content-center">
            <div class="col-sm-6">
                <a href="{{ route('auth.google') }}" class="btn btn-social btn-social-google shadow-sm">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                    Create Account with Google
                </a>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('auth.facebook') }}" class="btn btn-social btn-social-facebook shadow-sm">
                    <i class="bi bi-facebook text-primary"></i>
                    Create Account with Facebook
                </a>
            </div>
        </div>
    </div>

    <!-- Manual Registration Form -->
    <form action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Profile Photo / Avatar -->
        <div class="text-center mb-4">
            <div class="position-relative d-inline-block">
                <div id="avatarPreview" class="rounded-circle bg-secondary-subtle border d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; overflow: hidden; margin: 0 auto;">
                    <i class="bi bi-person text-secondary" style="font-size: 40px;"></i>
                </div>
                <label for="profile_photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 cursor-pointer shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <i class="bi bi-pencil-fill" style="font-size: 12px;"></i>
                </label>
                <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*" onchange="previewImage(this)">
            </div>
            <div class="small text-muted mt-1">Upload Profile Avatar (Optional)</div>
        </div>

        <div class="row g-3">
            <!-- First Name & Last Name -->
            <div class="col-md-6">
                <label for="first_name" class="form-label small fw-semibold text-secondary">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="First Name">
            </div>
            <div class="col-md-6">
                <label for="last_name" class="form-label small fw-semibold text-secondary">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required placeholder="Last Name">
            </div>

            <!-- Birthday & Education -->
            <div class="col-md-6">
                <label for="birthday" class="form-label small fw-semibold text-secondary">Birthday (DOB) <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('birthday') is-invalid @enderror" id="birthday" name="birthday" value="{{ old('birthday') }}" required>
                <div class="form-text small">Used for dual-factor identity verification during password recovery.</div>
            </div>
            <div class="col-md-6">
                <label for="education" class="form-label small fw-semibold text-secondary">Education</label>
                <select class="form-select @error('education') is-invalid @enderror" id="education" name="education">
                    <option value="" selected disabled>Select Education</option>
                    <option value="High School" {{ old('education') == 'High School' ? 'selected' : '' }}>High School / Senior Secondary</option>
                    <option value="Undergraduate Law (BA LL.B.)" {{ old('education') == 'Undergraduate Law (BA LL.B.)' ? 'selected' : '' }}>Undergraduate Law (BA LL.B. / BBA LL.B.)</option>
                    <option value="Bachelor of Laws (LL.B.)" {{ old('education') == 'Bachelor of Laws (LL.B.)' ? 'selected' : '' }}>Bachelor of Laws (LL.B.)</option>
                    <option value="Master of Laws (LL.M.)" {{ old('education') == 'Master of Laws (LL.M.)' ? 'selected' : '' }}>Master of Laws (LL.M.)</option>
                    <option value="Ph.D. in Law" {{ old('education') == 'Ph.D. in Law' ? 'selected' : '' }}>Doctorate / Ph.D. in Law</option>
                    <option value="Judicial Academy Fellow" {{ old('education') == 'Judicial Academy Fellow' ? 'selected' : '' }}>Judicial Academy Fellow</option>
                    <option value="Graduate" {{ old('education') == 'Graduate' ? 'selected' : '' }}>Graduate (Other Stream)</option>
                    <option value="Post Graduate" {{ old('education') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                </select>
            </div>

            <!-- Role Selection -->
            <div class="col-12">
                <label class="form-label small fw-semibold text-secondary d-block">Select Role <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-2">
                    <input type="radio" class="btn-check" name="role" id="role_public" value="public" {{ old('role', 'public') == 'public' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary px-4 fw-semibold" for="role_public">Public</label>

                    <input type="radio" class="btn-check" name="role" id="role_student" value="student" {{ old('role') == 'student' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary px-4 fw-semibold" for="role_student">Student</label>

                    <input type="radio" class="btn-check" name="role" id="role_advocate" value="advocate" {{ old('role') == 'advocate' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary px-4 fw-semibold" for="role_advocate">
                        Advocate <span class="badge bg-warning text-dark ms-1">Approval Req</span>
                    </label>

                    <input type="radio" class="btn-check" name="role" id="role_judge" value="judge" {{ old('role') == 'judge' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary px-4 fw-semibold" for="role_judge">
                        Judge <span class="badge bg-warning text-dark ms-1">Approval Req</span>
                    </label>
                </div>
                <div class="form-text small mt-1 text-muted">
                    <i class="bi bi-info-circle"></i> Note: Advocate and Judge roles require administrative verification and approval before portal access is activated.
                </div>
            </div>

            <!-- Residence Address -->
            <div class="col-12">
                <label for="residence_address" class="form-label small fw-semibold text-secondary">Residence Address</label>
                <input type="text" class="form-control" id="residence_address" name="residence_address" value="{{ old('residence_address') }}" placeholder="Enter full residence / office address">
            </div>

            <!-- Email & Mobile -->
            <div class="col-md-6">
                <label for="email" class="form-label small fw-semibold text-secondary">Email address <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
            </div>
            <div class="col-md-6">
                <label for="mobile_number" class="form-label small fw-semibold text-secondary">Mobile number</label>
                <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="+91 9876543210">
            </div>

            <!-- About -->
            <div class="col-12">
                <label for="about" class="form-label small fw-semibold text-secondary">About</label>
                <textarea class="form-control" id="about" name="about" rows="2" placeholder="Brief professional biography or legal interests...">{{ old('about') }}</textarea>
            </div>

            <!-- Password & Country -->
            <div class="col-md-6">
                <label for="password" class="form-label small fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimum 8 characters">
            </div>
            <div class="col-md-6">
                <label for="country" class="form-label small fw-semibold text-secondary">Country</label>
                <select class="form-select" id="country" name="country">
                    <option value="India" selected>India</option>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Canada">Canada</option>
                    <option value="Australia">Australia</option>
                    <option value="Singapore">Singapore</option>
                    <option value="United Arab Emirates">United Arab Emirates</option>
                </select>
            </div>

            <!-- Checkboxes as required in spec -->
            <div class="col-12">
                <div class="border rounded p-3 bg-light-subtle">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="receive_welcome_letter" id="receive_welcome_letter" value="1" {{ old('receive_welcome_letter', true) ? 'checked' : '' }}>
                        <label class="form-check-label small text-secondary" for="receive_welcome_letter">
                            I want to receive welcome letter
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" value="1" required {{ old('terms') ? 'checked' : '' }}>
                        <label class="form-check-label small text-secondary" for="terms">
                            I Accept the <em>Terms & Conditions</em> and <em>User Policy</em> of the <strong>© LawUniversePortal</strong> <span class="text-danger">*</span>
                        </label>
                        @error('terms')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-lu-primary w-100 py-2 fw-bold shadow-sm">
                    Create My Account
                </button>
            </div>

        </div>
    </form>

    <div class="text-center mt-4">
        <span class="small text-muted">Already have an account?</span>
        <a href="{{ route('login') }}" class="small fw-bold text-decoration-none ms-1">Log in here</a>
    </div>

</div>
@endsection

@section('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

