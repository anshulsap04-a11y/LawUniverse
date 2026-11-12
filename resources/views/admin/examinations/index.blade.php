@extends('layouts.admin', ['title' => 'Manage Examinations - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-mortarboard-fill text-warning me-2"></i> Examination Management</h4>
        <p class="text-muted small mb-0">Build timed mock exams, link question banks, configure pass benchmarks, and monitor attempts.</p>
    </div>
    <div>
        <a href="{{ route('admin.examinations.create') }}" class="btn btn-warning fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Create New Examination
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Exam Title</th>
                    <th>Category</th>
                    <th>Duration & Marks</th>
                    <th>Questions</th>
                    <th>Access Type</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examinations as $exam)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $exam->title }}</div>
                            <small class="text-muted">Pass Mark: {{ $exam->passing_percentage }}%</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $exam->category->name ?? 'General' }}</span></td>
                        <td>
                            <div><i class="bi bi-clock me-1 text-muted"></i> {{ $exam->duration_minutes }} Mins</div>
                            <small class="text-muted">{{ $exam->total_marks }} Marks</small>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info border border-info">{{ $exam->questions_count }} Questions</span>
                        </td>
                        <td>
                            @if($exam->is_paid && $exam->price > 0)
                                <span class="badge bg-warning-subtle text-dark border border-warning">&#8377; {{ number_format($exam->price, 2) }}</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success">FREE</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $exam->is_published ? 'success' : 'secondary' }}">
                                {{ $exam->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <form action="{{ route('exams.start', $exam->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Preview / Test Take Exam">
                                    <i class="bi bi-play-circle"></i> Test Take
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No examinations configured yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($examinations->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $examinations->links() }}</div>
    @endif
</div>
@endsection
