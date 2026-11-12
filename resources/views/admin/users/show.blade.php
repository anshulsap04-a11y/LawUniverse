@extends('layouts.admin', ['title' => 'User Account Details - Law Universe'])

@section('content')
<div class="content-card p-4">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-0 text-dark">User Account Details</h4>
            <span class="text-muted small">Comprehensive profile information for User ID: <strong>{{ $user->user_code }}</strong></span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm fw-bold">
                <i class="bi bi-pencil-square me-1"></i> Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Sidebar Card -->
        <div class="col-md-4 text-center">
            <div class="card p-4 border bg-light-subtle h-100">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" width="110" height="110" class="rounded-circle mx-auto mb-3 border shadow-sm">
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                
                <div class="mb-3">
                    <span class="badge bg-primary text-uppercase px-3 py-2">{{ $user->role }}</span>
                    @if($user->status === 'active')
                        <span class="badge bg-success px-3 py-2 ms-1">Active</span>
                    @elseif($user->status === 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2 ms-1">Pending Approval</span>
                    @else
                        <span class="badge bg-secondary px-3 py-2 ms-1">{{ ucfirst($user->status) }}</span>
                    @endif
                </div>

                @if($user->isPending())
                    <div class="d-grid gap-2 mt-3">
                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                <i class="bi bi-check-circle me-1"></i> Approve Role & Access
                            </button>
                        </form>
                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-x-circle me-1"></i> Reject Registration
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="col-md-8">
            <div class="card p-4 border h-100">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Profile Attributes</h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="small text-muted d-block">User ID Code</label>
                        <span class="fw-semibold">{{ $user->user_code }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block">Date of Birth</label>
                        <span class="fw-semibold">{{ $user->dob ? $user->dob->format('d M Y') : 'Not Provided' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block">Education Qualification</label>
                        <span class="fw-semibold">{{ $user->education ?? 'Not Provided' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block">Mobile Number</label>
                        <span class="fw-semibold">{{ $user->mobile_number ?? 'Not Provided' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block">Country</label>
                        <span class="fw-semibold">{{ $user->country ?? 'India' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block">Registration Date</label>
                        <span class="fw-semibold">{{ $user->created_at->format('d M Y H:i A') }}</span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">Residence Address</label>
                        <span class="fw-semibold">{{ $user->address ?? 'Not Provided' }}</span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">About / Professional Summary</label>
                        <p class="text-secondary small mb-0">{{ $user->about ?? 'No summary provided.' }}</p>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mt-4 mb-3">Security & Feature Flags</h6>
                <div class="row g-2 small">
                    <div class="col-sm-4">
                        <i class="bi {{ $user->enable_instant_otp ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-muted' }} me-1"></i> Instant OTP Login
                    </div>
                    <div class="col-sm-4">
                        <i class="bi {{ $user->enable_2fa ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-muted' }} me-1"></i> Two-Factor Auth
                    </div>
                    <div class="col-sm-4">
                        <i class="bi {{ $user->receive_welcome_letter ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-muted' }} me-1"></i> Welcome Letter
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

