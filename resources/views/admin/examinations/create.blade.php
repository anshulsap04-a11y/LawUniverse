@extends('layouts.admin', ['title' => 'Create Examination - Law Universe'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.examinations.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i> Back to Examinations
    </a>
    <h4 class="fw-bold mb-1"><i class="bi bi-mortarboard text-warning me-2"></i> Configure New Examination</h4>
    <p class="text-muted small mb-0">Set duration, passing criteria, and select questions from the centralized Question Bank.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.examinations.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Examination Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. All India Bar Examination (AIBE) Full Mock Test 1">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Exam Instructions / Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Candidates must choose the single best answer for each question..."></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Duration (Minutes) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_minutes" class="form-control" required value="60">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Total Marks <span class="text-danger">*</span></label>
                    <input type="number" name="total_marks" class="form-control" required value="100">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Passing Benchmark (%) <span class="text-danger">*</span></label>
                    <input type="number" name="passing_percentage" class="form-control" required value="40">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Label</label>
                    <select name="label_id" class="form-select">
                        <option value="">No Label</option>
                        @foreach($labels as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Price (&#8377;) if Paid</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="0">
                </div>

                <div class="col-md-4 d-flex align-items-center pt-4">
                    <div class="form-check me-4">
                        <input class="form-check-input" type="checkbox" name="is_paid" id="is_paid" value="1">
                        <label class="form-check-label fw-semibold" for="is_paid">Paid Exam</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" checked>
                        <label class="form-check-label fw-semibold" for="is_published">Publish Immediately</label>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <h5 class="fw-bold border-bottom pb-2">Select Questions from Question Bank</h5>
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAllQuestions" onclick="toggleAll(this)"></th>
                                    <th>Question</th>
                                    <th>Category</th>
                                    <th>Marks</th>
                                    <th>Difficulty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $q)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_questions[]" value="{{ $q->id }}" class="question-cb" checked>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ Str::limit($q->question_text, 120) }}</div>
                                            <small class="text-success">Correct: Option {{ $q->correct_option }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $q->category->name ?? 'General' }}</span></td>
                                        <td>{{ $q->marks }}</td>
                                        <td><span class="badge bg-{{ $q->difficulty === 'hard' ? 'danger' : ($q->difficulty === 'medium' ? 'warning' : 'success') }}">{{ ucfirst($q->difficulty) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">No questions found in bank. Add questions first!</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.examinations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Save & Build Examination
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAll(master) {
    document.querySelectorAll('.question-cb').forEach(cb => cb.checked = master.checked);
}
</script>
@endsection

