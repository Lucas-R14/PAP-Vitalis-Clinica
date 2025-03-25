<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['logado'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utilizador não está logado']);
    exit();
}

$dados = json_decode(file_get_contents('php://input'), true);
$codigo_informado = $dados['codigo'];

// Verifica se o código existe e não expirou (30 minutos)
if (!isset($_SESSION['codigo_verificacao']) || 
    !isset($_SESSION['tempo_codigo']) || 
    (time() - $_SESSION['tempo_codigo']) > 1800) {
    echo json_encode(['success' => false, 'message' => 'Código expirado ou inválido']);
    exit();
}

// Verifica se o código está correto
if ($codigo_informado !== $_SESSION['codigo_verificacao']) {
    echo json_encode(['success' => false, 'message' => 'Código incorreto']);
    exit();
}

// Recupera os dados temporários
$dados_alteracao = $_SESSION['dados_alteracao'];
$id_cliente = $_SESSION['id_cliente'];

// Atualiza os dados sensíveis
$sql = "UPDATE Cliente SET 
        email = ?, 
        nif = ?,
        cc = ?,
        data_nascimento = ?,
        sexo = ?
        WHERE id_cliente = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", 
    $dados_alteracao['email'],
    $dados_alteracao['nif'],
    $dados_alteracao['cc'],
    $dados_alteracao['data_nascimento'],
    $dados_alteracao['sexo'],
    $id_cliente
);

if ($stmt->execute()) {
    // Limpa as variáveis de sessão temporárias
    unset($_SESSION['codigo_verificacao']);
    unset($_SESSION['dados_alteracao']);
    unset($_SESSION['tempo_codigo']);
    
    echo json_encode(['success' => true, 'message' => 'Dados atualizados com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar dados']);
}
?> 