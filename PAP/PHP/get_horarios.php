<?php
require_once 'db_connect.php';

$medico = $_GET['medico'];
$data = $_GET['data'];

// Busca o horário de trabalho do médico
$sql = "SELECT inicio_turno, fim_turno FROM Funcionario WHERE id_funcionario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $medico);
$stmt->execute();
$result = $stmt->get_result();
$medicoInfo = $result->fetch_assoc();

$inicioTurno = strtotime($medicoInfo['inicio_turno']);
$fimTurno = strtotime($medicoInfo['fim_turno']);

// Busca os horários já agendados
$sql = "SELECT hora_consulta FROM Consulta WHERE id_funcionario = ? AND data_consulta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $medico, $data);
$stmt->execute();
$result = $stmt->get_result();

$horariosAgendados = [];
while ($row = $result->fetch_assoc()) {
    $horariosAgendados[] = strtotime($row['hora_consulta']);
}

// Gera os horários disponíveis
$horariosDisponiveis = [];
for ($hora = $inicioTurno; $hora < $fimTurno; $hora += 3600) { // Intervalos de 1 hora
    if (!in_array($hora, $horariosAgendados)) {
        $horariosDisponiveis[] = date('H:i', $hora);
    }
}

echo json_encode($horariosDisponiveis);
$stmt->close();
$conn->close();
?>