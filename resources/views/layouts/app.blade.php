<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexPraxis IA - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2c2f3a">
    <script>
        (function () {
            const savedTheme = localStorage.getItem("lp_theme") || "darkly";
            document.write('<link id="themeStylesheet" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/' + savedTheme + '/bootstrap.min.css">');
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: var(--bs-body-bg); }
        .sidebar { min-height: 100vh; background: var(--bs-tertiary-bg); box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .sidebar .nav-link { color: var(--bs-secondary-color); padding: 10px 20px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: var(--bs-primary); color: #fff; }
        .sidebar-logo { filter: drop-shadow(0 0 18px rgba(55,161,199,0.32)); width: 150px; max-width: 100%; }
        .topbar { background: var(--bs-tertiary-bg); border-bottom: 1px solid var(--bs-border-color); }
        .content-area { padding: 20px; }
        .card { border-radius: 10px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column p-3" style="width: 250px;">
            <div class="d-flex flex-column align-items-start mb-4 text-white text-decoration-none gap-2">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="sidebar-logo me-2">
                <span class="fs-5 fw-bold">LexPraxis IA</span>
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
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 d-flex flex-column" style="width: calc(100% - 250px); overflow-x: hidden;">
            <!-- Topbar -->
            <header class="topbar p-3 d-flex justify-content-end align-items-center gap-2">
                <select id="themeSelector" class="form-select form-select-sm" style="width: 170px;">
                    <option value="darkly">Darkly</option>
                    <option value="cosmo">Cosmo</option>
                    <option value="flatly">Flatly</option>
                    <option value="litera">Litera</option>
                    <option value="lux">Lux</option>
                    <option value="materia">Materia</option>
                    <option value="minty">Minty</option>
                    <option value="pulse">Pulse</option>
                    <option value="sandstone">Sandstone</option>
                    <option value="slate">Slate</option>
                    <option value="solar">Solar</option>
                    <option value="superhero">Superhero</option>
                    <option value="vapor">Vapor</option>
                    <option value="yeti">Yeti</option>
                    <option value="zephyr">Zephyr</option>
                </select>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <strong>{{ auth()->user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="dropdownUser1">
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
            </header>

            <!-- Page Content -->
            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const selector = document.getElementById("themeSelector");
            const stylesheet = document.getElementById("themeStylesheet");
            if (!selector || !stylesheet) return;
            const current = localStorage.getItem("lp_theme") || "darkly";
            selector.value = current;
            selector.addEventListener("change", function () {
                const theme = this.value;
                localStorage.setItem("lp_theme", theme);
                stylesheet.href = `https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/${theme}/bootstrap.min.css`;
            });
        })();
    </script>
    @yield('scripts')
</body>
</html>
