<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'conexao.php';

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

$usuario_id = $_GET['usuario_id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuario nao autenticado']);
    exit;
}

$usageStats = $_SESSION['usage_stats'] ?? defaultUsageStats();

$stmtSaldo = $conn->prepare("SELECT acessos_restantes FROM usuarios WHERE id = ?");
$stmtSaldo->bind_param("i", $usuario_id);
$stmtSaldo->execute();
$saldoRow = $stmtSaldo->get_result()->fetch_assoc();
$consultasRestantes = isset($saldoRow['acessos_restantes']) ? (int) $saldoRow['acessos_restantes'] : null;

$stmt = $conn->prepare("SELECT id, numero_processo, retorno_api, saldo_restante, data_hora FROM historico_consultas WHERE usuario_id = ? ORDER BY data_hora DESC LIMIT 20");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$historico = [];
while ($row = $result->fetch_assoc()) {
    $historico[] = [
        'id' => (int) $row['id'],
        'numero_processo' => $row['numero_processo'],
        'retorno_api' => $row['retorno_api'],
        'saldo_restante' => isset($row['saldo_restante']) ? (int) $row['saldo_restante'] : null,
        'data_hora' => $row['data_hora'],
    ];
}

echo json_encode([
    'items' => $historico,
    'summary' => [
        'consultas_restantes' => $consultasRestantes,
        'historico_count' => count($historico),
        'ultima_consulta_em' => $historico[0]['data_hora'] ?? null,
        'session_usage' => $usageStats,
    ],
], JSON_UNESCAPED_UNICODE);
