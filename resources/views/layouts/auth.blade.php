<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication') - Expense Tracker</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --auth-primary: #2563eb;
            --auth-primary-dark: #1d4ed8;
            --auth-text: #0f172a;
            --auth-muted: #64748b;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(14, 116, 144, 0.12), transparent 34%),
                linear-gradient(135deg, #f8fbff 0%, #eef2ff 52%, #f8fafc 100%);
            color: var(--auth-text);
        }

        .auth-shell {
            min-height: 100vh;
            padding: 1.5rem;
            max-width: 640px;
        }

        .auth-panel {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 32px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.86);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(16px);
        }

        .auth-form-side {
            padding: 2rem;
            background: rgba(255, 255, 255, 0.72);
            min-height: calc(100vh - 3rem);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-form-card {
            max-width: 460px;
            margin: 0 auto;
        }

        .auth-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 999px;
            padding: 0.45rem 0.75rem;
            background: rgba(37, 99, 235, 0.08);
            color: var(--auth-primary);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 1rem 0 0.5rem;
        }

        .auth-subtitle {
            color: var(--auth-muted);
            margin-bottom: 0;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .input-group-text {
            border-radius: 16px 0 0 16px;
            border-color: rgba(148, 163, 184, 0.35);
            background: #fff;
            color: #64748b;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .form-control {
            min-height: 52px;
            border-color: rgba(148, 163, 184, 0.35);
            border-radius: 16px;
            padding-left: 1rem;
            padding-right: 1rem;
            box-shadow: none;
        }

        .input-group .form-control {
            border-radius: 0 16px 16px 0;
        }

        .form-control:focus,
        .input-group:focus-within .input-group-text {
            border-color: rgba(37, 99, 235, 0.45);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.12);
        }

        .btn-auth {
            min-height: 52px;
            border-radius: 16px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--auth-primary), var(--auth-primary-dark));
            border: 0;
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.2);
        }

        .btn-auth:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }

        .auth-footer-link {
            color: var(--auth-primary);
        }

        .swal2-popup {
            border-radius: 24px !important;
        }

        @media (max-width: 991.98px) {
            .auth-form-side {
                padding: 1.5rem;
                min-height: calc(100vh - 3rem);
            }
        }

        @media (min-width: 992px) {
            .auth-form-side {
                min-height: 620px;
            }
        }
    </style>
</head>
<body>
    <div class="container auth-shell d-flex align-items-center justify-content-center">
        <div class="auth-panel">
            <div class="auth-form-side">
                <div class="auth-form-card w-100">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            toast: true,
            position: "top-end",
            title: "Success",
            text: "{{ session('success') }}",
            icon: "success",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            toast: true,
            position: "top-end",
            title: "Error",
            text: "{{ session('error') }}",
            icon: "error",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    @endif
</body>
</html>
