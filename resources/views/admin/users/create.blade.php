@extends('layouts.admin', ['title' => 'Add User Account - Law Universe'])

@section('content')
<div class="content-card p-4">

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="addUserForm">
        @csrf

        <!-- Top Header with Yellow Add User Button as in Screenshot (Page 3) -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="fw-bold mb-0 text-dark">New User Account</h4>
                <p class="text-muted small mb-0">Fill in the form to register new users manually into the system.</p>
            </div>
            <div>
                <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm">
                    <i class="bi bi-person-check-fill me-1"></i> Add User
                </button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="text-center mb-4">
                    <h5 class="fw-bold text-secondary">Create account by filling manual form</h5>
                    
                    <!-- Avatar Preview & Upload -->
                    <div class="position-relative d-inline-block mt-3">
                        <div id="avatarPreview" class="rounded-circle bg-secondary-subtle border d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; overflow: hidden; margin: 0 auto;">
                            <i class="bi bi-person text-secondary" style="font-size: 45px;"></i>
                        </div>
                        <label for="profile_photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="bi bi-pencil-fill" style="font-size: 12px;"></i>
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
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
                        <label for="birthday" class="form-label small fw-semibold text-secondary">Birthday</label>
                        <input type="date" class="form-control" id="birthday" name="birthday" value="{{ old('birthday') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="education" class="form-label small fw-semibold text-secondary">Education</label>
                        <select class="form-select" id="education" name="education">
                            <option value="" selected disabled>Select Education</option>
                            <option value="High School">High School</option>
                            <option value="Bachelor of Laws (LL.B.)">Bachelor of Laws (LL.B.)</option>
                            <option value="Master of Laws (LL.M.)">Master of Laws (LL.M.)</option>
                            <option value="Ph.D. in Law">Ph.D. in Law</option>
                            <option value="Graduate">Graduate</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- User Type & About -->
                    <div class="col-md-6">
                        <label for="role" class="form-label small fw-semibold text-secondary">User Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>
                                    {{ $r->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="about" class="form-label small fw-semibold text-secondary">About</label>
                        <input type="text" class="form-control" id="about" name="about" value="{{ old('about') }}" placeholder="Brief biography / notes">
                    </div>

                    <!-- Mobile Number & Email Address -->
                    <div class="col-md-6">
                        <label for="mobile_number" class="form-label small fw-semibold text-secondary">Mobile Number</label>
                        <input type="tel" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="Mobile number">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold text-secondary">Email address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="Email address">
                    </div>

                    <!-- Residence Address -->
                    <div class="col-12">
                        <label for="residence_address" class="form-label small fw-semibold text-secondary">Residence Address</label>
                        <input type="text" class="form-control" id="residence_address" name="residence_address" value="{{ old('residence_address') }}" placeholder="Residence Address">
                    </div>

                    <!-- Password & Country -->
                    <div class="col-md-6">
                        <label for="password" class="form-label small fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Password">
                    </div>
                    <div class="col-md-6">
                        <label for="country" class="form-label small fw-semibold text-secondary">Country</label>
                        <select class="form-select" id="country" name="country">
                            <option value="India" selected>India</option>
                            <option value="United States">United States</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="Canada">Canada</option>
                            <option value="Australia">Australia</option>
                        </select>
                    </div>

                    <!-- Status Selection -->
                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-semibold text-secondary">Account Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" selected>Activated</option>
                            <option value="pending">Pending Approval</option>
                            <option value="deactivated">Deactivated</option>
                        </select>
                    </div>

                    <!-- Three Checkboxes matching Spec Screenshot (Page 4) -->
                    <div class="col-12 mt-4">
                        <div class="border rounded p-3 bg-light-subtle">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receive_welcome_letter" id="receive_welcome_letter" value="1" checked>
                                <label class="form-check-label small text-secondary" for="receive_welcome_letter">
                                    Send welcome letter to the user
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="enable_2fa" id="enable_2fa" value="1">
                                <label class="form-check-label small text-secondary" for="enable_2fa">
                                    Enable dual security feature. (Two-Factor Authentication)
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_instant_otp" id="enable_instant_otp" value="1" checked>
                                <label class="form-check-label small text-secondary" for="enable_instant_otp">
                                    Enable instant login feature. (OTP based sign-in)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Submit button -->
                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            Save User Account
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

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

