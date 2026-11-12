@extends('layouts.admin', ['title' => 'Categories & Labels - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3 text-warning me-2"></i> Manage Categories & Labels</h4>
    <p class="text-muted small mb-0">Organize examinations and questions by judicial hierarchy, bar councils, and multi-tier labels.</p>
</div>

<div class="row g-4">
    <!-- Manage Categories (Page 22 & 23) -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Manage Categories</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCatModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Category
                </button>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($categories as $c)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <span class="fw-semibold text-dark">{{ $c->name }}</span>
                                <small class="text-muted d-block">{{ $c->description ?? 'No description' }}</small>
                            </div>
                            <span class="badge bg-light text-muted border">{{ $c->slug }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-3 text-muted">No categories created.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Manage Labels (Page 24 & 25) -->
    <div class="col-md-6">
        <form action="{{ route('admin.examinations.labels.bulk_trash') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold mb-0">Manage Label</h6>
                        <button type="submit" class="btn btn-danger btn-sm" id="multiRemoveLabels" onclick="return confirm('Remove selected labels?')" disabled>
                            Multi-Remove
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLabelModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Label
                    </button>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($labels as $l)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="ids[]" value="{{ $l->id }}" class="lbl-cb me-3" onchange="checkLblSelection()">
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $l->label_type }}</span>
                                        <strong class="text-dark">{{ $l->name }}</strong>
                                        <small class="text-muted d-block">{{ $l->description }}</small>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-3 text-muted">No labels created.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Category (Page 23) -->
<div class="modal fade" id="addCatModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.examinations.categories.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Category or Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Higher Judicial Services">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Label (Page 25) -->
<div class="modal fade" id="addLabelModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.examinations.labels.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Label</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Label Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Constitutional Law">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Label Type</label>
                    <select name="label_type" class="form-select">
                        <option value="First Label">First Label</option>
                        <option value="Second Label">Second Label</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Label</button>
            </div>
        </form>
    </div>
</div>

<script>
function checkLblSelection() {
    const checked = document.querySelectorAll('.lbl-cb:checked').length;
    document.getElementById('multiRemoveLabels').disabled = (checked === 0);
}
</script>
@endsection
