<?php

namespace App\Http\Controllers;

use App\Services\ConsultaCreditService;
use App\Services\DataJudService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $dataJudService = app(DataJudService::class);
        $consultaCreditService = app(ConsultaCreditService::class);
        $user = $request->user()->fresh();
        $processNumber = $dataJudService->extractProcessNumber($request->message);

        if ($processNumber) {
            if (! $dataJudService->isConfigured()) {
                return response()->json([
                    'error' => 'Integração DataJud não configurada no servidor.'
                ], 500);
            }

            if ($user->consulta_credits < 1) {
                return response()->json([
                    'error' => 'Você não possui créditos suficientes para consultar esse processo.',
                    'credits_remaining' => 0,
                    'buy_url' => route('credits.index'),
                ], 402);
            }

            try {
                $process = $dataJudService->queryByProcessNumber($processNumber);

                if (! $process) {
                    $user = $consultaCreditService->consumeForDataJud(
                        $user,
                        $processNumber,
                        'Processo não encontrado no DataJud.',
                        ['result' => 'not_found']
                    );

                    return response()->json([
                        'reply' => 'Processo não encontrado no DataJud para o número '.$processNumber.'.',
                        'credits_remaining' => $user->consulta_credits,
                    ]);
                }

                $reply = $dataJudService->formatProcessResponse($processNumber, $process);
                $user = $consultaCreditService->consumeForDataJud(
                    $user,
                    $processNumber,
                    $reply,
                    ['result' => 'found']
                );

                return response()->json([
                    'reply' => $reply,
                    'credits_remaining' => $user->consulta_credits,
                ]);
            } catch (RuntimeException $exception) {
                return response()->json([
                    'error' => $exception->getMessage()
                ], 500);
            }
        }

        $openaiApiKey = config('services.openai.api_key');
        
        if (!$openaiApiKey) {
            return response()->json([
                'error' => 'Chave de API da OpenAI não configurada no servidor.'
            ], 500);
        }

        try {
            $response = Http::withToken($openaiApiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente jurídico sênior especializado no Brasil. Ajude o advogado a analisar processos, gerar teses e tirar dúvidas legais.'],
                    ['role' => 'user', 'content' => $request->message]
                ],
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                return response()->json([
                    'reply' => $response->json('choices.0.message.content'),
                    'credits_remaining' => $user->consulta_credits,
                ]);
            }

            return response()->json(['error' => 'Falha ao se comunicar com a OpenAI.'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
