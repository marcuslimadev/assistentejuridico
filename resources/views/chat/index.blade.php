@extends('layouts.app')
@section('title', 'Chat IA')
@section('content')
<div class="container-fluid h-100 d-flex flex-column">
    <div class="page-header">
        <div>
            <h2 class="page-title">Assistente Jurídico</h2>
            <p class="page-subtitle">Converse com a IA sem quebrar o tema visual do restante da plataforma.</p>
        </div>
        <div class="text-end">
            <div class="small text-body-secondary">Créditos DataJud</div>
            <div class="fw-bold fs-5" id="chatCreditsBalance">{{ auth()->user()->consulta_credits }}</div>
        </div>
    </div>

    <div class="chat-shell">
        <div class="content-card chat-stage overflow-hidden p-0">
            <div class="chat-intro-card">
                <div class="chat-kicker"><i class="bi bi-stars"></i>Assistência premium</div>
                <h5 class="fw-bold mb-2">Peça análises, rascunhos e sínteses sem sair do fluxo jurídico.</h5>
                <p class="text-body-secondary mb-0">O assistente foi reposicionado como um cockpit de apoio. Comece com um prompt pronto ou escreva diretamente sua demanda.</p>
                <div class="chat-prompt-grid">
                    <button type="button" class="chat-prompt" data-prompt="Resuma este processo em linguagem objetiva e liste os próximos passos práticos.">Resumir processo e próximos passos</button>
                    <button type="button" class="chat-prompt" data-prompt="Monte um rascunho inicial de petição com base nos fatos e pedidos principais.">Rascunho inicial de petição</button>
                    <button type="button" class="chat-prompt" data-prompt="Liste riscos processuais e pontos de atenção desta causa.">Riscos processuais e pontos de atenção</button>
                    <button type="button" class="chat-prompt" data-prompt="Crie uma mensagem clara para atualizar o cliente sobre o andamento do caso.">Atualização de cliente</button>
                </div>
            </div>

            <div id="chatBox" class="chat-box">
                <div class="message-row assistant">
                    <div class="message-bubble assistant">
                        Olá! Sou seu assistente jurídico com GPT-4o. Posso ajudar a resumir processos, estruturar peças e transformar informação dispersa em próximos passos claros.
                    </div>
                </div>
            </div>

            <div class="chat-composer">
                <form id="chatForm" class="d-flex gap-2 align-items-stretch">
                    @csrf
                    <input type="text" id="chatInput" class="form-control" placeholder="Digite sua dúvida, cole fatos do caso ou peça uma minuta..." autocomplete="off" required>
                    <button type="submit" id="btnSend" class="btn btn-primary px-4"><i class="bi bi-send-fill"></i></button>
                </form>
                <div class="text-body-secondary small mt-2">Exemplo: "resuma a fase atual do processo e proponha próximos passos".</div>
            </div>
        </div>

        <div class="chat-side-stack">
            <div class="chat-side-card">
                <div class="chat-kicker"><i class="bi bi-compass"></i>Como usar</div>
                <h6 class="fw-bold mb-2">Melhores pedidos</h6>
                <ul class="chat-side-list">
                    <li>
                        <i class="bi bi-1-circle-fill text-primary"></i>
                        <div>
                            <div class="fw-bold">Contexto objetivo</div>
                            <div class="text-body-secondary small">Descreva fatos, fase processual e objetivo do pedido.</div>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-2-circle-fill text-primary"></i>
                        <div>
                            <div class="fw-bold">Formato esperado</div>
                            <div class="text-body-secondary small">Peça resumo, checklist, minuta, estratégia ou mensagem ao cliente.</div>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-3-circle-fill text-primary"></i>
                        <div>
                            <div class="fw-bold">Critério prático</div>
                            <div class="text-body-secondary small">Solicite objetividade, tópicos ou linguagem técnica conforme a necessidade.</div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="chat-side-card">
                <div class="chat-kicker"><i class="bi bi-cash-coin"></i>Créditos</div>
                <h6 class="fw-bold mb-2">Consulta DataJud custa 1 crédito</h6>
                <p class="text-body-secondary small mb-3">Saldo atual: <strong id="chatCreditsBalanceSide">{{ auth()->user()->consulta_credits }}</strong> crédito(s).</p>
                <a href="{{ route('credits.index') }}" class="btn btn-outline-primary w-100">Comprar créditos via Pix</a>
            </div>
            <div class="chat-side-card">
                <div class="chat-kicker"><i class="bi bi-lightning-charge"></i>Atalhos</div>
                <h6 class="fw-bold mb-2">Demandas frequentes</h6>
                <ul class="chat-side-list">
                    <li>
                        <i class="bi bi-file-earmark-text text-primary"></i>
                        <div class="text-body-secondary small">Minuta inicial com fatos, fundamentos e pedidos.</div>
                    </li>
                    <li>
                        <i class="bi bi-journal-text text-primary"></i>
                        <div class="text-body-secondary small">Resumo executivo para reunião ou atualização interna.</div>
                    </li>
                    <li>
                        <i class="bi bi-chat-left-dots text-primary"></i>
                        <div class="text-body-secondary small">Mensagem clara e humana para atualização ao cliente.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatBox = document.getElementById('chatBox');
    const btnSend = document.getElementById('btnSend');
    const chatCreditsBalance = document.getElementById('chatCreditsBalance');
    const chatCreditsBalanceSide = document.getElementById('chatCreditsBalanceSide');

    const updateCreditsBalance = function (value) {
        if (typeof value !== 'number') {
            return;
        }

        chatCreditsBalance.textContent = value;
        chatCreditsBalanceSide.textContent = value;
    };

    const appendMessage = function (role, text) {
        const safeText = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

        chatBox.insertAdjacentHTML('beforeend', `
            <div class="message-row ${role === 'user' ? 'user' : 'assistant'}">
                <div class="message-bubble ${role}">${safeText}</div>
            </div>`);
        chatBox.scrollTop = chatBox.scrollHeight;
    };

    document.querySelectorAll('[data-prompt]').forEach(function (button) {
        button.addEventListener('click', function () {
            chatInput.value = this.dataset.prompt;
            chatInput.focus();
        });
    });

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = chatInput.value.trim();
        if (!message) return;

        appendMessage('user', message);
        chatInput.value = '';

        btnSend.disabled = true;
        btnSend.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const formData = new FormData();
            formData.append('message', message);
            formData.append('_token', '{{ csrf_token() }}');

            const res = await fetch('{{ route("chat.send") }}', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await res.json();

            if (typeof data.credits_remaining === 'number') {
                updateCreditsBalance(data.credits_remaining);
            }

            if (data.reply) {
                appendMessage('assistant', data.reply);
            } else {
                const suffix = data.buy_url ? ' Abra a tela de créditos para comprar mais consultas.' : '';
                appendMessage('error', `Erro: ${data.error || 'Falha ao processar.'}${suffix}`);
            }
        } catch (error) {
            appendMessage('error', 'Erro de conexão.');
        }

        btnSend.disabled = false;
        btnSend.innerHTML = '<i class="bi bi-send-fill"></i>';
    });
</script>
@endsection
