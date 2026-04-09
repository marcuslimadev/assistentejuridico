@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h2 class="page-title">Dashboard</h2>
            <p class="page-subtitle">Visão rápida da operação do escritório, com leitura clara em qualquer tema.</p>
        </div>
        <div>
            <span class="text-body-secondary"><i class="bi bi-calendar3 me-1"></i> {{ date('d/m/Y') }}</span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-card d-flex flex-row align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $totalClientes }}</h4>
                    <span class="text-body-secondary small">Clientes ativos</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card d-flex flex-row align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-folder-fill"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $processosAndamento }}</h4>
                    <span class="text-body-secondary small">Processos em andamento</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card d-flex flex-row align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-calendar-event-fill"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $audiencias7Dias }}</h4>
                    <span class="text-body-secondary small">Audiências em 7 dias</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card d-flex flex-row align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">R$ {{ number_format($honorariosMes, 2, ',', '.') }}</h4>
                    <span class="text-body-secondary small">Honorários do mês</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="feature-panel h-100">
                <h5 class="mb-4">Próximas Audiências</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-body-secondary px-0 border-0">
                        <i class="bi bi-info-circle me-2"></i>Nenhuma audiência marcada.
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-panel h-100">
                <h5 class="mb-4">Tarefas Pendentes</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-body-secondary px-0 border-0">
                        <i class="bi bi-check-circle me-2"></i>Nenhuma tarefa pendente.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
