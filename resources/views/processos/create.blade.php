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
            
            <h5 class="mb-3 border-bottom pb-2">Informações Iniciais</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">Selecione um cliente...</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }} ({{ $cliente->cpf_cnpj ?: 'Sem documento' }})</option>
                        @endforeach
                    </select>
                    @error('cliente_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número CNJ</label>
                    <input type="text" name="numero_cnj" class="form-control" placeholder="0000000-00.0000.0.00.0000" value="{{ old('numero_cnj') }}">
                    @error('numero_cnj') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2">Detalhes Jurídicos</h5>
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

            <h5 class="mb-3 border-bottom pb-2">Localização e Responsabilidade</h5>
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

            <hr class="mb-4">

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Salvar Processo</button>
            </div>
        </form>
    </div>
</div>
@endsection
