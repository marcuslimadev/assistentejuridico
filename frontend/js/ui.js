async function carregarHistorico() {
  const res = await fetch("http://localhost/assistentejuridico/backend/listar_historico.php");
  if (!res.ok) return;
  const dados = await res.json();
  const historico = document.getElementById("historico");
  historico.innerHTML = '';

  dados.forEach((item, i) => {
    const card = document.createElement("div");
    card.className = "accordion-item";
    card.innerHTML = `
      <h2 class="accordion-header" id="h${item.id}">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c${item.id}">
          ${item.numero_processo} — ${item.data_hora}
        </button>
      </h2>
      <div id="c${item.id}" class="accordion-collapse collapse" data-bs-parent="#historico">
        <div class="accordion-body">
          <div><strong>Resposta:</strong><br>${item.retorno_api}</div>
          <div class="text-end mt-2">
            <button class="btn btn-sm btn-outline-danger" onclick="excluirHistorico(${item.id})">Excluir</button>
          </div>
        </div>
      </div>`;
    historico.appendChild(card);
  });
}

async function excluirHistorico(id) {
  await fetch("http://localhost/assistentejuridico/backend/excluir_historico.php", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({id})
  });
  carregarHistorico();
}
