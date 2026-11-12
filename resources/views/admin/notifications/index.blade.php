@extends('layouts.admin', ['title' => 'Manage Notification - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-bell-fill text-warning me-2"></i> List Notification</h4>
        <p class="text-muted small mb-0">Displays all notifications distributed to platform users (Page 40 & 41).</p>
    </div>
    <button class="btn btn-warning fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#postNotifModal">
        <i class="bi bi-send me-1"></i> Post Notification
    </button>
</div>

<!-- Filters Panel matching PDF Page 41 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search notifications by subject or message..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="Filter by Date">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.notifications.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiRemoveBtn" onclick="return confirm('Remove selected notifications?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi Remove
            </button>
            <span class="text-muted small">Showing {{ $notifications->total() }} Sent Notifications</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Subject Code</th>
                        <th>Subject</th>
                        <th>Recipient Type</th>
                        <th>Broadcast Date</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions (Page 41)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $n)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $n->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td><code>{{ $n->subject_code }}</code></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $n->subject }}</div>
                                <small class="text-muted">{{ Str::limit($n->message, 85) }}</small>
                            </td>
                            <td>
                                @if($n->recipient_type === 'role' && is_array($n->target_roles))
                                    <span class="badge bg-light text-dark border">Roles: {{ implode(', ', $n->target_roles) }}</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">All Users</span>
                                @endif
                            </td>
                            <td>{{ $n->post_date ? \Carbon\Carbon::parse($n->post_date)->format('d M, Y H:i') : $n->created_at->format('d M, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $n->status === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($n->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <!-- Details Button (Item 6) -->
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#nModal_{{ $n->id }}">
                                    Details
                                </button>
                                <!-- Remove Button (Item 7) -->
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete notification?')) document.getElementById('trash_{{ $n->id }}').submit()">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No notifications broadcasted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $notifications->links() }}</div>
        @endif
    </div>
</form>

@foreach($notifications as $n)
    <form action="{{ route('admin.notifications.destroy', $n->id) }}" method="POST" id="trash_{{ $n->id }}" class="d-none">@csrf @method('DELETE')</form>

    <div class="modal fade" id="nModal_{{ $n->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ $n->subject }} ({{ $n->subject_code }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong>Recipient Type:</strong> {{ ucfirst($n->recipient_type) }}</div>
                    <div class="p-3 bg-light rounded border mb-3">
                        {{ $n->message }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Post Notification Module (Page 41 & 42) -->
<div class="modal fade" id="postNotifModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.notifications.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Post Notification Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" required placeholder="Subject / topic of notification">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Recipient Type <span class="text-danger">*</span></label>
                    <select name="recipient_type" class="form-select" id="recipientType" onchange="toggleRoleAudience(this.value)">
                        <option value="all">All</option>
                        <option value="role">By Role</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="col-12" id="roleAudienceRow" style="display:none;">
                    <label class="form-label fw-semibold">Select Target Roles</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($roles as $r)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_roles[]" value="{{ $r->name }}" id="r_{{ $r->id }}">
                                <label class="form-check-label" for="r_{{ $r->id }}">{{ $r->label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Message Content <span class="text-danger">*</span></label>
                    <textarea name="message" rows="4" class="form-control" required placeholder="Compose main content to be delivered..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning fw-bold">Post Notification</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRoleAudience(val) {
    document.getElementById('roleAudienceRow').style.display = (val === 'role') ? 'block' : 'none';
}
function toggleSelectAll(master) {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = master.checked);
    checkSelection();
}
function checkSelection() {
    const checked = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('multiRemoveBtn').disabled = (checked === 0);
}
</script>
@endsection
