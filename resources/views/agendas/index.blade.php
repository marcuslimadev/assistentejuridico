@extends('layouts.app')
@section('title', 'Agenda')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h2 class="page-title">Agenda e Eventos</h2>
            <p class="page-subtitle">Agenda com estilo alinhado ao tema ativo.</p>
        </div>
        <a href="{{ route('agendas.create') }}" class="btn btn-primary"><i class="bi bi-calendar-plus me-2"></i>Novo Evento</a>
    </div>
    <div class="content-card">
        <p class="text-body-secondary">Módulo de Agenda (Fase 2) estruturado.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Título</th><th>Data/Hora</th><th>Local</th><th>Status</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($agendas as $agenda)
                        <tr>
                            <td>
                                <a href="#" class="entity-link">{{ $agenda->titulo }}</a>
                                <br><span class="inline-meta"><i class="bi bi-bookmark-star"></i>{{ $agenda->tipo }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($agenda->data_inicio)->format('d/m/Y H:i') }}</td>
                            <td>{{ $agenda->local ?? 'Virtual' }}</td>
                            <td><span class="badge bg-secondary status-badge">{{ $agenda->status }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm btn-outline-primary action-btn"><i class="bi bi-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center empty-state">Nenhum evento encontrado na agenda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $agendas->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
