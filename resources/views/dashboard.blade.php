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
        <div class="col-xl-8">
            <div class="dashboard-hero h-100">
                <div class="dashboard-kicker"><i class="bi bi-stars"></i>Painel premium</div>
                <div class="row g-4 align-items-end">
                    <div class="col-lg-8">
                        <h3 class="fw-bold mb-3">Operação jurídica sob controle, com leitura imediata do que merece atenção hoje.</h3>
                        <p class="text-body-secondary mb-0">O foco é deixar o estado do escritório evidente em segundos: carteira ativa, processos correntes e próximos compromissos em uma composição mais executiva.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="spotlight-item">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <div>
                                <div class="fw-bold">Foco do dia</div>
                                <div class="text-body-secondary small">Priorize follow-ups, organização de prazos e atualização de processos em andamento.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-stat-grid">
                    <div class="hero-stat">
                        <div class="hero-stat-label">Clientes ativos</div>
                        <div class="hero-stat-value">{{ $totalClientes }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-label">Processos vivos</div>
                        <div class="hero-stat-value">{{ $processosAndamento }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-label">Agenda próxima</div>
                        <div class="hero-stat-value">{{ $audiencias7Dias }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-label">Créditos de consulta</div>
                        <div class="hero-stat-value">{{ $user->consulta_credits }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="spotlight-panel">
                <div class="dashboard-kicker"><i class="bi bi-compass"></i>Radar</div>
                <h5 class="fw-bold mb-2">Direção operacional</h5>
                <p class="text-body-secondary mb-0">Uma leitura rápida para saber onde investir energia antes de abrir módulos específicos.</p>
                <ul class="spotlight-list">
                    <li class="spotlight-item">
                        <i class="bi bi-people-fill"></i>
                        <div>
                            <div class="fw-bold">Base ativa</div>
                            <div class="text-body-secondary small">{{ $totalClientes }} clientes ativos hoje.</div>
                        </div>
                    </li>
                    <li class="spotlight-item">
                        <i class="bi bi-folder-fill"></i>
                        <div>
                            <div class="fw-bold">Carteira corrente</div>
                            <div class="text-body-secondary small">{{ $processosAndamento }} processos em andamento.</div>
                        </div>
                    </li>
                    <li class="spotlight-item">
                        <i class="bi bi-cash-stack"></i>
                        <div>
                            <div class="fw-bold">Receita monitorada</div>
                            <div class="text-body-secondary small">R$ {{ number_format($honorariosMes, 2, ',', '.') }} em honorários no mês.</div>
                        </div>
                    </li>
                </ul>
            </div>
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
        <div class="col-md-3">
            <div class="metric-card d-flex flex-row align-items-center gap-3">
                <div class="metric-icon"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $user->consulta_credits }}</h4>
                    <span class="text-body-secondary small">Créditos DataJud</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="{{ route('clientes.index') }}" class="quick-action-card d-block">
                <i class="bi bi-person-lines-fill"></i>
                <div class="fw-bold mb-1">Clientes</div>
                <div class="text-body-secondary small">Acesse a carteira, filtre contatos e mantenha o relacionamento organizado.</div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('processos.index') }}" class="quick-action-card d-block">
                <i class="bi bi-folder2-open"></i>
                <div class="fw-bold mb-1">Processos</div>
                <div class="text-body-secondary small">Revise o pipeline jurídico e entre nos casos que demandam ação imediata.</div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('chat.index') }}" class="quick-action-card d-block">
                <i class="bi bi-chat-square-dots"></i>
                <div class="fw-bold mb-1">Chat IA</div>
                <div class="text-body-secondary small">Peça sínteses, rascunhos e apoio analítico direto no fluxo de trabalho.</div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('credits.index') }}" class="quick-action-card d-block">
                <i class="bi bi-qr-code-scan"></i>
                <div class="fw-bold mb-1">Comprar créditos</div>
                <div class="text-body-secondary small">Gere uma cobrança Pix via Stripe para repor consultas DataJud em tempo real.</div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="feature-panel h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Próximas Audiências</h5>
                    <span class="inline-meta"><i class="bi bi-calendar-week"></i>Próximos 7 dias</span>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-body-secondary px-0 border-0">
                        <i class="bi bi-info-circle me-2"></i>Nenhuma audiência marcada.
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-panel h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Tarefas Pendentes</h5>
                    <span class="inline-meta"><i class="bi bi-check2-circle"></i>Fila operacional</span>
                </div>
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
