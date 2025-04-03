<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario') {
    // Retorna resposta em JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit();
}

// Recebe os dados do formulário
$currentPassword = $_POST['currentPassword'];
$newPassword = $_POST['newPassword'];

// Validações
if (empty($currentPassword) || empty($newPassword)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
    exit();
}

if (strlen($newPassword) < 6) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'A nova senha deve ter pelo menos 6 caracteres']);
    exit();
}

// Conectar ao banco de dados
require_once 'db_connect.php';

// ID do funcionário logado
$id_funcionario = $_SESSION['id_funcionario'];

// Buscar a senha atual do banco de dados
$sql = "SELECT senha FROM Funcionario WHERE id_funcionario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Funcionário não encontrado']);
    $stmt->close();
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();
$senhaAtualHash = $row['senha'];

// Verificar se a senha atual está correta
if (!password_verify($currentPassword, $senhaAtualHash)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
    $stmt->close();
    $conn->close();
    exit();
}

// Gerar o hash da nova senha
$novaSenhaHash = password_hash($newPassword, PASSWORD_DEFAULT);

// Atualizar a senha no banco de dados
$sql = "UPDATE Funcionario SET senha = ? WHERE id_funcionario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $novaSenhaHash, $id_funcionario);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro ao alterar senha: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?> 