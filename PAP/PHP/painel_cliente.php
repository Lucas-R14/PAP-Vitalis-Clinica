<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

// Buscar as consultas agendadas do cliente
$id_cliente = $_SESSION['id_cliente'];
$sql = "SELECT c.data_consulta, c.hora_consulta, c.tipo_consulta, f.nome as nome_medico 
        FROM Consulta c 
        JOIN Funcionario f ON c.id_funcionario = f.id_funcionario 
        WHERE c.id_cliente = ? AND c.estado = 'Agendada' 
        ORDER BY c.data_consulta ASC, c.hora_consulta ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$consultas = $result->fetch_all(MYSQLI_ASSOC);

// Passar as consultas para o template
$_SESSION['consultas_agendadas'] = $consultas;

include '../HTML/painel_cliente.html';
?>