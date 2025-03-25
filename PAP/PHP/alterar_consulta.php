<?php
session_start();
require_once 'db_connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Busca as consultas agendadas do cliente
$sql = "SELECT c.id_consulta, c.data_consulta, c.hora_consulta, c.tipo_consulta, 
               c.id_funcionario, f.nome AS nome_medico 
        FROM Consulta c
        INNER JOIN Funcionario f ON c.id_funcionario = f.id_funcionario
        WHERE c.id_cliente = ? AND c.estado = 'Agendada'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

$consultas = [];
while ($row = $result->fetch_assoc()) {
    $consultas[] = $row;
}

$stmt->close();
$conn->close();

include '../HTML/alterar_consulta.html';
?>