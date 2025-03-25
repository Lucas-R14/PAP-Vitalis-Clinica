<?php
require_once 'db_connect.php';

$sql = "SELECT DISTINCT especialidade FROM Funcionario WHERE cargo = 'Médico' AND estado = 'Ativo'";
$result = $conn->query($sql);

$especialidades = [];
while ($row = $result->fetch_assoc()) {
    $especialidades[] = $row['especialidade'];
}

echo json_encode($especialidades);
$conn->close();
?>