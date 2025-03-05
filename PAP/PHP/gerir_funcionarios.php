<?php
session_start();

require_once 'db_connect.php'; // Inclui a conexão

if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

// Inicializa a variável $busca
$busca = "";

// Verifica se há uma busca
if (isset($_GET['busca'])) {
    $busca = $_GET['busca'];
    $sql = "SELECT id_funcionario, nome, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_admissao, nivel_acesso FROM Funcionario WHERE nome LIKE ?";
    $stmt = $conn->prepare($sql);
    $busca_param = "%$busca%";
    $stmt->bind_param("s", $busca_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id_funcionario, nome, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_admissao, nivel_acesso FROM Funcionario";
    $result = $conn->query($sql);
}
?>

<?php include '../HTML/gerir_funcionarios.html'; ?>

<?php
$conn->close();
?>