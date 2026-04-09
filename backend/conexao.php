<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

mysqli_report(MYSQLI_REPORT_OFF);

try {
    // Em ambiente Windows/XAMPP, 127.0.0.1 evita falhas de resolução para IPv6 (::1).
    $conn = new mysqli("127.0.0.1", "root", "", "assistentejuridico", 3306);

    if ($conn->connect_errno) {
        http_response_code(500);
        die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
} catch (Throwable $e) {
    http_response_code(500);
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>