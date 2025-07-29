<?php
session_start();
require 'conexao.php';

$usuario_id = $_SESSION["usuario_id"] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;

if (!$usuario_id || !$id) {
    http_response_code(400);
    echo "Requisição inválida.";
    exit;
}

$stmt = $conn->prepare("DELETE FROM historico_consultas WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();

echo "Excluído com sucesso";
?>
