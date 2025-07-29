<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
$conn = new mysqli("localhost", "root", "", "processos");
if ($conn->connect_error) {
    http_response_code(500);
    die("Erro ao conectar ao banco de dados");
}
?>