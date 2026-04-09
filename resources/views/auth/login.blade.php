<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LexPraxis IA - Login</title>
  <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/darkly/bootstrap.min.css">
  <style>
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #1a1a2e; }
    .login-card { width: 100%; max-width: 420px; padding: 2.5rem 2rem; }
    .logo-section { text-align: center; margin-bottom: 2rem; }
    .logo-section img { width: 210px; max-width: 100%; margin-bottom: 1.25rem; filter: drop-shadow(0 0 22px rgba(55,161,199,0.42)); }
    .logo-section h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
    .logo-section p { font-size: 0.9rem; color: #adb5bd; }
    .form-floating .form-control { background-color: #2c2f3a; border-color: #3d4155; color: #e9ecef; }
    .form-floating .form-control:focus { background-color: #2c2f3a; border-color: #375a7f; color: #e9ecef; box-shadow: 0 0 0 0.2rem rgba(55,90,127,0.35); }
    .form-floating label { color: #868e96; }
    .btn-entrar { font-weight: 600; letter-spacing: 0.03em; padding: 0.75rem; }
    .divider { display: flex; align-items: center; gap: 12px; margin: 1.5rem 0; color: #6c757d; font-size: 0.85rem; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #2c2f3a; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="card shadow-lg border-0" style="background:#222736;">
      <div class="card-body p-4 p-md-5">
        <div class="logo-section">
          <img src="{{ asset('logo.png') }}" alt="LexPraxis IA">
          <h1 class="text-white">LexPraxis IA</h1>
          <p>Entre na sua conta para continuar</p>
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
            <button type="submit" class="btn btn-primary btn-entrar">
              Entrar
            </button>
          </div>
          @if ($errors->any())
            <div class="alert alert-danger mt-3 mb-0 text-center py-2" role="alert">
              {{ $errors->first() }}
            </div>
          @endif
        </form>
        <div class="divider"><span>ou</span></div>
        <p class="text-center mb-0" style="font-size:.9rem; color:#adb5bd;">
          Não tem conta?
          <a href="{{ route('register') }}" class="text-info fw-semibold text-decoration-none">Registre-se</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
