<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Law Universe Portal' }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --lu-navy: #182234;
            --lu-gold: #d4af37;
            --lu-yellow-btn: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }

        .portal-navbar {
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
            max-width: 350px;
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

        .header-search .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .header-search .mic-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
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

        .btn-go-control-panel {
            background-color: #f59e0b;
            color: #111;
            font-weight: 700;
            font-size: 13px;
            padding: 8px 18px;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }

        .btn-go-control-panel:hover {
            background-color: #d97706;
            color: #fff;
        }

        .portal-sidebar {
            width: 250px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            min-height: calc(100vh - 65px);
        }

        .portal-sidebar-item {
            padding: 12px 18px;
            display: flex;
            align-items: center;
            color: #475569;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.15s;
        }

        .portal-sidebar-item:hover, .portal-sidebar-item.active {
            color: #0284c7;
            background-color: #f0f9ff;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <header class="portal-navbar d-flex align-items-center px-3">
        <a href="{{ auth()->user()->getDashboardRoute() }}" class="d-flex align-items-center text-decoration-none me-4">
            <img src="{{ asset('images/law-universe-logo.svg') }}" alt="Law Universe" width="38" height="38" class="me-2">
            <span class="text-white fw-bold d-none d-sm-inline" style="font-family: 'Cinzel', serif;">LAW UNIVERSE</span>
        </a>

        <div class="header-search me-auto d-none d-md-block">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="form-control" placeholder="Search legal topics, judgments, acts...">
            <i class="bi bi-mic mic-icon"></i>
        </div>

        <div class="d-flex align-items-center gap-1 me-3">
            <a href="{{ auth()->user()->getDashboardRoute() }}" class="nav-icon-link" title="Home"><i class="bi bi-house-door"></i></a>
            <a href="#" class="nav-icon-link" title="Help"><i class="bi bi-question-circle"></i></a>
            <a href="#" class="nav-icon-link" title="Workspace"><i class="bi bi-laptop"></i></a>
            <a href="#" class="nav-icon-link" title="Rankings"><i class="bi bi-trophy"></i></a>
            <a href="{{ route('chatbot.ask') }}" class="nav-icon-link" title="AI Chatbot"><i class="bi bi-robot"></i></a>
            <a href="{{ route('notifications.index') }}" class="nav-icon-link" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="nav-badge">5</span>
            </a>
        </div>

        <!-- User Info & Dropdown -->
        <div class="dropdown me-3">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="portalUserMenu" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" width="32" height="32" class="rounded-circle me-2 border">
                <div class="d-none d-md-block text-start me-1" style="line-height: 1.1;">
                    <span class="d-block small fw-bold">{{ auth()->user()->name }}</span>
                    <span class="d-block text-white-50" style="font-size: 10px;">{{ auth()->user()->email }}</span>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li class="dropdown-header text-uppercase small">Role: {{ auth()->user()->role }}</li>
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

    @if(session()->has('impersonating_admin_id'))
        <div class="alert alert-warning border-0 rounded-0 d-flex justify-content-between align-items-center px-4 py-2 mb-0 shadow-sm">
            <div>
                <i class="bi bi-shield-lock-fill me-2 fs-5"></i>
                <strong>Admin Impersonation Active:</strong> You are viewing Law Universe as <strong>{{ auth()->user()->full_name }}</strong> ({{ auth()->user()->role_name }}).
            </div>
            <form action="{{ route('impersonation.stop') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm fw-bold">
                    <i class="bi bi-box-arrow-left me-1"></i> Exit & Return to Super Admin
                </button>
            </form>
        </div>
    @endif

    <div class="container-fluid py-4 px-md-5">
        <!-- Global Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
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
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

