@extends('layouts.admin', ['title' => 'Super Admin Control Panel - Law Universe'])

@section('content')
<div class="row g-3 mb-4">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #182234 0%, #0f172a 100%); border-left: 5px solid var(--lu-gold) !important;">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1" style="font-family: 'Cinzel', serif;">Super Admin Control Center</h3>
                    <p class="mb-0 text-white-50 small">Law Universe Platform Governance, Dynamic RBAC, Approvals & System Analytics.</p>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-warning fw-bold text-dark btn-sm px-3">
                        <i class="bi bi-person-plus-fill me-1"></i> Add User
                    </a>
                    <a href="{{ route('admin.rbac.index') }}" class="btn btn-outline-light btn-sm px-3">
                        <i class="bi bi-shield-lock me-1"></i> Dynamic RBAC Matrix
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-primary-subtle p-3 me-3 text-primary">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Total Users</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalUsers }}</h3>
                    <span class="text-success small"><i class="bi bi-check2"></i> Registered across portal</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-warning-subtle p-3 me-3 text-warning">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Pending Approvals</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $pendingApprovals }}</h3>
                    <span class="text-warning small"><i class="bi bi-shield-exclamation"></i> Advocate & Judge verification</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-success-subtle p-3 me-3 text-success">
                    <i class="bi bi-award-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Judicial Officers & Advocates</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ ($roleCounts['judge'] ?? 0) + ($roleCounts['advocate'] ?? 0) }}</h3>
                    <span class="text-muted small">{{ $roleCounts['judge'] ?? 0 }} Judges · {{ $roleCounts['advocate'] ?? 0 }} Advocates</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-info-subtle p-3 me-3 text-info">
                    <i class="bi bi-mortarboard-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Students & Public</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ ($roleCounts['student'] ?? 0) + ($roleCounts['public'] ?? 0) }}</h3>
                    <span class="text-muted small">{{ $roleCounts['student'] ?? 0 }} Students · {{ $roleCounts['public'] ?? 0 }} Public</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pending Role Approvals Section -->
    <div class="col-lg-8">
        <div class="content-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-lock-fill text-warning me-2"></i> Role Approval Workflow (Pending Verification)
                    </h5>
                    <span class="text-muted small">Advocates and Judges awaiting credential approval to gain system access.</span>
                </div>
                <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="btn btn-outline-primary btn-sm">
                    View All ({{ $pendingApprovals }})
                </a>
            </div>

            @if($pendingUsers->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                    <h6 class="fw-semibold">All Pending Registrations Cleared!</h6>
                    <p class="small text-muted mb-0">No Advocate or Judge accounts are currently awaiting review.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Applicant</th>
                                <th>Applied Role</th>
                                <th>Education</th>
                                <th>Applied Date</th>
                                <th class="text-end">Verification Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUsers as $pUser)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $pUser->avatar_url }}" width="32" height="32" class="rounded-circle me-2 border">
                                            <div>
                                                <span class="fw-semibold text-dark d-block">{{ $pUser->name }}</span>
                                                <span class="small text-muted">{{ $pUser->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark text-uppercase">{{ $pUser->role }}</span>
                                    </td>
                                    <td class="small">{{ $pUser->education ?? 'LL.B. / LL.M.' }}</td>
                                    <td class="small text-muted">{{ $pUser->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <form action="{{ route('admin.users.approve', $pUser->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm fw-bold">
                                                    <i class="bi bi-check2"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.reject', $pUser->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-x"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Audit Activity Trail -->
    <div class="col-lg-4">
        <div class="content-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i> Recent Activity Log
                </h5>
                <a href="{{ route('admin.rbac.logs') }}" class="btn btn-link btn-sm text-decoration-none">
                    Logs &rarr;
                </a>
            </div>

            <div class="list-group list-group-flush">
                @forelse($recentActivities as $act)
                    <div class="list-group-item px-0 py-2 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-top">
                            <span class="small fw-semibold text-dark">{{ str_replace('_', ' ', ucfirst($act->action)) }}</span>
                            <span class="text-muted" style="font-size: 11px;">{{ $act->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="small text-secondary mb-0">{{ $act->description ?? '-' }}</p>
                        <span class="text-muted" style="font-size: 10px;">User: {{ $act->user?->name ?? 'System' }}</span>
                    </div>
                @empty
                    <p class="text-muted small text-center my-4">No activity logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

