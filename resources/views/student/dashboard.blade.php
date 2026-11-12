@extends('layouts.user-portal', ['title' => 'Student Control Panel - Law Universe'])

@section('content')
<div class="container-fluid py-2">

    <!-- Student Banner Matching PDF Page 1 -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%); border-top: 4px solid var(--lu-gold) !important;">
        <div class="card-body p-4 p-md-5 text-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Student & Aspirant Portal</span>
                    <h2 class="fw-bold mb-1" style="font-family: 'Cinzel', serif;">Welcome to LawUniverse Dashboard</h2>
                    <p class="text-white-50 mb-0">Legal Examination Academy: Timed Mock Tests, Question Papers & Bare Acts.</p>
                </div>
                <div>
                    <a href="#controlPanelSection" class="btn-go-control-panel">
                        <i class="bi bi-speedometer2 me-2"></i> Go to Control Panel
                    </a>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-md-7">
                    <h4 class="fw-bold text-warning mb-3">Judicial Services & Bar Council Exam Academy</h4>
                    <p class="text-white-50">
                        Access structured law education, interactive timed mock tests, previous year papers, model answers, and AI-powered statutory explanations.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="#availableExams" class="btn btn-warning btn-sm px-3 fw-bold text-dark">
                            <i class="bi bi-card-checklist me-1"></i> Practice Mock Exams
                        </a>
                        <a href="{{ route('chatbot.ask') }}" class="btn btn-outline-warning btn-sm px-3">
                            <i class="bi bi-robot me-1"></i> AI Study Assistant
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-bell me-1"></i> Exam Alerts ({{ $notifications->count() }})
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center mt-4 mt-md-0">
                    <img src="{{ asset('images/law-universe-logo.svg') }}" width="140" height="140" alt="Emblem" class="img-fluid drop-shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Student Key Performance Metrics (Strictly User-Scoped) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Available Exams</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['available_exams']) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-mortarboard-fill fs-4"></i>
                    </div>
                </div>
                <span class="small text-muted mt-2 d-inline-block">Open for attempts</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">My Attempts</span>
                        <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['my_attempts']) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-pencil-square fs-4"></i>
                    </div>
                </div>
                <span class="small text-muted mt-2 d-inline-block">{{ $stats['completed_exams'] }} completed</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Average Score</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['avg_score'] }}%</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-trophy-fill fs-4"></i>
                    </div>
                </div>
                <span class="small text-success mt-2 d-inline-block fw-bold">{{ $stats['passed_exams'] }} passed</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">AI Study Queries</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['ai_queries']) }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('chatbot.history') }}" class="small text-info text-decoration-none mt-2 d-inline-block">View History &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Available Mock Examinations -->
    <div id="availableExams" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-card-checklist text-success me-2"></i> Available Online Mock Examinations
                </h4>
                <p class="text-muted small mb-0">Select an exam to practice under real timed conditions with instant scorecards.</p>
            </div>
        </div>

        <div class="row g-3">
            @forelse($availableExams as $exam)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 p-3 shadow-none bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-secondary">{{ $exam->exam_code }}</span>
                            <span class="badge bg-primary"><i class="bi bi-clock me-1"></i> {{ $exam->duration_minutes }} Mins</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $exam->title }}</h6>
                        <p class="text-muted small mb-3 text-truncate">{{ $exam->description ?? 'Comprehensive legal assessment.' }}</p>
                        
                        <div class="d-flex justify-content-between small text-muted mb-3 border-top pt-2">
                            <span><i class="bi bi-question-circle me-1"></i> {{ $exam->questions_count }} Questions</span>
                            <span><i class="bi bi-award me-1"></i> Pass: {{ $exam->passing_marks }}/{{ $exam->total_marks }}</span>
                        </div>

                        <form action="{{ route('exams.start', $exam->id) }}" method="POST" class="mt-auto">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                <i class="bi bi-play-circle me-1"></i> Attempt Examination
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">
                    <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>
                    No mock exams are currently scheduled.
                </div>
            @endforelse
        </div>
    </div>

    <!-- My Recent Exam Attempts & Performance (Strictly Scoped) -->
    <div id="myAttempts" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
            <i class="bi bi-clock-history text-primary me-2"></i> My Recent Exam Attempts & Scorecards
        </h4>

        @if($recentAttempts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Exam Code</th>
                            <th>Examination Title</th>
                            <th>Attempt Date</th>
                            <th>Score / Total</th>
                            <th>Percentage</th>
                            <th>Result</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttempts as $att)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $att->examination->exam_code ?? 'EXAM' }}</span></td>
                                <td class="fw-bold text-dark">{{ $att->examination->title ?? 'Legal Exam' }}</td>
                                <td class="small text-muted">{{ $att->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <strong>{{ number_format($att->total_score, 1) }}</strong> / {{ $att->examination->total_marks ?? 100 }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar {{ $att->is_passed ? 'bg-success' : 'bg-danger' }}" style="width: {{ min(100, $att->percentage) }}%"></div>
                                        </div>
                                        <span class="small fw-bold">{{ $att->percentage }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($att->status === 'completed')
                                        @if($att->is_passed)
                                            <span class="badge bg-success-subtle text-success border border-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger">Failed</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('exams.result', $att->id) }}" class="btn btn-outline-primary btn-sm py-1 px-3">
                                        <i class="bi bi-eye me-1"></i> Review Result
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-file-earmark-text fs-2 d-block mb-2"></i>
                You have not attempted any mock tests yet. Take an available mock exam above to record your score!
            </div>
        @endif
    </div>

    <!-- Student Control Panel Section (Matching PDF Page 2 Modules) -->
    <div id="controlPanelSection" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h4 class="fw-bold text-dark mb-4 border-bottom pb-2">
            <i class="bi bi-sliders text-warning me-2"></i> Student Control Panel Modules
        </h4>

        <div class="row g-4">
            <!-- Module 1: Manage AI Chatbot -->
            <div class="col-md-6">
                <div class="card h-100 border p-3 rounded-3 shadow-none bg-light-subtle">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded p-3 me-3">
                            <i class="bi bi-robot fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Module 1: Manage AI Chatbot</h5>
                            <span class="text-muted small">Ask academic questions on case laws, doctrines, and bare acts.</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2">
                            <span><i class="bi bi-chat-dots me-2 text-primary"></i> Ask AI Legal Tutor</span>
                            <a href="{{ route('chatbot.ask') }}" class="btn btn-sm btn-primary px-3">Launch</a>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2">
                            <span><i class="bi bi-clock-history me-2 text-primary"></i> AI Chatbot History</span>
                            <a href="{{ route('chatbot.history') }}" class="btn btn-sm btn-outline-primary px-3">
                                Review ({{ $stats['ai_queries'] }})
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Module 2: Manage Notification -->
            <div class="col-md-6">
                <div class="card h-100 border p-3 rounded-3 shadow-none bg-light-subtle">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning text-dark rounded p-3 me-3">
                            <i class="bi bi-bell-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Module 2: Manage Notification</h5>
                            <span class="text-muted small">Stay updated on exam dates, syllabus changes, and notices.</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2">
                            <span><i class="bi bi-card-list me-2 text-warning"></i> List Notification</span>
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-dark px-3">
                                View Notices ({{ $notifications->count() }})
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Subscription Status -->
    <div id="subscriptionCard" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-credit-card text-success me-2"></i> Academic Subscription Status</h5>
                @if($activeSubscription)
                    <p class="text-muted small mb-0">
                        Plan: <strong>{{ $activeSubscription->package->name ?? 'Student Pro' }}</strong> | Expiry: <strong>{{ $activeSubscription->expiry_date->format('d M Y') }}</strong>
                    </p>
                @else
                    <p class="text-muted small mb-0">You are currently on the Free Tier. Upgrade to access premium examination question banks.</p>
                @endif
            </div>
            <div>
                <a href="{{ route('home') }}#packages" class="btn btn-warning btn-sm fw-bold px-3">
                    <i class="bi bi-arrow-repeat me-1"></i> {{ $activeSubscription ? 'Renew / Change Plan' : 'Upgrade Plan' }}
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
