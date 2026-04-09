<?php
session_start();

function jsonResponse($mensagem, $status = 200, $extra = []) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'resposta' => $mensagem,
        'session_usage' => getSessionUsageStats(),
    ], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

function defaultUsageStats() {
    return [
        'prompt_tokens' => 0,
        'completion_tokens' => 0,
        'total_tokens' => 0,
        'requests' => 0,
        'last_model' => null,
        'last_usage' => null,
        'last_updated_at' => null,
    ];
}

function getSessionUsageStats() {
    if (empty($_SESSION['usage_stats']) || !is_array($_SESSION['usage_stats'])) {
        $_SESSION['usage_stats'] = defaultUsageStats();
    }

    return $_SESSION['usage_stats'];
}

function normalizeUsage($usage) {
    if (!is_array($usage)) {
        return null;
    }

    return [
        'prompt_tokens' => (int) ($usage['prompt_tokens'] ?? 0),
        'completion_tokens' => (int) ($usage['completion_tokens'] ?? 0),
        'total_tokens' => (int) ($usage['total_tokens'] ?? 0),
        'prompt_tokens_details' => is_array($usage['prompt_tokens_details'] ?? null) ? $usage['prompt_tokens_details'] : null,
        'completion_tokens_details' => is_array($usage['completion_tokens_details'] ?? null) ? $usage['completion_tokens_details'] : null,
    ];
}

function registerUsage($usage, $model = null) {
    $stats = getSessionUsageStats();

    if ($usage) {
        $stats['prompt_tokens'] += (int) ($usage['prompt_tokens'] ?? 0);
        $stats['completion_tokens'] += (int) ($usage['completion_tokens'] ?? 0);
        $stats['total_tokens'] += (int) ($usage['total_tokens'] ?? 0);
        $stats['requests'] += 1;
        $stats['last_model'] = $model;
        $stats['last_usage'] = $usage;
        $stats['last_updated_at'] = date('c');
        $_SESSION['usage_stats'] = $stats;
    }

    return $stats;
}

function extractOpenAIMessage($rawResponse, $fallback, $requestedModel = null) {
    if (!$rawResponse) {
        return [
            'content' => $fallback,
            'usage' => null,
            'model' => $requestedModel,
            'raw' => null,
        ];
    }

    $decoded = json_decode($rawResponse, true);
    if (!is_array($decoded)) {
        return [
            'content' => $fallback,
            'usage' => null,
            'model' => $requestedModel,
            'raw' => null,
        ];
    }

    $content = $fallback;
    $messageContent = $decoded['choices'][0]['message']['content'] ?? null;

    if (is_string($messageContent) && $messageContent !== '') {
        $content = $messageContent;
    } elseif (is_array($messageContent)) {
        $parts = [];
        foreach ($messageContent as $part) {
            if (($part['type'] ?? null) === 'text' && isset($part['text'])) {
                $parts[] = $part['text'];
            }
        }
        if ($parts) {
            $content = implode("\n", $parts);
        }
    } elseif (!empty($decoded['error']['message'])) {
        $msg = $decoded['error']['message'];
        $msgLower = strtolower($msg);

        if (str_contains($msgLower, 'exceeded your current quota') || str_contains($msgLower, 'insufficient_quota')) {
            $content = "O servico de IA esta temporariamente indisponivel por limite de cota da conta OpenAI. Verifique faturamento ou creditos e tente novamente.";
        } elseif (str_contains($msgLower, 'incorrect api key') || str_contains($msgLower, 'invalid api key')) {
            $content = "A chave da OpenAI esta invalida. Atualize a OPENAI_API_KEY no servidor.";
        } else {
            $content = "Falha na OpenAI: " . $msg;
        }
    }

    return [
        'content' => $content,
        'usage' => normalizeUsage($decoded['usage'] ?? null),
        'model' => $decoded['model'] ?? $requestedModel,
        'raw' => $decoded,
    ];
}

