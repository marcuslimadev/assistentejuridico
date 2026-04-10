<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Cliente;
use App\Models\Processo;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index(GoogleCalendarService $googleCalendarService)
    {
        $agendas = Agenda::with(['processo', 'cliente', 'usuario'])
            ->orderBy('data_inicio')
            ->paginate(10);

        $googleCalendarConfigured = $googleCalendarService->isConfigured();
        $googleCalendarConnected = $googleCalendarConfigured && $googleCalendarService->isConnected(Auth::user());

        return view('agendas.index', compact('agendas', 'googleCalendarConfigured', 'googleCalendarConnected'));
    }

    public function create(GoogleCalendarService $googleCalendarService)
    {
        $processos = Processo::with('cliente')
            ->orderByDesc('created_at')
            ->get();

        $clientes = Cliente::orderBy('nome')->get();
        $googleCalendarConfigured = $googleCalendarService->isConfigured();
        $googleCalendarConnected = $googleCalendarConfigured && $googleCalendarService->isConnected(Auth::user());

        return view('agendas.create', compact('processos', 'clientes', 'googleCalendarConfigured', 'googleCalendarConnected'));
    }

    public function store(Request $request, GoogleCalendarService $googleCalendarService)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'local' => 'nullable|string|max:255',
            'link_virtual' => 'nullable|url|max:255',
            'descricao' => 'nullable|string',
            'recorrente' => 'nullable|boolean',
            'status' => 'required|in:pendente,realizado,cancelado',
            'processo_id' => 'nullable|exists:processos,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'sincronizar_google' => 'nullable|boolean',
        ]);

        if (!empty($validated['processo_id']) && empty($validated['cliente_id'])) {
            $processo = Processo::find($validated['processo_id']);
            $validated['cliente_id'] = $processo?->cliente_id;
        }

        $validated['user_id'] = Auth::id();
        $validated['recorrente'] = $request->boolean('recorrente');

        unset($validated['sincronizar_google']);

        $agenda = Agenda::create($validated);
        $syncMessages = [];
        $warningMessages = [];

        if ($request->boolean('sincronizar_google')) {
            if (! $googleCalendarService->isConfigured()) {
                $warningMessages[] = 'Configure as credenciais do Google Calendar no ambiente antes de sincronizar.';
            } elseif (! $googleCalendarService->isConnected($request->user())) {
                $warningMessages[] = 'Evento salvo internamente, mas a conta Google ainda não está conectada.';
            } else {
                try {
                    $googleCalendarService->syncAgenda($request->user(), $agenda);
                    $syncMessages[] = 'Evento sincronizado com o Google Calendar.';
                } catch (\Throwable $exception) {
                    $agenda->forceFill([
                        'google_calendar_sync_error' => $exception->getMessage(),
                    ])->save();

                    $warningMessages[] = 'Evento salvo internamente, mas a sincronização com o Google Calendar falhou.';
                }
            }
        }

        $successMessage = 'Evento cadastrado com sucesso!';

        if ($syncMessages !== []) {
            $successMessage .= ' '.implode(' ', $syncMessages);
        }

        $redirect = redirect()->route('agendas.index')->with('success', $successMessage);

        if ($warningMessages !== []) {
            $redirect->with('warning', implode(' ', $warningMessages));
        }

        return $redirect;
    }
}
