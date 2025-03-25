<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

$criacao_sucesso = false; // Variável para controlar se a criação foi bem-sucedida

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'db_connect.php'; // Conexão com o banco de dados
    require_once 'gerarIdFuncionario.php'; // Gera ID único

    // Gera um ID único
    $id_funcionario = gerarIdFuncionario($conn);

    // Dados do formulário
    $nome = $_POST['nome'];
    $nif = $_POST['nif'];
    $cc = $_POST['cc'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $morada = $_POST['morada'];
    $apartamento = $_POST['apartamento'];
    $codigo_postal = $_POST['codigo_postal'];
    $distrito = $_POST['distrito'];
    $concelho = $_POST['concelho'];
    $cargo = $_POST['cargo'];
    $especialidade = $_POST['especialidade']; // NOVO CAMPO
    $inicio_turno = $_POST['inicio_turno'];   // NOVO CAMPO
    $fim_turno = $_POST['fim_turno'];         // NOVO CAMPO
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); 
    $nivel_acesso = $_POST['nivel_acesso'];
    $data_admissao = date("Y-m-d"); 
    $estado = 'Ativo';

    // Concatena endereço
    $endereco = "$morada, " . ($apartamento ? "$apartamento, " : "") . "$codigo_postal, $distrito, $concelho";

    // Inserção no banco (removido o campo "departamento")
    $sql = "INSERT INTO funcionario (id_funcionario, nome, nif, cc, data_nascimento, sexo, telefone, email, endereco, cargo, especialidade, inicio_turno, fim_turno, senha, nivel_acesso, data_admissao, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssss", $id_funcionario, $nome, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $cargo, $especialidade, $inicio_turno, $fim_turno, $senha, $nivel_acesso, $data_admissao, $estado);

    if ($stmt->execute()) {
        $criacao_sucesso = true;
    } else {
        $mensagem_erro = "Erro ao adicionar funcionário: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

include '../HTML/criar_funcionarios.html';
?>