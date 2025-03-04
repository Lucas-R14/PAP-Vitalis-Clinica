<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

$id_cliente = $_GET['id'];

// Conexão com o banco de dados
$host = "localhost";
$user = "root";
$password = "mysql";
$dbname = "vitalis_clinica";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Busca os dados do cliente
$sql = "SELECT id_cliente, nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_registo FROM Cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

// Atualiza os dados do cliente
$atualizacao_sucesso = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $nacionalidade = $_POST['nacionalidade'];
    $nif = $_POST['nif'];
    $cc = $_POST['cc'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;

    if ($senha) {
        $sql = "UPDATE Cliente SET nome = ?, nacionalidade = ?, nif = ?, cc = ?, data_nascimento = ?, sexo = ?, telefone = ?, email = ?, endereco = ?, senha = ? WHERE id_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $nome, $nacionalidade, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $senha, $id_cliente);
    } else {
        $sql = "UPDATE Cliente SET nome = ?, nacionalidade = ?, nif = ?, cc = ?, data_nascimento = ?, sexo = ?, telefone = ?, email = ?, endereco = ? WHERE id_cliente = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $nome, $nacionalidade, $nif, $cc, $data_nascimento, $sexo, $telefone, $email, $endereco, $id_cliente);
    }

    if ($stmt->execute()) {
        $atualizacao_sucesso = true; // Marca a atualização como bem-sucedida
    } else {
        $mensagem_erro = "Erro ao atualizar cliente: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="../CSS/editar_cliente.css">

</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Cliente</h1>
            <nav>
                <a class="btn-voltar" href="gerir_clientes.php">Voltar</a>
            </nav>
        </header>

        <main>
            <?php if ($atualizacao_sucesso): ?>
                <!-- Mensagem de sucesso -->
                <div class="mensagem-sucesso">
                    <p>Cliente atualizado com sucesso!</p>
                    <p>Redirecionando para a página de gerir clientes...</p>
                </div>

                <!-- Redirecionamento após 2 segundos -->
                <script src="../JS/editar_cliente.js"></script>

            <?php else: ?>
                <!-- Formulário de edição -->
                <h2>Editar Informações do Cliente</h2>

                <form method="POST" action="editar_cliente.php?id=<?php echo $id_cliente; ?>">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo $cliente['nome']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nacionalidade">Nacionalidade:</label>
                        <input type="text" id="nacionalidade" name="nacionalidade" value="<?php echo $cliente['nacionalidade']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nif">NIF:</label>
                        <input type="text" id="nif" name="nif" value="<?php echo $cliente['nif']; ?>" required maxlength="9">
                    </div>

                    <div class="form-group">
                        <label for="cc">Cartão de Cidadão:</label>
                        <input type="text" id="cc" name="cc" value="<?php echo $cliente['cc']; ?>" required maxlength="12">
                    </div>

                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo $cliente['data_nascimento']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sexo">Sexo:</label>
                        <select id="sexo" name="sexo" required>
                            <option value="Masculino" <?php echo $cliente['sexo'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Feminino" <?php echo $cliente['sexo'] === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                            <option value="Outro" <?php echo $cliente['sexo'] === 'Outro' ? 'selected' : ''; ?>>Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" value="<?php echo $cliente['telefone']; ?>" required maxlength="15">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo $cliente['email']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="endereco">Endereço:</label>
                        <input type="text" id="endereco" name="endereco" value="<?php echo $cliente['endereco']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="senha">Nova Senha (deixe em branco para manter a atual):</label>
                        <input type="password" id="senha" name="senha">
                    </div>

                    <button type="submit" class="btn-atualizar">Atualizar Cliente</button>
                </form>

                <?php if (isset($mensagem_erro)): ?>
                    <p class="mensagem-erro"><?php echo $mensagem_erro; ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </main>

        <footer>
            <p>Copyright &copy; 2025 Vitalis Clínica</p>
        </footer>
    </div>
</body>
</html>