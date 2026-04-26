<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
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
            overflow-x: clip;
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
            justify-content: space-between;
            gap: 0.7rem;
            padding-bottom: 1.2rem;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-brand-content {
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .sidebar-close-btn {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sidebar-close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
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
            position: relative;
        }

        .sidebar .nav-link i {
            width: 1.2rem;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: var(--sidebar-active);
            transform: translateX(4px);
            box-shadow: inset 0 0 0 1px rgba(96, 165, 250, 0.18);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: var(--sidebar-active);
            transform: translateX(4px);
            box-shadow: inset 0 0 0 1px rgba(96, 165, 250, 0.18);
            border-left: 3px solid var(--brand);
        }

        .sidebar-user {
            padding: 0.85rem;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-user-toggle {
            width: 100%;
            border: 0;
            background: transparent;
            color: #fff;
            padding: 0;
        }

        .sidebar-user-toggle:focus-visible {
            outline: 2px solid rgba(96, 165, 250, 0.75);
            outline-offset: 4px;
        }

        .sidebar-user-avatar {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.95), rgba(37, 99, 235, 0.95));
            color: #fff;
            font-weight: 800;
            font-size: 1rem;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        }

        .sidebar-user-name {
            color: #fff;
            font-weight: 700;
        }

        .sidebar-user-role {
            color: rgba(255, 255, 255, 0.62);
            font-size: 0.82rem;
        }

        .sidebar-user-menu {
            margin-bottom: 0.85rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            inset: auto 0 100% 0 !important;
            transform: none !important;
        }

        .sidebar-user-menu .dropdown-item {
            border-radius: 12px;
            color: #e2e8f0;
            font-weight: 600;
            padding: 0.7rem 0.85rem;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .sidebar-user-menu .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .sidebar-user-menu .dropdown-item.text-danger {
            color: #fca5a5 !important;
        }

        .sidebar-user-menu .dropdown-item.text-danger:hover {
            color: #fff !important;
            background: rgba(239, 68, 68, 0.35);
        }

        .sidebar-user small {
            color: rgba(255, 255, 255, 0.62);
        }

        .main-panel {
            min-width: 0;
            flex: 1;
            width: 100%;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.88);
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
            width: 100%;
            overflow: hidden;
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            overflow-x: auto;
            overflow-y: visible;
        }

        /* Enhanced Table Responsive Styles */
        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        .table-responsive-wrapper table {
            width: 100%;
            min-width: 768px;
            margin-bottom: 0;
        }

        @media (max-width: 767.98px) {
            .table-responsive-wrapper table {
                min-width: 600px;
            }
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive>.table {
            min-width: 600px;
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }

        .page-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: nowrap;
        }

        .inline-form {
            display: inline-block;
            margin: 0;
        }

        .field-label-cell {
            width: 100px;
        }

        .actions-column {
            width: 150px;
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

            .sidebar-close-btn {
                display: flex;
                align-items: center;
                justify-content: center;
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
                padding: 1rem;
            }

            .topbar-title,
            .topbar-subtitle {
                max-width: 200px;
            }

            .btn-block-mobile {
                width: 100%;
                display: block;
            }

            .page-actions {
                width: 100%;
            }

            .page-actions>* {
                flex: 1 1 auto;
            }

            .form-stack-mobile>* {
                width: 100%;
                margin-bottom: 0.75rem;
            }

            .form-stack-mobile>*:last-child {
                margin-bottom: 0;
            }
        }

        @media (max-width: 575.98px) {

            .topbar-title,
            .topbar-subtitle {
                max-width: 150px;
            }

            .btn-full-mobile {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .content-card {
                padding: 0.75rem;
            }
        }

        @media (min-width: 992px) {
            .sidebar {
                position: sticky;
                top: 0;
            }

            .sidebar-close-btn {
                display: none !important;
            }
        }

    </style>
</head>
<body>
    @php
    $currentUser = auth()->user();
    $currentUserRoles = $currentUser ? $currentUser->roleNames()->map(fn ($role) => str_replace('_', ' ', $role))->map(fn ($role) => \Illuminate\Support\Str::title($role))->join(', ') : '';
    $nameParts = $currentUser ? collect(preg_split('/\s+/', trim($currentUser->name)))->filter()->values() : collect();
    $firstInitial = $nameParts->isNotEmpty() ? strtoupper(substr($nameParts->first(), 0, 1)) : '';
    $lastInitial = $nameParts->count() > 1 ? strtoupper(substr($nameParts->last(), 0, 1)) : '';
    $currentUserInitial = $firstInitial . $lastInitial;
    @endphp

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="d-flex app-shell">
        <aside class="sidebar d-flex flex-column p-3 p-lg-4" id="appSidebar">

            {{-- BRAND --}}
            <div class="sidebar-brand mb-4">
                <div class="sidebar-brand-content">
                    <span class="sidebar-brand-icon">
                        <i class="bi bi-wallet2"></i>
                    </span>
                    <div>
                        <h1 class="h5 fw-bold mb-1">Expense Tracker</h1>
                        <div class="sidebar-subtitle">Smart admin workspace</div>
                    </div>
                </div>

                <button class="sidebar-close-btn" id="sidebarCloseBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            @php
            $menus = config('sidebar');
            @endphp

            <ul class="nav flex-column mb-4" id="sidebarNav">

                @foreach($menus as $menu)

                @if(is_null($menu['permission']) || auth()->user()->hasPermission($menu['permission']))

                <li class="nav-item">
                    <a href="{{ route($menu['route']) }}" class="nav-link {{ request()->routeIs(...($menu['active'] ?? [$menu['route']])) ? 'active' : '' }}">
                        <i class="bi {{ $menu['icon'] }}"></i>
                        <span>{{ $menu['title'] }}</span>
                    </a>
                </li>

                @endif

                @endforeach

            </ul>

            @if($currentUser)
            <div class="sidebar-user dropdown mt-auto">
                <button class="sidebar-user-toggle dropdown-toggle d-flex align-items-center gap-3 text-start" id="sidebarUserToggle" type="button" aria-expanded="false">
                    <span class="sidebar-user-avatar">{{ $currentUserInitial }}</span>
                    <span class="min-width-0">
                        <span class="sidebar-user-name d-block text-truncate">{{ $currentUser->name }}</span>
                        {{-- <small class="sidebar-user-role d-block text-truncate">{{ $currentUserRoles ?: 'User' }}</small> --}}
                    </span>
                </button>

                <div class="sidebar-user-menu dropdown-menu dropdown-menu-dark border-0 shadow-sm w-100" aria-labelledby="sidebarUserToggle">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </aside>

        {{-- MAIN --}}
        <div class="main-panel d-flex flex-column">

            {{-- TOPBAR --}}
            <nav class="topbar px-3 px-lg-4 py-3">
                <div class="topbar-card px-3 px-lg-4 py-3">

                    <div class="d-flex align-items-center justify-content-between gap-3">

                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-soft d-lg-none" id="sidebarToggle">
                                <i class="bi bi-list fs-5"></i>
                            </button>

                            <div>
                                <p class="topbar-title mb-0">
                                    @yield('page_title', 'Dashboard')
                                </p>
                                <div class="topbar-subtitle">
                                    @yield('page_subtitle', 'Monitor activity')
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </nav>

            {{-- CONTENT --}}
            <main class="content-wrap">
                <div class="content-card">
                    @if ($errors->any())
                    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

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

    <style>
        .min-width-0 {
            min-width: 0;
        }

        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive table {
            min-width: 600px;
            width: 100%;
        }

        @media (max-width: 767.98px) {
            .row-cols-mobile-stack>[class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 1rem;
            }

            .table-responsive,
            [class*="table-responsive"] {
                overflow-x: auto !important;
            }
        }

        .form-label {
            font-weight: 500;
        }

        @media (max-width: 575.98px) {
            .btn-mobile-full {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .input-group-mobile-stack {
                flex-direction: column;
            }

            .input-group-mobile-stack> :not(:first-child) {
                margin-top: 0.5rem;
                border-radius: 0.375rem !important;
            }
        }

        /* Custom Timeline Styles */
        .timeline-wrapper {
            position: relative;
            padding-left: 0;
        }

        .timeline-item {
            position: relative;
        }

        .timeline-item:not(:last-child) {
            margin-bottom: 1rem;
        }

        /* Card Hover Effect */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        /* Table Styles */
        .table-borderless td {
            border: none;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .card-header {
                padding: 1rem !important;
            }

            .card-body {
                padding: 1rem !important;
            }

            .badge {
                font-size: 0.75rem;
            }

            h3 {
                font-size: 1.35rem;
            }
        }

        /* Custom Badge Colors */
        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        .text-decoration-line-through {
            text-decoration: line-through;
        }

        /* Smooth Animations */
        .rounded-4 {
            border-radius: 1rem !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

    </style>

    <script>
        // Sidebar toggle functionality
        const body = document.body;
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        const sidebarUserToggle = document.getElementById('sidebarUserToggle');
        const sidebarUserMenu = document.querySelector('.sidebar-user-menu');

        function closeSidebar() {
            body.classList.remove('sidebar-open');
        }

        function openSidebar() {
            body.classList.add('sidebar-open');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                openSidebar();
            });
        }

        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebar();
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeSidebar);
        }

        function closeSidebarUserMenu() {
            if (sidebarUserMenu && sidebarUserToggle) {
                sidebarUserMenu.classList.remove('show');
                sidebarUserToggle.setAttribute('aria-expanded', 'false');
            }
        }

        if (sidebarUserToggle && sidebarUserMenu) {
            sidebarUserToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = sidebarUserMenu.classList.toggle('show');
                sidebarUserToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', function(e) {
                if (!sidebarUserMenu.contains(e.target) && !sidebarUserToggle.contains(e.target)) {
                    closeSidebarUserMenu();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebarUserMenu();
                }
            });
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });

        const sidebarLinks = document.querySelectorAll('#sidebarNav .nav-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    setTimeout(closeSidebar, 150);
                }
                closeSidebarUserMenu();
            });
        });

    </script>

    @stack('scripts')
</body>
</html>
