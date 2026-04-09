@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h2 class="page-title">Gestão de Clientes</h2>
            <p class="page-subtitle">Visual consistente para busca, tabela e ações em claro ou escuro.</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Novo Cliente</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-card">
        <form method="GET" action="{{ route('clientes.index') }}" class="toolbar-form" style="max-width: 36rem;">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou CPF/CNPJ..." value="{{ request('busca') }}">
            <button type="submit" class="btn btn-secondary">Buscar</button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF/CNPJ</th>
                        <th>Celular/Telefone</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr>
                            <td>
                                <a href="#" class="entity-link">{{ $cliente->nome }}</a>
                            </td>
                            <td>{{ $cliente->cpf_cnpj ?? '-' }}</td>
                            <td>{{ $cliente->celular ?? $cliente->telefone ?? '-' }}</td>
                            <td>
                                @if($cliente->status == 'ativo')
                                    <span class="badge bg-success status-badge">Ativo</span>
                                @elseif($cliente->status == 'inativo')
                                    <span class="badge bg-danger status-badge">Inativo</span>
                                @else
                                    <span class="badge text-bg-info status-badge">Prospecto</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm btn-outline-primary action-btn" title="Ver Perfil"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary action-btn" title="Editar"><i class="bi bi-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center empty-state">Nenhum cliente encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $clientes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
