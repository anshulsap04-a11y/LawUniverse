@extends('layouts.admin', ['title' => 'Categories & Labels - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3 text-warning me-2"></i> Examination Taxonomy</h4>
    <p class="text-muted small mb-0">Organize examinations and mock tests by judicial services, bar councils, and difficulty labels.</p>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Examination Categories</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCatModal">Add Category</button>
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

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Examination Labels & Badges</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLabelModal">Add Label</button>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($labels as $l)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="badge fs-6" style="background-color: {{ $l->color ?? '#3b82f6' }}">{{ $l->name }}</span>
                            <span class="badge bg-light text-muted border">{{ $l->slug }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-3 text-muted">No labels created.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Category -->
<div class="modal fade" id="addCatModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.examinations.categories.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Category</h5>
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

<!-- Modal Label -->
<div class="modal fade" id="addLabelModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.examinations.labels.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Label</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Label Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. High Priority">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Color Hex Code</label>
                    <input type="color" name="color" class="form-control form-control-color w-100" value="#10b981">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Label</button>
            </div>
        </form>
    </div>
</div>
@endsection
