@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Dashboard</h2>
        <div>
            <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ date('d/m/Y') }}</span>
        </div>
    </div>

    <!-- Cards Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card p-3 d-flex flex-row align-items-center shadow-sm">
                <div class="fs-1 text-primary me-3"><i class="bi bi-people-fill"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $totalClientes }}</h4>
                    <span class="text-muted small">Clientes Ativos</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 d-flex flex-row align-items-center shadow-sm">
                <div class="fs-1 text-info me-3"><i class="bi bi-folder-fill"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $processosAndamento }}</h4>
                    <span class="text-muted small">Processos Andamento</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 d-flex flex-row align-items-center shadow-sm">
                <div class="fs-1 text-warning me-3"><i class="bi bi-calendar-event-fill"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $audiencias7Dias }}</h4>
                    <span class="text-muted small">Audiências (7 dias)</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 d-flex flex-row align-items-center shadow-sm">
                <div class="fs-1 text-success me-3"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">R$ {{ number_format($honorariosMes, 2, ',', '.') }}</h4>
                    <span class="text-muted small">Honorários do Mês</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-4 h-100 shadow-sm">
                <h5 class="mb-4">Próximas Audiências</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-muted px-0 border-0">
                        <i class="bi bi-info-circle me-2"></i>Nenhuma audiência marcada.
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4 h-100 shadow-sm">
                <h5 class="mb-4">Tarefas Pendentes</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-muted px-0 border-0">
                        <i class="bi bi-check-circle me-2"></i>Nenhuma tarefa pendente.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
