<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'marcusabagnale@gmail.com';
$mail->Password = 'mpglanqsashpfsaj';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('marcusabagnale@gmail.com', 'Seu Nome');
$mail->addAddress('marcus.lima@gmail.com');
$mail->Subject = 'Teste';
$mail->Body = 'Email funcionando!';
$mail->send();

echo "Enviado com sucesso";
