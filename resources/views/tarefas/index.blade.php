@extends('layouts.app')
@section('title', 'Tarefas')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestão de Tarefas</h2>
        <a href="{{ route('tarefas.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Nova Tarefa</a>
    </div>
    <div class="card p-4 shadow-sm">
        <p class="text-muted">Módulo de Tarefas (Fase 2) estruturado.</p>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr><th>Título</th><th>Responsável</th><th>Prazo</th><th>Status</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($tarefas as $tarefa)
                        <tr>
                            <td>{{ $tarefa->titulo }}</td>
                            <td>{{ $tarefa->responsavel->name ?? '-' }}</td>
                            <td>{{ $tarefa->prazo ? \Carbon\Carbon::parse($tarefa->prazo)->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $tarefa->status }}</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma tarefa encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $tarefas->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
