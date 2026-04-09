<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexPraxis IA - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0f172a">
    <script>
        (function () {
            const themeMap = { light: "flatly", dark: "darkly" };
            const storedMode = localStorage.getItem("lp_theme_mode");
            const preferredMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            const mode = storedMode || preferredMode;
            document.documentElement.setAttribute("data-theme-mode", mode);
            document.write('<link id="themeStylesheet" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/' + themeMap[mode] + '/bootstrap.min.css">');
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --lp-radius-xl: 28px;
            --lp-radius-lg: 22px;
            --lp-shadow: 0 28px 80px rgba(15, 23, 42, 0.18);
            --lp-sidebar-width: 300px;
        }

        html, body {
            min-height: 100%;
        }

        body {
            font-family: "Manrope", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 28%),
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 24%),
                linear-gradient(180deg, color-mix(in srgb, var(--bs-body-bg) 94%, #ffffff 6%) 0%, var(--bs-body-bg) 100%);
            color: var(--bs-body-color);
        }

        [data-theme-mode="dark"] body {
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.24), transparent 28%),
                radial-gradient(circle at top right, rgba(6, 182, 212, 0.18), transparent 22%),
                linear-gradient(180deg, #081120 0%, #0f172a 100%);
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--lp-sidebar-width);
            min-height: 100vh;
            padding: 1.5rem;
            background: color-mix(in srgb, var(--bs-body-bg) 80%, var(--bs-tertiary-bg) 20%);
            border-right: 1px solid color-mix(in srgb, var(--bs-border-color) 75%, transparent 25%);
        }

        [data-theme-mode="dark"] .sidebar {
            background: rgba(8, 15, 28, 0.78);
            backdrop-filter: blur(22px);
        }

        .brand-card,
        .topbar,
        .content-card,
        .feature-panel,
        .metric-card,
        .card {
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 82%, transparent 18%);
            border-radius: var(--lp-radius-lg);
            background: color-mix(in srgb, var(--bs-body-bg) 84%, white 16%);
            box-shadow: var(--lp-shadow);
        }

        [data-theme-mode="dark"] .brand-card,
        [data-theme-mode="dark"] .topbar,
        [data-theme-mode="dark"] .content-card,
        [data-theme-mode="dark"] .feature-panel,
        [data-theme-mode="dark"] .metric-card,
        [data-theme-mode="dark"] .card {
            background: rgba(15, 23, 42, 0.78);
            box-shadow: 0 28px 80px rgba(2, 6, 23, 0.42);
        }

        .brand-card {
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .sidebar-logo {
            display: block;
            width: 150px;
            max-width: 100%;
            margin-bottom: 0.85rem;
            filter: drop-shadow(0 14px 24px rgba(56, 189, 248, 0.22));
        }

        .brand-kicker {
            margin-bottom: 0.35rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: var(--bs-primary);
            opacity: 0.9;
        }

        .brand-title {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .brand-copy {
            margin: 0.45rem 0 0;
            color: var(--bs-secondary-color);
            font-size: 0.93rem;
            line-height: 1.55;
        }

        .sidebar .nav {
            gap: 0.35rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.9rem 1rem;
            border-radius: 16px;
            color: var(--bs-body-color);
            font-weight: 700;
            transition: transform 0.18s ease, background-color 0.18s ease, border-color 0.18s ease;
            border: 1px solid transparent;
        }

        .sidebar .nav-link:hover {
            background: color-mix(in srgb, var(--bs-primary) 10%, transparent 90%);
            border-color: color-mix(in srgb, var(--bs-primary) 24%, transparent 76%);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, var(--bs-primary), color-mix(in srgb, var(--bs-primary) 72%, #22c55e 28%));
            box-shadow: 0 18px 34px rgba(37, 99, 235, 0.22);
        }

        .main-panel {
            flex: 1;
            min-width: 0;
            padding: 1.25rem;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .topbar-meta {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .topbar-meta strong {
            font-size: 1rem;
            font-weight: 800;
        }

        .topbar-meta span {
            color: var(--bs-secondary-color);
            font-size: 0.9rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
            border-radius: 999px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%);
            background: color-mix(in srgb, var(--bs-body-bg) 74%, var(--bs-tertiary-bg) 26%);
            color: var(--bs-body-color);
            cursor: pointer;
        }

        .theme-toggle i {
            color: var(--bs-primary);
        }

        .theme-toggle .form-check-input {
            width: 2.8rem;
            height: 1.4rem;
            margin: 0;
            cursor: pointer;
        }

        .content-area {
            padding: 0;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            margin: 0;
            font-size: clamp(1.6rem, 1.4rem + 0.8vw, 2.2rem);
            font-weight: 800;
        }

        .page-subtitle {
            margin: 0.35rem 0 0;
            color: var(--bs-secondary-color);
        }

        .card,
        .content-card,
        .metric-card,
        .feature-panel {
            padding: 1.5rem;
        }

        .metric-card {
            height: 100%;
            overflow: hidden;
        }

        .metric-icon {
            width: 3.25rem;
            height: 3.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            font-size: 1.4rem;
            background: color-mix(in srgb, var(--bs-primary) 12%, transparent 88%);
            color: var(--bs-primary);
        }

        .table,
        .table > :not(caption) > * > * {
            color: var(--bs-body-color);
            border-color: color-mix(in srgb, var(--bs-border-color) 72%, transparent 28%);
        }

        .table-hover tbody tr:hover {
            background: color-mix(in srgb, var(--bs-primary) 8%, transparent 92%);
        }

        .form-control,
        .form-select,
        .input-group-text {
            border-radius: 14px;
            border-color: color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%);
            background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%);
            color: var(--bs-body-color);
        }

        .form-control:focus,
        .form-select:focus {
            background: color-mix(in srgb, var(--bs-body-bg) 78%, var(--bs-tertiary-bg) 22%);
            color: var(--bs-body-color);
            border-color: color-mix(in srgb, var(--bs-primary) 60%, white 40%);
            box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-primary) 18%, transparent 82%);
        }

        .btn {
            border-radius: 14px;
            font-weight: 700;
        }

        .dropdown-menu {
            border-radius: 18px;
            border-color: color-mix(in srgb, var(--bs-border-color) 82%, transparent 18%);
            background: color-mix(in srgb, var(--bs-body-bg) 84%, var(--bs-tertiary-bg) 16%);
        }

        .dropdown-item {
            color: var(--bs-body-color);
        }

        @media (max-width: 991.98px) {
            .app-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .main-panel {
                padding-top: 0;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar d-flex flex-column">
            <div class="brand-card">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="sidebar-logo">
                <div class="brand-kicker">Plataforma jurídica</div>
                <h1 class="brand-title">LexPraxis IA</h1>
                <p class="brand-copy">Operação jurídica com visual limpo, contraste correto e tema consistente em claro e escuro.</p>
            </div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i> Clientes
                    </a>
                </li>
                <li>
                    <a href="{{ route('processos.index') }}" class="nav-link {{ request()->routeIs('processos.*') ? 'active' : '' }}">
                        <i class="bi bi-folder me-2"></i> Processos
                    </a>
                </li>
                <li>
                    <a href="{{ route('agendas.index') }}" class="nav-link {{ request()->routeIs('agendas.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event me-2"></i> Agenda
                    </a>
                </li>
                <li>
                    <a href="{{ route('prazos.index') }}" class="nav-link {{ request()->routeIs('prazos.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i> Prazos
                    </a>
                </li>
                <li>
                    <a href="{{ route('tarefas.index') }}" class="nav-link {{ request()->routeIs('tarefas.*') ? 'active' : '' }}">
                        <i class="bi bi-check2-square me-2"></i> Tarefas
                    </a>
                </li>
                <li>
                    <a href="{{ route('documentos.index') }}" class="nav-link {{ request()->routeIs('documentos.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Documentos
                    </a>
                </li>
                <li>
                    <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots me-2"></i> Chat IA
                    </a>
                </li>
            </ul>
        </aside>

        <div class="main-panel d-flex flex-column">
            <header class="topbar">
                <div class="topbar-meta">
                    <strong>@yield('title')</strong>
                    <span>{{ now()->format('d/m/Y') }} • Ambiente jurídico operacional</span>
                </div>
                <div class="topbar-actions">
                    <label class="theme-toggle mb-0" for="themeToggle">
                        <i class="bi bi-brightness-high"></i>
                        <span class="small fw-semibold">Claro</span>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="themeToggle">
                        </div>
                        <span class="small fw-semibold">Escuro</span>
                        <i class="bi bi-moon-stars"></i>
                    </label>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-body text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <strong>{{ auth()->user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#">Configurações</a></li>
                        <li><a class="dropdown-item" href="#">Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Sair</button>
                            </form>
                        </li>
                    </ul>
                </div>
                </div>
            </header>

            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const themeMap = { light: "flatly", dark: "darkly" };
            const stylesheet = document.getElementById("themeStylesheet");
            const toggle = document.getElementById("themeToggle");
            const themeColor = document.querySelector('meta[name="theme-color"]');

            if (!toggle || !stylesheet) return;

            const applyMode = function (mode) {
                document.documentElement.setAttribute("data-theme-mode", mode);
                stylesheet.href = `https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/${themeMap[mode]}/bootstrap.min.css`;
                toggle.checked = mode === "dark";
                localStorage.setItem("lp_theme_mode", mode);

                if (themeColor) {
                    themeColor.setAttribute("content", mode === "dark" ? "#0f172a" : "#f8fafc");
                }
            };

            applyMode(localStorage.getItem("lp_theme_mode") || document.documentElement.getAttribute("data-theme-mode") || "dark");

            toggle.addEventListener("change", function () {
                applyMode(this.checked ? "dark" : "light");
            });
        })();
    </script>
    @yield('scripts')
</body>
</html>
