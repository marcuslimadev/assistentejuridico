<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
$conn = new mysqli("193.203.166.228", "u815655858_assistentejuri", "Manaus@526341", "u815655858_assistentejuri");
if ($conn->connect_error) {
    http_response_code(500);
    die("Erro ao conectar ao banco de dados");
}
?>