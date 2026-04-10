@extends('layouts.app')

@section('title', 'Novo Processo')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex align-items-center gap-3">
        <a href="{{ route('processos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
        <div>
            <h2 class="page-title">Cadastrar Novo Processo</h2>
            <p class="page-subtitle">Campos e seletores adaptados para o tema responder ao Bootswatch.</p>
        </div>
        </div>
    </div>

    <div class="content-card mx-auto" style="max-width: 900px;">
        <form method="POST" action="{{ route('processos.store') }}">
            @csrf
            
            <div class="form-section">
            <h5 class="section-title">Informações iniciais</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 align-items-start flex-wrap">
                        <select name="cliente_id" id="clienteSelect" class="form-select" required>
                            <option value="">Selecione um cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }} ({{ $cliente->cpf_cnpj ?: 'Sem documento' }})</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickClientModal">
                            <i class="bi bi-person-plus"></i>
                        </button>
                    </div>
                    <div class="text-body-secondary small mt-2">Cadastre um cliente sem sair desta tela.</div>
                    @error('cliente_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número CNJ</label>
                    <input type="text" name="numero_cnj" class="form-control" placeholder="0000000-00.0000.0.00.0000" value="{{ old('numero_cnj') }}">
                    @error('numero_cnj') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            </div>

            <div class="form-section">
            <h5 class="section-title">Detalhes jurídicos</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tipo de Ação</label>
                    <input type="text" name="tipo_acao" class="form-control" placeholder="Ex: Indenizatória, Divórcio..." value="{{ old('tipo_acao') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Área do Direito</label>
                    <select name="area_direito" class="form-select">
                        <option value="">Selecione...</option>
                        <option value="Cível">Cível</option>
                        <option value="Trabalhista">Trabalhista</option>
                        <option value="Criminal">Criminal</option>
                        <option value="Previdenciário">Previdenciário</option>
                        <option value="Tributário">Tributário</option>
                        <option value="Família e Sucessões">Família e Sucessões</option>
                        <option value="Empresarial">Empresarial</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Polo Penal/Processual <span class="text-danger">*</span></label>
                    <select name="polo" class="form-select" required>
                        <option value="ativo" {{ old('polo') == 'ativo' ? 'selected' : '' }}>Polo Ativo (Autor/Requerente)</option>
                        <option value="passivo" {{ old('polo') == 'passivo' ? 'selected' : '' }}>Polo Passivo (Réu/Requerido)</option>
                        <option value="terceiro" {{ old('polo') == 'terceiro' ? 'selected' : '' }}>Terceiro Interessado</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Partes Contrárias</label>
                    <input type="text" name="partes_contrarias" class="form-control" placeholder="Nome, CPF/CNPJ, Advogado..." value="{{ old('partes_contrarias') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Valor da Causa (R$)</label>
                    <input type="number" step="0.01" name="valor_causa" class="form-control" placeholder="0.00" value="{{ old('valor_causa') }}">
                </div>
            </div>
            </div>

            <div class="form-section">
            <h5 class="section-title">Localização e responsabilidade</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Tribunal / Comarca / Vara</label>
                    <input type="text" name="comarca" class="form-control" placeholder="Ex: TJSP - Foro de São Paulo" value="{{ old('comarca') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Advogado Responsável</label>
                    <select name="advogado_id" class="form-select">
                        <option value="">Selecione...</option>
                        @foreach($advogados as $advogado)
                            <option value="{{ $advogado->id }}" {{ old('advogado_id', auth()->id()) == $advogado->id ? 'selected' : '' }}>{{ $advogado->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="em andamento" {{ old('status') == 'em andamento' ? 'selected' : '' }}>Em andamento</option>
                        <option value="suspenso" {{ old('status') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                        <option value="arquivado" {{ old('status') == 'arquivado' ? 'selected' : '' }}>Arquivado</option>
                        <option value="encerrado" {{ old('status') == 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                        <option value="ganho" {{ old('status') == 'ganho' ? 'selected' : '' }}>Ganho</option>
                        <option value="perdido" {{ old('status') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                    </select>
                </div>
            </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Salvar Processo</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="quickClientModal" tabindex="-1" aria-labelledby="quickClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="quickClientModalLabel">Cadastrar cliente rapidamente</h5>
                    <p class="text-body-secondary mb-0 small">Crie o cliente e continue o cadastro do processo sem trocar de tela.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div id="quickClientAlert" class="alert d-none" role="alert"></div>
                <form id="quickClientForm" class="row g-3">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label">Nome completo / razão social <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select" required>
                            <option value="PF">Pessoa Física</option>
                            <option value="PJ">Pessoa Jurídica</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CPF / CNPJ</label>
                        <input type="text" name="cpf_cnpj" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="ativo">Ativo</option>
                            <option value="prospecto">Prospecto</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Celular</label>
                        <input type="text" name="celular" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="quickClientSubmit">Salvar cliente</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const modalElement = document.getElementById('quickClientModal');
        const form = document.getElementById('quickClientForm');
        const alertBox = document.getElementById('quickClientAlert');
        const submitButton = document.getElementById('quickClientSubmit');
        const clienteSelect = document.getElementById('clienteSelect');
        const modal = modalElement ? bootstrap.Modal.getOrCreateInstance(modalElement) : null;

        if (!modalElement || !form || !alertBox || !submitButton || !clienteSelect || !modal) {
            return;
        }

        const showAlert = function (message, type) {
            alertBox.className = `alert alert-${type}`;
            alertBox.textContent = message;
            alertBox.classList.remove('d-none');
        };

        const clearAlert = function () {
            alertBox.className = 'alert d-none';
            alertBox.textContent = '';
        };

        modalElement.addEventListener('hidden.bs.modal', function () {
            form.reset();
            clearAlert();
            submitButton.disabled = false;
            submitButton.innerHTML = 'Salvar cliente';
        });

        submitButton.addEventListener('click', async function () {
            clearAlert();

            if (!form.reportValidity()) {
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando';

            try {
                const formData = new FormData(form);

                const response = await fetch("{{ route('clientes.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        throw new Error(firstError);
                    }

                    throw new Error(data.message || 'Não foi possível cadastrar o cliente.');
                }

                const cliente = data.cliente;
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = `${cliente.nome} (${cliente.cpf_cnpj || 'Sem documento'})`;
                option.selected = true;
                clienteSelect.appendChild(option);
                clienteSelect.value = cliente.id;

                modal.hide();
            } catch (error) {
                showAlert(error.message || 'Não foi possível cadastrar o cliente.', 'danger');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Salvar cliente';
            }
        });
    })();
</script>
@endsection
