<?php
session_start();

require_once 'db_connect.php'; // Inclui a conexão

// Verifica se o utilizador está logado e é um administrador
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

// Barra de busca
$busca = "";
if (isset($_GET['busca'])) {
    $busca = $_GET['busca'];
    $sql = "SELECT id_cliente, nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_registo FROM Cliente WHERE nome LIKE ?";
    $stmt = $conn->prepare($sql);
    $busca_param = "%$busca%";
    $stmt->bind_param("s", $busca_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id_cliente, nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_registo FROM Cliente";
    $result = $conn->query($sql);
}

include '../HTML/gerir_clientes.html';

$conn->close();
?>
