<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'conexao.php';

$usuario_id = $_GET['usuario_id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit;
}

$stmt = $conn->prepare("SELECT id, numero_processo, retorno_api, saldo_restante, data_hora FROM historico_consultas WHERE usuario_id = ? ORDER BY data_hora DESC LIMIT 20");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$historico = [];
while ($row = $result->fetch_assoc()) {
    $historico[] = [
        'id' => $row['id'],
        'numero_processo' => $row['numero_processo'],
        'retorno_api' => $row['retorno_api'],
        'saldo_restante' => $row['saldo_restante'],
        'data_hora' => $row['data_hora'],
    ];
}

echo json_encode($historico, JSON_UNESCAPED_UNICODE);
?>
