@extends('layouts.user-portal', ['title' => 'Advocate Control Panel - Law Universe'])

@section('content')
<div class="container-fluid py-2">

    <!-- Advocate Banner Matching PDF Page 1 -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #182234 0%, #0d1522 100%); border-top: 4px solid var(--lu-gold) !important;">
        <div class="card-body p-4 p-md-5 text-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Advocate Portal</span>
                    <h2 class="fw-bold mb-1" style="font-family: 'Cinzel', serif;">Welcome to LawUniverse Dashboard</h2>
                    <p class="text-white-50 mb-0">Advocate Workspace: Specialized tools and digital infrastructure for your practice.</p>
                </div>
                <div>
                    <a href="#controlPanelSection" class="btn-go-control-panel">
                        <i class="bi bi-speedometer2 me-2"></i> Go to Control Panel
                    </a>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-md-7">
                    <h4 class="fw-bold text-warning mb-3">Empowering Legal Advocates & Counsel</h4>
                    <p class="text-white-50">
                        Law Universe empowers advocates and litigators by offering a range of specialized services to support case research, draft preparation, statutory amendments, and professional practice growth.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('chatbot.ask') }}" class="btn btn-warning btn-sm px-3 fw-bold text-dark">
                            <i class="bi bi-robot me-1"></i> AI Case Drafting & Research
                        </a>
                        <a href="{{ route('public.landmark-judgments') }}" class="btn btn-outline-warning btn-sm px-3">
                            <i class="bi bi-bank me-1"></i> Landmark Precedents ({{ number_format($stats['landmark_judgments']) }})
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-bell me-1"></i> Cause Lists & Notices ({{ $notifications->count() }})
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center mt-4 mt-md-0">
                    <img src="{{ asset('images/law-universe-logo.svg') }}" width="140" height="140" alt="Emblem" class="img-fluid drop-shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Practice Analytics & Repository Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Precedents</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['landmark_judgments']) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-bank fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('public.landmark-judgments') }}" class="small text-primary text-decoration-none mt-2 d-inline-block">Search Precedents &rarr;</a>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Statutes</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['bare_acts']) }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-book fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('public.bare-acts') }}" class="small text-info text-decoration-none mt-2 d-inline-block">View Bare Acts &rarr;</a>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Maxims</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['legal_maxims']) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-feather fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('public.legal-maxims') }}" class="small text-success text-decoration-none mt-2 d-inline-block">Legal Doctrines &rarr;</a>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Drafting Queries</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['ai_queries']) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('chatbot.history') }}" class="small text-warning text-decoration-none mt-2 d-inline-block">Draft History &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Precedents & Landmark Judgments Section -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-bank text-primary me-2"></i> Landmark Case Law & Judicial Rulings
                </h4>
                <p class="text-muted small mb-0">Search authoritative rulings with citations, coram details, and ratio decidendi.</p>
            </div>
            <a href="{{ route('public.landmark-judgments') }}" class="btn btn-outline-primary btn-sm">View All Precedents</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>Case Title</th>
                        <th>Citation</th>
                        <th>Bench / Coram</th>
                        <th>Court</th>
                        <th>Year</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJudgments as $judg)
                        <tr>
                            <td class="fw-bold text-dark">{{ $judg->case_title }}</td>
                            <td><span class="badge bg-secondary">{{ $judg->citation ?? 'AIR / SCC' }}</span></td>
                            <td class="small text-muted">{{ $judg->bench ?? 'Hon\'ble Judges' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $judg->court_name }}</span></td>
                            <td>{{ $judg->judgment_year }}</td>
                            <td class="text-end">
                                <a href="{{ route('public.landmark-judgments') }}?search={{ urlencode($judg->case_title) }}" class="btn btn-outline-dark btn-sm py-1 px-3">
                                    <i class="bi bi-file-earmark-text me-1"></i> Read Ratio
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No judgments catalogued yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Advocate Control Panel Section (Matching PDF Page 2 Modules) -->
    <div id="controlPanelSection" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-sliders text-warning me-2"></i> Advocate Control Panel Modules
                </h4>
                <p class="text-muted small mb-0">Authorized advocate workflows and administrative utilities.</p>
            </div>
            <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2">
                <i class="bi bi-award-fill text-warning me-1"></i> Bar Verified Advocate
            </span>
        </div>

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
                            <span class="text-muted small">AI-driven legal assistance, statutory citations, and draft generation.</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2">
                            <span><i class="bi bi-chat-dots me-2 text-primary"></i> Ask AI Chatbot</span>
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
                            <span class="text-muted small">Stay updated on cause lists, bar council notices, and legal alerts.</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2">
                            <span><i class="bi bi-card-list me-2 text-warning"></i> List Notification</span>
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-dark px-3">
                                Open Table ({{ $notifications->count() }})
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Advocate Pro Plan & Verification Status -->
    <div id="subscriptionCard" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-1"><i class="bi bi-shield-check text-success me-2"></i> Advocate Practice Status & Subscription</h5>
                <p class="text-muted small mb-0">
                    Bar Credentials: <strong>{{ $user->education ?? 'LL.B. / Advocate' }}</strong> | Status: <span class="badge bg-success">Active & Verified</span>
                    @if($activeSubscription)
                        | Plan: <strong>{{ $activeSubscription->package->name ?? 'Advocate Pro' }}</strong> (Valid until {{ $activeSubscription->expiry_date->format('d M Y') }})
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('home') }}#packages" class="btn btn-warning btn-sm fw-bold px-3">
                    <i class="bi bi-arrow-repeat me-1"></i> Manage Advocate Pro Subscription
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
