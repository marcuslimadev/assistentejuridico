<?php
require 'conexao.php';

$email = $_GET["email"] ?? '';
$token = $_GET["token"] ?? '';

$stmt = $conn->prepare("UPDATE usuarios SET email_validado = 1 WHERE email = ? AND token_validacao = ?");
$stmt->bind_param("ss", $email, $token);
$stmt->execute();

if ($stmt->affected_rows) {
    echo "E-mail validado com sucesso. Você já pode fazer login.";
} else {
    echo "Link inválido ou já utilizado.";
}
?>
