@php
    $user = auth()->user();
    $role = $user->role ?? 'public';
    $isMobile = $isMobile ?? false;
@endphp

@if($isMobile)
<div class="portal-sidebar-mobile w-100">
@else
<aside class="portal-sidebar shadow-sm d-none d-lg-block">
@endif
    
    <!-- User / Role Identification Header -->
    <div class="p-3 border-bottom bg-light">
        <div class="d-flex align-items-center">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" width="42" height="42" class="rounded-circle me-2 border border-warning shadow-sm">
            <div style="line-height: 1.2; min-width: 0;">
                <span class="d-block fw-bold text-dark text-truncate" style="font-size: 13px;">{{ $user->full_name ?? $user->name }}</span>
                <span class="badge bg-warning text-dark text-uppercase fw-bold" style="font-size: 10px;">{{ $user->role_name }}</span>
            </div>
        </div>

        @if($user->isSuperAdmin())
            <div class="mt-2 pt-2 border-top">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm w-100 fw-bold" style="font-size: 11px;">
                    <i class="bi bi-shield-lock-fill text-warning me-1"></i> Super Admin Panel
                </a>
            </div>
        @endif
    </div>

    <div class="portal-sidebar-nav py-2">

        {{-- ------------------------------------------------------------------------- --}}
        {{-- STUDENT PORTAL SIDEBAR                                                   --}}
        {{-- ------------------------------------------------------------------------- --}}
        @if($role === 'student')
            <div class="sidebar-section-title px-3 py-2 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Student Academic Menu
            </div>

            <a href="{{ route('student.dashboard') }}" class="portal-sidebar-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 text-primary me-2"></i> Dashboard Overview
            </a>

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_examinations'))
                <a href="{{ route('student.dashboard') }}#availableExams" class="portal-sidebar-item">
                    <i class="bi bi-journal-check text-success me-2"></i> Available Mock Tests
                </a>
                <a href="{{ route('student.dashboard') }}#myAttempts" class="portal-sidebar-item">
                    <i class="bi bi-trophy text-warning me-2"></i> My Exam Results
                </a>
                <a href="{{ route('student.dashboard') }}#previousPapers" class="portal-sidebar-item">
                    <i class="bi bi-clock-history text-secondary me-2"></i> Previous Year Papers
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_resources'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    Study Resources
                </div>
                <a href="{{ route('public.bare-acts') }}" class="portal-sidebar-item">
                    <i class="bi bi-book text-info me-2"></i> Bare Acts Repository
                </a>
                <a href="{{ route('public.legal-maxims') }}" class="portal-sidebar-item">
                    <i class="bi bi-feather text-info me-2"></i> Legal Maxims
                </a>
                <a href="{{ route('public.landmark-judgments') }}" class="portal-sidebar-item">
                    <i class="bi bi-bank text-info me-2"></i> Landmark Judgments
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_ai_chatbot'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    AI Study Assistant
                </div>
                <a href="{{ route('chatbot.ask') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.ask') ? 'active' : '' }}">
                    <i class="bi bi-robot text-primary me-2"></i> Ask AI Legal Tutor
                </a>
                <a href="{{ route('chatbot.history') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.history') ? 'active' : '' }}">
                    <i class="bi bi-clock-history text-primary me-2"></i> AI Chat History
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_notifications'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    Announcements
                </div>
                <a href="{{ route('notifications.index') }}" class="portal-sidebar-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                    <i class="bi bi-bell text-danger me-2"></i> Exam Notifications
                </a>
            @endif

            <a href="{{ route('student.dashboard') }}#subscriptionCard" class="portal-sidebar-item">
                <i class="bi bi-credit-card text-success me-2"></i> My Study Plan
            </a>

        {{-- ------------------------------------------------------------------------- --}}
        {{-- ADVOCATE PORTAL SIDEBAR                                                   --}}
        {{-- ------------------------------------------------------------------------- --}}
        @elseif($role === 'advocate')
            <div class="sidebar-section-title px-3 py-2 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Advocate Practice Menu
            </div>

            <a href="{{ route('advocate.dashboard') }}" class="portal-sidebar-item {{ request()->routeIs('advocate.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 text-warning me-2"></i> Practice Dashboard
            </a>

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_resources'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    Legal Research Library
                </div>
                <a href="{{ route('public.landmark-judgments') }}" class="portal-sidebar-item">
                    <i class="bi bi-bank text-primary me-2"></i> Landmark Judgments
                </a>
                <a href="{{ route('public.bare-acts') }}" class="portal-sidebar-item">
                    <i class="bi bi-book text-info me-2"></i> Bare Acts & Amendments
                </a>
                <a href="{{ route('public.legal-maxims') }}" class="portal-sidebar-item">
                    <i class="bi bi-feather text-success me-2"></i> Latin Legal Maxims
                </a>
                <a href="{{ route('advocate.dashboard') }}#notesSection" class="portal-sidebar-item">
                    <i class="bi bi-journal-text text-secondary me-2"></i> Practice Notes
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_ai_chatbot'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    AI Legal Drafter
                </div>
                <a href="{{ route('chatbot.ask') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.ask') ? 'active' : '' }}">
                    <i class="bi bi-robot text-primary me-2"></i> Draft Briefs & Queries
                </a>
                <a href="{{ route('chatbot.history') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.history') ? 'active' : '' }}">
                    <i class="bi bi-clock-history text-primary me-2"></i> Saved AI Drafts
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_blogging'))
                <a href="{{ route('public.blogs') }}" class="portal-sidebar-item">
                    <i class="bi bi-journal-richtext text-danger me-2"></i> Legal Commentary & Blogs
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_notifications'))
                <a href="{{ route('notifications.index') }}" class="portal-sidebar-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                    <i class="bi bi-bell text-warning me-2"></i> Cause Lists & Notices
                </a>
            @endif

            <a href="{{ route('advocate.dashboard') }}#subscriptionCard" class="portal-sidebar-item">
                <i class="bi bi-shield-check text-success me-2"></i> Advocate Pro Plan
            </a>

        {{-- ------------------------------------------------------------------------- --}}
        {{-- JUDGE PORTAL SIDEBAR                                                      --}}
        {{-- ------------------------------------------------------------------------- --}}
        @elseif($role === 'judge')
            <div class="sidebar-section-title px-3 py-2 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Judicial Chambers Menu
            </div>

            <a href="{{ route('judge.dashboard') }}" class="portal-sidebar-item {{ request()->routeIs('judge.dashboard') ? 'active' : '' }}">
                <i class="bi bi-bank2 text-danger me-2"></i> Chambers Overview
            </a>

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_resources'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    Judicial Reference
                </div>
                <a href="{{ route('public.landmark-judgments') }}" class="portal-sidebar-item">
                    <i class="bi bi-bank text-primary me-2"></i> Landmark Rulings
                </a>
                <a href="{{ route('public.bare-acts') }}" class="portal-sidebar-item">
                    <i class="bi bi-book text-info me-2"></i> Central & State Acts
                </a>
                <a href="{{ route('public.legal-maxims') }}" class="portal-sidebar-item">
                    <i class="bi bi-feather text-success me-2"></i> Legal Jurisprudence
                </a>
                <a href="{{ route('judge.dashboard') }}#notesSection" class="portal-sidebar-item">
                    <i class="bi bi-journal-bookmark text-secondary me-2"></i> Judicial Notes
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_ai_chatbot'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    Judicial AI Assistant
                </div>
                <a href="{{ route('chatbot.ask') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.ask') ? 'active' : '' }}">
                    <i class="bi bi-robot text-primary me-2"></i> Research Legal Queries
                </a>
                <a href="{{ route('chatbot.history') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.history') ? 'active' : '' }}">
                    <i class="bi bi-clock-history text-primary me-2"></i> Private Research Log
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_notifications'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    Bench Directives
                </div>
                <a href="{{ route('notifications.index') }}" class="portal-sidebar-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                    <i class="bi bi-bell text-danger me-2"></i> Official Circulars
                </a>
            @endif

        {{-- ------------------------------------------------------------------------- --}}
        {{-- PUBLIC PORTAL SIDEBAR                                                     --}}
        {{-- ------------------------------------------------------------------------- --}}
        @else
            <div class="sidebar-section-title px-3 py-2 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Citizen Legal Menu
            </div>

            <a href="{{ route('public.dashboard') }}" class="portal-sidebar-item {{ request()->routeIs('public.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 text-primary me-2"></i> Citizen Dashboard
            </a>

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_resources'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    Free Legal Knowledge
                </div>
                <a href="{{ route('public.bare-acts') }}" class="portal-sidebar-item">
                    <i class="bi bi-book text-info me-2"></i> Free Bare Acts
                </a>
                <a href="{{ route('public.legal-maxims') }}" class="portal-sidebar-item">
                    <i class="bi bi-feather text-success me-2"></i> Legal Maxims
                </a>
                <a href="{{ route('public.landmark-judgments') }}" class="portal-sidebar-item">
                    <i class="bi bi-bank text-primary me-2"></i> Landmark Judgments
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_blogging'))
                <a href="{{ route('public.blogs') }}" class="portal-sidebar-item">
                    <i class="bi bi-journal-richtext text-danger me-2"></i> Citizen Legal Blogs
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_ai_chatbot'))
                <div class="sidebar-section-title px-3 py-2 mt-2 text-uppercase text-muted fw-bold" style="font-size: 11px;">
                    AI Assistant
                </div>
                <a href="{{ route('chatbot.ask') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.ask') ? 'active' : '' }}">
                    <i class="bi bi-robot text-primary me-2"></i> Ask Legal Question
                </a>
                <a href="{{ route('chatbot.history') }}" class="portal-sidebar-item {{ request()->routeIs('chatbot.history') ? 'active' : '' }}">
                    <i class="bi bi-clock-history text-primary me-2"></i> My Questions History
                </a>
            @endif

            @if($user->isSuperAdmin() || $user->hasModuleAccess('manage_notifications'))
                <a href="{{ route('notifications.index') }}" class="portal-sidebar-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                    <i class="bi bi-bell text-warning me-2"></i> Public Notices
                </a>
            @endif

            <a href="{{ route('public.dashboard') }}#upgradeSection" class="portal-sidebar-item">
                <i class="bi bi-stars text-warning me-2"></i> Upgrade Subscription
            </a>
        @endif

    </div>
@if($isMobile)
</div>
@else
</aside>
@endif
