@extends('layouts.app')

@section('title', 'Processos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestão de Processos</h2>
        <a href="{{ route('processos.create') }}" class="btn btn-primary"><i class="bi bi-folder-plus me-2"></i>Novo Processo</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card p-4 shadow-sm">
        <form method="GET" action="{{ route('processos.index') }}" class="mb-4 d-flex gap-2 w-75">
            <input type="text" name="busca" class="form-control bg-dark text-light border-secondary" placeholder="Buscar por cliente ou número CNJ..." value="{{ request('busca') }}">
            <select name="status" class="form-select bg-dark text-light border-secondary w-auto">
                <option value="">Status</option>
                <option value="em andamento" {{ request('status') == 'em andamento' ? 'selected' : '' }}>Em andamento</option>
                <option value="arquivado" {{ request('status') == 'arquivado' ? 'selected' : '' }}>Arquivado</option>
                <option value="encerrado" {{ request('status') == 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                <option value="ganho" {{ request('status') == 'ganho' ? 'selected' : '' }}>Ganho</option>
                <option value="perdido" {{ request('status') == 'perdido' ? 'selected' : '' }}>Perdido</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
        </form>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Número CNJ</th>
                        <th>Cliente</th>
                        <th>Ação / Área</th>
                        <th>Polo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processos as $processo)
                        <tr>
                            <td><strong><a href="#" class="text-info text-decoration-none">{{ $processo->numero_cnj ?: 'Sem número' }}</a></strong></td>
                            <td>{{ $processo->cliente->nome ?? 'N/A' }}</td>
                            <td>
                                <div>{{ $processo->tipo_acao ?: '-' }}</div>
                                <small class="text-muted">{{ $processo->area_direito ?: '-' }}</small>
                            </td>
                            <td><span class="badge bg-secondary text-capitalize">{{ $processo->polo }}</span></td>
                            <td>
                                @if($processo->status == 'em andamento')
                                    <span class="badge bg-primary">Em andamento</span>
                                @elseif($processo->status == 'ganho')
                                    <span class="badge bg-success">Ganho</span>
                                @elseif($processo->status == 'perdido')
                                    <span class="badge bg-danger">Perdido</span>
                                @elseif(in_array($processo->status, ['encerrado', 'arquivado', 'suspenso']))
                                    <span class="badge bg-secondary text-capitalize">{{ $processo->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-info" title="Ver Detalhes"><i class="bi bi-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Nenhum processo encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $processos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