function callOpenAI($apiOpenAI, $chatData, $fallback) {
    if (!$apiOpenAI) {
        return [
            'content' => "A chave da OpenAI nao esta configurada no servidor.",
            'usage' => null,
            'model' => $chatData['model'] ?? null,
        ];
    }

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($chatData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiOpenAI
        ],
        CURLOPT_TIMEOUT => 90,
    ]);

    $rawResponse = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($rawResponse === false) {
        return [
            'content' => $curlError ? "Falha de comunicacao com a OpenAI: " . $curlError : $fallback,
            'usage' => null,
            'model' => $chatData['model'] ?? null,
        ];
    }

    $result = extractOpenAIMessage($rawResponse, $fallback, $chatData['model'] ?? null);
    registerUsage($result['usage'], $result['model']);

    return [
        'content' => $result['content'],
        'usage' => $result['usage'],
        'model' => $result['model'],
    ];
}

function buildAiPayload($result, $extra = []) {
    return array_merge([
        'usage' => $result['usage'] ?? null,
        'model' => $result['model'] ?? null,
        'session_usage' => getSessionUsageStats(),
    ], $extra);
}

function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $vars = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($vars as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($trimmed, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $trimmed, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

loadEnv(__DIR__ . '/.env');

$apiOpenAI = getenv('OPENAI_API_KEY') ?: getenv('OPENAI_KEY');
$apiDataJudKey = getenv('DATAJUD_KEY');

require 'conexao.php';
include 'urls.php';

$data = json_decode(file_get_contents("php://input"), true);
$mensagem = trim($data["mensagem"] ?? '');
$usuario_id = $_SESSION["usuario_id"] ?? null;

if (!$usuario_id) {
    jsonResponse("Voce precisa estar logado.", 401);
}

if ($mensagem === '') {
    jsonResponse("Mensagem ausente.", 400);
}

if (!isset($_SESSION['chat_history']) || !is_array($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

getSessionUsageStats();

$numeroProcesso = null;

if (preg_match('/(\d{7}-?\d{2}\.?\d{4}\.?\d\.?\d{2}\.?\d{4})|(\d{20})/', $mensagem, $match)) {
    $numeroProcesso = preg_replace('/\D/', '', $match[0]);
    $_SESSION['ultimo_numero_processo'] = $numeroProcesso;
} elseif (!empty($_SESSION['ultimo_numero_processo'])) {
    $numeroProcesso = $_SESSION['ultimo_numero_processo'];
}

if ($numeroProcesso && strlen($numeroProcesso) === 20 && preg_match('/^\d{20}$/', $numeroProcesso)) {
    $uf = substr($numeroProcesso, 14, 2);
    $url = $urls[$uf] ?? null;

    if (!$url) {
        $erro = "Nao foi possivel identificar o tribunal responsavel por este numero.";
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

        $prompt = "Explique para o usuario que nao foi possivel identificar o tribunal do processo $numeroProcesso, provavelmente o numero esta incorreto ou o tribunal nao esta integrado.";
        $aiResult = callOpenAI($apiOpenAI, [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($_SESSION['chat_history'], [['role' => 'user', 'content' => $prompt]])
        ], $erro);

        jsonResponse($aiResult['content'], 200, buildAiPayload($aiResult));
    }

    $stmtSaldo = $conn->prepare("SELECT acessos_restantes FROM usuarios WHERE id = ?");
    $stmtSaldo->bind_param("i", $usuario_id);
    $stmtSaldo->execute();
    $resSaldo = $stmtSaldo->get_result()->fetch_assoc();
    $consultasRestantes = isset($resSaldo['acessos_restantes']) ? (int) $resSaldo['acessos_restantes'] : null;

    if (!$resSaldo || $consultasRestantes <= 0) {
        $erro = "Voce nao possui mais acessos.";
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

        $prompt = "Explique ao usuario que ele nao possui mais acessos disponiveis para consulta de processos.";
        $aiResult = callOpenAI($apiOpenAI, [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($_SESSION['chat_history'], [['role' => 'user', 'content' => $prompt]])
        ], $erro);

        jsonResponse($aiResult['content'], 403, buildAiPayload($aiResult, [
            'consultas_restantes' => 0,
        ]));
    }

    $stmtDebito = $conn->prepare("UPDATE usuarios SET acessos_restantes = acessos_restantes - 1 WHERE id = ?");
    $stmtDebito->bind_param("i", $usuario_id);
    $stmtDebito->execute();

    $queryDatajud = ["query" => ["term" => ["numeroProcesso" => $numeroProcesso]]];
    $headers = [
        "Authorization: ApiKey $apiDataJudKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($queryDatajud));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90);
    $resDatajud = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($resDatajud, true);
    if (!$dados || isset($dados['error'])) {
        $erro = "Erro na consulta ao DataJud.";
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

        $prompt = "Explique para o usuario que houve uma falha tecnica ao consultar o DataJud.";
        $aiResult = callOpenAI($apiOpenAI, [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($_SESSION['chat_history'], [['role' => 'user', 'content' => $prompt]])
        ], $erro);

        jsonResponse($aiResult['content'], 200, buildAiPayload($aiResult, [
            'consultas_restantes' => max(0, $consultasRestantes - 1),
        ]));
    }

    if (!empty($dados['hits']['hits'])) {
        foreach ($dados['hits']['hits'] as $item) {
            $processo = $item['_source'] ?? null;
            if (!$processo) {
                continue;
            }

            $movimentos = $processo['movimentos'] ?? [];
            usort($movimentos, function ($a, $b) {
                return strtotime($b['dataHora'] ?? '') <=> strtotime($a['dataHora'] ?? '');
            });

            $resumo = [
                'classeProcessual' => $processo['classeProcessual'] ?? '',
                'assunto' => $processo['assunto'] ?? '',
                'tribunal' => $processo['orgaoJulgador']['nomeOrgao'] ?? '',
                'partes' => $processo['partes'] ?? [],
                'movimentos' => array_slice($movimentos, 0, 5),
            ];

            $prompt = "Fale para um advogado, descreva o processo com os detalhes mais recentes no inicio do texto:\n"
                . json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $prompt = mb_substr($prompt, 0, 3500);

            $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $prompt];
            $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

            $aiResult = callOpenAI($apiOpenAI, [
                'model' => 'gpt-4o',
                'messages' => $_SESSION['chat_history']
            ], 'Falha ao processar a resposta do modelo.');

            $conteudoResposta = $aiResult['content'];
            $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $conteudoResposta];
            $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

            $saldoRestante = max(0, $consultasRestantes - 1);
            $stmtHist = $conn->prepare("INSERT INTO historico_consultas (usuario_id, numero_processo, retorno_api, saldo_restante) VALUES (?, ?, ?, ?)");
            $stmtHist->bind_param("issi", $usuario_id, $numeroProcesso, $conteudoResposta, $saldoRestante);
            $stmtHist->execute();

            jsonResponse($conteudoResposta, 200, buildAiPayload($aiResult, [
                'consultas_restantes' => $saldoRestante,
                'numero_processo' => $numeroProcesso,
            ]));
        }
    }

    $erro = "Processo nao encontrado.";
    $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
    $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

    $prompt = "Explique para o usuario que o processo $numeroProcesso nao foi encontrado no DataJud.";
    $aiResult = callOpenAI($apiOpenAI, [
        'model' => 'gpt-3.5-turbo',
        'messages' => array_merge($_SESSION['chat_history'], [['role' => 'user', 'content' => $prompt]])
    ], $erro);

    jsonResponse($aiResult['content'], 200, buildAiPayload($aiResult, [
        'consultas_restantes' => max(0, $consultasRestantes - 1),
        'numero_processo' => $numeroProcesso,
    ]));
}

$_SESSION['chat_history'][] = ['role' => 'user', 'content' => $mensagem];
$_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

$aiResult = callOpenAI($apiOpenAI, [
    'model' => 'gpt-3.5-turbo',
    'messages' => $_SESSION['chat_history']
], 'Por favor, forneca o numero do processo no padrao CNJ para consulta. Exemplo: 0574988-41.2023.8.04.0001 ou apenas os 20 digitos.');

$conteudoResposta = $aiResult['content'];
$_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $conteudoResposta];
$_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

jsonResponse($conteudoResposta, 200, buildAiPayload($aiResult));
