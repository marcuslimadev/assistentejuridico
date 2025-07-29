<?php
session_start();
require 'conexao.php';

$usuario_id = $_SESSION["usuario_id"] ?? null;
if (!$usuario_id) {
    http_response_code(401);
    echo json_encode(["erro" => "Não autenticado"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, numero_processo, retorno_api, data_hora, saldo_restante FROM historico_consultas WHERE usuario_id = ? ORDER BY data_hora DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$dados = [];
while ($row = $result->fetch_assoc()) {
    $dados[] = $row;
}

header('Content-Type: application/json');
echo json_encode($dados);
?>
