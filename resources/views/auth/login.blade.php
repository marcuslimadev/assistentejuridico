<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LexPraxis IA - Login</title>
  <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
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
    body {
      min-height: 100vh;
      font-family: "Manrope", sans-serif;
      background:
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 28%),
        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.18), transparent 26%),
        linear-gradient(180deg, color-mix(in srgb, var(--bs-body-bg) 94%, #ffffff 6%) 0%, var(--bs-body-bg) 100%);
    }

    [data-theme-mode="dark"] body {
      background:
        radial-gradient(circle at top left, rgba(37, 99, 235, 0.24), transparent 28%),
        radial-gradient(circle at bottom right, rgba(6, 182, 212, 0.18), transparent 22%),
        linear-gradient(180deg, #081120 0%, #0f172a 100%);
    }

    .auth-shell {
      min-height: 100vh;
      padding: 2rem 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .auth-card {
      width: 100%;
      max-width: 1080px;
      border-radius: 30px;
      border: 1px solid color-mix(in srgb, var(--bs-border-color) 82%, transparent 18%);
      background: color-mix(in srgb, var(--bs-body-bg) 84%, white 16%);
      box-shadow: 0 28px 80px rgba(15, 23, 42, 0.18);
      overflow: hidden;
    }

    [data-theme-mode="dark"] .auth-card {
      background: rgba(15, 23, 42, 0.82);
      box-shadow: 0 28px 80px rgba(2, 6, 23, 0.42);
    }

    .auth-hero {
      padding: 2.5rem;
      background: linear-gradient(160deg, color-mix(in srgb, var(--bs-primary) 20%, transparent 80%), transparent 50%), linear-gradient(180deg, color-mix(in srgb, var(--bs-tertiary-bg) 86%, transparent 14%), transparent 100%);
    }

    .auth-form {
      padding: 2.5rem;
    }

    .theme-toggle {
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.6rem 0.95rem;
      border-radius: 999px;
      border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%);
      background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%);
    }

    .theme-toggle .form-check-input {
      width: 2.8rem;
      height: 1.4rem;
      margin: 0;
      cursor: pointer;
    }

    .logo-section img { width: 210px; max-width: 100%; margin-bottom: 1.25rem; filter: drop-shadow(0 14px 24px rgba(56, 189, 248, 0.26)); }
    .eyebrow { text-transform: uppercase; letter-spacing: 0.18em; font-size: 0.74rem; font-weight: 800; color: var(--bs-primary); }
    .hero-copy { max-width: 28rem; color: var(--bs-secondary-color); line-height: 1.7; }
    .metric-strip { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.9rem; margin-top: 1.75rem; }
    .metric-chip { padding: 1rem; border-radius: 20px; background: color-mix(in srgb, var(--bs-body-bg) 70%, var(--bs-tertiary-bg) 30%); border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%); }
    .form-floating > .form-control { background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%); color: var(--bs-body-color); border-color: color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%); }
    .form-floating > .form-control:focus { background: color-mix(in srgb, var(--bs-body-bg) 78%, var(--bs-tertiary-bg) 22%); color: var(--bs-body-color); box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-primary) 18%, transparent 82%); }
    .form-floating > label { color: var(--bs-secondary-color); }
    .btn-entrar { font-weight: 700; letter-spacing: 0.03em; padding: 0.95rem; border-radius: 14px; }
    .divider { display: flex; align-items: center; gap: 12px; margin: 1.5rem 0; color: var(--bs-secondary-color); font-size: 0.85rem; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: color-mix(in srgb, var(--bs-border-color) 75%, transparent 25%); }

    @media (max-width: 991.98px) {
      .auth-hero,
      .auth-form {
        padding: 1.75rem;
      }

      .metric-strip {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="auth-shell">
    <div class="auth-card row g-0">
      <div class="col-lg-6 auth-hero d-flex flex-column justify-content-between">
        <div class="d-flex justify-content-end mb-4">
          <label class="theme-toggle" for="themeToggle">
            <i class="bi bi-brightness-high text-primary"></i>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" id="themeToggle">
            </div>
            <i class="bi bi-moon-stars text-primary"></i>
          </label>
        </div>
        <div class="logo-section">
          <div class="eyebrow mb-3">Plataforma jurídica</div>
          <img src="{{ asset('logo.png') }}" alt="LexPraxis IA">
          <h1 class="display-6 fw-bold mb-3">LexPraxis IA</h1>
          <p class="hero-copy mb-0">Bootswatch aplicado do jeito certo: componentes Bootstrap consistentes, contraste previsível e visual coerente em claro e escuro.</p>
        </div>
        <div class="metric-strip">
          <div class="metric-chip">
            <div class="small text-uppercase text-primary fw-bold mb-2">Operação</div>
            <div class="fw-semibold">Gestão de clientes, processos, tarefas e prazos em um único fluxo.</div>
          </div>
          <div class="metric-chip">
            <div class="small text-uppercase text-primary fw-bold mb-2">Assistência</div>
            <div class="fw-semibold">IA integrada para apoiar análise, redação e rotina do escritório.</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 auth-form d-flex flex-column justify-content-center">
        <div class="mb-4">
          <div class="eyebrow mb-2">Acesso seguro</div>
          <h2 class="fw-bold mb-2">Entre na sua conta</h2>
          <p class="text-body-secondary mb-0">Use seu e-mail para continuar no ambiente de trabalho.</p>
        </div>
        <form method="POST" action="{{ route('login.post') }}">
          @csrf
          <div class="form-floating mb-3">
            <input type="text" name="email" id="email" class="form-control" placeholder="E-mail ou telefone" autocomplete="username" required value="{{ old('email') }}">
            <label for="email">E-mail ou telefone</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" name="password" id="password" class="form-control" placeholder="Senha" autocomplete="current-password" required>
            <label for="password">Senha</label>
          </div>
          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-entrar">Entrar</button>
          </div>
          @if ($errors->any())
            <div class="alert alert-danger mt-3 mb-0 text-center py-2" role="alert">
              {{ $errors->first() }}
            </div>
          @endif
        </form>
        <div class="divider"><span>ou</span></div>
        <p class="text-center mb-0 text-body-secondary">
          Não tem conta?
          <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Registre-se</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const themeMap = { light: "flatly", dark: "darkly" };
      const stylesheet = document.getElementById("themeStylesheet");
      const toggle = document.getElementById("themeToggle");
      const themeColor = document.querySelector('meta[name="theme-color"]');
      if (!stylesheet || !toggle) return;

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
</body>
</html>
