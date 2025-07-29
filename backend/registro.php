<?php
require 'conexao.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);
$nome = $data["nome"] ?? '';
$email = $data["email"] ?? '';
$senha = password_hash($data["senha"] ?? '', PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(16));

// Verifica se o e-mail já existe
$stmt = $conn->prepare("SELECT id, email_validado, token_validacao FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if ($usuario) {
    if ($usuario["email_validado"]) {
        echo "E-mail já cadastrado e validado. Faça login.";
    } else {
        // Reenviar e-mail de validação
        $token = $usuario["token_validacao"];
        enviarEmailValidacao($email, $nome, $token, true);
    }
    exit;
}

// Cadastro novo
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, token_validacao) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $email, $senha, $token);
$stmt->execute();

// Envia e-mail de validação
enviarEmailValidacao($email, $nome, $token, false);

// Função de envio
function enviarEmailValidacao($email, $nome, $token, $reenviar = false) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'marcusabagnale@gmail.com';
        $mail->Password = 'mpglanqsashpfsaj'; // ✅ senha de app
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('marcusabagnale@gmail.com', 'Assistente Jurídico');
        $mail->addAddress($email, $nome);

        $mail->Subject = $reenviar ? 'Reenvio: Valide seu cadastro' : 'Valide seu cadastro no Assistente Jurídico';
        $mail->Body = "Olá $nome,\n\nClique para validar seu cadastro:\nhttp://localhost/assistentejuridico/backend/validar.php?email=$email&token=$token";

        $mail->send();
        echo $reenviar
            ? "E-mail já cadastrado, mas ainda não validado. Reenviamos o link."
            : "Cadastro realizado. Verifique seu e-mail para validar.";
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
}
?>
