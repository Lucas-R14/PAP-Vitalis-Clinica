<?php

function gerarIdFuncionario($conn) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id_funcionario = '';
    $tentativas = 0;

    do {
        // Gera um ID de 8 caracteres
        for ($i = 0; $i < 8; $i++) {
            $id_funcionario .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        // Verifica se o ID já existe na base de dados
        $sql = "SELECT id_funcionario FROM Funcionario WHERE id_funcionario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_funcionario);
        $stmt->execute();
        $stmt->store_result();

        // Se o ID já existir, gera um novo
        if ($stmt->num_rows > 0) {
            $id_funcionario = ''; // Reseta o ID para gerar outro
            $tentativas++;
        } else {
            break; // ID único encontrado
        }

        // Prevenção contra loops infinitos (nunca deve acontecer, mas é bom ter)
        if ($tentativas > 100) {
            die("Erro: Não foi possível gerar um ID único após 100 tentativas.");
        }
    } while (true);

    return $id_funcionario;
}
?>