<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    // Recebe e decodifica os dados JSON
    $dados = json_decode(file_get_contents('php://input'), true);

    // Verifica se o utilizador está autenticado
    if (!isset($_SESSION['id_cliente'])) {
        throw new Exception('Utilizador não autenticado');
    }

    // Verifica se todos os campos necessários foram enviados
    if (!isset($dados['senhaAtual']) || !isset($dados['novaSenha'])) {
        throw new Exception('Dados incompletos');
    }

    $id_cliente = $_SESSION['id_cliente'];
    $senha_atual = $dados['senhaAtual'];
    $nova_senha = $dados['novaSenha'];

    // Busca a senha hash atual do utilizador
    $sql = "SELECT senha FROM Cliente WHERE id_cliente = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Erro na preparação da consulta: ' . $conn->error);
    }

    $stmt->bind_param("s", $id_cliente);
    
    if (!$stmt->execute()) {
        throw new Exception('Erro ao executar a consulta: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if (!$usuario) {
        throw new Exception('Utilizador não encontrado');
    }

    // Verifica se a senha atual está correta usando password_verify
    if (!password_verify($senha_atual, $usuario['senha'])) {
        throw new Exception('Senha atual incorreta');
    }

    // Gera o hash da nova senha
    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Atualiza para a nova senha
    $sql_update = "UPDATE Cliente SET senha = ? WHERE id_cliente = ?";
    $stmt_update = $conn->prepare($sql_update);

    if (!$stmt_update) {
        throw new Exception('Erro na preparação da atualização: ' . $conn->error);
    }

    $stmt_update->bind_param("ss", $nova_senha_hash, $id_cliente);

    if (!$stmt_update->execute()) {
        throw new Exception('Erro ao atualizar senha: ' . $stmt_update->error);
    }

    if ($stmt_update->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
    } else {
        throw new Exception('Nenhuma alteração realizada');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_update)) $stmt_update->close();
    if (isset($conn)) $conn->close();
}
?> 