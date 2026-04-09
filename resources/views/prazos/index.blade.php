@extends('layouts.app')
@section('title', 'Prazos')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h2 class="page-title">Controle de Prazos</h2>
            <p class="page-subtitle">Tabelas e alertas agora seguem a base do tema corretamente.</p>
        </div>
        <a href="{{ route('prazos.create') }}" class="btn btn-primary"><i class="bi bi-clock-history me-2"></i>Registrar Prazo</a>
    </div>
    <div class="content-card">
        <p class="text-body-secondary">Módulo de Prazos (Fase 2) estruturado.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Descrição</th><th>Processo</th><th>Data Fatal</th><th>Status</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($prazos as $prazo)
                        <tr>
                            <td>
                                <a href="#" class="entity-link">{{ $prazo->descricao }}</a>
                                <br><span class="inline-meta"><i class="bi bi-hourglass-split"></i>{{ $prazo->tipo_prazo }}</span>
                            </td>
                            <td><span class="inline-meta"><i class="bi bi-folder2-open"></i>{{ $prazo->processo->numero_cnj ?? '-' }}</span></td>
                            <td class="{{ \Carbon\Carbon::parse($prazo->data_prazo)->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($prazo->data_prazo)->format('d/m/Y') }}
                            </td>
                            <td><span class="badge bg-secondary status-badge">{{ $prazo->status }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm btn-outline-primary action-btn"><i class="bi bi-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center empty-state">Nenhum prazo encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $prazos->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
