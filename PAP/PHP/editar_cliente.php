<?php
session_start();

require_once 'db_connect.php'; // Inclui a conexão

if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

$id_cliente = $_GET['id'];

// Busca os dados do cliente
$sql = "SELECT id_cliente, nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_registo FROM Cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

// Atualiza os dados do cliente
$atualizacao_sucesso = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $nacionalidade = $_POST['nacionalidade'];
    $nif = $_POST['nif'];
    $cc = $_POST['cc'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;

    if ($senha) {
        $sql = "UPDATE Cliente SET nome = ?, nacionalidade = ?, nif = ?, cc = ?, data_nascimento = ?, sexo = ?, telefone = ?, email = ?, endereco = ?, senha = ? WHERE id_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $nome, $nacionalidade, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $senha, $id_cliente);
    } else {
        $sql = "UPDATE Cliente SET nome = ?, nacionalidade = ?, nif = ?, cc = ?, data_nascimento = ?, sexo = ?, telefone = ?, email = ?, endereco = ? WHERE id_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $nome, $nacionalidade, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $id_cliente);
    }

    if ($stmt->execute()) {
        $atualizacao_sucesso = true; // Marca a atualização como bem-sucedida
    } else {
        $mensagem_erro = "Erro ao atualizar cliente: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<?php include '../HTML/editar_cliente.html'; ?>