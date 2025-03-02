<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cc = trim($_POST['cc']); // Cartão de Cidadão (cc)
    $senha_digitada = $_POST['senha']; // Senha digitada

    // Conexão com o banco de dados
    $host = "localhost";
    $user = "root";
    $password = "mysql";
    $dbname = "vitalis_clinica";

    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

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

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="container">
        <header>
            <a href="index.html"><img src="images/logo_transparent.png" alt="" class="img-header"> </a>
            <h1 class="h1-header"> Vitalis Clínica</h1>
            <nav class="navbar">
                <a class="btn-cadastrar" href="registo.html">Registar-se</a>
                <a class="btn-entrar" href="login.php">Entrar</a>
            </nav>
        </header>

        <main>
            <div class="login-container">
                <h2>Login</h2>
                <form method="POST" action="login.php">
                    <label for="cc">Cartão de Cidadão:</label>
                    <input type="text" id="cc" name="cc" required>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>

                    <button type="submit">Entrar</button>
                </form>
                <?php if (isset($mensagem_erro)): ?>
                    <p class="mensagem-erro"><?php echo $mensagem_erro; ?></p>
                <?php endif; ?>
                <br>
                <p>Não tem uma conta? <a href="registo.html">Registre-se aqui</a></p>
            </div>
        </main>

        <br><br><br><br>
        <footer class="footer">
            <p>Copyright &copy; 2025 Vitalis Clínica</p>
        </footer>
    </div>
</body>

</html>