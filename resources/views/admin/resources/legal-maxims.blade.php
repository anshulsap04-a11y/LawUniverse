@extends('layouts.admin', ['title' => 'Legal Maxims - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-feather text-info me-2"></i> Legal Maxims</h4>
        <p class="text-muted small mb-0">Classical Latin phrases, doctrines, and principles of natural justice.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaximModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Legal Maxim
    </button>
</div>

<!-- Filters Panel matching PDF Page 11 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.resources.legal_maxims') }}" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by Latin maxim or English meaning..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.resources.legal_maxims') }}" class="btn btn-outline-secondary" title="Clear"><i class="bi bi-arrow-counterclockwise"></i> Clear</a>
            </div>

            <!-- Checkbox matching PDF Page 11 Item 7 -->
            <div class="col-12 pt-2 border-top mt-2 d-flex gap-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="my_data" id="my_data" value="1" {{ request('my_data') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="my_data">Data Uploaded by Logged-in ID</label>
                </div>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.resources.legal_maxims.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected Legal Maxims?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $maxims->total() }} Legal Maxims</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Latin Term</th>
                        <th>English Meaning</th>
                        <th>Category</th>
                        <th>Explanation</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maxims as $m)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $m->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td class="fw-bold text-primary fst-italic">{{ $m->latin_maxim }}</td>
                            <td class="fw-semibold text-dark">{{ $m->english_meaning }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $m->category->name ?? 'General Doctrine' }}</span></td>
                            <td><small class="text-muted">{{ Str::limit($m->legal_explanation, 75) }}</small></td>
                            <td class="text-end pe-3">
                                <!-- Read Button -->
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#readModal_{{ $m->id }}">
                                    <i class="bi bi-book-open"></i> Read
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No Legal Maxims found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($maxims->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $maxims->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add -->
<div class="modal fade" id="addMaximModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.resources.legal_maxims.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Legal Maxim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Latin Term <span class="text-danger">*</span></label>
                    <input type="text" name="latin_maxim" class="form-control" required placeholder="e.g. Audi alteram partem">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">English Meaning <span class="text-danger">*</span></label>
                    <input type="text" name="english_meaning" class="form-control" required placeholder="e.g. Listen to the other side">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Main Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Related Leading Precedents</label>
                    <input type="text" name="related_cases" class="form-control" placeholder="e.g. Maneka Gandhi v. UOI (1978)">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description / Jurisprudential Origin</label>
                    <input type="text" name="origin" class="form-control" placeholder="Roman Jurisprudence / Common Law">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Doctrinal Explanation <span class="text-danger">*</span></label>
                    <textarea name="legal_explanation" rows="4" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Maxim</button>
            </div>
        </form>
    </div>
</div>

@foreach($maxims as $m)
    <div class="modal fade" id="readModal_{{ $m->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary fst-italic">{{ $m->latin_maxim }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold text-dark">{{ $m->english_meaning }}</h6>
                    <p class="text-muted small mb-2">{{ $m->description }}</p>
                    <hr>
                    <p style="white-space: pre-wrap;">{{ $m->legal_explanation }}</p>
                    @if($m->reference)
                        <div class="alert alert-light border small"><i class="bi bi-journal-bookmark me-1 text-primary"></i> <strong>Cases:</strong> {{ $m->reference }}</div>
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
