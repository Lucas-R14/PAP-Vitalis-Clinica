<?php
require_once 'db_connect.php';

$especialidade = $_GET['especialidade'];
$sql = "SELECT id_funcionario, nome FROM Funcionario WHERE especialidade = ? AND cargo = 'Médico' AND estado = 'Ativo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $especialidade);
$stmt->execute();
$result = $stmt->get_result();

$medicos = [];
while ($row = $result->fetch_assoc()) {
    $medicos[] = $row;
}

echo json_encode($medicos);
$stmt->close();
$conn->close();
?>