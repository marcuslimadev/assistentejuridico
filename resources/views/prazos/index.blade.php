@extends('layouts.app')
@section('title', 'Prazos')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Controle de Prazos</h2>
        <a href="{{ route('prazos.create') }}" class="btn btn-primary"><i class="bi bi-clock-history me-2"></i>Registrar Prazo</a>
    </div>
    <div class="card p-4 shadow-sm">
        <p class="text-muted">Módulo de Prazos (Fase 2) estruturado.</p>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr><th>Descrição</th><th>Processo</th><th>Data Fatal</th><th>Status</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($prazos as $prazo)
                        <tr>
                            <td>{{ $prazo->descricao }} <br><small class="text-info">{{ $prazo->tipo_prazo }}</small></td>
                            <td>{{ $prazo->processo->numero_cnj ?? '-' }}</td>
                            <td class="{{ \Carbon\Carbon::parse($prazo->data_prazo)->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($prazo->data_prazo)->format('d/m/Y') }}
                            </td>
                            <td><span class="badge bg-secondary">{{ $prazo->status }}</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Nenhum prazo encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $prazos->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
