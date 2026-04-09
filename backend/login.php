<?php
session_start();
require __DIR__ . '/conexao.php';

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data["email"] ?? '');
$senha = trim($data["senha"] ?? '');

$stmt = $conn->prepare("SELECT id, senha, email_validado FROM usuarios WHERE email = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro interno ao preparar login."]);
    exit;
}

$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro interno ao executar login."]);
    exit;
}

$stmt->store_result();
if ($stmt->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "E-mail ou senha inválidos."]);
    exit;
}

$stmt->bind_result($id, $senhaHash, $emailValidado);
$stmt->fetch();
$stmt->close();

if (!password_verify($senha, $senhaHash)) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "E-mail ou senha inválidos."]);
    exit;
}

$_SESSION["usuario_id"] = $id;
echo json_encode([
    "success" => true,
    "usuario_id" => $_SESSION["usuario_id"]
]);
