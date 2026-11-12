<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Law Universe Portal' }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --lu-gold: #d4af37;
            --lu-gold-dark: #997b1a;
            --lu-dark: #121316;
            --lu-darker: #0b0c0e;
            --lu-blue: #0284c7;
            --lu-blue-hover: #0369a1;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6fa;
            color: #2d3748;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .brand-logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .brand-logo-img {
            width: 55px;
            height: 55px;
        }

        .brand-name {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 26px;
            letter-spacing: 1.5px;
            color: #111827;
            margin: 0;
            line-height: 1;
        }

        .brand-tagline {
            font-size: 11px;
            color: var(--lu-gold-dark);
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 16px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            color: #374151;
            transition: all 0.2s;
            text-decoration: none;
            width: 100%;
        }

        .btn-social:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
            color: #111827;
        }

        .btn-social i {
            font-size: 18px;
        }

        .btn-social-google {
            border-color: #e5e7eb;
        }

        .btn-social-facebook {
            border-color: #e5e7eb;
        }

        .btn-lu-primary {
            background-color: #0284c7;
            border-color: #0284c7;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-lu-primary:hover {
            background-color: #0369a1;
            border-color: #0369a1;
            color: #ffffff;
        }

        .role-selector-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #475569;
            background: #334155;
            color: #f8fafc;
            text-align: center;
        }

        .role-selector-btn.active, .role-selector-btn:hover {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .auth-footer {
            margin-top: auto;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <main class="py-5 flex-grow-1 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 {{ $cardWidth ?? 'col-md-6 col-lg-5' }}">
                    
                    <!-- Flash Alerts -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i> Please correct the following errors:</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')

                </div>
            </div>
        </div>
    </main>

    <footer class="auth-footer">
        <div class="container">
            <p class="mb-1">© {{ date('Y') }} <strong>Law Universe Portal</strong>. All rights reserved.</p>
            <p class="mb-0 text-muted">।। यतो धर्मस्ततो जयः ।। (Where there is Righteousness, there is Victory)</p>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>

