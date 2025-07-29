document.getElementById("loginForm").onsubmit = async (e) => {
  e.preventDefault();
  const ok = await login(email.value, senha.value);
  if (ok) {
    showApp();
    carregarHistorico();
  } else {
    alert("Login inválido.");
  }
};

document.getElementById("registerForm").onsubmit = async (e) => {
  e.preventDefault();
  const msg = await register(nome.value, regEmail.value, regSenha.value);
  alert(msg);
  showLogin();
};

document.getElementById("formConsulta").onsubmit = async (e) => {
  e.preventDefault();
  const texto = mensagem.value.trim();
  if (!texto) return;
  mensagens.innerHTML += `<div><strong>Você:</strong> ${texto}</div>`;
  mensagem.value = '';
  const resposta = await consultar(texto);
  mensagens.innerHTML += `<div><strong>Assistente:</strong> ${resposta}</div>`;
    carregarHistorico();
};
