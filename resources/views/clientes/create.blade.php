@extends('layouts.app')

@section('title', 'Novo Cliente')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex align-items-center gap-3">
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
        <div>
            <h2 class="page-title">Cadastrar Novo Cliente</h2>
            <p class="page-subtitle">Formulário baseado em componentes padrão do Bootstrap.</p>
        </div>
        </div>
    </div>

    <div class="content-card mx-auto" style="max-width: 800px;">
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label">Nome Completo / Razão Social <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" required value="{{ old('nome') }}">
                    @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select">
                        <option value="PF" {{ old('tipo') == 'PF' ? 'selected' : '' }}>Pessoa Física (PF)</option>
                        <option value="PJ" {{ old('tipo') == 'PJ' ? 'selected' : '' }}>Pessoa Jurídica (PJ)</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">CPF / CNPJ</label>
                    <input type="text" name="cpf_cnpj" class="form-control" value="{{ old('cpf_cnpj') }}">
                    @error('cpf_cnpj') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select">
                        <option value="ativo" {{ old('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="prospecto" {{ old('status') == 'prospecto' ? 'selected' : '' }}>Prospecto</option>
                        <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Celular / WhatsApp</label>
                    <input type="text" name="celular" class="form-control" value="{{ old('celular') }}">
                </div>
            </div>

            <hr class="mb-4">

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Salvar Cliente</button>
            </div>
        </form>
    </div>
</div>
@endsection
