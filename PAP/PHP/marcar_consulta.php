<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit();
}

// Verifica o número de consultas agendadas do cliente
$id_cliente = $_SESSION['id_cliente'];
$sql = "SELECT COUNT(*) as total FROM Consulta WHERE id_cliente = ? AND estado = 'Agendada'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc();
$limite_atingido = $count['total'] >= 4;

function gerarIdConsulta($conn) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id_consulta = '';
    $tentativas = 0;

    do {
        $id_consulta = '';
        for ($i = 0; $i < 8; $i++) {
            $id_consulta .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        $sql = "SELECT id_consulta FROM Consulta WHERE id_consulta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_consulta);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $tentativas++;
        } else {
            break;
        }

        if ($tentativas > 100) {
            die("Erro: Não foi possível gerar um ID único.");
        }
    } while (true);

    return $id_consulta;
}

// Verifica se é uma requisição POST com JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // Recebe e decodifica os dados JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $id_cliente = $_SESSION['id_cliente'];
    $id_funcionario = $data['medico'];
    $data_consulta = $data['data'];
    $hora_consulta = $data['horario'];
    $tipo_consulta = $data['especialidade'];
    $senha = $data['senha'];

    // Verifica a senha do cliente
    $sql = "SELECT senha FROM Cliente WHERE id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente = $result->fetch_assoc();

    if (!password_verify($senha, $cliente['senha'])) {
        echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
        exit();
    }

    // Verifica o número de consultas agendadas do cliente
    $sql = "SELECT COUNT(*) as total FROM Consulta WHERE id_cliente = ? AND estado = 'Agendada'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();

    if ($count['total'] >= 4) {
        echo json_encode(['success' => false, 'message' => 'Não é possível agendar mais consultas. Você já atingiu o limite máximo de 4 consultas agendadas.']);
        exit();
    }

    // Verifica se já existe uma consulta no mesmo horário para o médico
    $sql = "SELECT id_consulta FROM Consulta WHERE id_funcionario = ? AND data_consulta = ? AND hora_consulta = ? AND estado = 'Agendada'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $id_funcionario, $data_consulta, $hora_consulta);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Este horário já está ocupado.']);
        exit();
    }

    // Gera um novo ID de consulta
    $id_consulta = gerarIdConsulta($conn);

    // Insere a nova consulta
    $sql = "INSERT INTO Consulta (id_consulta, id_cliente, id_funcionario, data_consulta, hora_consulta, tipo_consulta, estado) VALUES (?, ?, ?, ?, ?, ?, 'Agendada')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $id_consulta, $id_cliente, $id_funcionario, $data_consulta, $hora_consulta, $tipo_consulta);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Consulta agendada com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao agendar a consulta.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

include '../HTML/marcar_consulta.html';
?>