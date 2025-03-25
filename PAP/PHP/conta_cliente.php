<?php
session_start();
require_once 'db_connect.php';

// Verifica se o utilizador está logado
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Busca todos os dados do cliente
$sql = "SELECT id_cliente, nome, nacionalidade, nif, cc, data_nascimento, 
        sexo, telefone, email, endereco, data_registo, historico_medico 
        FROM Cliente 
        WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

$stmt->close();
$conn->close();

include '../HTML/conta_cliente.html';
?> 