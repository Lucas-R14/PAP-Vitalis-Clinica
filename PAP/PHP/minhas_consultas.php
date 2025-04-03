<?php
session_start();

// Verificar se o utilizador está autenticado e é médico
if (!isset($_SESSION['id_funcionario']) || $_SESSION['nivel_acesso'] !== 'Médico') {
    header('Location: login.php');
    exit();
}

require_once 'db_connect.php';

// Obter o ID do médico logado
$id_medico = $_SESSION['id_funcionario'];

// Se for uma requisição AJAX para obter as consultas
if (isset($_GET['action']) && $_GET['action'] === 'getConsultas') {
    // Consulta SQL para obter as consultas do médico
    $sql = "SELECT c.*, cl.nome as nome_cliente 
            FROM consulta c 
            INNER JOIN cliente cl ON c.id_cliente = cl.id_cliente 
            WHERE c.id_funcionario = ? 
            AND c.data_consulta >= CURRENT_DATE 
            ORDER BY c.data_consulta, c.hora_consulta";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $id_medico);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $consultas = array();
    while ($row = $resultado->fetch_assoc()) {
        $consultas[] = array(
            'id_consulta' => $row['id_consulta'],
            'data_consulta' => date('d/m/Y', strtotime($row['data_consulta'])),
            'hora_consulta' => date('H:i', strtotime($row['hora_consulta'])),
            'nome_cliente' => htmlspecialchars($row['nome_cliente']),
            'tipo_consulta' => htmlspecialchars($row['tipo_consulta']),
            'estado' => $row['estado']
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($consultas);
    exit();

    include '../HTML/minhas_consultas.html';
}

