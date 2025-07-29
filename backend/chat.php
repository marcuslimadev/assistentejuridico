<?php
session_start();

function loadEnv($path)
{
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

header('Content-Type: text/html; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$mensagem = $data["mensagem"] ?? '';
$usuario_id = $_SESSION["usuario_id"] ?? null;

if (!$usuario_id) {
    http_response_code(401);
    exit("Você precisa estar logado.");
}

if (!$mensagem) {
    http_response_code(400);
    exit("Mensagem ausente.");
}

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

if (preg_match('/\d{7}-?\d{2}\.\d{4}\.\d\.\d{2}\.\d{4}/', $mensagem, $m)) {
    $numero = preg_replace('/\D/', '', $m[0]);
    $uf = substr($numero, 14, 2);
    $url = $urls[$uf] ?? null;

    if (!$url) {
        exit("Não foi possível identificar o tribunal responsável por este número.");
    }

    $stmtSaldo = $conn->prepare("SELECT acessos_restantes FROM usuarios WHERE id = ?");
    $stmtSaldo->bind_param("i", $usuario_id);
    $stmtSaldo->execute();
    $resSaldo = $stmtSaldo->get_result()->fetch_assoc();

    if (!$resSaldo || $resSaldo["acessos_restantes"] <= 0) {
        http_response_code(403);
        exit("Você não possui mais acessos.");
    }

    $stmtDebito = $conn->prepare("UPDATE usuarios SET acessos_restantes = acessos_restantes - 1 WHERE id = ?");
    $stmtDebito->bind_param("i", $usuario_id);
    $stmtDebito->execute();

    $data = ["query" => ["term" => ["numeroProcesso" => $numero]]];
    $headers = [
        "Authorization: ApiKey $apiDataJudKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $resDatajud = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($resDatajud, true);
    if (!$dados || isset($dados['error'])) {
        exit("Erro na consulta ao DataJud.");
    }

    if (!empty($dados['hits']['hits'])) {
        $respostas = [];
        foreach ($dados['hits']['hits'] as $item) {
            $processo = $item['_source'] ?? null;
            if ($processo) {
                $resumo = [
                    'classeProcessual' => $processo['classeProcessual'] ?? '',
                    'assunto' => $processo['assunto'] ?? '',
                    'tribunal' => $processo['orgaoJulgador']['nomeOrgao'] ?? '',
                    'partes' => $processo['partes'] ?? [],
                    'movimentos' => array_slice($processo['movimentos'] ?? [], 0, 3)
                ];

                $prompt = "Explique esse processo judicial de forma simples, mas com todos os detalhes importantes:
"
                    . json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $prompt = mb_substr($prompt, 0, 3000);

                $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $prompt];
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
                $conteudoResposta = $resposta['choices'][0]['message']['content'] ?? 'Erro ao interpretar resposta.';
                $respostas[] = $conteudoResposta;

                $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $conteudoResposta];
                $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

                $saldoRestante = $resSaldo['acessos_restantes'] - 1;
                $stmtHist = $conn->prepare("INSERT INTO historico_consultas (usuario_id, numero_processo, retorno_api, saldo_restante) VALUES (?, ?, ?, ?)");
                $stmtHist->bind_param("issi", $usuario_id, $numero, $conteudoResposta, $saldoRestante);
                $stmtHist->execute();
            }
        }

        echo implode("<hr>", $respostas);
        exit;
    } else {
        exit("Processo não encontrado.");
    }
}

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
$conteudoResposta = $resposta['choices'][0]['message']['content'] ?? 'Erro ao gerar resposta.';

$_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $conteudoResposta];
$_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);

echo $conteudoResposta;