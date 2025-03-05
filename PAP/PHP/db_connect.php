<?php
require_once __DIR__ . '/config.php'; 

$host = env('DB_HOST', 'localhost');
$user = env('DB_USER', 'root');
$password = env('DB_PASSWORD', '');
$dbname = env('DB_NAME', '');

// Criar a conexão
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão com a base de dados: " . $conn->connect_error);
}
?>
