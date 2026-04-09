@extends('layouts.app')
@section('title', 'Tarefas')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h2 class="page-title">Gestão de Tarefas</h2>
            <p class="page-subtitle">Leitura mais limpa e compatível com os temas do Bootswatch.</p>
        </div>
        <a href="{{ route('tarefas.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Nova Tarefa</a>
    </div>
    <div class="content-card">
        <p class="text-body-secondary">Módulo de Tarefas (Fase 2) estruturado.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Título</th><th>Responsável</th><th>Prazo</th><th>Status</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($tarefas as $tarefa)
                        <tr>
                            <td><a href="#" class="entity-link">{{ $tarefa->titulo }}</a></td>
                            <td>{{ $tarefa->responsavel->name ?? '-' }}</td>
                            <td>{{ $tarefa->prazo ? \Carbon\Carbon::parse($tarefa->prazo)->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge bg-secondary status-badge">{{ $tarefa->status }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm btn-outline-primary action-btn"><i class="bi bi-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center empty-state">Nenhuma tarefa encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $tarefas->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
