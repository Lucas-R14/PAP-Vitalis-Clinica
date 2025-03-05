<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cc = trim($_POST['cc']); // Cartão de Cidadão (cc)
    $senha_digitada = $_POST['senha']; // Senha digitada

    require_once 'db_connect.php'; // Inclui a conexão

    // Busca o cliente no banco de dados pelo Cartão de Cidadão (cc)
    $sql = "SELECT id_cliente, nome, senha FROM Cliente WHERE cc = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $senha_armazenada = $row['senha'];

        // Verifica a senha
        if (password_verify($senha_digitada, $senha_armazenada)) {
            // Login bem-sucedido: define a sessão
            $_SESSION['logado'] = true;
            $_SESSION['id_cliente'] = $row['id_cliente'];
            $_SESSION['nome_cliente'] = $row['nome'];
            header("Location: painel_utilizador.php"); // Redireciona para o painel
            exit();
        } else {
            $mensagem_erro = "Senha incorreta!";
        }
    } else {
        $mensagem_erro = "Cartão de Cidadão não encontrado!";
    }

    $conn->close();
}
?>

<?php include '../HTML/login.html'; ?>