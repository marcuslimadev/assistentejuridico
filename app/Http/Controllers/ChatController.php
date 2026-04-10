<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
                    'reply' => $response->json('choices.0.message.content')
                ]);
            }

            return response()->json(['error' => 'Falha ao se comunicar com a OpenAI.'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
