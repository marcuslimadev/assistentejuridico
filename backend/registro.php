<?php

require 'conexao.php';

$data = json_decode(file_get_contents("php://input"), true);
$nome = $data["nome"] ?? '';
$email = $data["email"] ?? '';
$celular = $data["celular"] ?? '';
$senha = password_hash($data["senha"] ?? '', PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(16));

// Verifica se o e-mail já existe
$stmt = $conn->prepare("SELECT id, email_validado FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if ($usuario) {
    if ($usuario["email_validado"]) {
        echo "E-mail já cadastrado e validado. Faça login.";
    } else {
        echo "E-mail já cadastrado, mas ainda não validado. Aguarde liberação.";
    }
    exit;
}

// Cadastro novo (agora salvando o celular)
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, celular, senha, token_validacao) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nome, $email, $celular, $senha, $token);
$stmt->execute();

echo 'Cadastro realizado com sucesso.';
?>


