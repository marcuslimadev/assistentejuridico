@extends('layouts.app')
@section('title', 'Chat IA')
@section('content')
<div class="container-fluid h-100 d-flex flex-column" style="min-height: calc(100vh - 180px);">
    <div class="page-header">
        <div>
            <h2 class="page-title">Assistente Jurídico</h2>
            <p class="page-subtitle">Converse com a IA sem quebrar o tema visual do restante da plataforma.</p>
        </div>
    </div>

    <div class="content-card flex-grow-1 d-flex flex-column overflow-hidden p-0">
        <div id="chatBox" class="p-4 flex-grow-1 overflow-auto" style="height: 50vh; background: color-mix(in srgb, var(--bs-body-bg) 78%, var(--bs-tertiary-bg) 22%);">
            <div class="d-flex mb-3">
                <div class="bg-primary text-white p-3 rounded-4 shadow-sm" style="max-width: 75%;">
                    Olá! Sou seu assistente jurídico com GPT-4o. Como posso ajudar com seus processos hoje?
                </div>
            </div>
        </div>
        <div class="p-3 border-top">
            <form id="chatForm" class="d-flex gap-2">
                @csrf
                <input type="text" id="chatInput" class="form-control" placeholder="Digite sua dúvida ou peça para redigir um documento..." autocomplete="off" required>
                <button type="submit" id="btnSend" class="btn btn-primary px-4"><i class="bi bi-send-fill"></i></button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('chatForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        if (!message) return;

        const chatBox = document.getElementById('chatBox');
        const btnSend = document.getElementById('btnSend');
        
        // Add User Message
        chatBox.innerHTML += `
            <div class="d-flex justify-content-end mb-3">
                <div class="p-3 rounded-4 shadow-sm border" style="max-width: 75%; background: color-mix(in srgb, var(--bs-primary) 10%, var(--bs-body-bg) 90%); border-color: color-mix(in srgb, var(--bs-primary) 20%, transparent 80%) !important;">
                    ${message}
                </div>
            </div>`;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;
        
        // Loading State
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
            
            if (data.reply) {
                chatBox.innerHTML += `
                    <div class="d-flex mb-3">
                        <div class="bg-primary text-white p-3 rounded-4 shadow-sm" style="max-width: 75%; white-space: pre-wrap;">
                            ${data.reply}
                        </div>
                    </div>`;
            } else {
                chatBox.innerHTML += `
                    <div class="d-flex mb-3">
                        <div class="bg-danger text-white p-3 rounded-4 shadow-sm" style="max-width: 75%;">
                            Erro: ${data.error || 'Falha ao processar.'}
                        </div>
                    </div>`;
            }
        } catch (error) {
             chatBox.innerHTML += `
                <div class="d-flex mb-3">
                    <div class="bg-danger text-white p-3 rounded-4 shadow-sm" style="max-width: 75%;">
                        Erro de conexão.
                    </div>
                </div>`;
        }

        btnSend.disabled = false;
        btnSend.innerHTML = '<i class="bi bi-send-fill"></i>';
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection
