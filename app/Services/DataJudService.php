<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DataJudService
{
    protected const STATE_ENDPOINTS = [
        '01' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjac/_search',
        '02' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjal/_search',
        '03' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjap/_search',
        '04' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjam/_search',
        '05' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjba/_search',
        '06' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjce/_search',
        '07' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjdft/_search',
        '08' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjes/_search',
        '09' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjgo/_search',
        '10' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjma/_search',
        '11' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjmt/_search',
        '12' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjms/_search',
        '13' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjmg/_search',
        '14' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjpa/_search',
        '15' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjpb/_search',
        '16' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjpr/_search',
        '17' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjpe/_search',
        '18' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjpi/_search',
        '19' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjrj/_search',
        '20' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjrn/_search',
        '21' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjrs/_search',
        '22' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjro/_search',
        '23' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjrr/_search',
        '24' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjsc/_search',
        '25' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjse/_search',
        '26' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjsp/_search',
        '27' => 'https://api-publica.datajud.cnj.jus.br/api_publica_tjto/_search',
    ];

    protected const FEDERAL_ENDPOINTS = [
        '01' => 'https://api-publica.datajud.cnj.jus.br/api_publica_trf1/_search',
        '02' => 'https://api-publica.datajud.cnj.jus.br/api_publica_trf2/_search',
        '03' => 'https://api-publica.datajud.cnj.jus.br/api_publica_trf3/_search',
        '04' => 'https://api-publica.datajud.cnj.jus.br/api_publica_trf4/_search',
        '05' => 'https://api-publica.datajud.cnj.jus.br/api_publica_trf5/_search',
        '06' => 'https://api-publica.datajud.cnj.jus.br/api_publica_trf6/_search',
    ];

    public function isConfigured(): bool
    {
        return filled(config('services.datajud.api_key'));
    }

    public function extractProcessNumber(string $message): ?string
    {
        if (! preg_match('/(\d{7}-?\d{2}\.?\d{4}\.?\d\.?\d{2}\.?\d{4})|(\d{20})/', $message, $match)) {
            return null;
        }

        $normalized = preg_replace('/\D/', '', $match[0]);

        return strlen($normalized) === 20 ? $normalized : null;
    }

    public function queryByProcessNumber(string $processNumber): ?array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Integração DataJud não configurada no servidor.');
        }

        $endpoint = $this->resolveEndpoint($processNumber);

        if (! $endpoint) {
            throw new RuntimeException('Não foi possível identificar um endpoint DataJud compatível para esse número CNJ.');
        }

        $response = Http::timeout(90)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'ApiKey '.config('services.datajud.api_key'),
            ])
            ->post($endpoint, [
                'query' => [
                    'term' => [
                        'numeroProcesso' => $processNumber,
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao consultar o DataJud.');
        }

        $process = $response->json('hits.hits.0._source');

        return is_array($process) ? $process : null;
    }

    public function formatProcessResponse(string $processNumber, array $process): string
    {
        $lines = [
            'Consulta DataJud do processo '.$processNumber,
            '',
            'Classe: '.($process['classeProcessual'] ?? 'Não informado'),
            'Assunto: '.$this->stringifySubject($process['assunto'] ?? null),
            'Tribunal/Órgão: '.($process['orgaoJulgador']['nomeOrgao'] ?? 'Não informado'),
            'Data de ajuizamento: '.$this->formatDate($process['dataAjuizamento'] ?? null),
        ];

        $partes = $this->formatParts($process['partes'] ?? []);
        if ($partes !== []) {
            $lines[] = '';
            $lines[] = 'Partes:';
            foreach ($partes as $parte) {
                $lines[] = '- '.$parte;
            }
        }

        $movements = $this->formatMovements($process['movimentos'] ?? []);
        if ($movements !== []) {
            $lines[] = '';
            $lines[] = 'Últimas movimentações:';
            foreach ($movements as $movement) {
                $lines[] = '- '.$movement;
            }
        }

        return implode("\n", $lines);
    }

    protected function resolveEndpoint(string $processNumber): ?string
    {
        if (! preg_match('/^\d{20}$/', $processNumber)) {
            return null;
        }

        $justiceSegment = substr($processNumber, 13, 1);
        $tribunalCode = substr($processNumber, 14, 2);

        return match ($justiceSegment) {
            '8' => self::STATE_ENDPOINTS[$tribunalCode] ?? null,
            '4' => self::FEDERAL_ENDPOINTS[$tribunalCode] ?? null,
            default => null,
        };
    }

    protected function stringifySubject(mixed $subject): string
    {
        if (is_string($subject) && $subject !== '') {
            return $subject;
        }

        if (is_array($subject)) {
            $values = array_filter(array_map(function ($item) {
                if (is_string($item)) {
                    return trim($item);
                }

                if (is_array($item)) {
                    return $item['nome'] ?? $item['descricao'] ?? null;
                }

                return null;
            }, $subject));

            if ($values !== []) {
                return implode(', ', $values);
            }
        }

        return 'Não informado';
    }

    protected function formatParts(array $parts): array
    {
        $formatted = [];

        foreach (array_slice($parts, 0, 8) as $part) {
            if (! is_array($part)) {
                continue;
            }

            $role = $part['polo'] ?? $part['tipoParte'] ?? $part['descricao'] ?? 'Parte';
            $name = $part['nome'] ?? $part['nomeParte'] ?? null;

            if (! $name) {
                continue;
            }

            $formatted[] = trim($role).': '.$name;
        }

        return $formatted;
    }

    protected function formatMovements(array $movements): array
    {
        usort($movements, function ($left, $right) {
            return strtotime($right['dataHora'] ?? '') <=> strtotime($left['dataHora'] ?? '');
        });

        $formatted = [];

        foreach (array_slice($movements, 0, 5) as $movement) {
            if (! is_array($movement)) {
                continue;
            }

            $date = $this->formatDate($movement['dataHora'] ?? null);
            $name = $movement['nome'] ?? $movement['descricao'] ?? $movement['movimentoNacional']['descricao'] ?? 'Movimentação sem descrição';
            $formatted[] = $date.' - '.$name;
        }

        return $formatted;
    }

    protected function formatDate(?string $value): string
    {
        if (! $value) {
            return 'Não informado';
        }

        try {
            return Carbon::parse($value)->format('d/m/Y H:i');
        } catch (\Throwable) {
            return $value;
        }
    }
}