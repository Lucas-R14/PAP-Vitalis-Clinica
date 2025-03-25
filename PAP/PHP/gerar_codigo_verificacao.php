<?php
session_start();
require_once 'db_connect.php';
require_once 'email.php';

if (!isset($_SESSION['logado'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utilizador não está logado']);
    exit();
}

$dados = json_decode(file_get_contents('php://input'), true);
$id_cliente = $_SESSION['id_cliente'];

// Verifica a senha
$sql = "SELECT senha, email, nome FROM Cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if (!password_verify($dados['senha'], $cliente['senha'])) {
    echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
    exit();
}

// Gera código de verificação de 6 dígitos
$codigo_verificacao = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Armazena o código e os dados temporários na sessão
$_SESSION['codigo_verificacao'] = $codigo_verificacao;
$_SESSION['dados_alteracao'] = $dados;
$_SESSION['tempo_codigo'] = time(); // Para expiração do código

// Envia o email com o código
$resultado = enviarEmailVerificacao($cliente['email'], $cliente['nome'], $codigo_verificacao);

if ($resultado === true) {
    echo json_encode(['success' => true, 'message' => 'Código de verificação enviado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao enviar código de verificação']);
}
?> 