@extends('layouts.admin', ['title' => 'Audit & Activity Logs - Law Universe'])

@section('content')
<div class="content-card p-4">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-journal-text text-primary me-2"></i> Audit & Activity Logs
            </h4>
            <span class="text-muted small">Comprehensive security and operational event trail.</span>
        </div>
        <a href="{{ route('admin.rbac.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-shield-lock me-1"></i> Back to RBAC Matrix
        </a>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('admin.rbac.logs') }}" method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by user or description..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="action" class="form-control form-control-sm" placeholder="Filter by action code..." value="{{ request('action') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.rbac.logs') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="table-dark small">
                <tr>
                    <th width="60">#</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>
                            @if($log->user)
                                <div class="d-flex align-items-center">
                                    <img src="{{ $log->user->avatar_url }}" width="24" height="24" class="rounded-circle me-2 border">
                                    <div>
                                        <span class="fw-semibold small d-block">{{ $log->user->name }}</span>
                                        <span class="text-muted" style="font-size: 11px;">{{ $log->user->email }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">System / Guest</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace">{{ $log->action }}</span>
                        </td>
                        <td class="small">{{ $log->description ?? '-' }}</td>
                        <td class="small text-muted font-monospace">{{ $log->ip_address ?? '-' }}</td>
                        <td class="small text-muted">{{ $log->created_at->format('d M Y, h:i:s A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No activity logs recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

</div>
@endsection

