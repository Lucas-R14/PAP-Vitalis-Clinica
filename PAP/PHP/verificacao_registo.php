<?php
session_start(); // Inicia a sessão
require 'gerarIdCliente.php'; // Inclui o arquivo com a função gerarIdCliente

// Mensagem de status para exibir após o envio
$status_message = "";

// Verifica se o código de verificação foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_digitado = $_POST['codigo_verificacao'];

    // Verifica se o código digitado corresponde ao código armazenado na sessão
    if ($codigo_digitado == $_SESSION['codigo_verificacao']) {
        // Conexão com o banco de dados
        $host = "localhost";
        $user = "root";
        $password = "mysql";
        $dbname = "vitalis_clinica";

        $conn = new mysqli($host, $user, $password, $dbname);
        if ($conn->connect_error) {
            die("Falha na conexão com o banco de dados: " . $conn->connect_error);
        }

        // Recupera os dados do cliente da sessão
        $dados_cliente = $_SESSION['dados_cliente'];

        // Gera um ID único para o cliente
        $id_cliente = gerarIdCliente($conn);

        // Prepara a query SQL para inserir os dados na tabela Cliente
        $sql = "INSERT INTO Cliente (id_cliente, nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, senha, data_registo, historico_medico)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)";

        // Prepara a declaração
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro na preparação da query: " . $conn->error);
        }

        // Criptografa a senha antes de armazenar no banco de dados
        $senha_criptografada = password_hash($dados_cliente['senha'], PASSWORD_DEFAULT);

        // Associa os parâmetros
        $stmt->bind_param(
            "ssssssssssss",
            $id_cliente,
            $dados_cliente['nome'],
            $dados_cliente['nacionalidade'],
            $dados_cliente['nif'],
            $dados_cliente['cc'],
            $dados_cliente['data_nascimento'],
            $dados_cliente['sexo'],
            $dados_cliente['telefone'],
            $dados_cliente['email'],
            $dados_cliente['endereco'],
            $senha_criptografada, // Senha criptografada
            $dados_cliente['historico_medico']
        );

        // Executa a query
        if ($stmt->execute()) {
            $status_message = "Cliente registado com sucesso! ID do Cliente: " . $id_cliente;
            $registro_concluido = true; // Indica que o registro foi concluído
        } else {
            $status_message = "Erro ao registar cliente: " . $stmt->error;
        }

        // Fecha a declaração
        $stmt->close();

        // Limpa a sessão
        session_unset();
        session_destroy();
    } else {
        $status_message = "Erro: Código de verificação inválido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Registo</title>
    <link rel="stylesheet" href="../CSS/verificacao_registo.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="../HTML/index.html"><img src="../images/logo_transparent.png" alt="" class="img-header"> </a>
            <h1 class="h1-header"> Vitalis Clínica</h1>
            <nav class="navbar">
                <a class="btn-cadastrar" href="../HTML/registo.html">Registar-se</a>
                <a class="btn-entrar" href="../HTML/login.html">Entrar</a>
            </nav>
        </header>

        <main>
            <?php if (isset($registro_concluido) && $registro_concluido): ?>
                <!-- Exibe a mensagem de sucesso e o botão de redirecionamento -->
                <div class="mensagem-sucesso">
                    <h2><?php echo $status_message; ?></h2>
                    <a href="../HTML/index.html" class="btn-redirecionar">Voltar à Página Inicial</a>
                </div>
            <?php else: ?>
                <!-- Exibe o formulário de verificação -->
                <div class="verificacao-container">
                    <h2>Verificação de Registo</h2>
                    <form method="POST" action="verificacao_registo.php">
                        <input type="text" id="codigo_verificacao" name="codigo_verificacao" placeholder="Digite o código de verificação" required>
                        <button type="submit">Verificar</button>
                    </form>
                    <?php if (!empty($status_message)): ?>
                        <p class="status-message"><?php echo $status_message; ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>

        <br><br><br><br>
        <footer class="footer">
            <p>Copyright &copy; 2025 Vitalis Clínica</p>
        </footer>
    </div>
</body>
</html>