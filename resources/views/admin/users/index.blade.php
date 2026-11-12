@extends('layouts.admin', ['title' => 'List User Account - Law Universe'])

@section('content')
<div class="content-card p-3 p-md-4">

    <!-- Top Action & Search Bar matching specification screenshot -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-0 text-dark">List User Account</h4>
            <span class="text-muted small">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of total {{ $users->total() }} record(s)</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-warning fw-bold text-dark px-3 btn-sm">
                <i class="bi bi-person-plus-fill me-1"></i> Add User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-spec-refresh btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </a>
        </div>
    </div>

    <!-- Filter Form matching Spec Screenshot (Page 5) -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
        <div class="row g-2 align-items-center">
            <!-- Search Text Field -->
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search here ..." value="{{ request('search') }}">
            </div>

            <!-- Clear / Refresh button for search -->
            <div class="col-auto">
                <a href="{{ route('admin.users.index') }}" class="btn btn-spec-refresh btn-sm">
                    Refresh
                </a>
            </div>

            <!-- Multi-Trash Button trigger -->
            <div class="col-auto">
                <button type="button" class="btn btn-spec-trash btn-sm" onclick="submitMultiTrash()">
                    Multi-Trash
                </button>
            </div>

            <!-- Filter by Add Date -->
            <div class="col-auto d-flex align-items-center gap-2">
                <span class="small text-muted fw-semibold">Filter by Add Date</span>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" onchange="this.form.submit()">
            </div>

            <!-- Filter by Status -->
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activated</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="deactivated" {{ request('status') == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                </select>
            </div>

            <!-- Filter by User Type / Role -->
            <div class="col-auto">
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">All Roles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Multi-Trash Form wrapper -->
    <form id="multiTrashForm" action="{{ route('admin.users.bulk_trash') }}" method="POST">
        @csrf

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle table-custom-header mb-0">
                <thead>
                    <tr>
                        <th width="40" class="text-center">
                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                        </th>
                        <th width="40">#</th>
                        <th>User ID#</th>
                        <th>User Role</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th class="text-center" width="220">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="text-center">
                                @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                    <input class="form-check-input user-select-checkbox" type="checkbox" name="selected_ids[]" value="{{ $user->id }}">
                                @endif
                            </td>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $user->user_code ?? 'LU-' . $user->id }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($user->role) {
                                        'super_admin', 'admin' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'judge' => 'bg-purple-subtle text-purple border border-purple',
                                        'advocate' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'student' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                        default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} fw-semibold text-uppercase" style="font-size: 11px;">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" width="28" height="28" class="rounded-circle me-2 border">
                                    <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $user->email }}</td>
                            <td class="small">{{ $user->mobile_number ?? '-' }}</td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">Activated</span>
                                @elseif($user->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending Approval</span>
                                @elseif($user->status === 'deactivated')
                                    <span class="badge bg-secondary">Deactivated</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <!-- Details Button -->
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-table-action btn-table-details" title="Details">
                                        Details
                                    </a>

                                    <!-- Update Button -->
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-table-action btn-table-update" title="Update">
                                        Update
                                    </a>

                                    <!-- Approve Button (if pending) -->
                                    @if($user->isPending())
                                        <button type="button" class="btn btn-table-action btn-success" onclick="approveUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Approve Role">
                                            Approve
                                        </button>
                                    @endif

                                    <!-- Trash Button -->
                                    @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                        <button type="button" class="btn btn-table-action btn-table-trash" onclick="trashSingleUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Trash">
                                            Trash
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No user accounts found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- Single Trash Hidden Form -->
<form id="singleTrashForm" action="" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- Approve Hidden Form -->
<form id="approveUserForm" action="" method="POST" style="display:none;">
    @csrf
</form>
@endsection

@section('scripts')
<script>
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.user-select-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
}

function submitMultiTrash() {
    const selected = document.querySelectorAll('.user-select-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one user from the list to Multi-Trash.');
        return;
    }
    if (confirm('Are you sure you want to trash ' + selected.length + ' selected user account(s)?')) {
        document.getElementById('multiTrashForm').submit();
    }
}

function trashSingleUser(id, name) {
    if (confirm('Are you sure you want to move user "' + name + '" to trash?')) {
        const form = document.getElementById('singleTrashForm');
        form.action = '/admin/users/' + id;
        form.submit();
    }
}

function approveUser(id, name) {
    if (confirm('Approve account for "' + name + '" and activate portal access?')) {
        const form = document.getElementById('approveUserForm');
        form.action = '/admin/users/' + id + '/approve';
        form.submit();
    }
}
</script>
@endsection

