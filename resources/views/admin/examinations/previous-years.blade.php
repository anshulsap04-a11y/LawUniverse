@extends('layouts.admin', ['title' => 'Previous Year Papers - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history text-warning me-2"></i> Previous Year Paper</h4>
        <p class="text-muted small mb-0">Repository of past exam papers, judiciary prelims, and study archives.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaperModal">
        <i class="bi bi-plus-lg me-1"></i> Upload Question Paper
    </button>
</div>

<!-- Filters Panel matching PDF Page 22 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.examinations.previous_years') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name, exam..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.examinations.previous_years') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.examinations.previous_years.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected papers?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $papers->total() }} Past Question Papers</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Exam Name & Year</th>
                        <th>Paper Title</th>
                        <th>Category</th>
                        <th>File Attachment</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($papers as $p)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $p->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td class="fw-bold text-dark">{{ $p->exam_name }} ({{ $p->year }})</td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $p->title }}</div>
                                <small class="text-muted">{{ $p->subject ?? 'General Law' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $p->category->name ?? 'Judicial Exams' }}</span></td>
                            <td>
                                @if($p->file_path)
                                    <a href="{{ asset('storage/' . $p->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> View PDF
                                    </a>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Digital Question Set</span>
                                @endif
                            </td>
                            <td>{{ $p->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No past question papers catalogued.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($papers->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $papers->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add -->
<div class="modal fade" id="addPaperModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.examinations.previous_years.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Upload Previous Year Question Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Paper Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Delhi Judicial Service Preliminary Paper 2024">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Examination Governing Body / Name <span class="text-danger">*</span></label>
                    <input type="text" name="exam_name" class="form-control" required placeholder="e.g. Delhi Judicial Service Examination (DJS)">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control" required value="{{ date('Y') - 1 }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Subject / Scope</label>
                    <input type="text" name="subject" class="form-control" placeholder="Constitutional Law, CrPC, CPC, Evidence">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Attach PDF Paper</label>
                    <input type="file" name="paper_file" class="form-control" accept=".pdf">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Paper</button>
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
    document.getElementById('multiTrashBtn').disabled = (checked === 0);
}
</script>
@endsection
