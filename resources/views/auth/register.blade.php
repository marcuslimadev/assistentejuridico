<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LexPraxis IA - Registro</title>
  <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
  <meta name="theme-color" content="#0f172a">
  <script>
    (function () {
      const preferredTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "darkly" : "flatly";
      const theme = localStorage.getItem("lp_theme") || preferredTheme;
      const darkThemes = ["cyborg", "darkly", "quartz", "slate", "solar", "superhero", "vapor"];
      document.documentElement.setAttribute("data-theme-name", theme);
      document.documentElement.setAttribute("data-theme-mode", darkThemes.includes(theme) ? "dark" : "light");
      document.write('<link id="themeStylesheet" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/' + theme + '/bootstrap.min.css">');
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
      max-width: 1120px;
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

    .theme-toggle-label { display: flex; flex-direction: column; gap: 0.1rem; line-height: 1.1; text-align: left; }
    .theme-toggle-label strong { font-size: 0.9rem; font-weight: 800; }
    .theme-toggle-label span { font-size: 0.76rem; color: var(--bs-secondary-color); }
    .theme-current-name { text-transform: capitalize; }
    .theme-picker { position: relative; }
    .theme-panel { position: absolute; top: calc(100% + 0.75rem); right: 0; width: min(28rem, calc(100vw - 2rem)); padding: 1rem; border-radius: 24px; border: 1px solid color-mix(in srgb, var(--bs-border-color) 82%, transparent 18%); background: color-mix(in srgb, var(--bs-body-bg) 90%, var(--bs-tertiary-bg) 10%); box-shadow: 0 28px 80px rgba(15, 23, 42, 0.2); opacity: 0; pointer-events: none; transform: translateY(-6px); transition: opacity 0.18s ease, transform 0.18s ease; z-index: 1050; }
    .theme-panel.is-open { opacity: 1; pointer-events: auto; transform: translateY(0); }
    .theme-panel-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 0.9rem; }
    .theme-panel-header h6 { margin: 0; font-weight: 800; }
    .theme-panel-header p { margin: 0.2rem 0 0; color: var(--bs-secondary-color); font-size: 0.84rem; }
    .theme-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; max-height: 22rem; overflow: auto; padding-right: 0.15rem; }
    .theme-option { width: 100%; padding: 0.8rem; border-radius: 18px; border: 1px solid color-mix(in srgb, var(--bs-border-color) 78%, transparent 22%); background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%); text-align: left; color: var(--bs-body-color); transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease; }
    .theme-option:hover { transform: translateY(-2px); border-color: color-mix(in srgb, var(--bs-primary) 36%, transparent 64%); box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12); }
    .theme-option.is-active { border-color: color-mix(in srgb, var(--bs-primary) 58%, transparent 42%); box-shadow: 0 0 0 0.22rem color-mix(in srgb, var(--bs-primary) 16%, transparent 84%); }
    .theme-swatch { display: flex; gap: 0.35rem; margin-bottom: 0.65rem; }
    .theme-swatch span { display: block; width: 100%; height: 0.62rem; border-radius: 999px; }
    .theme-name { display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; font-weight: 800; font-size: 0.9rem; }
    .theme-kind { font-size: 0.72rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: var(--bs-secondary-color); }

    .eyebrow { text-transform: uppercase; letter-spacing: 0.18em; font-size: 0.74rem; font-weight: 800; color: var(--bs-primary); }
    .hero-copy { max-width: 30rem; color: var(--bs-secondary-color); line-height: 1.7; }
    .logo-image { width: 320px; max-width: 100%; margin-bottom: 1rem; filter: drop-shadow(0 14px 24px rgba(56, 189, 248, 0.26)); }
    .metric-strip { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.9rem; margin-top: 1.75rem; }
    .metric-chip { padding: 1rem; border-radius: 20px; background: color-mix(in srgb, var(--bs-body-bg) 70%, var(--bs-tertiary-bg) 30%); border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%); }
    .form-control { border-radius: 14px; padding: 0.95rem 1rem; background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%); color: var(--bs-body-color); border-color: color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%); }
    .form-control:focus { background: color-mix(in srgb, var(--bs-body-bg) 78%, var(--bs-tertiary-bg) 22%); color: var(--bs-body-color); box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-primary) 18%, transparent 82%); }
    .register-btn { border-radius: 14px; padding: 0.95rem 1rem; font-weight: 700; }

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
          <div class="theme-picker">
            <button type="button" class="theme-toggle" id="themePickerButton" aria-expanded="false" aria-controls="themePanel">
              <i class="bi bi-brush text-primary"></i>
              <span class="theme-toggle-label">
                <strong>Tema visual</strong>
                <span class="theme-current-name" id="themeCurrentName">Darkly</span>
              </span>
              <i class="bi bi-chevron-down text-primary"></i>
            </button>
            <div class="theme-panel" id="themePanel" role="dialog" aria-label="Selecionar tema">
              <div class="theme-panel-header">
                <div>
                  <h6>Bootswatch completo</h6>
                  <p>Escolha qualquer tema oficial para a experiência de cadastro e do painel.</p>
                </div>
              </div>
              <div class="theme-grid" id="themeGrid"></div>
            </div>
          </div>
        </div>
        <div>
          <div class="eyebrow mb-3">Nova conta</div>
          <img src="{{ asset('logo.png') }}" alt="LexPraxis IA" class="logo-image">
          <h1 class="display-6 fw-bold mb-3">Crie sua base operacional</h1>
          <p class="hero-copy mb-0">Nada de Tailwind brigando com Bootstrap. Aqui o registro obedece a mesma base visual do restante do sistema e aceita qualquer tema oficial do Bootswatch.</p>
        </div>
        <div class="metric-strip">
          <div class="metric-chip">
            <div class="small text-uppercase text-primary fw-bold mb-2">Organização</div>
            <div class="fw-semibold">Cadastre clientes, distribua tarefas e acompanhe prazos com leitura clara.</div>
          </div>
          <div class="metric-chip">
            <div class="small text-uppercase text-primary fw-bold mb-2">Consistência</div>
            <div class="fw-semibold">Mesmo comportamento visual em claro e escuro nas telas de entrada e no painel interno.</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 auth-form d-flex flex-column justify-content-center">
        <div class="mb-4">
          <div class="eyebrow mb-2">Cadastro</div>
          <h2 class="fw-bold mb-2">Crie sua conta</h2>
          <p class="text-body-secondary mb-0">Preencha seus dados para acessar a plataforma.</p>
        </div>
        <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <div class="row g-3">
          <div class="col-12">
            <input type="text" name="nome" id="nome" placeholder="Nome completo" value="{{ old('nome') }}" class="form-control" required>
          </div>
          <div class="col-12">
            <input type="email" name="email" id="email" placeholder="E-mail" value="{{ old('email') }}" class="form-control" autocomplete="username" required>
          </div>
          <div class="col-12">
            <input type="tel" name="celular" id="celular" placeholder="Celular (com DDD)" value="{{ old('celular') }}" class="form-control" maxlength="15" autocomplete="tel-national" required>
          </div>
          <div class="col-md-6">
            <input type="password" name="senha" id="senha" placeholder="Senha" class="form-control" autocomplete="new-password" required>
          </div>
          <div class="col-md-6">
            <input type="password" name="senha_confirmation" id="confirmaSenha" placeholder="Confirme sua senha" class="form-control" autocomplete="new-password" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-4 register-btn">Registrar</button>

        @if ($errors->any())
          <div class="alert alert-danger mt-3 mb-0 text-center py-2" role="alert">
            {{ $errors->first() }}
          </div>
        @endif

        @if (session('success'))
          <div class="alert alert-success mt-3 mb-0 text-center py-2" role="alert">
            {{ session('success') }}
          </div>
        @endif
      </form>

      <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Já tem conta? Entrar</a>
      </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const themes = [
        { name: "cerulean", label: "Cerulean", kind: "claro", swatches: ["#033c73", "#2fa4e7", "#ffffff"] },
        { name: "cosmo", label: "Cosmo", kind: "claro", swatches: ["#2780e3", "#373a3c", "#ffffff"] },
        { name: "cyborg", label: "Cyborg", kind: "escuro", swatches: ["#060606", "#2a9fd6", "#adafae"] },
        { name: "darkly", label: "Darkly", kind: "escuro", swatches: ["#375a7f", "#00bc8c", "#222222"] },
        { name: "flatly", label: "Flatly", kind: "claro", swatches: ["#2c3e50", "#18bc9c", "#ffffff"] },
        { name: "journal", label: "Journal", kind: "claro", swatches: ["#eb6864", "#4e5d6c", "#ffffff"] },
        { name: "litera", label: "Litera", kind: "claro", swatches: ["#4582ec", "#adb5bd", "#ffffff"] },
        { name: "lumen", label: "Lumen", kind: "claro", swatches: ["#158cba", "#f6f6f6", "#ffffff"] },
        { name: "lux", label: "Lux", kind: "claro", swatches: ["#1a1a1a", "#f8f9fa", "#ffffff"] },
        { name: "materia", label: "Materia", kind: "claro", swatches: ["#2196f3", "#ff9800", "#ffffff"] },
        { name: "minty", label: "Minty", kind: "claro", swatches: ["#78c2ad", "#f3969a", "#ffffff"] },
        { name: "morph", label: "Morph", kind: "claro", swatches: ["#6e00ff", "#e5d9ff", "#f7f4ff"] },
        { name: "pulse", label: "Pulse", kind: "claro", swatches: ["#593196", "#ff7851", "#ffffff"] },
        { name: "quartz", label: "Quartz", kind: "escuro", swatches: ["#2b1f5f", "#e83283", "#170f2f"] },
        { name: "sandstone", label: "Sandstone", kind: "claro", swatches: ["#325d88", "#93c54b", "#f8f5f0"] },
        { name: "simplex", label: "Simplex", kind: "claro", swatches: ["#d9230f", "#469408", "#ffffff"] },
        { name: "sketchy", label: "Sketchy", kind: "claro", swatches: ["#333333", "#17a2b8", "#ffffff"] },
        { name: "slate", label: "Slate", kind: "escuro", swatches: ["#3a3f44", "#7a8288", "#272b30"] },
        { name: "solar", label: "Solar", kind: "escuro", swatches: ["#b58900", "#2aa198", "#002b36"] },
        { name: "spacelab", label: "Spacelab", kind: "claro", swatches: ["#446e9b", "#d9534f", "#ffffff"] },
        { name: "superhero", label: "Superhero", kind: "escuro", swatches: ["#df691a", "#5bc0de", "#2b3e50"] },
        { name: "united", label: "United", kind: "claro", swatches: ["#e95420", "#772953", "#ffffff"] },
        { name: "vapor", label: "Vapor", kind: "escuro", swatches: ["#6f42c1", "#32fbe2", "#190831"] },
        { name: "yeti", label: "Yeti", kind: "claro", swatches: ["#008cba", "#43ac6a", "#ffffff"] },
        { name: "zephyr", label: "Zephyr", kind: "claro", swatches: ["#3459e6", "#2fb380", "#ffffff"] }
      ];
      const stylesheet = document.getElementById("themeStylesheet");
      const pickerButton = document.getElementById("themePickerButton");
      const panel = document.getElementById("themePanel");
      const grid = document.getElementById("themeGrid");
      const currentName = document.getElementById("themeCurrentName");
      const themeColor = document.querySelector('meta[name="theme-color"]');
      if (!stylesheet || !pickerButton || !panel || !grid || !currentName) return;

      const darkThemes = new Set(themes.filter((theme) => theme.kind === "escuro").map((theme) => theme.name));

      const setPanelState = function (isOpen) {
        panel.classList.toggle("is-open", isOpen);
        pickerButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
      };

      const renderGrid = function () {
        grid.innerHTML = themes.map((theme) => `
          <button type="button" class="theme-option" data-theme="${theme.name}">
            <span class="theme-swatch">
              ${theme.swatches.map((color) => `<span style="background:${color}"></span>`).join("")}
            </span>
            <span class="theme-name">
              <span>${theme.label}</span>
              <span class="theme-kind">${theme.kind}</span>
            </span>
          </button>
        `).join("");
      };

      const updateActiveOption = function (themeName) {
        Array.from(grid.querySelectorAll(".theme-option")).forEach((option) => {
          option.classList.toggle("is-active", option.dataset.theme === themeName);
        });
      };

      const applyTheme = function (themeName) {
        const theme = themes.find((item) => item.name === themeName) || themes.find((item) => item.name === "darkly");
        const mode = darkThemes.has(theme.name) ? "dark" : "light";
        document.documentElement.setAttribute("data-theme-name", theme.name);
        document.documentElement.setAttribute("data-theme-mode", mode);
        stylesheet.href = `https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/${theme.name}/bootstrap.min.css`;
        currentName.textContent = theme.label;
        localStorage.setItem("lp_theme", theme.name);
        updateActiveOption(theme.name);
        if (themeColor) {
          themeColor.setAttribute("content", mode === "dark" ? "#0f172a" : "#f8fafc");
        }
      };

      renderGrid();
      applyTheme(localStorage.getItem("lp_theme") || document.documentElement.getAttribute("data-theme-name") || "darkly");
      pickerButton.addEventListener("click", function () {
        setPanelState(!panel.classList.contains("is-open"));
      });
      grid.addEventListener("click", function (event) {
        const option = event.target.closest(".theme-option");
        if (!option) return;
        applyTheme(option.dataset.theme);
        setPanelState(false);
      });
      document.addEventListener("click", function (event) {
        if (!event.target.closest(".theme-picker")) {
          setPanelState(false);
        }
      });
    })();

    const celularInput = document.getElementById("celular");
    celularInput.addEventListener("blur", function (e) {
      let v = e.target.value.replace(/\D/g, "").slice(0, 11);
      if (v.length === 11) {
        const ddd = v.slice(0, 2);
        const prefixo = v.slice(2, 7);
        const sufixo = v.slice(7, 11);
        e.target.value = `(${ddd}) ${prefixo}-${sufixo}`;
      }
    });
  </script>
</body>
</html>
