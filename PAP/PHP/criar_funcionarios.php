<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    require_once 'db_connect.php'; // Inclui a conexão

    // Dados do formulário
    $nome = $_POST['nome'];
    $nif = $_POST['nif'];
    $cc = $_POST['cc'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Encripta a senha
    $nivel_acesso = $_POST['nivel_acesso'];

    // Inserir novo funcionário
    $sql = "INSERT INTO Funcionario (nome, nif, cc, data_nascimento, sexo, telefone, email, endereco, senha, nivel_acesso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $nome, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $senha, $nivel_acesso);

    if ($stmt->execute()) {
        $mensagem_sucesso = "Funcionário adicionado com sucesso!";
    } else {
        $mensagem_erro = "Erro ao adicionar funcionário: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<?php include '../HTML/criar_funcionarios'; ?>