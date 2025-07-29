function showRegister() {
  loginForm.classList.add("d-none");
  registerForm.classList.remove("d-none");
}

function showLogin() {
  loginForm.classList.remove("d-none");
  registerForm.classList.add("d-none");
}

function showApp() {
  document.getElementById("auth").classList.add("d-none");
  document.getElementById("app").classList.remove("d-none");
  const historico = document.getElementById("historicoContainer");
  if (historico) historico.classList.remove("d-none");
}

function logout() {
  location.reload();
}

async function login(email, senha) {
  const res = await fetch('http://localhost/assistentejuridico/backend/login.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({email, senha})
  });
  return res.ok;
}

async function register(nome, email, senha) {
  const res = await fetch('http://localhost/assistentejuridico/backend/registro.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({nome, email, senha})
  });
  return res.text();
}

async function consultar(msg) {
  const res = await fetch('http://localhost/assistentejuridico/backend/chat.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({mensagem: msg})
  });
  return res.text();
}
