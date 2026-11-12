@extends('layouts.admin', ['title' => 'Bare Acts - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-book text-info me-2"></i> Bare Acts</h4>
        <p class="text-muted small mb-0">Statutes, central/state legislations in original form without commentary.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActModal">
        <i class="bi bi-plus-lg me-1"></i> Add Bare Act
    </button>
</div>

<!-- Filters Panel matching PDF Page 9 & 10 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.resources.bare_acts') }}" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search Bare Acts by name, act no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.resources.bare_acts') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>

            <!-- Checkboxes matching PDF Page 9 Item 2, 3, 7 -->
            <div class="col-12 pt-2 border-top mt-2 d-flex flex-wrap gap-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="my_data" id="my_data" value="1" {{ request('my_data') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="my_data">My Data (Uploaded by Me)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_free" id="is_free" value="1" {{ request('is_free') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="is_free">Offered Free of Cost</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_copyright" id="is_copyright" value="1" {{ request('is_copyright') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="is_copyright">Copyright Protected</label>
                </div>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.resources.bare_acts.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected Bare Acts?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $acts->total() }} Bare Acts</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Act No. & Year</th>
                        <th>Act Name</th>
                        <th>Category</th>
                        <th>Jurisdiction</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acts as $act)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $act->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td class="fw-bold text-muted">{{ $act->act_number ?? 'Act' }} of {{ $act->year }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $act->act_name }}</div>
                                <small class="text-muted">{{ Str::limit($act->description, 80) }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $act->category->name ?? 'Central Act' }}</span></td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $act->jurisdiction }}</span></td>
                            <td class="text-end pe-3">
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#actModal_{{ $act->id }}">Details</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No Bare Acts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($acts->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $acts->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add -->
<div class="modal fade" id="addActModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.resources.bare_acts.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Register New Bare Act</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Title of Legislation</label>
                    <input type="text" name="act_name" class="form-control" required placeholder="e.g. Bharatiya Nagarik Suraksha Sanhita, 2023">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Act No.</label>
                    <input type="text" name="act_number" class="form-control" placeholder="Act 46">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Year</label>
                    <input type="number" name="year" class="form-control" required value="{{ date('Y') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-center pt-4">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" name="is_free" id="act_free" value="1" checked>
                        <label class="form-check-label fw-semibold" for="act_free">Free of Cost</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_copyright" id="act_copy" value="1">
                        <label class="form-check-label fw-semibold" for="act_copy">Copyright Protected</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description / Preamble</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Codified Statutory Text (Markdown)</label>
                    <textarea name="content" rows="6" class="form-control font-monospace"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Bare Act</button>
            </div>
        </form>
    </div>
</div>

@foreach($acts as $act)
    <div class="modal fade" id="actModal_{{ $act->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ $act->act_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{ $act->description }}</p>
                    <div class="border p-3 rounded bg-light" style="max-height: 350px; overflow-y: auto;">
                        <pre style="white-space: pre-wrap;">{{ $act->content ?? 'No digital text attached.' }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
function toggleSelectAll(master) {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = master.checked);
    checkSelection();
}
function checkSelection() {
    const checked = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('multiTrashBtn').disabled = (checked === 0);
}
</script>
@endsection
