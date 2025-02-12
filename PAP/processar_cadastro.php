<?php
$host = "localhost";
$username = "root"; // Substitua pelo seu nome de utilizador do MySQL
$password = "mysql"; // Substitua pela sua senha do MySQL
$dbname = "vitalis_clinica";

// Criar conexão
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se as senhas coincidem
if ($_POST['senha'] !== $_POST['confirmar_senha']) {
    die("As senhas não coincidem.");
}

// Preparar e executar a query SQL
$stmt = $conn->prepare("INSERT INTO Cliente (nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_registro, historico_medico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)");
$stmt->bind_param("ssssssssss", $nome, $nacionalidade, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $historico_medico);

// Atribuir valores às variáveis
$nome = $_POST['nome'];
$nacionalidade = $_POST['nacionalidade'];
$nif = $_POST['nif'];
$cc = $_POST['cc'];
$data_nascimento = $_POST['data_nascimento'];
$sexo = $_POST['sexo'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$endereco = $_POST['endereco'];
$historico_medico = $_POST['historico_medico'];

// Executar a query
if ($stmt->execute()) {
    echo "Registo efetuado com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>