<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexPraxis IA - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <!-- Bootswatch Darkly -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/darkly/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #1a1a2e; }
        .sidebar { min-height: 100vh; background: #222736; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .sidebar .nav-link { color: #adb5bd; padding: 10px 20px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #375a7f; color: #fff; }
        .sidebar-logo { filter: drop-shadow(0 0 10px rgba(55,161,199,0.3)); max-width: 50px; }
        .topbar { background: #222736; border-bottom: 1px solid #2c2f3a; }
        .content-area { padding: 20px; }
        .card { background: #222736; border: 1px solid #2c2f3a; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column p-3" style="width: 250px;">
            <div class="d-flex align-items-center mb-4 text-white text-decoration-none">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="sidebar-logo me-2">
                <span class="fs-5 fw-bold ms-2">LexPraxis IA</span>
            </div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="bi bi-people me-2"></i> Clientes
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="bi bi-folder me-2"></i> Processos
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="bi bi-chat-dots me-2"></i> Chat IA
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 d-flex flex-column" style="width: calc(100% - 250px); overflow-x: hidden;">
            <!-- Topbar -->
            <header class="topbar p-3 d-flex justify-content-end align-items-center">
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
    @yield('scripts')
</body>
</html>
