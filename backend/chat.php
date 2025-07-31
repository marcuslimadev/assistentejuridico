<?php
session_start();

function jsonResponse($mensagem, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['resposta' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}

function loadEnv($path) {
    if (!file_exists($path)) return;
    $vars = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($vars as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

loadEnv(__DIR__ . '/.env');

$apiOpenAI = getenv('OPENAI_KEY');
$apiDataJudKey = getenv('DATAJUD_KEY');

require 'conexao.php';
include 'urls.php';

$data = json_decode(file_get_contents("php://input"), true);
$mensagem = $data["mensagem"] ?? '';
$usuario_id = $_SESSION["usuario_id"] ?? null;

if (!$usuario_id) {
    jsonResponse("Você precisa estar logado.", 401);
}

if (!$mensagem) {
    jsonResponse("Mensagem ausente.", 400);
}

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

$numeroProcesso = null;
// 1. Procura número de processo (com ou sem pontuação)
if (preg_match('/(\d{7}-?\d{2}\.?\d{4}\.?\d\.?\d{2}\.?\d{4})|(\d{20})/', $mensagem, $m)) {
    $numeroProcesso = preg_replace('/\D/', '', $m[0]);
    // Grava o número na sessão para manter contexto
    $_SESSION['ultimo_numero_processo'] = $numeroProcesso;
} elseif (!empty($_SESSION['ultimo_numero_processo'])) {
    $numeroProcesso = $_SESSION['ultimo_numero_processo'];
}

// 2. Se for um número válido, busca no DataJud
if ($numeroProcesso && strlen($numeroProcesso) == 20 && preg_match('/^\d{20}$/', $numeroProcesso)) {
    $uf = substr($numeroProcesso, 14, 2);
    $url = $urls[$uf] ?? null;

    if (!$url) {
        // Mesmo mensagem de erro via OpenAI
        $erro = "Não foi possível identificar o tribunal responsável por este número.";
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

        $prompt = "Explique para o usuário que não foi possível identificar o tribunal do processo $numeroProcesso, provavelmente o número está incorreto ou o tribunal não está integrado.";
        $chatData = [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($_SESSION['chat_history'], [['role'=>'user', 'content'=>$prompt]])
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($chatData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiOpenAI
            ]
        ]);
        $respostaIA = curl_exec($ch);
        curl_close($ch);
        $resposta = json_decode($respostaIA, true);
        $conteudoResposta = $resposta['choices'][0]['message']['content'] ?? $erro;
        jsonResponse($conteudoResposta);
    }

    // Consulta saldo
    $stmtSaldo = $conn->prepare("SELECT acessos_restantes FROM usuarios WHERE id = ?");
    $stmtSaldo->bind_param("i", $usuario_id);
    $stmtSaldo->execute();
    $resSaldo = $stmtSaldo->get_result()->fetch_assoc();

    if (!$resSaldo || $resSaldo["acessos_restantes"] <= 0) {
        $erro = "Você não possui mais acessos.";
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

        $prompt = "Explique ao usuário que ele não possui mais acessos disponíveis para consulta de processos.";
        $chatData = [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($_SESSION['chat_history'], [['role'=>'user', 'content'=>$prompt]])
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($chatData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiOpenAI
            ]
        ]);
        $respostaIA = curl_exec($ch);
        curl_close($ch);
        $resposta = json_decode($respostaIA, true);
        $conteudoResposta = $resposta['choices'][0]['message']['content'] ?? $erro;
        jsonResponse($conteudoResposta, 403);
    }

    // Debita saldo
    $stmtDebito = $conn->prepare("UPDATE usuarios SET acessos_restantes = acessos_restantes - 1 WHERE id = ?");
    $stmtDebito->bind_param("i", $usuario_id);
    $stmtDebito->execute();

    // Consulta DataJud
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
    $resDatajud = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($resDatajud, true);
    if (!$dados || isset($dados['error'])) {
        $erro = "Erro na consulta ao DataJud.";
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

        $prompt = "Explique para o usuário que houve uma falha técnica ao consultar o DataJud.";
        $chatData = [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($_SESSION['chat_history'], [['role'=>'user', 'content'=>$prompt]])
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($chatData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiOpenAI
            ]
        ]);
        $respostaIA = curl_exec($ch);
        curl_close($ch);
        $resposta = json_decode($respostaIA, true);
        $conteudoResposta = $resposta['choices'][0]['message']['content'] ?? $erro;
        jsonResponse($conteudoResposta);
    }

    if (!empty($dados['hits']['hits'])) {
        foreach ($dados['hits']['hits'] as $item) {
            $processo = $item['_source'] ?? null;
            if ($processo) {
                $resumo = [
                    'classeProcessual' => $processo['classeProcessual'] ?? '',
                    'assunto' => $processo['assunto'] ?? '',
                    'tribunal' => $processo['orgaoJulgador']['nomeOrgao'] ?? '',
                    'partes' => $processo['partes'] ?? [],
                    // Movimentos: ordena por data e pega até 5 mais recentes
                    'movimentos' => array_slice(
                        (function($movs) {
                            usort($movs, function($a, $b) {
                                return strtotime($b['dataHora']) <=> strtotime($a['dataHora']);
                            });
                            return $movs;
                        })($processo['movimentos'] ?? []),
                        0, 5
                    )
                ];

                $prompt = "Fale para um advogado, descreva o processo com od detalhes mais recentes no início do texto:\n"
                    . json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $prompt = mb_substr($prompt, 0, 3500);

                $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $prompt];
                $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

                $chatData = [
                    'model' => 'gpt-4o',
                    'messages' => $_SESSION['chat_history']
                ];

                $ch = curl_init('https://api.openai.com/v1/chat/completions');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($chatData),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $apiOpenAI
                    ]
                ]);
                $respostaIA = curl_exec($ch);
                curl_close($ch);

                $resposta = json_decode($respostaIA, true);
                $conteudoResposta = $resposta['choices'][0]['message']['content'] ?? 'Erro ao interpretar resposta.';
                $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $conteudoResposta];
                $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

                $saldoRestante = $resSaldo['acessos_restantes'] - 1;
                $stmtHist = $conn->prepare("INSERT INTO historico_consultas (usuario_id, numero_processo, retorno_api, saldo_restante) VALUES (?, ?, ?, ?)");
                $stmtHist->bind_param("issi", $usuario_id, $numeroProcesso, $conteudoResposta, $saldoRestante);
                $stmtHist->execute();

                jsonResponse($conteudoResposta);
            }
        }
    } else {
        $erro = "Processo não encontrado.";
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $erro];
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

        $prompt = "Explique para o usuário que o processo $numeroProcesso não foi encontrado no DataJud.";
        $chatData = [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($_SESSION['chat_history'], [['role'=>'user', 'content'=>$prompt]])
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($chatData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiOpenAI
            ]
        ]);
        $respostaIA = curl_exec($ch);
        curl_close($ch);
        $resposta = json_decode($respostaIA, true);
        $conteudoResposta = $resposta['choices'][0]['message']['content'] ?? $erro;
        jsonResponse($conteudoResposta);
    }
}

// Se chegou aqui, não há número de processo no contexto/sessão
$_SESSION['chat_history'][] = ['role' => 'user', 'content' => $mensagem];
$_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

$chatData = [
    'model' => 'gpt-3.5-turbo',
    'messages' => $_SESSION['chat_history']
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($chatData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiOpenAI
    ]
]);
$respostaIA = curl_exec($ch);
curl_close($ch);

$resposta = json_decode($respostaIA, true);
$conteudoResposta = $resposta['choices'][0]['message']['content'] ?? 'Por favor, forneça o número do processo no padrão CNJ para consulta. Exemplo: 0574988-41.2023.8.04.0001 ou apenas os 20 dígitos.';

$_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $conteudoResposta];
$_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

jsonResponse($conteudoResposta);

?>
