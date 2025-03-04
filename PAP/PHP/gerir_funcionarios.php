<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

// Conexão com o banco de dados
$host = "localhost";
$user = "root";
$password = "mysql";
$dbname = "vitalis_clinica";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Busca todos os funcionários
$sql = "SELECT id_funcionario, nome, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_admissao, nivel_acesso FROM Funcionario";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Funcionários</title>
    <link rel="stylesheet" href="../CSS/painel_funcionario.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gerir Funcionários</h1>
            <nav>
                <a class="btn-sair" href="painel_funcionario.php">Voltar</a>
            </nav>
        </header>

        <main>
            <h2>Lista de Funcionários</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>NIF</th>
                        <th>CC</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Endereço</th>
                        <th>Data de Admissão</th>
                        <th>Nível de Acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_funcionario']; ?></td>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['nif']; ?></td>
                            <td><?php echo $row['cc']; ?></td>
                            <td><?php echo $row['data_nascimento']; ?></td>
                            <td><?php echo $row['sexo']; ?></td>
                            <td><?php echo $row['telefone']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['endereco']; ?></td>
                            <td><?php echo $row['data_admissao']; ?></td>
                            <td><?php echo $row['nivel_acesso']; ?></td>
                            <td>
                                <a href="editar_funcionario.php?id=<?php echo $row['id_funcionario']; ?>">Editar</a>
                                <a href="excluir_funcionario.php?id=<?php echo $row['id_funcionario']; ?>" onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>

        <footer>
            <p>Copyright &copy; 2025 Vitalis Clínica</p>
        </footer>
    </div>
</body>
</html>

<?php
$conn->close();
?>