<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Expense Tracker')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --app-bg: #eef2f7;
            --sidebar-bg: linear-gradient(180deg, #111827 0%, #0f172a 100%);
            --sidebar-text: rgba(255, 255, 255, 0.78);
            --sidebar-active: rgba(59, 130, 246, 0.18);
            --sidebar-border: rgba(255, 255, 255, 0.08);
            --brand: #2563eb;
            --brand-soft: rgba(37, 99, 235, 0.12);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-soft: rgba(148, 163, 184, 0.22);
            --shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.10), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.08), transparent 30%),
                var(--app-bg);
            color: var(--text-main);
        }

        .app-shell {
            min-height: 100vh;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.48);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 1030;
        }

        .sidebar {
            width: 280px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.32);
            transition: transform 0.3s ease;
            z-index: 1040;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding-bottom: 1.2rem;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-brand-icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            font-size: 1.3rem;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.35);
        }

        .sidebar-subtitle {
            color: rgba(255, 255, 255, 0.62);
            font-size: 0.85rem;
        }

        .sidebar-menu-label {
            color: rgba(255, 255, 255, 0.42);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.74rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.60rem;
            color: var(--sidebar-text);
            padding: 0.60rem 1rem;
            margin-bottom: 0.45rem;
            border-radius: 18px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .sidebar .nav-link i {
            width: 1.2rem;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: var(--sidebar-active);
            transform: translateX(4px);
            box-shadow: inset 0 0 0 1px rgba(96, 165, 250, 0.18);
        }

        .sidebar-user {
            margin-top: auto;
            padding: 5px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-user small {
            color: rgba(255, 255, 255, 0.62);
        }

        .main-panel {
            min-width: 0;
            flex: 1;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.88);
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
        }

        .topbar-card {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .topbar-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0;
            color: var(--text-main);
        }

        .topbar-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .content-wrap {
            padding: 1.5rem;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 28px;
            box-shadow: var(--shadow-soft);
            padding: 1.5rem;
        }

        .btn-soft {
            background: var(--brand-soft);
            color: var(--brand);
            border: 0;
        }

        .btn-soft:hover {
            background: rgba(37, 99, 235, 0.16);
            color: #1d4ed8;
        }

        .page-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--brand);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .page-title {
            font-size: clamp(1.8rem, 2vw, 2.4rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
        }

        .page-description {
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 0;
        }

        .metric-card {
            height: 100%;
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            padding: 1.25rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: var(--brand-soft);
            color: var(--brand);
            font-size: 1.2rem;
        }

        .metric-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .metric-value {
            font-size: 1.85rem;
            font-weight: 800;
            margin: 0.75rem 0 0.25rem;
        }

        .section-card {
            height: 100%;
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            background: #fff;
            padding: 1.5rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .section-text {
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .list-clean {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .list-clean li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.95rem 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.75);
        }

        .list-clean li:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .list-clean li:first-child {
            padding-top: 0;
        }

        .bullet-soft {
            width: 2rem;
            height: 2rem;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.06);
            color: var(--text-main);
        }

        .swal2-popup {
            border-radius: 24px !important;
        }

        .swal2-confirm {
            border-radius: 999px !important;
            padding: 0.7rem 1.4rem !important;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-open .sidebar-backdrop {
                opacity: 1;
                visibility: visible;
            }

            .content-wrap {
                padding: 1rem;
            }

            .content-card {
                border-radius: 24px;
                padding: 1.15rem;
            }
        }

        @media (min-width: 992px) {
            .sidebar {
                position: sticky;
                top: 0;
            }
        }

    </style>
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="d-flex app-shell">
        <aside class="sidebar d-flex flex-column p-3 p-lg-4" id="appSidebar">
            <div class="sidebar-brand mb-4">
                <span class="sidebar-brand-icon">
                    <i class="bi bi-wallet2"></i>
                </span>
                <div>
                    <h1 class="h5 fw-bold mb-1">Expense Tracker</h1>
                    <div class="sidebar-subtitle">Smart admin workspace</div>
                </div>
            </div>

            {{-- <div class="sidebar-menu-label">Navigation</div> --}}

            <ul class="nav flex-column mb-4">
                <li class="nav-item">
                    <a href="{{ auth()->user()->hasRole('manager') ? route('manager.dashboard') : (auth()->user()->hasRole('staff') ? route('staff.dashboard') : route('member.dashboard')) }}" class="nav-link {{ request()->is('*dashboard*') ? 'active' : '' }}">

                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>

                    </a>
                </li>

                @if(auth()->check() && auth()->user()->hasRole('manager'))

                <li class="nav-item">
                    <a href="{{ route('roles.index') }}" class="nav-link">
                        <i class="bi bi-shield-plus"></i>
                        <span>Create Roles</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('permissions.index') }}" class="nav-link">
                        <i class="bi bi-key"></i>
                        <span>Create Permissions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('role.permissions.index') }}" class="nav-link">
                        <i class="bi bi-shield-lock"></i>
                        <span>Roles & Permissions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>

                @endif

                @if(auth()->check() && auth()->user()->hasRole('staff'))
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-receipt-cutoff"></i>
                        <span>Manage Expenses</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && auth()->user()->hasRole('member'))
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-credit-card-2-front-fill"></i>
                        <span>My Payments</span>
                    </a>
                </li>
                @endif

            </ul>

            @if(auth()->check())
            <div class="sidebar-user">
                <b>{{ auth()->user()->name }}</b>
            </div>
            @endif
        </aside>

        <div class="main-panel d-flex flex-column">
            <nav class="topbar px-3 px-lg-4 py-3">
                <div class="topbar-card px-3 px-lg-4 py-3">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-soft rounded-circle d-lg-none" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                                <i class="bi bi-list fs-5"></i>
                            </button>
                            <div>
                                <p class="topbar-title">@yield('page_title', 'Dashboard')</p>
                                <div class="topbar-subtitle">@yield('page_subtitle', 'Monitor activity and manage your workspace.')</div>
                            </div>
                        </div>

                        @if(auth()->check())
                        <div class="d-flex align-items-center gap-3 ms-auto">

                            <form method="POST" action="/logout" class="mb-0">
                                @csrf
                                <button class="btn btn-danger rounded-pill px-4 shadow-sm">
                                    Logout
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </nav>

            <main class="content-wrap">
                <div class="content-card">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            toast: true
            , position: "top-end"
            , title: "Success"
            , text: "{{ session('success') }}"
            , icon: "success"
            , showConfirmButton: false
            , timer: 3000
            , timerProgressBar: true
        });

    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            toast: true
            , position: "top-end"
            , title: "Error"
            , text: "{{ session('error') }}"
            , icon: "error"
            , showConfirmButton: false
            , timer: 3000
            , timerProgressBar: true
        });

    </script>
    @endif

    <script>
        const body = document.body;
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function closeSidebar() {
            body.classList.remove('sidebar-open');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                body.classList.toggle('sidebar-open');
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });

    </script>
</body>
</html>
