@extends('layouts.app')

@section('title', 'Novo Evento')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            <div>
                <h2 class="page-title">Cadastrar Novo Evento</h2>
                <p class="page-subtitle">Crie eventos internos e mantenha a visualização do Google Calendar como apoio operacional.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card h-100">
                <form method="POST" action="{{ route('agendas.store') }}">
                    @csrf

                    <div class="form-section">
                        <h5 class="section-title">Dados do evento</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" required>
                                @error('titulo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" class="form-select" required>
                                    <option value="audiencia" {{ old('tipo') == 'audiencia' ? 'selected' : '' }}>Audiência</option>
                                    <option value="reuniao" {{ old('tipo') == 'reuniao' ? 'selected' : '' }}>Reunião</option>
                                    <option value="prazo" {{ old('tipo') == 'prazo' ? 'selected' : '' }}>Prazo</option>
                                    <option value="despacho" {{ old('tipo') == 'despacho' ? 'selected' : '' }}>Despacho</option>
                                    <option value="visita" {{ old('tipo') == 'visita' ? 'selected' : '' }}>Visita</option>
                                    <option value="outro" {{ old('tipo') == 'outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('tipo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Início <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="data_inicio" class="form-control" value="{{ old('data_inicio') }}" required>
                                @error('data_inicio') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fim</label>
                                <input type="datetime-local" name="data_fim" class="form-control" value="{{ old('data_fim') }}">
                                @error('data_fim') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Local</label>
                                <input type="text" name="local" class="form-control" placeholder="Ex: Fórum, escritório, videoconferência" value="{{ old('local') }}">
                                @error('local') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Link virtual</label>
                                <input type="url" name="link_virtual" class="form-control" placeholder="https://meet.google.com/..." value="{{ old('link_virtual') }}">
                                @error('link_virtual') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title">Vínculos</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Processo</label>
                                <select name="processo_id" id="processoSelect" class="form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($processos as $processo)
                                        <option value="{{ $processo->id }}" data-cliente-id="{{ $processo->cliente_id }}" {{ old('processo_id') == $processo->id ? 'selected' : '' }}>
                                            {{ $processo->numero_cnj ?: 'Sem número CNJ' }} - {{ $processo->cliente->nome ?? 'Sem cliente' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('processo_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cliente</label>
                                <select name="cliente_id" id="clienteSelectAgenda" class="form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="pendente" {{ old('status', 'pendente') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="realizado" {{ old('status') == 'realizado' ? 'selected' : '' }}>Realizado</option>
                                    <option value="cancelado" {{ old('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="recorrente" name="recorrente" value="1" {{ old('recorrente') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="recorrente">Evento recorrente</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="sincronizarGoogle"
                                        name="sincronizar_google"
                                        value="1"
                                        {{ old('sincronizar_google', $googleCalendarConnected) ? 'checked' : '' }}
                                        {{ !($googleCalendarConfigured && $googleCalendarConnected) ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-semibold" for="sincronizarGoogle">Sincronizar também com Google Calendar</label>
                                </div>
                                @if($googleCalendarConfigured && $googleCalendarConnected)
                                    <div class="text-body-secondary small mt-2">O evento será cadastrado internamente e enviado para sua agenda Google.</div>
                                @elseif($googleCalendarConfigured)
                                    <div class="text-body-secondary small mt-2">Conecte sua conta Google na página da agenda para habilitar a sincronização automática.</div>
                                @else
                                    <div class="text-body-secondary small mt-2">A sincronização depende de configurar as credenciais OAuth do Google no ambiente.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title">Descrição</h5>
                        <label class="form-label">Observações</label>
                        <textarea name="descricao" rows="5" class="form-control" placeholder="Contexto, participantes, providências ou observações para o evento.">{{ old('descricao') }}</textarea>
                        @error('descricao') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Salvar Evento</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card h-100">
                <div class="dashboard-kicker"><i class="bi bi-google"></i>Google Calendar</div>
                <h5 class="fw-bold mb-2">Pronto para sincronização</h5>
                <p class="text-body-secondary">
                    @if($googleCalendarConfigured && $googleCalendarConnected)
                        Sua conta Google já está conectada. Você pode salvar no LexPraxis e sincronizar no mesmo fluxo.
                    @elseif($googleCalendarConfigured)
                        As credenciais já existem. Falta apenas autorizar sua conta Google para começar a sincronizar eventos.
                    @else
                        A integração real precisa das credenciais OAuth do Google configuradas no ambiente antes da conexão do usuário.
                    @endif
                </p>
                <div class="d-grid gap-2 mt-3">
                    @if($googleCalendarConfigured && !$googleCalendarConnected)
                        <a href="{{ route('google-calendar.redirect') }}" class="btn btn-primary"><i class="bi bi-google me-2"></i>Conectar agora</a>
                    @elseif($googleCalendarConfigured && $googleCalendarConnected)
                        <a href="{{ route('agendas.index') }}" class="btn btn-outline-primary"><i class="bi bi-cloud-check me-2"></i>Ver status da conexão</a>
                    @endif
                </div>
                <ul class="chat-side-list mt-4">
                    <li>
                        <i class="bi bi-check2-circle text-primary"></i>
                        <div>
                            <div class="fw-bold">Cadastro interno já funcional</div>
                            <div class="text-body-secondary small">Permite organizar compromissos jurídicos vinculados a processo e cliente.</div>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-arrow-repeat text-primary"></i>
                        <div>
                            <div class="fw-bold">Sincronização por OAuth</div>
                            <div class="text-body-secondary small">Depois de conectar a conta, os novos eventos podem ser enviados automaticamente ao Google Calendar.</div>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-link-45deg text-primary"></i>
                        <div>
                            <div class="fw-bold">Reuniões virtuais</div>
                            <div class="text-body-secondary small">Use o campo de link virtual para Meet, Zoom ou qualquer sala online.</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const processoSelect = document.getElementById('processoSelect');
        const clienteSelect = document.getElementById('clienteSelectAgenda');

        if (!processoSelect || !clienteSelect) {
            return;
        }

        processoSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const clienteId = selectedOption ? selectedOption.getAttribute('data-cliente-id') : '';

            if (clienteId) {
                clienteSelect.value = clienteId;
            }
        });
    })();
</script>
@endsection