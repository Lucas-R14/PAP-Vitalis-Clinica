<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['logado'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$idConsulta = $data['id'];
$novaData = $data['data'];
$novoHorario = $data['horario'];
$senha = $data['senha'];
$id_cliente = $_SESSION['id_cliente'];

// Verifica a senha do cliente
$sql = "SELECT senha FROM Cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!password_verify($senha, $row['senha'])) {
        echo json_encode(['success' => false, 'message' => 'Senha incorreta.']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Cliente não encontrado.']);
    exit();
}

// Verifica se já existe uma consulta no mesmo horário para o médico
$sql = "SELECT c.id_funcionario FROM Consulta c WHERE c.id_consulta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idConsulta);
$stmt->execute();
$result = $stmt->get_result();
$consulta = $result->fetch_assoc();
$idMedico = $consulta['id_funcionario'];

// Verifica disponibilidade do novo horário
$sql = "SELECT COUNT(*) as total FROM Consulta 
        WHERE id_funcionario = ? 
        AND data_consulta = ? 
        AND hora_consulta = ? 
        AND id_consulta != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $idMedico, $novaData, $novoHorario, $idConsulta);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['total'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Horário já ocupado.']);
    exit();
}

// Atualiza a consulta
$sql = "UPDATE Consulta SET data_consulta = ?, hora_consulta = ? WHERE id_consulta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $novaData, $novoHorario, $idConsulta);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Consulta alterada com sucesso.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao alterar a consulta.']);
}

$stmt->close();
$conn->close();
?> 