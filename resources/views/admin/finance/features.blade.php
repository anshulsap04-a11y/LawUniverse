@extends('layouts.admin', ['title' => 'Package Features - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-sliders text-danger me-2"></i> Package Features & Capabilities</h4>
        <p class="text-muted small mb-0">Define granular capabilities attached to subscription tiers.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
        <i class="bi bi-plus-lg me-1"></i> Add Feature
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Feature Name</th>
                    <th>System Code</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($features as $f)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">{{ $f->name }}</td>
                        <td><code>{{ $f->code }}</code></td>
                        <td class="text-muted">{{ $f->description ?? 'Platform capability' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-4 text-muted">No features defined.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addFeatureModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.finance.features.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Platform Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Feature Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Offline PDF Download">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unique Code</label>
                    <input type="text" name="code" class="form-control" required placeholder="offline_pdf_download">
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
@endsection
