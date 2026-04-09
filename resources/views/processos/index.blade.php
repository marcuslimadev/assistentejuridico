@extends('layouts.app')

@section('title', 'Processos')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h2 class="page-title">Gestão de Processos</h2>
            <p class="page-subtitle">Filtros e listagem sem classes presas no escuro, para o tema responder como deveria.</p>
        </div>
        <a href="{{ route('processos.create') }}" class="btn btn-primary"><i class="bi bi-folder-plus me-2"></i>Novo Processo</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-card">
        <form method="GET" action="{{ route('processos.index') }}" class="toolbar-form">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por cliente ou número CNJ..." value="{{ request('busca') }}" style="max-width: 26rem;">
            <select name="status" class="form-select w-auto">
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
            <table class="table table-hover align-middle mb-0">
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
                            <td><a href="#" class="entity-link">{{ $processo->numero_cnj ?: 'Sem número' }}</a></td>
                            <td>{{ $processo->cliente->nome ?? 'N/A' }}</td>
                            <td>
                                <div>{{ $processo->tipo_acao ?: '-' }}</div>
                                <span class="inline-meta"><i class="bi bi-briefcase"></i>{{ $processo->area_direito ?: '-' }}</span>
                            </td>
                            <td><span class="badge bg-secondary status-badge text-capitalize">{{ $processo->polo }}</span></td>
                            <td>
                                @if($processo->status == 'em andamento')
                                    <span class="badge bg-primary status-badge">Em andamento</span>
                                @elseif($processo->status == 'ganho')
                                    <span class="badge bg-success status-badge">Ganho</span>
                                @elseif($processo->status == 'perdido')
                                    <span class="badge bg-danger status-badge">Perdido</span>
                                @elseif(in_array($processo->status, ['encerrado', 'arquivado', 'suspenso']))
                                    <span class="badge bg-secondary status-badge text-capitalize">{{ $processo->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm btn-outline-primary action-btn" title="Ver Detalhes"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary action-btn" title="Editar"><i class="bi bi-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center empty-state">Nenhum processo encontrado.</td>
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
