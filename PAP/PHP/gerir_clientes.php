<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

// Conexão com o banco de dados
$host = "localhost";
$user = "root";
$password = "mysql";
$dbname = "vitalis_clinica";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
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
?>

<?php include '../HTML/gerir_clientes.html'; ?>

<?php
$conn->close();
?>