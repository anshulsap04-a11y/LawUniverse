@extends('layouts.user-portal', ['title' => 'Advocate Control Panel - Law Universe'])

@section('content')
<div class="container-fluid py-4 px-md-5">

    <!-- Advocate Banner Matching PDF Page 1 -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #182234 0%, #0d1522 100%); border-top: 4px solid var(--lu-gold) !important;">
        <div class="card-body p-4 p-md-5 text-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Advocate Portal</span>
                    <h2 class="fw-bold mb-1" style="font-family: 'Cinzel', serif;">Welcome to the LawUniverse Dashboard</h2>
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
                        Law Universe is dedicated to empowering students, legal researchers, and advocates by offering a range of specialized services to support their practice growth and professional development.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('chatbot.ask') }}" class="btn btn-outline-warning btn-sm px-3">
                            <i class="bi bi-robot me-1"></i> Ask AI Legal Bot
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-bell me-1"></i> Notifications & Cause Lists
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center mt-4 mt-md-0">
                    <img src="{{ asset('images/law-universe-logo.svg') }}" width="150" height="150" alt="Emblem" class="img-fluid drop-shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Advocate Control Panel Section (Matching PDF Page 2) -->
    <div id="controlPanelSection" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h4 class="fw-bold text-dark mb-4 border-bottom pb-2">
            <i class="bi bi-sliders text-warning me-2"></i> Advocate Control Panel Modules
        </h4>

        <div class="row g-4">
            <!-- Module 1: Manage AI Chatbot -->
            <div class="col-md-6">
                <div class="card h-100 border p-3 bg-light-subtle">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded p-3 me-3">
                            <i class="bi bi-robot fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Module 1: Manage AI Chatbot</h5>
                            <span class="text-muted small">AI-driven legal assistance and draft generation.</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-chat-dots me-2 text-primary"></i> Ask AI Chatbot</span>
                            <a href="{{ route('chatbot.ask') }}" class="btn btn-sm btn-outline-primary py-0">Launch</a>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-clock-history me-2 text-primary"></i> AI Chatbot History</span>
                            <a href="{{ route('chatbot.history') }}" class="btn btn-sm btn-outline-primary py-0">Review</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Module 2: Manage Notification -->
            <div class="col-md-6">
                <div class="card h-100 border p-3 bg-light-subtle">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning text-dark rounded p-3 me-3">
                            <i class="bi bi-bell-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Module 2: Manage Notification</h5>
                            <span class="text-muted small">Stay updated on bar notices and legal alerts.</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-card-list me-2 text-warning"></i> List Notification</span>
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-dark py-0">Open Table</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

