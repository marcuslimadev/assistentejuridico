@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestão de Clientes</h2>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Novo Cliente</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card p-4 shadow-sm">
        <form method="GET" action="{{ route('clientes.index') }}" class="mb-4 d-flex gap-2 w-50">
            <input type="text" name="busca" class="form-control bg-dark text-light border-secondary" placeholder="Buscar por nome ou CPF/CNPJ..." value="{{ request('busca') }}">
            <button type="submit" class="btn btn-secondary">Buscar</button>
        </form>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
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
                            <td>{{ $cliente->nome }}</td>
                            <td>{{ $cliente->cpf_cnpj ?? '-' }}</td>
                            <td>{{ $cliente->celular ?? $cliente->telefone ?? '-' }}</td>
                            <td>
                                @if($cliente->status == 'ativo')
                                    <span class="badge bg-success">Ativo</span>
                                @elseif($cliente->status == 'inativo')
                                    <span class="badge bg-danger">Inativo</span>
                                @else
                                    <span class="badge bg-info text-dark">Prospecto</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-info" title="Ver Perfil"><i class="bi bi-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Nenhum cliente encontrado.</td>
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
