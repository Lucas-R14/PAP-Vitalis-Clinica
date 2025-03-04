<?php
session_start();

// Verifica se o usuário está logado e é um funcionário
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

$nivel_acesso = $_SESSION['nivel_acesso'];
?>

<?php include '../HTML/painel_funcionario.html'; ?>