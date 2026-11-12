@extends('layouts.admin', ['title' => 'Manage Examinations - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-mortarboard-fill text-warning me-2"></i> Examination</h4>
        <p class="text-muted small mb-0">Design and set up new examinations, question links, and passing benchmarks.</p>
    </div>
    <div>
        <a href="{{ route('admin.examinations.create') }}" class="btn btn-warning fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Create New Examination
        </a>
    </div>
</div>

<!-- Filters Panel matching PDF Page 16 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.examinations.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search Examination by name..." value="{{ request('search') }}">
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
                <select name="label_id" class="form-select">
                    <option value="">Labels</option>
                    @foreach($labels as $lbl)
                        <option value="{{ $lbl->id }}" {{ request('label_id') == $lbl->id ? 'selected' : '' }}>{{ $lbl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="Filter by Date">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.examinations.index') }}" class="btn btn-outline-secondary" title="Clear"><i class="bi bi-arrow-counterclockwise"></i> Clear</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.examinations.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected Examinations?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $examinations->total() }} Examinations</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Exam Code & Title</th>
                        <th>Category</th>
                        <th>Duration & Marks</th>
                        <th>Cutoff Benchmark</th>
                        <th>Questions</th>
                        <th class="text-end pe-3">Actions (Page 17)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examinations as $exam)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $exam->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $exam->title }}</div>
                                <small class="text-muted">{{ $exam->exam_code }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $exam->category->name ?? 'General' }}</span></td>
                            <td>
                                <div><i class="bi bi-clock me-1 text-muted"></i> {{ $exam->duration_minutes }} Mins</div>
                                <small class="text-muted">{{ $exam->total_marks }} Marks</small>
                            </td>
                            <td><span class="badge bg-info-subtle text-info">{{ $exam->cutoff_percentage }}%</span></td>
                            <td><span class="badge bg-light text-primary border">{{ $exam->questions_count }} Linked</span></td>
                            <td class="text-end pe-3">
                                <!-- 1. Attempt Button -->
                                <button type="button" class="btn btn-sm btn-success" onclick="document.getElementById('attempt_{{ $exam->id }}').submit()" title="Attempt Exam">
                                    <i class="bi bi-play-circle"></i> Attempt
                                </button>
                                <!-- 2. Details Button -->
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#examModal_{{ $exam->id }}" title="Details">
                                    Details
                                </button>
                                <!-- 3. Trash -->
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete exam?')) document.getElementById('trash_{{ $exam->id }}').submit()" title="Trash">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No examinations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($examinations->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $examinations->links() }}</div>
        @endif
    </div>
</form>

@foreach($examinations as $exam)
    <form action="{{ route('exams.start', $exam->id) }}" method="POST" id="attempt_{{ $exam->id }}" class="d-none">@csrf</form>
    <form action="{{ route('admin.examinations.destroy', $exam->id) }}" method="POST" id="trash_{{ $exam->id }}" class="d-none">@csrf @method('DELETE')</form>

    <div class="modal fade" id="examModal_{{ $exam->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ $exam->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{ $exam->instructions }}</p>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item d-flex justify-content-between"><span>Duration:</span> <strong>{{ $exam->duration_minutes }} Minutes</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Total Marks:</span> <strong>{{ $exam->total_marks }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Cutoff:</span> <strong>{{ $exam->cutoff_percentage }}%</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Questions Linked:</span> <strong>{{ $exam->questions_count }}</strong></li>
                    </ul>
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
