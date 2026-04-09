<?php

require __DIR__ . '/conexao.php';

$data = json_decode(file_get_contents("php://input"), true);
$nome = $data["nome"] ?? '';
$email = $data["email"] ?? '';
$celular = $data["celular"] ?? '';
$senha = password_hash($data["senha"] ?? '', PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(16));

// Verifica se o e-mail já existe
$stmt = $conn->prepare("SELECT id, email_validado FROM usuarios WHERE email = ?");
if (!$stmt) {
    http_response_code(500);
    echo "Erro ao preparar consulta de validação.";
    exit;
}
$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    http_response_code(500);
    echo "Erro ao verificar e-mail.";
    exit;
}
$stmt->store_result();
$usuario = null;
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $email_validado);
    $stmt->fetch();
    $usuario = [
        "id" => $id,
        "email_validado" => $email_validado
    ];
}
$stmt->close();

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
if (!$stmt) {
    http_response_code(500);
    echo "Erro ao preparar cadastro.";
    exit;
}
$stmt->bind_param("sssss", $nome, $email, $celular, $senha, $token);
if (!$stmt->execute()) {
    http_response_code(500);
    echo "Erro ao cadastrar usuário.";
    exit;
}
$stmt->close();

echo 'Cadastro realizado com sucesso.';
?>


