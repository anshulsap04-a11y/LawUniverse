@extends('layouts.admin', ['title' => 'List Resources - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-folder2-open text-primary me-2"></i> List Resources</h4>
        <p class="text-muted small mb-0">Search, filter, preview, and manage legal acts, commentary documents, and resources.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.resources.create') }}" class="btn btn-warning fw-semibold shadow-sm">
            <i class="bi bi-cloud-arrow-up me-1"></i> Upload Resources
        </a>
    </div>
</div>

<!-- Filters Panel matching PDF Page 7 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.resources.index') }}" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name, author..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="year" class="form-control" placeholder="Filter by Year" value="{{ request('year') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="Filter by Date">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-secondary" title="Refresh / Clear"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>

            <!-- Additional Checkbox Filters matching PDF Page 7 Item 4, 5, 6, 7 -->
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

<form action="{{ route('admin.resources.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected resources?')" disabled>
                    <i class="bi bi-trash me-1"></i> Multi-Trash
                </button>
            </div>
            <span class="text-muted small">Showing {{ $resources->total() }} total resources</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Title & Metadata</th>
                        <th>Category</th>
                        <th>Author & Year</th>
                        <th>Access</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resources as $res)
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" name="ids[]" value="{{ $res->id }}" class="item-checkbox" onchange="checkSelection()">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $res->title }}</div>
                                <small class="text-muted">{{ strtoupper($res->resource_type) }} &bull; {{ $res->language }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $res->category->name ?? 'General' }}</span></td>
                            <td>
                                <div>{{ $res->author ?? 'Editorial' }}</div>
                                <small class="text-muted">{{ $res->year ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($res->is_free || $res->price <= 0)
                                    <span class="badge bg-success-subtle text-success border border-success">FREE</span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning">&#8377; {{ number_format($res->price, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $res->status === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <!-- Details Button -->
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal_{{ $res->id }}" title="Details">
                                    <i class="bi bi-eye"></i> Details
                                </button>
                                <!-- Clone -->
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('clone_{{ $res->id }}').submit()" title="Clone">
                                    <i class="bi bi-copy"></i>
                                </button>
                                <!-- Trash -->
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete resource?')) document.getElementById('trash_{{ $res->id }}').submit()" title="Trash">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No legal resources found matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($resources->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $resources->links() }}
            </div>
        @endif
    </div>
</form>

<!-- Hidden clone/trash forms -->
@foreach($resources as $res)
    <form action="{{ route('admin.resources.clone', $res->id) }}" method="POST" id="clone_{{ $res->id }}" class="d-none">@csrf</form>
    <form action="{{ route('admin.resources.destroy', $res->id) }}" method="POST" id="trash_{{ $res->id }}" class="d-none">@csrf @method('DELETE')</form>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal_{{ $res->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Resource Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="fw-bold text-primary">{{ $res->title }}</h5>
                    <p class="text-muted">{{ $res->description }}</p>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item d-flex justify-content-between"><span>Type:</span> <strong>{{ ucfirst($res->resource_type) }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Category:</span> <strong>{{ $res->category->name ?? 'General' }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Author:</span> <strong>{{ $res->author ?? 'N/A' }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Year:</span> <strong>{{ $res->year ?? 'N/A' }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Price:</span> <strong>&#8377; {{ number_format($res->price, 2) }}</strong></li>
                    </ul>
                    @if($res->file_path)
                        <a href="{{ asset('storage/' . $res->file_path) }}" target="_blank" class="btn btn-primary w-100"><i class="bi bi-download me-1"></i> Download File</a>
                    @endif
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
