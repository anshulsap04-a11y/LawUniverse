<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Law Universe – Centralized Legal Education & Research Platform</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --lu-navy: #182234;
            --lu-navy-dark: #0f172a;
            --lu-gold: #d4af37;
            --lu-yellow-btn: #f59e0b;
        }
        body { font-family: 'Inter', sans-serif; color: #1e293b; background-color: #f8fafc; }
        .hero-section {
            background: linear-gradient(135deg, var(--lu-navy-dark) 0%, var(--lu-navy) 100%);
            color: #fff;
            padding: 90px 0 80px 0;
            border-bottom: 3px solid var(--lu-gold);
        }
        .hero-title { font-family: 'Cinzel', serif; font-size: 3rem; color: #fff; }
        .hero-subtitle { color: rgba(255, 255, 255, 0.85); font-size: 1.2rem; }
        .gold-divider { height: 3px; width: 60px; background-color: var(--lu-gold); margin: 20px auto; }
        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.25s ease-in-out;
            background: #fff;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-color: var(--lu-gold);
        }
        .navbar-brand-logo { height: 42px; }
    </style>
</head>
<body>

    <!-- Public Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: var(--lu-navy); border-bottom: 2px solid var(--lu-gold);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/law-universe-logo.svg') }}" alt="Law Universe" class="navbar-brand-logo me-2">
                <span style="font-family: 'Cinzel', serif; font-weight: 700; letter-spacing: 0.5px;">LAW UNIVERSE</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link text-white fw-semibold" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ route('public.bare-acts') }}">Bare Acts</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ route('public.legal-maxims') }}">Legal Maxims</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ route('public.landmark-judgments') }}">Landmark Judgments</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ route('public.resources') }}">Library</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ route('public.blogs') }}">Legal Blog</a></li>
                    @auth
                        <li class="nav-item ms-lg-2">
                            <a href="{{ auth()->user()->dashboard_url }}" class="btn btn-warning fw-bold px-3">
                                <i class="bi bi-speedometer2 me-1"></i> Control Panel
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-light fw-semibold px-3">Log In</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-warning fw-bold px-3">Create Account</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <div class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">Enterprise Legal Education & Research</div>
            <h1 class="hero-title fw-bold mb-3">LAW UNIVERSE</h1>
            <p class="hero-subtitle max-w-2xl mx-auto mb-4" style="max-width: 720px;">
                Comprehensive statutory repositories, landmark precedents, judicial examinations, and context-aware Legal AI Assistant. Built for Advocates, Judges, Students, and Legal Scholars.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-4 shadow">
                    Get Started Free <i class="bi bi-arrow-right ms-1"></i>
                </a>
                <a href="{{ route('public.bare-acts') }}" class="btn btn-outline-light btn-lg px-4">
                    Explore Bare Acts
                </a>
            </div>
        </div>
    </header>

    <!-- Core Highlights -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Centralized Legal Repositories</h2>
                <div class="gold-divider"></div>
                <p class="text-muted">Authoritative statutes, codified precedents, and examination preparation resources.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 feature-card p-4 text-center">
                        <div class="text-primary fs-1 mb-3"><i class="fa-solid fa-scale-balanced"></i></div>
                        <h5 class="fw-bold">Bare Acts</h5>
                        <p class="text-muted small">Constitution of India, BNS, BNSS, BSA, and centralized Indian codes with section analysis.</p>
                        <a href="{{ route('public.bare-acts') }}" class="mt-auto fw-bold text-primary text-decoration-none">View Acts &rarr;</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 feature-card p-4 text-center">
                        <div class="text-info fs-1 mb-3"><i class="fa-solid fa-feather-pointed"></i></div>
                        <h5 class="fw-bold">Legal Maxims</h5>
                        <p class="text-muted small">Latin doctrines, natural justice principles, and authoritative judicial interpretations.</p>
                        <a href="{{ route('public.legal-maxims') }}" class="mt-auto fw-bold text-info text-decoration-none">Explore Maxims &rarr;</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 feature-card p-4 text-center">
                        <div class="text-danger fs-1 mb-3"><i class="fa-solid fa-landmark"></i></div>
                        <h5 class="fw-bold">Landmark Judgments</h5>
                        <p class="text-muted small">Constitution bench precedents, ratio decidendi, and Supreme Court ruling summaries.</p>
                        <a href="{{ route('public.landmark-judgments') }}" class="mt-auto fw-bold text-danger text-decoration-none">Read Judgments &rarr;</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 feature-card p-4 text-center">
                        <div class="text-success fs-1 mb-3"><i class="fa-solid fa-robot"></i></div>
                        <h5 class="fw-bold">Legal AI Assistant</h5>
                        <p class="text-muted small">Interactive AI research engine synthesizing citations, statutory links, and legal insights.</p>
                        <a href="{{ route('login') }}" class="mt-auto fw-bold text-success text-decoration-none">Chat with AI &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Subscription Plans -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Flexible Professional Plans</h2>
                <div class="gold-divider"></div>
                <p class="text-muted">Select the right package for your legal studies or law firm research needs.</p>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($packages as $pkg)
                    <div class="col-md-4">
                        <div class="card h-100 border-2 rounded-3 p-4 {{ $pkg->price > 0 ? 'border-primary shadow-sm' : 'border-light' }}">
                            <h4 class="fw-bold mb-2">{{ $pkg->name }}</h4>
                            <p class="text-muted small mb-3">{{ $pkg->description }}</p>
                            <div class="mb-4">
                                <span class="display-6 fw-bold text-dark">&#8377; {{ number_format($pkg->price, 0) }}</span>
                                <span class="text-muted">/ {{ $pkg->duration_in_days }} days</span>
                            </div>
                            <ul class="list-unstyled mb-4 small">
                                @foreach($pkg->features as $f)
                                    <li class="mb-2 text-success"><i class="bi bi-check-circle-fill me-2"></i> {{ $f->name }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-{{ $pkg->price > 0 ? 'primary' : 'outline-dark' }} w-100 fw-bold mt-auto py-2">
                                Choose {{ $pkg->name }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5" style="background-color: var(--lu-navy); color: #94a3b8;">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h5 class="text-white fw-bold" style="font-family: 'Cinzel', serif;">LAW UNIVERSE</h5>
                    <p class="small mb-0">Centralized Legal Education & Research Platform for Advocates, Judges, Students and Public Seekers.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-white fw-semibold mb-3">Quick Links</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('public.bare-acts') }}" class="text-decoration-none text-secondary">Bare Acts</a></li>
                        <li><a href="{{ route('public.legal-maxims') }}" class="text-decoration-none text-secondary">Legal Maxims</a></li>
                        <li><a href="{{ route('public.landmark-judgments') }}" class="text-decoration-none text-secondary">Landmark Judgments</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="text-white fw-semibold mb-3">Portals</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('login') }}" class="text-decoration-none text-secondary">Student Control Panel</a></li>
                        <li><a href="{{ route('login') }}" class="text-decoration-none text-secondary">Advocate Control Panel</a></li>
                        <li><a href="{{ route('login') }}" class="text-decoration-none text-secondary">Judge Control Panel</a></li>
                        <li><a href="{{ route('login') }}" class="text-decoration-none text-secondary">Super Admin Panel</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-4 border-top border-secondary text-center small">
                &copy; {{ date('Y') }} Law Universe. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
