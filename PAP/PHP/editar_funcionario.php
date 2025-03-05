<?php
session_start();

require_once 'db_connect.php'; // Inclui a conexão

// Verifica se o utilizador está logado e é um administrador
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

$id_funcionario = $_GET['id'];

// Busca os dados do funcionário
$sql = "SELECT id_funcionario, nome, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_admissao, nivel_acesso FROM Funcionario WHERE id_funcionario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
$funcionario = $result->fetch_assoc();

// Verifica se o funcionário é um Administrador
if ($funcionario['nivel_acesso'] === 'Administrador') {
    $conn->close();
    header("Location: gerir_funcionarios.php?erro=Não é possível editar um Administrador.");
    exit();
}

// Atualiza os dados do funcionário
$atualizacao_sucesso = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $nif = $_POST['nif'];
    $cc = $_POST['cc'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $data_admissao = $_POST['data_admissao'];
    $nivel_acesso = $_POST['nivel_acesso'];
    $senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;

    if ($senha) {
        $sql = "UPDATE Funcionario SET nome = ?, nif = ?, cc = ?, data_nascimento = ?, sexo = ?, telefone = ?, email = ?, endereco = ?, data_admissao = ?, nivel_acesso = ?, senha = ? WHERE id_funcionario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $nome, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $data_admissao, $nivel_acesso, $senha, $id_funcionario);
    } else {
        $sql = "UPDATE Funcionario SET nome = ?, nif = ?, cc = ?, data_nascimento = ?, sexo = ?, telefone = ?, email = ?, endereco = ?, data_admissao = ?, nivel_acesso = ? WHERE id_funcionario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $nome, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $data_admissao, $nivel_acesso, $id_funcionario);
    }

    if ($stmt->execute()) {
        $atualizacao_sucesso = true; // Marca a atualização como bem-sucedida
    } else {
        $mensagem_erro = "Erro ao atualizar funcionário: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<?php include '../HTML/editar_funcionario.html'; ?>