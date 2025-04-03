<?php
session_start();

// Verifica se o usuário está logado como funcionário
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

require_once 'db_connect.php'; // Inclui a conexão

$id_funcionario = $_SESSION['id_funcionario'];

// Busca os dados do funcionário
$sql = "SELECT id_funcionario, nome, nif, cc, data_nascimento, sexo, telefone, email, 
       endereco, data_admissao, nivel_acesso, especialidade, cargo, inicio_turno, fim_turno 
       FROM Funcionario WHERE id_funcionario = ?";
       
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
$funcionario = $result->fetch_assoc();

// Formatar dados para exibição
$data_nascimento_formatada = date('d/m/Y', strtotime($funcionario['data_nascimento']));
$data_admissao_formatada = date('d/m/Y', strtotime($funcionario['data_admissao']));

// Calcular idade
$hoje = new DateTime();
$nascimento = new DateTime($funcionario['data_nascimento']);
$idade = $nascimento->diff($hoje)->y;

$conn->close();

include '../HTML/conta_funcionario.html';
?> 