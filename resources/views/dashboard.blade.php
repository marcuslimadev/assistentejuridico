<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>LexPraxis IA - Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/darkly/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h1>Dashboard Principal</h1>
    <p>Bem-vindo ao LexPraxis IA, {{ auth()->user()->name }}.</p>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-danger" type="submit">Sair</button>
    </form>
  </div>
</body>
</html>
