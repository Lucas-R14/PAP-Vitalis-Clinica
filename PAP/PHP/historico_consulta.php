<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

// Buscar as consultas concluídas do cliente
$id_cliente = $_SESSION['id_cliente'];

// Parâmetros de pesquisa
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$filter_by = isset($_GET['filter']) ? $_GET['filter'] : 'id_consulta';

// Definir o número de consultas por página
$consultas_por_pagina = 5;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $consultas_por_pagina;

// Preparar a condição de pesquisa baseada no filtro
$search_condition = "";
$search_params = [];

if (!empty($search_term)) {
    switch ($filter_by) {
        case 'id_consulta':
            $search_condition = "AND c.id_consulta LIKE ?";
            $search_params[] = "%$search_term%";
            break;
        case 'tipo_consulta':
            $search_condition = "AND c.tipo_consulta LIKE ?";
            $search_params[] = "%$search_term%";
            break;
        case 'nome_medico':
            $search_condition = "AND f.nome LIKE ?";
            $search_params[] = "%$search_term%";
            break;
        case 'data_consulta':
            // Converter formato de data se necessário (dia/mês/ano para ano-mês-dia)
            $formatted_date = $search_term;
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $search_term)) {
                $date_parts = explode('/', $search_term);
                $formatted_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
            $search_condition = "AND c.data_consulta = ?";
            $search_params[] = $formatted_date;
            break;
    }
}

// Contar total de consultas
$sql_total = "SELECT COUNT(*) as total FROM Consulta c
              JOIN Funcionario f ON c.id_funcionario = f.id_funcionario 
              WHERE c.id_cliente = ? AND (c.estado = 'Concluída' OR c.estado = 'Não compareceu')
              $search_condition";

$stmt_total = $conn->prepare($sql_total);

// Bind parameters
$bind_params = ["i"];  // primeiro parâmetro é o id_cliente
$params = [$id_cliente];

// Adicionar parâmetros de pesquisa se existirem
if (!empty($search_params)) {
    foreach ($search_params as $param) {
        $bind_params[0] .= "s";  // assumindo que todos os parâmetros de pesquisa são strings
        $params[] = $param;
    }
}

$stmt_total->bind_param(...array_merge([$bind_params[0]], $params));
$stmt_total->execute();
$total_consultas = $stmt_total->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_consultas / $consultas_por_pagina);

// Buscar consultas da página atual
$sql = "SELECT 
            c.id_consulta,
            c.data_consulta, 
            c.hora_consulta, 
            c.tipo_consulta,
            c.estado,
            c.descricao,
            c.prescricao,
            c.observacoes,
            f.nome as nome_medico,
            f.especialidade
        FROM Consulta c 
        JOIN Funcionario f ON c.id_funcionario = f.id_funcionario 
        WHERE c.id_cliente = ? 
        AND (c.estado = 'Concluída' OR c.estado = 'Não compareceu')
        $search_condition
        ORDER BY c.data_consulta DESC, c.hora_consulta DESC 
        LIMIT ? OFFSET ?";

// Preparar e executar a query com os parâmetros
$stmt = $conn->prepare($sql);

// Adicionar os limites de paginação aos parâmetros
$bind_params[0] .= "ii";  // adicionar os tipos para limit e offset
$params[] = $consultas_por_pagina;
$params[] = $offset;

$stmt->bind_param(...array_merge([$bind_params[0]], $params));
$stmt->execute();
$result = $stmt->get_result();
$consultas = $result->fetch_all(MYSQLI_ASSOC);

// Preparar dados para JSON
$response = [
    'consultas' => $consultas,
    'paginacao' => [
        'total_paginas' => $total_paginas,
        'pagina_atual' => $pagina_atual,
        'total_consultas' => $total_consultas
    ]
];

// Se for uma chamada AJAX, retorna JSON
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Se não for AJAX, inclui o template HTML
$_SESSION['consultas_historico'] = $consultas;
$_SESSION['paginacao'] = $response['paginacao'];
include '../HTML/historico_consulta.html';
?> 