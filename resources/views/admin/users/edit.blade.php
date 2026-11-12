@extends('layouts.admin', ['title' => 'Update User Account - Law Universe'])

@section('content')
<div class="content-card p-4">

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Update User Details</h4>
                <p class="text-muted small mb-0">Editing account: <strong>{{ $user->name }}</strong> (User ID: {{ $user->user_code }})</p>
            </div>
            <div>
                <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Save Changes
                </button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <div id="avatarPreview" class="rounded-circle bg-secondary-subtle border d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; overflow: hidden; margin: 0 auto;">
                            <img src="{{ $user->avatar_url }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <label for="profile_photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="bi bi-pencil-fill" style="font-size: 12px;"></i>
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label small fw-semibold text-secondary">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label small fw-semibold text-secondary">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="birthday" class="form-label small fw-semibold text-secondary">Birthday</label>
                        <input type="date" class="form-control" id="birthday" name="birthday" value="{{ old('birthday', $user->dob?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="education" class="form-label small fw-semibold text-secondary">Education</label>
                        <input type="text" class="form-control" id="education" name="education" value="{{ old('education', $user->education) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label small fw-semibold text-secondary">User Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ old('role', $user->role) == $r->name ? 'selected' : '' }}>
                                    {{ $r->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-semibold text-secondary">Account Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Activated</option>
                            <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="deactivated" {{ old('status', $user->status) == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                            <option value="rejected" {{ old('status', $user->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold text-secondary">Email address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mobile_number" class="form-label small fw-semibold text-secondary">Mobile Number</label>
                        <input type="tel" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}">
                    </div>

                    <div class="col-12">
                        <label for="residence_address" class="form-label small fw-semibold text-secondary">Residence Address</label>
                        <input type="text" class="form-control" id="residence_address" name="residence_address" value="{{ old('residence_address', $user->address) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label small fw-semibold text-secondary">Password <span class="text-muted fw-normal">(Leave blank to keep current)</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••">
                    </div>
                    <div class="col-md-6">
                        <label for="country" class="form-label small fw-semibold text-secondary">Country</label>
                        <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $user->country) }}">
                    </div>

                    <div class="col-12">
                        <label for="about" class="form-label small fw-semibold text-secondary">About</label>
                        <textarea class="form-control" id="about" name="about" rows="3">{{ old('about', $user->about) }}</textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Update Account</button>
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

