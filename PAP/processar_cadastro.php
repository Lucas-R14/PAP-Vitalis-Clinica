<?php
$host = "localhost";
$user = "root";
$password = "mysql";
$dbname = "vitalis_clinica";

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Mensagem de status para exibir após o envio
$status_message = "";

// Função para gerar um ID único de 8 caracteres (letras e números)
function gerarIdCliente($conn) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id_cliente = '';
    $tentativas = 0;

    do {
        // Gera um ID de 8 caracteres
        for ($i = 0; $i < 8; $i++) {
            $id_cliente .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        // Verifica se o ID já existe na base de dados
        $sql = "SELECT id_cliente FROM Cliente WHERE id_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_cliente);
        $stmt->execute();
        $stmt->store_result();

        // Se o ID já existir, gera um novo
        if ($stmt->num_rows > 0) {
            $id_cliente = ''; // Reseta o ID para gerar outro
            $tentativas++;
        } else {
            break; // ID único encontrado
        }

        // Prevenção contra loops infinitos (nunca deve acontecer, mas é bom ter)
        if ($tentativas > 100) {
            die("Erro: Não foi possível gerar um ID único após 100 tentativas.");
        }
    } while (true);

    return $id_cliente;
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $nacionalidade = $_POST['nacionalidade'];
    $nif = $_POST['nif'];
    $cc = $_POST['cc'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $historico_medico = $_POST['historico_medico'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        $status_message = "Erro: As senhas não coincidem.";
    } else {
        // Gera um ID único para o cliente
        $id_cliente = gerarIdCliente($conn);

        // Prepara a query SQL para inserir os dados na tabela Cliente
        $sql = "INSERT INTO Cliente (id_cliente, nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_registro, historico_medico)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)";

        // Prepara a declaração
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro na preparação da query: " . $conn->error);
        }

        // Associa os parâmetros
        $stmt->bind_param("sssssssssss", $id_cliente, $nome, $nacionalidade, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $historico_medico);

        // Executa a query
        if ($stmt->execute()) {
            $status_message = "Cliente cadastrado com sucesso! ID do Cliente: " . $id_cliente;
        } else {
            $status_message = "Erro ao cadastrar cliente: " . $stmt->error;
        }

        // Fecha a declaração
        $stmt->close();
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Cadastro</title>
    <link rel="stylesheet" href="processar_cadastro.css">
    
</head>
<body>
    <div class="container">
        <header>
            <a href="index.html"><img src="images/logo_transparent.png" alt="" class="img-header"> </a>
            <h1 class="h1-header"> Vitalis Clínica</h1>
            <nav class="navbar">
                <a class="btn-cadastrar" href="cadastro.html">Cadastrar-se</a>
                <a class="btn-entrar" href="index.html">Entrar</a>
            </nav>
        </header>

        <main class="main-content">
            <h2><?php echo $status_message; ?></h2>
            <a href="cadastro.html">Voltar ao formulário de cadastro</a>
        </main>

        <br><br><br><br>
        <footer class="footer">
            <p>Copyright &copy; 2025 Vitalis Clínica</p>
        </footer>
    </div>
</body>
</html>
