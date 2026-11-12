@extends('layouts.user-portal', ['title' => 'Student Control Panel - Law Universe'])

@section('content')
<div class="container-fluid py-4 px-md-5">

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%); border-top: 4px solid var(--lu-gold) !important;">
        <div class="card-body p-4 p-md-5 text-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Student & Aspirant Portal</span>
                    <h2 class="fw-bold mb-1" style="font-family: 'Cinzel', serif;">Welcome to the LawUniverse Dashboard</h2>
                    <p class="text-white-50 mb-0">Legal Examination Academy: Mock Tests, Question Papers & Bare Acts.</p>
                </div>
                <div>
                    <a href="#controlPanelSection" class="btn-go-control-panel">
                        <i class="bi bi-speedometer2 me-2"></i> Go to Control Panel
                    </a>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-md-7">
                    <h4 class="fw-bold text-warning mb-3">Judicial Services & Bar Council Exam Prep</h4>
                    <p class="text-white-50">
                        Access structured law education, mock tests, previous years question papers, model answers, and bare act commentaries.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('chatbot.ask') }}" class="btn btn-outline-warning btn-sm px-3">
                            <i class="bi bi-robot me-1"></i> AI Study Assistant
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-bell me-1"></i> Exam Alerts
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center mt-4 mt-md-0">
                    <img src="{{ asset('images/law-universe-logo.svg') }}" width="150" height="150" alt="Emblem" class="img-fluid drop-shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Student Control Panel Section -->
    <div id="controlPanelSection" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h4 class="fw-bold text-dark mb-4 border-bottom pb-2">
            <i class="bi bi-mortarboard-fill text-success me-2"></i> Student Learning Modules
        </h4>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border p-3 bg-light-subtle">
                    <div class="bg-success text-white rounded p-3 mb-3 d-inline-block">
                        <i class="bi bi-card-checklist fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Examinations & Mock Tests</h5>
                    <p class="small text-muted">Practice online timed tests for judicial services and LL.B. semesters.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border p-3 bg-light-subtle">
                    <div class="bg-primary text-white rounded p-3 mb-3 d-inline-block">
                        <i class="bi bi-robot fs-3"></i>
                    </div>
                    <h5 class="fw-bold">AI Legal Tutor</h5>
                    <p class="small text-muted">Ask questions regarding legal concepts, doctrines, and case law summaries.</p>
                    <a href="{{ route('chatbot.ask') }}" class="btn btn-sm btn-primary mt-auto">Open Chatbot</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border p-3 bg-light-subtle">
                    <div class="bg-warning text-dark rounded p-3 mb-3 d-inline-block">
                        <i class="bi bi-book fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Bare Acts & Legal Maxims</h5>
                    <p class="small text-muted">Browse full original legislative texts and Latin legal maxims.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

