@extends('layouts.user-portal', ['title' => 'Examination: ' . $exam->title])

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">{{ $exam->title }}</h5>
                    <small class="text-muted">Total Questions: {{ $questions->count() }} &bull; Passing Benchmark: {{ $exam->passing_percentage }}%</small>
                </div>
                <div class="badge bg-danger fs-6 px-3 py-2" id="examTimer">
                    <i class="bi bi-clock me-1"></i> <span id="timeDisplay">--:--</span>
                </div>
            </div>
            <div class="card-body p-4">
                <form id="examForm" action="{{ route('exams.submit', $attempt->id) }}" method="POST">
                    @csrf

                    @foreach($questions as $idx => $q)
                        <div class="question-block mb-4 p-3 border rounded-3 {{ $loop->first ? '' : 'd-none' }}" id="qBlock_{{ $idx }}" data-qid="{{ $q->id }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary">Question {{ $idx + 1 }} of {{ $questions->count() }}</span>
                                <span class="badge bg-light text-dark border">{{ $q->marks }} Marks</span>
                            </div>

                            <h5 class="fw-bold text-dark mb-3">{{ $q->question_text }}</h5>

                            <div class="options-group d-flex flex-column gap-2 mb-3">
                                @php $saved = $savedAnswers[$q->id] ?? null; @endphp
                                @foreach(['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d] as $optKey => $optVal)
                                    <label class="p-3 border rounded-2 d-flex align-items-center cursor-pointer option-label hover-bg-light">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $optKey }}" class="form-check-input me-3" {{ $saved === $optKey ? 'checked' : '' }} onchange="autoSaveAnswer({{ $q->id }}, '{{ $optKey }}', {{ $idx }})">
                                        <div>
                                            <strong class="me-2 text-primary">{{ $optKey }}.</strong> {{ $optVal }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevQuestion({{ $idx }})" {{ $loop->first ? 'disabled' : '' }}>
                                    <i class="bi bi-arrow-left me-1"></i> Previous
                                </button>
                                @if($loop->last)
                                    <button type="button" class="btn btn-success fw-bold px-4" onclick="confirmSubmit()">
                                        <i class="bi bi-check2-circle me-1"></i> Submit Examination
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary" onclick="nextQuestion({{ $idx }})">
                                        Next <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 80px;">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i> Question Navigator</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-4" id="navGrid">
                    @foreach($questions as $idx => $q)
                        @php $isAns = isset($savedAnswers[$q->id]); @endphp
                        <button type="button" class="btn btn-sm {{ $isAns ? 'btn-success' : ($idx === 0 ? 'btn-primary' : 'btn-outline-secondary') }} fw-semibold rounded-2 q-nav-btn" id="navBtn_{{ $idx }}" style="width: 42px; height: 42px;" onclick="goToQuestion({{ $idx }})">
                            {{ $idx + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="d-flex flex-column gap-2 small border-top pt-3 text-muted">
                    <div class="d-flex align-items-center"><span class="badge bg-success me-2" style="width:16px;height:16px;"> </span> Answered</div>
                    <div class="d-flex align-items-center"><span class="badge bg-outline-secondary border me-2" style="width:16px;height:16px;"> </span> Not Visited / Unanswered</div>
                </div>

                <button type="button" class="btn btn-danger w-100 fw-bold mt-4" onclick="confirmSubmit()">
                    <i class="bi bi-send-check me-1"></i> Final Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentIdx = 0;
const totalCount = {{ $questions->count() }};
const attemptId = {{ $attempt->id }};
const saveUrl = "{{ route('exams.save_answer', $attempt->id) }}";
const csrfToken = "{{ csrf_token() }}";

function goToQuestion(idx) {
    document.querySelectorAll('.question-block').forEach(b => b.classList.add('d-none'));
    const target = document.getElementById('qBlock_' + idx);
    if (target) target.classList.remove('d-none');
    currentIdx = idx;
}

function nextQuestion(idx) {
    if (idx + 1 < totalCount) {
        goToQuestion(idx + 1);
    }
}

function prevQuestion(idx) {
    if (idx - 1 >= 0) {
        goToQuestion(idx - 1);
    }
}

function autoSaveAnswer(questionId, selectedOption, idx) {
    fetch(saveUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            question_id: questionId,
            selected_option: selectedOption
        })
    }).then(res => res.json()).then(data => {
        const btn = document.getElementById('navBtn_' + idx);
        if (btn) {
            btn.className = 'btn btn-sm btn-success fw-semibold rounded-2 q-nav-btn';
        }
    }).catch(err => console.error(err));
}

function confirmSubmit() {
    if (confirm('Are you sure you want to finalize and submit your examination?')) {
        document.getElementById('examForm').submit();
    }
}

// Countdown timer
let durationMinutes = {{ $exam->duration_minutes }};
let totalSeconds = durationMinutes * 60;
const timerEl = document.getElementById('timeDisplay');

const countdown = setInterval(() => {
    if (totalSeconds <= 0) {
        clearInterval(countdown);
        alert('Time is up! Submitting your examination automatically.');
        document.getElementById('examForm').submit();
        return;
    }
    totalSeconds--;
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    timerEl.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
}, 1000);
</script>
@endsection

