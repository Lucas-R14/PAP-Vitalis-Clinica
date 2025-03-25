<?php
session_start();
require_once 'db_connect.php';

// Verifica se o utilizador está logado
if (!isset($_SESSION['logado'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utilizador não está logado']);
    exit();
}

// Recebe os dados do POST
$dados = json_decode(file_get_contents('php://input'), true);

// Verifica se todos os campos necessários foram enviados
if (!isset($dados['nome']) || !isset($dados['email']) || !isset($dados['telefone']) || 
    !isset($dados['nacionalidade']) || !isset($dados['endereco']) || 
    !isset($dados['historico_medico']) || !isset($dados['senha'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Primeiro, verifica a senha
$sql_senha = "SELECT senha FROM Cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql_senha);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

// Verifica se a senha está correta
if (!password_verify($dados['senha'], $cliente['senha'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
    exit();
}

// Atualiza os dados do cliente
$sql = "UPDATE Cliente SET 
        nome = ?, 
        nacionalidade = ?,
        telefone = ?, 
        email = ?, 
        endereco = ?, 
        historico_medico = ?
        WHERE id_cliente = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", 
    $dados['nome'],
    $dados['nacionalidade'],
    $dados['telefone'],
    $dados['email'],
    $dados['endereco'],
    $dados['historico_medico'],
    $id_cliente
);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Dados atualizados com sucesso']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar dados: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?> 