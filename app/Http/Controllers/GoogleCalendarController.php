<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GoogleCalendarController extends Controller
{
    public function redirect(Request $request, GoogleCalendarService $googleCalendarService): RedirectResponse
    {
        if (! $googleCalendarService->isConfigured()) {
            return redirect()->route('agendas.index')->with('warning', 'Configure as credenciais do Google Calendar antes de conectar a conta.');
        }

        $state = Str::random(40);

        $request->session()->put('google_calendar_oauth_state', $state);
        $request->session()->put('google_calendar_return_to', url()->previous());

        return redirect()->away($googleCalendarService->getAuthorizationUrl($state));
    }

    public function callback(Request $request, GoogleCalendarService $googleCalendarService): RedirectResponse
    {
        $expectedState = $request->session()->pull('google_calendar_oauth_state');
        $returnTo = $request->session()->pull('google_calendar_return_to', route('agendas.index'));

        if (! $googleCalendarService->isConfigured()) {
            return redirect($returnTo)->with('warning', 'A configuração do Google Calendar ainda não está disponível no ambiente.');
        }

        if ($request->filled('error')) {
            return redirect($returnTo)->with('warning', 'A autorização do Google Calendar foi cancelada.');
        }

        if (! $expectedState || $request->string('state')->toString() !== $expectedState) {
            return redirect($returnTo)->with('error', 'Não foi possível validar a resposta do Google. Tente conectar novamente.');
        }

        $code = $request->string('code')->toString();

        if ($code === '') {
            return redirect($returnTo)->with('error', 'O Google não retornou um código de autorização válido.');
        }

        try {
            $googleCalendarService->storeAuthorizationCode($request->user(), $code);
        } catch (\Throwable $exception) {
            return redirect($returnTo)->with('error', 'A conexão com Google Calendar falhou: '.$exception->getMessage());
        }

        return redirect($returnTo)->with('success', 'Google Calendar conectado com sucesso.');
    }

    public function destroy(Request $request, GoogleCalendarService $googleCalendarService): RedirectResponse
    {
        $googleCalendarService->disconnect($request->user());

        return redirect()->route('agendas.index')->with('success', 'Google Calendar desconectado com sucesso.');
    }
}