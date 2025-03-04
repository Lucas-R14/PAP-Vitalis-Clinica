<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<?php include '../HTML/painel_utilizador.html'; ?>