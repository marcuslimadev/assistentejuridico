<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LexPraxis IA - Registro</title>
  <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
  @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 flex items-center justify-center py-8 sm:py-0">
  <div class="w-full max-w-xs sm:max-w-md mx-auto px-2">
    <div class="w-full text-center mt-4">
      <img src="{{ asset('logo.png') }}" alt="LexPraxis IA" class="mx-auto w-40 sm:w-48 mb-2">
    </div>
    <div class="bg-white rounded-2xl shadow-xl px-4 py-8 sm:px-8 sm:py-10 flex flex-col items-center border border-blue-100">
      <div class="w-full flex flex-col items-center mb-4">
        <div class="text-xl sm:text-2xl font-bold text-blue-700 mb-2 tracking-tight">Assistente Jurídico</div>
        <div class="text-base text-gray-600 mb-3">Crie sua conta</div>
      </div>
      
      <form method="POST" action="{{ route('register.post') }}" class="w-full">
        @csrf
        <div class="w-full space-y-4">
          <input type="text" name="nome" id="nome" placeholder="Nome completo" value="{{ old('nome') }}"
            class="w-full px-4 py-4 rounded-xl border border-blue-100 bg-blue-50 text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-400 outline-none transition text-base sm:text-lg"
            required>
          
          <input type="email" name="email" id="email" placeholder="E-mail" value="{{ old('email') }}"
            class="w-full px-4 py-4 rounded-xl border border-blue-100 bg-blue-50 text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-400 outline-none transition text-base sm:text-lg"
            autocomplete="username" required>
            
          <input type="tel" name="celular" id="celular" placeholder="Celular (com DDD)" value="{{ old('celular') }}"
            class="w-full px-4 py-4 rounded-xl border border-blue-100 bg-blue-50 text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-400 outline-none transition text-base sm:text-lg"
            maxlength="15" autocomplete="tel-national" required>
            
          <input type="password" name="senha" id="senha" placeholder="Senha"
            class="w-full px-4 py-4 rounded-xl border border-blue-100 bg-blue-50 text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-400 outline-none transition text-base sm:text-lg"
            autocomplete="new-password" required>
            
          <input type="password" name="senha_confirmation" id="confirmaSenha" placeholder="Confirme sua senha"
            class="w-full px-4 py-4 rounded-xl border border-blue-100 bg-blue-50 text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-400 outline-none transition text-base sm:text-lg"
            autocomplete="new-password" required>
        </div>
        
        <button type="submit"
          class="w-full mt-6 py-4 rounded-xl font-semibold text-base sm:text-lg bg-gradient-to-r from-blue-500 to-blue-400 text-white shadow-lg hover:scale-[1.03] hover:from-blue-600 hover:to-blue-500 transition active:scale-95 mb-2">
          Registrar
        </button>
        
        @if ($errors->any())
          <div class="mt-2 w-full text-center text-red-500 bg-red-100 border border-red-200 px-4 py-2 rounded-xl font-medium text-sm sm:text-base">
            {{ $errors->first() }}
          </div>
        @endif
        
        @if (session('success'))
          <div class="mt-2 w-full text-center text-green-600 bg-green-100 border border-green-200 px-4 py-2 rounded-xl font-medium text-sm sm:text-base">
            {{ session('success') }}
          </div>
        @endif
      </form>

      <div class="mt-6 w-full flex items-center justify-center gap-2">
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800 transition text-sm sm:text-base">
          Já tem conta? Entrar
        </a>
      </div>
    </div>
  </div>

  <script>
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
