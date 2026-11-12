@extends('layouts.admin', ['title' => 'System Settings - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-gear-fill text-secondary me-2"></i> System Configuration & Settings</h4>
    <p class="text-muted small mb-0">Platform branding, maintenance state, email notification defaults, and security policies.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Platform Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? 'Law Universe' }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Official Contact Email</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'support@lawuniverse.com' }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Legal Portal Jurisdiction</label>
                    <input type="text" name="default_jurisdiction" class="form-control" value="{{ $settings['default_jurisdiction'] ?? 'Republic of India' }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Default Currency</label>
                    <input type="text" name="currency" class="form-control" value="{{ $settings['currency'] ?? 'INR' }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Public Footer Legal Disclaimer</label>
                    <textarea name="footer_disclaimer" rows="3" class="form-control">{{ $settings['footer_disclaimer'] ?? 'Law Universe is an enterprise legal education and research technology platform. Content is published for pedagogical and reference purposes.' }}</textarea>
                </div>

                <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Update System Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

