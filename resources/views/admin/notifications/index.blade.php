@extends('layouts.admin', ['title' => 'Notifications - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-bell text-warning me-2"></i> System & Role Notifications</h4>
        <p class="text-muted small mb-0">Broadcast alerts, exam announcements, and legal statutory updates across roles.</p>
    </div>
    <button class="btn btn-warning fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#postNotifModal">
        <i class="bi bi-send me-1"></i> Post Notification
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Title & Message</th>
                    <th>Type</th>
                    <th>Audience</th>
                    <th>Date Broadcast</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $n)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $n->title }}</div>
                            <small class="text-muted">{{ Str::limit($n->message, 90) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $n->type === 'alert' ? 'danger' : ($n->type === 'announcement' ? 'info' : 'primary') }}">
                                {{ ucfirst($n->type) }}
                            </span>
                        </td>
                        <td>
                            @if($n->target_audience === 'role')
                                <span class="badge bg-light text-dark border">Role: {{ ucfirst($n->target_role) }}</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Everyone</span>
                            @endif
                        </td>
                        <td>{{ $n->sent_at ? \Carbon\Carbon::parse($n->sent_at)->format('d M, Y H:i') : $n->created_at->format('d M, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No notifications posted.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($notifications->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $notifications->links() }}</div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="postNotifModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.notifications.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Broadcast Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Notification Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. AIBE XVIII Registration Deadline Approaching">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type</label>
                    <select name="type" class="form-select">
                        <option value="announcement">Announcement</option>
                        <option value="alert">Critical Alert</option>
                        <option value="system">System Notice</option>
                        <option value="reminder">Reminder</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Target Audience</label>
                    <select name="target_audience" class="form-select" id="targetAudience" onchange="toggleRoleSelect(this.value)">
                        <option value="all">All Platform Users</option>
                        <option value="role">Specific Role</option>
                    </select>
                </div>
                <div class="col-12" id="roleSelectRow" style="display:none;">
                    <label class="form-label fw-semibold">Target Role</label>
                    <select name="target_role" class="form-select">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                    <textarea name="message" rows="3" class="form-control" required></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Action URL (Optional)</label>
                    <input type="url" name="action_url" class="form-control" placeholder="https://... or /exams">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning fw-bold">Send Notification</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRoleSelect(val) {
    document.getElementById('roleSelectRow').style.display = (val === 'role') ? 'block' : 'none';
}
</script>
@endsection
