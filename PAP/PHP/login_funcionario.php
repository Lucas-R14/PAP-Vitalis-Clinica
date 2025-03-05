<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cc = trim($_POST['cc']); // Cartão de Cidadão (cc)
    $senha_digitada = $_POST['senha']; // Senha digitada

    require_once 'db_connect.php'; // Inclui a conexão

    // Busca o funcionário no banco de dados pelo Cartão de Cidadão (cc)
    $sql = "SELECT id_funcionario, nome, senha, nivel_acesso FROM Funcionario WHERE cc = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($senha_digitada, $row['senha'])) {
            // Login bem-sucedido: define a sessão
            $_SESSION['logado'] = true;
            $_SESSION['id_funcionario'] = $row['id_funcionario'];
            $_SESSION['nome_funcionario'] = $row['nome'];
            $_SESSION['nivel_acesso'] = $row['nivel_acesso']; // Define o nível de acesso
            $_SESSION['tipo'] = 'funcionario'; // Define o tipo de usuário
            header("Location: ../PHP/painel_funcionario.php"); // Redireciona para o painel
            exit();
        } else {
            $mensagem_erro = "Senha incorreta!";
        }
    } else {
        $mensagem_erro = "Cartão de Cidadão não encontrado!";
    }

    $conn->close();
}

// Se o login falhar, exibe a página de login com a mensagem de erro
include '../HTML/login_funcionario.html';
?>