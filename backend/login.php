<?php
session_start();
require 'conexao.php';
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data["email"] ?? '');
$senha = trim($data["senha"] ?? '');
$stmt = $conn->prepare("SELECT id, senha, email_validado FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res && password_verify($senha, $res["senha"]) && $res["email_validado"]) {
    $_SESSION["usuario_id"] = $res["id"];
    echo json_encode([
        "success" => true,
        "usuario_id" => $_SESSION["usuario_id"]
    ]);
} else {
    http_response_code(401);
    echo json_encode(["success" => false]);
}
