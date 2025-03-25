<?php
session_start();
require_once 'db_connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['logado'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado.']);
    exit();
}

// Recebe os dados enviados via POST
$data = json_decode(file_get_contents('php://input'), true);
$idConsulta = $data['id'];
$senha = $data['senha'];

if (empty($idConsulta) || empty($senha)) {
    echo json_encode(['success' => false, 'message' => 'ID da consulta ou senha não fornecidos.']);
    exit();
}

// Verifica a senha do cliente
$id_cliente = $_SESSION['id_cliente'];
$sql = "SELECT senha FROM Cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if (!password_verify($senha, $cliente['senha'])) {
    echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
    exit();
}

// Apaga a consulta da base de dados
$sql = "DELETE FROM Consulta WHERE id_consulta = ? AND id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $idConsulta, $id_cliente);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Consulta cancelada com sucesso.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cancelar a consulta.']);
}

$stmt->close();
$conn->close();
?>