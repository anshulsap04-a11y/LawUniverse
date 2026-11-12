@extends('layouts.admin', ['title' => 'Question Paper - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-warning me-2"></i> Question Paper</h4>
        <p class="text-muted small mb-0">Centralized question bank, composition, formatting options, and multi-question management.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Question
    </button>
</div>

<!-- Filters Panel matching PDF Page 19 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.examinations.questions') }}" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name or question code..." value="{{ request('search') }}">
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
                <select name="label_id" class="form-select">
                    <option value="">Labels</option>
                    @foreach($labels as $lbl)
                        <option value="{{ $lbl->id }}" {{ request('label_id') == $lbl->id ? 'selected' : '' }}>{{ $lbl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.examinations.questions') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>

            <!-- Checkbox matching PDF Page 19 Item 3 -->
            <div class="col-12 pt-2 border-top mt-2 d-flex gap-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="my_data" id="my_data" value="1" {{ request('my_data') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="my_data">My Data (Questions Uploaded by Me)</label>
                </div>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.examinations.questions.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected questions?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $questions->total() }} Questions in Bank</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Code</th>
                        <th style="width: 45%;">Question Title & Options</th>
                        <th>Category</th>
                        <th>Correct Key</th>
                        <th>Marks</th>
                        <th>Difficulty</th>
                        <th class="text-end pe-3">Actions (Page 20)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $q->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td><code>{{ $q->question_code }}</code></td>
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $q->question_title }}</div>
                                <div class="small text-muted">
                                    @if(is_array($q->options))
                                        @foreach($q->options as $opt)
                                            <span class="me-2"><strong>{{ $opt['id'] }}:</strong> {{ $opt['text'] }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $q->category->name ?? 'General' }}</span></td>
                            <td><span class="badge bg-success-subtle text-success fs-6">Option {{ $q->correct_answer }}</span></td>
                            <td>{{ $q->marks }}</td>
                            <td>
                                <span class="badge bg-{{ $q->difficulty === 'hard' ? 'danger' : ($q->difficulty === 'medium' ? 'warning' : 'success') }}">
                                    {{ ucfirst($q->difficulty) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#qModal_{{ $q->id }}">
                                    Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No questions found in bank.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $questions->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.examinations.questions.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Question (Page 20)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Difficulty</label>
                    <select name="difficulty" class="form-select">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Marks</label>
                    <input type="number" step="0.5" name="marks" class="form-control" value="1.0" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Question Stem / Prompt <span class="text-danger">*</span></label>
                    <textarea name="question_title" rows="3" class="form-control" required placeholder="Under which section of the Bharatiya Nyaya Sanhita, 2023 is..."></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Option A <span class="text-danger">*</span></label>
                    <input type="text" name="option_a" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Option B <span class="text-danger">*</span></label>
                    <input type="text" name="option_b" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Option C <span class="text-danger">*</span></label>
                    <input type="text" name="option_c" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Option D <span class="text-danger">*</span></label>
                    <input type="text" name="option_d" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-success">Correct Key / Option <span class="text-danger">*</span></label>
                    <select name="correct_answer" class="form-select fw-bold border-success" required>
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-danger">Negative Marks (if wrong)</label>
                    <input type="number" step="0.25" name="negative_marks" class="form-control" value="0.0">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Explanation / Statutory Reference</label>
                    <textarea name="explanation" rows="2" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Question</button>
            </div>
        </form>
    </div>
</div>

@foreach($questions as $q)
    <div class="modal fade" id="qModal_{{ $q->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Question Details ({{ $q->question_code }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold mb-3">{{ $q->question_title }}</h6>
                    <div class="p-3 bg-light rounded mb-3">
                        @if(is_array($q->options))
                            @foreach($q->options as $opt)
                                <div class="mb-1 {{ $opt['id'] === $q->correct_answer ? 'text-success fw-bold' : '' }}">
                                    {{ $opt['id'] }}. {{ $opt['text'] }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if($q->explanation)
                        <div class="small text-muted"><strong>Explanation:</strong> {{ $q->explanation }}</div>
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
