@extends('layouts.user-portal', ['title' => 'Examination Result'])

@section('content')
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4 text-center">
        @if($attempt->is_passed)
            <div class="display-4 text-success mb-2"><i class="bi bi-award-fill"></i></div>
            <h3 class="fw-bold text-success mb-1">Congratulations! You Passed</h3>
            <p class="text-muted">You have successfully met the qualifying benchmark for {{ $attempt->examination->title }}.</p>
        @else
            <div class="display-4 text-danger mb-2"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h3 class="fw-bold text-danger mb-1">Did Not Qualify</h3>
            <p class="text-muted">You did not achieve the required {{ $attempt->examination->passing_percentage }}% passing benchmark.</p>
        @endif

        <div class="row g-3 justify-content-center mt-3">
            <div class="col-6 col-md-2">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Score</small>
                    <span class="fs-4 fw-bold text-dark">{{ $attempt->score }}</span>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Percentage</small>
                    <span class="fs-4 fw-bold text-primary">{{ $attempt->percentage }}%</span>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Correct</small>
                    <span class="fs-4 fw-bold text-success">{{ $attempt->correct_answers }}</span>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Wrong</small>
                    <span class="fs-4 fw-bold text-danger">{{ $attempt->wrong_answers }}</span>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Time Taken</small>
                    <span class="fs-4 fw-bold text-secondary">{{ round($attempt->time_taken_seconds / 60, 1) }}m</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">Detailed Question & Answer Review</h5>
    </div>
    <div class="card-body p-4">
        @foreach($attempt->answers as $idx => $ans)
            @php $q = $ans->question; @endphp
            <div class="p-3 border rounded-3 mb-3 {{ $ans->is_correct ? 'border-success bg-success-subtle bg-opacity-10' : 'border-danger bg-danger-subtle bg-opacity-10' }}">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Q{{ $idx + 1 }}. {{ $q->question_text }}</span>
                    @if($ans->is_correct)
                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Correct (+{{ $ans->marks_awarded }})</span>
                    @else
                        <span class="badge bg-danger"><i class="bi bi-x-lg me-1"></i> Incorrect</span>
                    @endif
                </div>

                <div class="row g-2 small mb-2">
                    <div class="col-md-6">Your Choice: <strong>Option {{ $ans->selected_option }}</strong></div>
                    <div class="col-md-6 text-success">Correct Answer: <strong>Option {{ $q->correct_option }}</strong></div>
                </div>

                @if($q->explanation)
                    <div class="small text-muted bg-white p-2 rounded border">
                        <i class="bi bi-info-circle text-primary me-1"></i> <strong>Explanation:</strong> {{ $q->explanation }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
