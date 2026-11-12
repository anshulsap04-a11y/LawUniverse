@extends('layouts.admin', ['title' => 'Manage Features - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-sliders text-danger me-2"></i> Manage Feature</h4>
        <p class="text-muted small mb-0">Define and organize the features available within different price plans (Page 33 & 34).</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Feature
    </button>
</div>

<!-- Filters Panel matching PDF Page 33 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.finance.features') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by feature name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="feature_type" class="form-select">
                    <option value="">All Feature Types</option>
                    <option value="boolean" {{ request('feature_type') === 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                    <option value="usage_limit" {{ request('feature_type') === 'usage_limit' ? 'selected' : '' }}>Usage Limit</option>
                    <option value="download_limit" {{ request('feature_type') === 'download_limit' ? 'selected' : '' }}>Download Limit</option>
                    <option value="time_access" {{ request('feature_type') === 'time_access' ? 'selected' : '' }}>Time Access</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.finance.features') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.finance.features.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiRemoveBtn" onclick="return confirm('Delete selected features?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi Remove
            </button>
            <span class="text-muted small">Showing {{ $features->total() }} Features</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Feature Name</th>
                        <th>Slug Code</th>
                        <th>Feature Type</th>
                        <th>Description</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($features as $f)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $f->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td class="fw-bold text-dark">{{ $f->name }}</td>
                            <td><code>{{ $f->slug }}</code></td>
                            <td><span class="badge bg-light text-primary border">{{ ucfirst(str_replace('_', ' ', $f->feature_type)) }}</span></td>
                            <td class="text-muted small">{{ $f->description ?? 'N/A' }}</td>
                            <td>{{ $f->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No features defined.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($features->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $features->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add (Page 33) -->
<div class="modal fade" id="addFeatureModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.finance.features.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Feature Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Offline PDF Download">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Feature Type</label>
                    <select name="feature_type" class="form-select">
                        <option value="boolean">Boolean (Enabled/Disabled)</option>
                        <option value="usage_limit">Usage Limit (e.g. 50 AI queries)</option>
                        <option value="download_limit">Download Limit</option>
                        <option value="time_access">Time-based Access</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Feature</button>
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
