@extends('layouts.admin', ['title' => 'Manage Package - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-box-seam text-danger me-2"></i> Manage Package</h4>
        <p class="text-muted small mb-0">Create, update, and manage service packages, pricing tiers, and bundled features (Page 31 & 32).</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Plan
    </button>
</div>

<!-- Filters Panel matching PDF Page 31 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.finance.packages') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search Plans by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="Filter by Date">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.finance.packages') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.finance.packages.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiRemoveBtn" onclick="return confirm('Delete selected plans?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi Remove
            </button>
            <span class="text-muted small">Showing {{ $packages->total() }} Subscription Plans</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Plan Code & Title</th>
                        <th>Price (&#8377;)</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Bundled Features</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $pkg)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $pkg->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $pkg->title }}</div>
                                <code>{{ $pkg->plan_code }}</code>
                            </td>
                            <td class="fs-6 fw-bold text-primary">&#8377; {{ number_format($pkg->current_price, 2) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $pkg->duration_days }} Days</span></td>
                            <td>
                                <span class="badge bg-{{ $pkg->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($pkg->status) }}
                                </span>
                            </td>
                            <td><span class="badge bg-info-subtle text-info">{{ $pkg->features->count() }} Features Attached</span></td>
                            <td>{{ $pkg->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No plans created.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($packages->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $packages->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add (Page 31) -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.finance.packages.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Plan Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Advocate Pro Practice Suite">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Price (&#8377;) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="current_price" class="form-control" required value="999.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Duration (Days) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_days" class="form-control" required value="365">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Plan Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Bundle Platform Features</label>
                    <div class="row g-2">
                        @foreach($features as $feat)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="selected_features[]" value="{{ $feat->id }}" id="f_{{ $feat->id }}">
                                    <label class="form-check-label small" for="f_{{ $feat->id }}">
                                        <strong>{{ $feat->name }}</strong> ({{ $feat->slug }})
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<script>
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
