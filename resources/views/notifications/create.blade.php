@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.user-portal', ['title' => 'Post Notification - Law Universe'])

@section('content')
<div class="container-fluid py-3">
    <div class="content-card p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Post Notification</h4>
                <p class="text-muted small mb-0">Fill the necessary details to broadcast a notification to portal users.</p>
            </div>
            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Notifications
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border p-4 bg-light-subtle">
                    <h5 class="fw-bold text-secondary mb-4 border-bottom pb-2">Enter Notification Subject & Message</h5>

                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="subject" class="form-label small fw-semibold text-secondary">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" required placeholder="Enter Subject">
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label small fw-semibold text-secondary">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Enter Message"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary d-block">Recipient Type</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recipient_profile" id="rec_profile" value="1">
                                    <label class="form-check-label small" for="rec_profile">Send by Profile</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recipient_role" id="rec_role" value="1" checked>
                                    <label class="form-check-label small" for="rec_role">Send by Role</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

