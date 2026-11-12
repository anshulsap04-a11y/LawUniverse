@extends('layouts.admin', ['title' => 'Question Bank - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-warning me-2"></i> Centralized Question Bank</h4>
        <p class="text-muted small mb-0">Manage objective MCQs, legal reasoning items, explanations, and mark distributions.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
        <i class="bi bi-plus-lg me-1"></i> Add Question to Bank
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width: 45%;">Question & Options</th>
                    <th>Category</th>
                    <th>Correct Key</th>
                    <th>Marks</th>
                    <th>Difficulty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark mb-1">{{ $q->question_text }}</div>
                            <div class="small text-muted">
                                <span class="me-2"><strong>A:</strong> {{ $q->option_a }}</span>
                                <span class="me-2"><strong>B:</strong> {{ $q->option_b }}</span>
                                <span class="me-2"><strong>C:</strong> {{ $q->option_c }}</span>
                                <span><strong>D:</strong> {{ $q->option_d }}</span>
                            </div>
                            @if($q->explanation)
                                <div class="small text-info mt-1"><i class="bi bi-info-circle me-1"></i> {{ $q->explanation }}</div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $q->category->name ?? 'General' }}</span></td>
                        <td><span class="badge bg-success-subtle text-success fs-6">Option {{ $q->correct_option }}</span></td>
                        <td>{{ $q->marks }} (Neg: {{ $q->negative_marks }})</td>
                        <td>
                            <span class="badge bg-{{ $q->difficulty === 'hard' ? 'danger' : ($q->difficulty === 'medium' ? 'warning' : 'success') }}">
                                {{ ucfirst($q->difficulty) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No questions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($questions->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $questions->links() }}</div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.examinations.questions.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Question to Bank</h5>
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
                    <textarea name="question_text" rows="3" class="form-control" required placeholder="Under which section of the Bharatiya Nyaya Sanhita, 2023 is..."></textarea>
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
                    <select name="correct_option" class="form-select fw-bold border-success" required>
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
@endsection
