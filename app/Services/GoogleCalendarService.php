<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Carbon;
use RuntimeException;

class GoogleCalendarService
{
    public function isConfigured(): bool
    {
        return filled(config('services.google_calendar.client_id'))
            && filled(config('services.google_calendar.client_secret'))
            && filled(config('services.google_calendar.redirect'));
    }

    public function isConnected(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return ! empty($user->google_calendar_token) && ! empty($user->google_calendar_connected_at);
    }

    public function getAuthorizationUrl(string $state): string
    {
        $client = $this->makeClient();
        $client->setState($state);

        return $client->createAuthUrl();
    }

    public function storeAuthorizationCode(User $user, string $code): void
    {
        $client = $this->makeClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new RuntimeException($token['error_description'] ?? $token['error']);
        }

        $this->persistToken($user, $token);
    }

    public function disconnect(User $user): void
    {
        if (! $user->google_calendar_token) {
            return;
        }

        try {
            $client = $this->makeClient($user);
            $client->revokeToken();
        } catch (\Throwable) {
        }

        $user->forceFill([
            'google_calendar_token' => null,
            'google_calendar_refresh_token' => null,
            'google_calendar_token_expires_at' => null,
            'google_calendar_connected_at' => null,
        ])->save();
    }

    public function syncAgenda(User $user, Agenda $agenda): void
    {
        $client = $this->getAuthorizedClient($user);
        $service = new Calendar($client);
        $agenda->loadMissing(['cliente', 'processo']);

        $start = Carbon::parse($agenda->data_inicio, config('app.timezone'));
        $end = $agenda->data_fim
            ? Carbon::parse($agenda->data_fim, config('app.timezone'))
            : $start->copy()->addHour();

        $payload = new Event([
            'summary' => $agenda->titulo,
            'location' => $agenda->local,
            'description' => $this->buildDescription($agenda),
            'start' => [
                'dateTime' => $start->toRfc3339String(),
                'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
            ],
            'end' => [
                'dateTime' => $end->toRfc3339String(),
                'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
            ],
        ]);

        $calendarId = $user->google_calendar_calendar_id ?: config('services.google_calendar.calendar_id', 'primary');

        if ($agenda->google_calendar_event_id) {
            $googleEvent = $service->events->update($calendarId, $agenda->google_calendar_event_id, $payload);
        } else {
            $googleEvent = $service->events->insert($calendarId, $payload);
        }

        $agenda->forceFill([
            'google_calendar_event_id' => $googleEvent->getId(),
            'google_calendar_synced_at' => now(),
            'google_calendar_sync_error' => null,
        ])->save();
    }

    protected function getAuthorizedClient(User $user): GoogleClient
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Credenciais do Google Calendar ausentes.');
        }

        if (! $user->google_calendar_token) {
            throw new RuntimeException('Conta Google ainda não conectada.');
        }

        $client = $this->makeClient($user);

        if ($client->isAccessTokenExpired()) {
            if (! $user->google_calendar_refresh_token) {
                throw new RuntimeException('A sessão do Google expirou e precisa ser reconectada.');
            }

            $token = $client->fetchAccessTokenWithRefreshToken($user->google_calendar_refresh_token);

            if (isset($token['error'])) {
                throw new RuntimeException($token['error_description'] ?? $token['error']);
            }

            $token['refresh_token'] = $user->google_calendar_refresh_token;

            $this->persistToken($user, $token);
            $client->setAccessToken($user->fresh()->google_calendar_token);
        }

        return $client;
    }

    protected function makeClient(?User $user = null): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google_calendar.client_id'));
        $client->setClientSecret(config('services.google_calendar.client_secret'));
        $client->setRedirectUri(config('services.google_calendar.redirect'));
        $client->setAccessType('offline');
        $client->setPrompt('consent select_account');
        $client->setIncludeGrantedScopes(true);
        $client->setScopes([
            Calendar::CALENDAR,
            Calendar::CALENDAR_EVENTS,
        ]);

        if ($user?->google_calendar_token) {
            $client->setAccessToken($user->google_calendar_token);
        }

        return $client;
    }

    protected function persistToken(User $user, array $token): void
    {
        $storedToken = $token;
        $refreshToken = $token['refresh_token'] ?? $user->google_calendar_refresh_token;

        if ($refreshToken) {
            $storedToken['refresh_token'] = $refreshToken;
        }

        $user->forceFill([
            'google_calendar_token' => $storedToken,
            'google_calendar_refresh_token' => $refreshToken,
            'google_calendar_token_expires_at' => now()->addSeconds((int) ($token['expires_in'] ?? 3600)),
            'google_calendar_calendar_id' => $user->google_calendar_calendar_id ?: config('services.google_calendar.calendar_id', 'primary'),
            'google_calendar_connected_at' => now(),
        ])->save();
    }

    protected function buildDescription(Agenda $agenda): string
    {
        $parts = [
            'Tipo: '.$agenda->tipo,
        ];

        if ($agenda->cliente?->nome) {
            $parts[] = 'Cliente: '.$agenda->cliente->nome;
        }

        if ($agenda->processo?->numero_cnj) {
            $parts[] = 'Processo: '.$agenda->processo->numero_cnj;
        }

        if ($agenda->descricao) {
            $parts[] = 'Observações: '.$agenda->descricao;
        }

        if ($agenda->link_virtual) {
            $parts[] = 'Link virtual: '.$agenda->link_virtual;
        }

        return implode("\n\n", $parts);
    }
}