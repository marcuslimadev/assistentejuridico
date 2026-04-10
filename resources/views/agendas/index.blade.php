@extends('layouts.app')
@section('title', 'Agenda')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h2 class="page-title">Agenda e Eventos</h2>
            <p class="page-subtitle">Sua agenda interna agora convive com o Google Calendar e pode sincronizar novos eventos.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($googleCalendarConfigured && $googleCalendarConnected)
                <span class="badge text-bg-success align-self-center px-3 py-2"><i class="bi bi-check2-circle me-2"></i>Google conectado</span>
                <form method="POST" action="{{ route('google-calendar.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-plug me-2"></i>Desconectar Google</button>
                </form>
            @elseif($googleCalendarConfigured)
                <a href="{{ route('google-calendar.redirect') }}" class="btn btn-outline-primary"><i class="bi bi-google me-2"></i>Conectar Google Calendar</a>
            @else
                <span class="badge text-bg-warning align-self-center px-3 py-2"><i class="bi bi-exclamation-triangle me-2"></i>Google não configurado</span>
            @endif
            <a href="https://calendar.google.com/calendar/embed?src=marcusabagnale%40gmail.com&ctz=America%2FSao_Paulo" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary"><i class="bi bi-google me-2"></i>Abrir no Google Calendar</a>
            <a href="{{ route('agendas.create') }}" class="btn btn-primary"><i class="bi bi-calendar-plus me-2"></i>Novo Evento</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="content-card h-100 p-0 overflow-hidden">
                <div class="p-4 pb-0">
                    <div class="dashboard-kicker"><i class="bi bi-google"></i>Google Calendar</div>
                    <h5 class="fw-bold mb-2">Visualização e sincronização</h5>
                    <p class="text-body-secondary mb-0">
                        @if($googleCalendarConfigured && $googleCalendarConnected)
                            Novos eventos podem ser enviados ao Google Calendar diretamente no cadastro.
                        @elseif($googleCalendarConfigured)
                            Conecte sua conta para enviar eventos do LexPraxis ao Google Calendar.
                        @else
                            O embed continua disponível, mas a sincronização exige configurar as credenciais OAuth no ambiente.
                        @endif
                    </p>
                </div>
                <div class="p-4 pt-3">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden border">
                        <iframe
                            src="https://calendar.google.com/calendar/embed?src=marcusabagnale%40gmail.com&ctz=America%2FSao_Paulo"
                            style="border:0"
                            frameborder="0"
                            scrolling="no"
                            title="Google Calendar">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Eventos internos</h5>
                        <div class="text-body-secondary small">Eventos cadastrados dentro do LexPraxis.</div>
                    </div>
                    <span class="inline-meta"><i class="bi bi-calendar-week"></i>{{ $agendas->total() }} registros</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr><th>Título</th><th>Data/Hora</th><th>Status</th><th>Ações</th></tr>
                        </thead>
                        <tbody>
                            @forelse($agendas as $agenda)
                                <tr>
                                    <td>
                                        <a href="#" class="entity-link">{{ $agenda->titulo }}</a>
                                        <br><span class="inline-meta"><i class="bi bi-bookmark-star"></i>{{ $agenda->tipo }}</span>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($agenda->data_inicio)->format('d/m/Y H:i') }}
                                        <br><span class="inline-meta"><i class="bi bi-geo-alt"></i>{{ $agenda->local ?? 'Virtual' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary status-badge">{{ $agenda->status }}</span>
                                        @if($agenda->google_calendar_synced_at)
                                            <br><span class="inline-meta mt-2 d-inline-flex"><i class="bi bi-cloud-check"></i>Sincronizado</span>
                                        @elseif($agenda->google_calendar_sync_error)
                                            <br><span class="inline-meta mt-2 d-inline-flex text-danger"><i class="bi bi-cloud-slash"></i>Falha no sync</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="#" class="btn btn-sm btn-outline-primary action-btn"><i class="bi bi-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center empty-state">Nenhum evento encontrado na agenda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $agendas->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
