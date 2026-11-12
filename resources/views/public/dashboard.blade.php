@extends('layouts.user-portal', ['title' => 'Public Portal - Law Universe'])

@section('content')
<div class="container-fluid py-4 px-md-5">

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-top: 4px solid var(--lu-gold) !important;">
        <div class="card-body p-4 p-md-5 text-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-secondary text-uppercase px-3 py-1 fw-bold mb-2">Public Legal Portal</span>
                    <h2 class="fw-bold mb-1" style="font-family: 'Cinzel', serif;">Welcome to LawUniverse</h2>
                    <p class="text-white-50 mb-0">Democratizing Legal Knowledge: Citizen Rights, Bare Acts & Public Legal Resources.</p>
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
                        Law Universe empowers the public with clear explanations of rights, consumer protection guidelines, legal maxims, and direct access to legal research.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('chatbot.ask') }}" class="btn btn-outline-warning btn-sm px-3">
                            <i class="bi bi-robot me-1"></i> Ask AI Legal Information
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-bell me-1"></i> Public Announcements
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center mt-4 mt-md-0">
                    <img src="{{ asset('images/law-universe-logo.svg') }}" width="150" height="150" alt="Emblem" class="img-fluid drop-shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Public Control Panel Section -->
    <div id="controlPanelSection" class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h4 class="fw-bold text-dark mb-4 border-bottom pb-2">
            <i class="bi bi-grid text-primary me-2"></i> Public Resources & Information
        </h4>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border p-3 bg-light-subtle">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded p-3 me-3">
                            <i class="bi bi-robot fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">AI Legal Query Assistant</h5>
                            <span class="text-muted small">Ask plain questions about statutes, rules, and rights.</span>
                        </div>
                    </div>
                    <a href="{{ route('chatbot.ask') }}" class="btn btn-primary btn-sm w-100">Launch Assistant</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border p-3 bg-light-subtle">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning text-dark rounded p-3 me-3">
                            <i class="bi bi-bell-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Notifications & Public Notices</h5>
                            <span class="text-muted small">Stay updated on essential changes to laws and portal announcements.</span>
                        </div>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="btn btn-dark btn-sm w-100">View Notices</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

