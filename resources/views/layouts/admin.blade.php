<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Control Panel - Law Universe' }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --lu-navy: #182234;
            --lu-navy-dark: #0f172a;
            --lu-navy-light: #243046;
            --lu-gold: #d4af37;
            --lu-gold-light: #fef08a;
            --lu-yellow-btn: #f59e0b;
            --lu-yellow-hover: #d97706;
            --lu-sidebar-width: 270px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Top Navbar */
        .admin-navbar {
            background-color: var(--lu-navy);
            color: #ffffff;
            height: 65px;
            position: sticky;
            top: 0;
            z-index: 1030;
            border-bottom: 2px solid var(--lu-gold);
        }

        .header-search {
            position: relative;
            max-width: 320px;
        }

        .header-search input {
            border-radius: 20px;
            padding-left: 36px;
            padding-right: 36px;
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-size: 13px;
        }

        .header-search input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .header-search input:focus {
            background-color: rgba(255, 255, 255, 0.25);
            border-color: var(--lu-gold);
            color: #fff;
            box-shadow: none;
        }

        .header-search .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .header-search .mic-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .nav-icon-link {
            color: rgba(255, 255, 255, 0.85);
            font-size: 18px;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s;
            position: relative;
        }

        .nav-icon-link:hover {
            color: var(--lu-gold);
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background-color: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 10px;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--lu-sidebar-width);
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            min-height: calc(100vh - 65px);
            position: sticky;
            top: 65px;
            overflow-y: auto;
        }

        .sidebar-module-header {
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s;
            text-decoration: none;
        }

        .sidebar-module-header:hover {
            background-color: #f8fafc;
            color: #0f172a;
        }

        .sidebar-submodule-list {
            list-style: none;
            padding: 0;
            margin: 0;
            background-color: #f8fafc;
        }

        .sidebar-submodule-item a {
            display: flex;
            align-items: center;
            padding: 9px 16px 9px 36px;
            font-size: 12.5px;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s;
            position: relative;
        }

        .sidebar-submodule-item a:hover {
            color: #0284c7;
            background-color: #e2e8f0;
        }

        .sidebar-submodule-item a.active {
            color: #0284c7;
            font-weight: 600;
            background-color: #e0f2fe;
            border-right: 3px solid #0284c7;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #22c55e;
            display: inline-block;
            margin-left: auto;
        }

        /* Spec Action Buttons */
        .btn-spec-refresh {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            padding: 5px 16px;
            border-radius: 4px;
        }
        .btn-spec-refresh:hover {
            background-color: #d97706;
            color: #fff;
        }

        .btn-spec-trash {
            background-color: #eab308;
            border-color: #eab308;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            padding: 5px 16px;
            border-radius: 4px;
        }
        .btn-spec-trash:hover {
            background-color: #ca8a04;
            color: #fff;
        }

        .btn-table-action {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 3px;
            text-transform: capitalize;
        }

        .btn-table-details {
            background-color: #eab308;
            color: #ffffff;
            border: none;
        }
        .btn-table-details:hover {
            background-color: #ca8a04;
            color: #ffffff;
        }

        .btn-table-update {
            background-color: #f59e0b;
            color: #ffffff;
            border: none;
        }
        .btn-table-update:hover {
            background-color: #d97706;
            color: #ffffff;
        }

        .btn-table-trash {
            background-color: #ea580c;
            color: #ffffff;
            border: none;
        }
        .btn-table-trash:hover {
            background-color: #c2410c;
            color: #ffffff;
        }

        .content-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .table-custom-header thead th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            border: none;
            padding: 10px;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Admin Top Navbar -->
    <header class="admin-navbar d-flex align-items-center px-3">
        <!-- Grid App Launcher Icon -->
        <button class="btn btn-link text-white p-1 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" class="d-md-none">
            <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
        </button>

        <!-- Brand Logo & Name -->
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none me-4">
            <img src="{{ asset('images/law-universe-logo.svg') }}" alt="Law Universe" width="38" height="38" class="me-2">
            <span class="d-none d-lg-inline text-white fw-bold" style="font-family: 'Cinzel', serif; letter-spacing: 1px;">LAW UNIVERSE</span>
        </a>

        <!-- Global Search Bar -->
        <div class="header-search me-auto d-none d-md-block">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="form-control" placeholder="Search here ...">
            <i class="bi bi-mic mic-icon"></i>
        </div>

        <!-- Navigation Action Icons -->
        <div class="d-flex align-items-center gap-1 me-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-icon-link" title="Dashboard Home"><i class="bi bi-house-door"></i></a>
            <a href="#" class="nav-icon-link" title="Help & Documentation"><i class="bi bi-question-circle"></i></a>
            <a href="#" class="nav-icon-link" title="Desktop Workspace"><i class="bi bi-laptop"></i></a>
            <a href="#" class="nav-icon-link" title="Achievements"><i class="bi bi-trophy"></i></a>
            <a href="{{ route('chatbot.ask') }}" class="nav-icon-link" title="AI Legal Assistant"><i class="bi bi-robot"></i></a>
            <a href="#" class="nav-icon-link" title="Judicial Scales"><i class="bi bi-feather"></i></a>
            <a href="{{ route('notifications.index') }}" class="nav-icon-link" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="nav-badge">5</span>
            </a>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userMenu" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" width="32" height="32" class="rounded-circle me-2 border border-warning">
                <div class="d-none d-sm-block text-start me-1" style="line-height: 1.1;">
                    <span class="d-block small fw-bold">{{ auth()->user()->name }}</span>
                    <span class="d-block" style="font-size: 10px; color: var(--lu-gold);">{{ auth()->user()->email }}</span>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li class="dropdown-header text-uppercase small text-muted">Role: {{ auth()->user()->role }}</li>
                <li><a class="dropdown-item" href="{{ route('admin.users.show', auth()->id()) }}"><i class="bi bi-person me-2"></i> My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.rbac.index') }}"><i class="bi bi-shield-lock me-2"></i> RBAC Matrix</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <div class="d-flex">
        <!-- Sidebar Navigation (Accordion matching specification) -->
        <aside class="admin-sidebar d-none d-md-block shadow-sm">
            <div class="accordion accordion-flush" id="sidebarModulesAccordion">

                <!-- Module 1: Manage User Account -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingUser">
                        <button class="accordion-button {{ request()->is('admin/users*') ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUser" aria-expanded="{{ request()->is('admin/users*') ? 'true' : 'false' }}">
                            <i class="bi bi-people-fill text-primary me-2"></i> Manage User Account
                        </button>
                    </h2>
                    <div id="collapseUser" class="accordion-collapse collapse {{ request()->is('admin/users*') ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item">
                                <a href="{{ route('admin.users.create') }}" class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                                    <i class="bi bi-person-plus me-2 text-primary"></i> Add User Account
                                    @if(request()->routeIs('admin.users.create')) <span class="status-dot"></span> @endif
                                </a>
                            </li>
                            <li class="sidebar-submodule-item">
                                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                    <i class="bi bi-list-task me-2 text-primary"></i> List User Account
                                    @if(request()->routeIs('admin.users.index')) <span class="status-dot"></span> @endif
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Module 2: Manage Resource -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingResource">
                        <button class="accordion-button {{ request()->is('admin/resources*') ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseResource">
                            <i class="bi bi-folder2-open text-primary me-2"></i> Manage Resource
                        </button>
                    </h2>
                    <div id="collapseResource" class="accordion-collapse collapse {{ request()->is('admin/resources*') ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.resources.create') }}" class="{{ request()->routeIs('admin.resources.create') ? 'active' : '' }}"><i class="bi bi-cloud-arrow-up me-2 text-info"></i> Upload Resources</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.resources.index') }}" class="{{ request()->routeIs('admin.resources.index') ? 'active' : '' }}"><i class="bi bi-list-ul me-2 text-info"></i> List Resources</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.resources.bare_acts') }}" class="{{ request()->routeIs('admin.resources.bare_acts') ? 'active' : '' }}"><i class="bi bi-book me-2 text-info"></i> Bare Acts</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.resources.legal_maxims') }}" class="{{ request()->routeIs('admin.resources.legal_maxims') ? 'active' : '' }}"><i class="bi bi-feather me-2 text-info"></i> Legal Maxims</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.resources.landmark_judgments') }}" class="{{ request()->routeIs('admin.resources.landmark_judgments') ? 'active' : '' }}"><i class="bi bi-bank me-2 text-info"></i> Landmark Judgement</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.resources.notes') }}" class="{{ request()->routeIs('admin.resources.notes') ? 'active' : '' }}"><i class="bi bi-journal-text me-2 text-info"></i> Notes</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Module 3: Manage Examination -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingExam">
                        <button class="accordion-button {{ request()->is('admin/examinations*') ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExam">
                            <i class="bi bi-mortarboard-fill text-primary me-2"></i> Manage Examination
                        </button>
                    </h2>
                    <div id="collapseExam" class="accordion-collapse collapse {{ request()->is('admin/examinations*') ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.examinations.index') }}" class="{{ request()->routeIs('admin.examinations.index') ? 'active' : '' }}"><i class="bi bi-card-checklist me-2 text-warning"></i> Examination</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.examinations.questions') }}" class="{{ request()->routeIs('admin.examinations.questions') ? 'active' : '' }}"><i class="bi bi-file-earmark-text me-2 text-warning"></i> Question Paper</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.examinations.previous_years') }}" class="{{ request()->routeIs('admin.examinations.previous_years') ? 'active' : '' }}"><i class="bi bi-clock-history me-2 text-warning"></i> Previous Years Paper</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.examinations.categories_labels') }}" class="{{ request()->routeIs('admin.examinations.categories_labels') ? 'active' : '' }}"><i class="bi bi-diagram-3 me-2 text-warning"></i> Manage Categories & Labels</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Module 4: Manage Blogging -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingBlog">
                        <button class="accordion-button {{ request()->is('admin/blogs*') ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBlog">
                            <i class="bi bi-journal-richtext text-primary me-2"></i> Manage Blogging
                        </button>
                    </h2>
                    <div id="collapseBlog" class="accordion-collapse collapse {{ request()->is('admin/blogs*') ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.blogs.statistics') }}" class="{{ request()->routeIs('admin.blogs.statistics') ? 'active' : '' }}"><i class="bi bi-graph-up me-2 text-success"></i> Blogs Statistics</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.blogs.create') }}" class="{{ request()->routeIs('admin.blogs.create') ? 'active' : '' }}"><i class="bi bi-pencil-square me-2 text-success"></i> Compose Blogs</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.blogs.index') }}" class="{{ request()->routeIs('admin.blogs.index') ? 'active' : '' }}"><i class="bi bi-list-columns me-2 text-success"></i> List Blogs</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Module 5: Manage Finance -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingFinance">
                        <button class="accordion-button {{ request()->is('admin/finance*') ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFinance">
                            <i class="bi bi-cash-coin text-primary me-2"></i> Manage Finance
                        </button>
                    </h2>
                    <div id="collapseFinance" class="accordion-collapse collapse {{ request()->is('admin/finance*') ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.finance.statistics') }}" class="{{ request()->routeIs('admin.finance.statistics') ? 'active' : '' }}"><i class="bi bi-bar-chart me-2 text-danger"></i> Finance Statistics</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.finance.packages') }}" class="{{ request()->routeIs('admin.finance.packages') ? 'active' : '' }}"><i class="bi bi-box-seam me-2 text-danger"></i> Manage Package</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.finance.features') }}" class="{{ request()->routeIs('admin.finance.features') ? 'active' : '' }}"><i class="bi bi-sliders me-2 text-danger"></i> Manage Features</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.finance.transactions') }}" class="{{ request()->routeIs('admin.finance.transactions') ? 'active' : '' }}"><i class="bi bi-receipt me-2 text-danger"></i> Manage Transaction</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.finance.subscriptions') }}" class="{{ request()->routeIs('admin.finance.subscriptions') ? 'active' : '' }}"><i class="bi bi-credit-card me-2 text-danger"></i> Manage Subscription</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.finance.coupons') }}" class="{{ request()->routeIs('admin.finance.coupons') ? 'active' : '' }}"><i class="bi bi-ticket-perforated me-2 text-danger"></i> Manage Coupon</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Module 6: Manage AI Chatbot -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingChatbot">
                        <button class="accordion-button {{ (request()->is('chatbot*') || request()->is('admin/ai*')) ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChatbot">
                            <i class="bi bi-robot text-primary me-2"></i> Manage AI Chatbot
                        </button>
                    </h2>
                    <div id="collapseChatbot" class="accordion-collapse collapse {{ (request()->is('chatbot*') || request()->is('admin/ai*')) ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item"><a href="{{ route('chatbot.ask') }}" class="{{ request()->routeIs('chatbot.ask') ? 'active' : '' }}"><i class="bi bi-chat-dots me-2 text-primary"></i> Ask AI Chatbot</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.ai.conversations') }}" class="{{ request()->routeIs('admin.ai.conversations') ? 'active' : '' }}"><i class="bi bi-clock-history me-2 text-primary"></i> AI Chatbot History</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.ai.index') }}" class="{{ request()->routeIs('admin.ai.index') ? 'active' : '' }}"><i class="bi bi-cpu me-2 text-primary"></i> Manage GPT Engine API</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Module 7: Manage Notification -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingNotif">
                        <button class="accordion-button {{ request()->is('admin/notifications*') ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotif">
                            <i class="bi bi-bell-fill text-primary me-2"></i> Manage Notification
                        </button>
                    </h2>
                    <div id="collapseNotif" class="accordion-collapse collapse {{ request()->is('admin/notifications*') ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}"><i class="bi bi-card-list me-2 text-warning"></i> List & Post Notification</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Module 8: Dynamic RBAC & Roles -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingRbac">
                        <button class="accordion-button {{ request()->is('admin/rbac*') ? '' : 'collapsed' }} py-3 px-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRbac">
                            <i class="bi bi-shield-lock-fill text-primary me-2"></i> Dynamic RBAC & Roles
                        </button>
                    </h2>
                    <div id="collapseRbac" class="accordion-collapse collapse {{ request()->is('admin/rbac*') ? 'show' : '' }}" data-bs-parent="#sidebarModulesAccordion">
                        <ul class="sidebar-submodule-list">
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.rbac.index') }}" class="{{ request()->routeIs('admin.rbac.index') ? 'active' : '' }}"><i class="bi bi-grid-3x3 me-2 text-danger"></i> Roles & Permissions Matrix</a></li>
                            <li class="sidebar-submodule-item"><a href="{{ route('admin.rbac.logs') }}" class="{{ request()->routeIs('admin.rbac.logs') ? 'active' : '' }}"><i class="bi bi-journal-text me-2 text-danger"></i> Audit & Activity Logs</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-grow-1 p-3 p-md-4" style="min-width: 0;">
            
            @if(session()->has('impersonating_admin_id'))
                <div class="alert alert-warning border-2 border-warning d-flex justify-content-between align-items-center shadow-sm mb-4">
                    <div>
                        <i class="bi bi-shield-lock-fill me-2 fs-5"></i>
                        <strong>Impersonation Active:</strong> You are currently logged in as <strong>{{ auth()->user()->full_name }}</strong> ({{ auth()->user()->email }}).
                    </div>
                    <form action="{{ route('impersonation.stop') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-dark btn-sm fw-bold">
                            <i class="bi bi-box-arrow-left me-1"></i> Exit Impersonation & Return to Admin
                        </button>
                    </form>
                </div>
            @endif

            <!-- Global Flash Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')

        </main>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>

