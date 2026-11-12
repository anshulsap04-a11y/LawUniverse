@extends('layouts.user-portal', ['title' => 'Public Citizen Portal - Law Universe'])

@section('content')
<div class="container-fluid py-2">

    <!-- Welcome Hero Banner (Matching PDF Page 1) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #182234 0%, #0f172a 100%); border-top: 4px solid var(--lu-gold) !important;">
        <div class="card-body p-4 p-md-5 text-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Public Legal Portal</span>
                    <h2 class="fw-bold mb-1" style="font-family: 'Cinzel', serif;">Welcome to LawUniverse Dashboard</h2>
                    <p class="text-white-50 mb-0">Democratizing Legal Knowledge: Citizen Rights, Free Bare Acts & Public Legal Resources.</p>
                </div>
                <div>
                    <a href="#controlPanelSection" class="btn-go-control-panel">
                        <i class="bi bi-speedometer2 me-2"></i> Go to Control Panel
                    </a>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-md-7">
                    <h4 class="fw-bold text-warning mb-3">Accessible Legal Information for Every Citizen</h4>
                    <p class="text-white-50">
                        Law Universe empowers the public with clear explanations of fundamental rights, consumer protection guidelines, statutory bare acts, legal maxims, and direct access to legal research.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('chatbot.ask') }}" class="btn btn-outline-warning btn-sm px-3">
                            <i class="bi bi-robot me-1"></i> Ask AI Legal Information
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-bell me-1"></i> Public Announcements ({{ $notifications->count() }})
                        </a>
                        <a href="#upgradeSection" class="btn btn-warning btn-sm px-3 fw-bold text-dark">
                            <i class="bi bi-stars me-1"></i> View Premium Upgrades
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center mt-4 mt-md-0">
                    <img src="{{ asset('images/law-universe-logo.svg') }}" width="140" height="140" alt="Emblem" class="img-fluid drop-shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Bare Acts</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['bare_acts']) }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-book fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('public.bare-acts') }}" class="small text-info text-decoration-none mt-2 d-inline-block">Browse Library &rarr;</a>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Legal Maxims</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['legal_maxims']) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-feather fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('public.legal-maxims') }}" class="small text-success text-decoration-none mt-2 d-inline-block">Explore Maxims &rarr;</a>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Landmarks</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['landmark_judgments']) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-bank fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('public.landmark-judgments') }}" class="small text-primary text-decoration-none mt-2 d-inline-block">Read Precedents &rarr;</a>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Legal Blogs</span>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['blogs']) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-journal-richtext fs-4"></i>
                    </div>
                </div>
                <a href="{{ route('public.blogs') }}" class="small text-warning text-decoration-none mt-2 d-inline-block">Read Articles &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Public Control Panel Section (Matching PDF Page 2 Modules) -->
    <div id="controlPanelSection" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-sliders text-warning me-2"></i> Public Control Panel Modules
                </h4>
                <p class="text-muted small mb-0">Authorized citizen services and tools configured for your account.</p>
            </div>
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                <i class="bi bi-shield-check me-1"></i> Active Citizen Role
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
                            <span class="text-muted small">Ask questions regarding legal rights, definitions, and procedures.</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2">
                            <span><i class="bi bi-chat-dots me-2 text-primary"></i> Ask AI Chatbot</span>
                            <a href="{{ route('chatbot.ask') }}" class="btn btn-sm btn-primary px-3">Ask Question</a>
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
                            <span class="text-muted small">Official notices, amendments, and public alerts.</span>
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

    <!-- Active Subscription / Upgrade Plans -->
    <div id="upgradeSection" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-stars text-warning me-2"></i> Subscription Plans & Role Upgrades
                </h4>
                <p class="text-muted small mb-0">Unlock complete mock tests, professional judicial precedents, or advocate case drafts.</p>
            </div>
            @if($activeSubscription)
                <span class="badge bg-success fs-6 py-2 px-3">
                    <i class="bi bi-check-circle-fill me-1"></i> Active: {{ $activeSubscription->package->name ?? 'Subscribed' }} (Exp: {{ $activeSubscription->expiry_date->format('d M Y') }})
                </span>
            @else
                <span class="badge bg-secondary fs-6 py-2 px-3">
                    Current: Free Public Tier
                </span>
            @endif
        </div>

        <div class="row g-4">
            @forelse($packages as $pkg)
                <div class="col-md-4">
                    <div class="card h-100 border rounded-4 p-4 shadow-sm {{ $pkg->price > 0 ? 'border-warning' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">{{ $pkg->name }}</h5>
                            <span class="badge bg-dark">{{ $pkg->billing_cycle }}</span>
                        </div>
                        <h2 class="fw-bold text-primary mb-3">
                            ₹{{ number_format($pkg->price, 2) }}
                            <small class="text-muted fs-6 fw-normal">/ {{ $pkg->billing_cycle }}</small>
                        </h2>
                        <p class="text-muted small mb-4">{{ $pkg->description }}</p>
                        
                        <h6 class="small fw-bold text-uppercase text-muted mb-2">Included Features:</h6>
                        <ul class="list-unstyled small mb-4">
                            @foreach($pkg->features as $feat)
                                <li class="mb-2 d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i> {{ $feat->name }}
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('home') }}#packages" class="btn btn-warning w-100 fw-bold">Select Plan</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                    No packages currently published.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
