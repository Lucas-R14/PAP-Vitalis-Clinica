<?php
session_start();

require_once 'db_connect.php'; // Inclui a conexão

// Verifica se o utilizador está logado e é um administrador
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

$id_funcionario = $_GET['id'];

// Busca os dados do funcionário
$sql = "SELECT id_funcionario, nome, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_admissao, nivel_acesso FROM Funcionario WHERE id_funcionario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
$funcionario = $result->fetch_assoc();

// Verifica se o funcionário é um Administrador
if ($funcionario['nivel_acesso'] === 'Administrador') {
    $conn->close();
    header("Location: gerir_funcionarios.php?erro=Não é possível excluir um Administrador.");
    exit();
}

// Processa a exclusão
$exclusao_sucesso = false;
$mensagem_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirmar_exclusao'])) {
        // Verifica se a senha foi enviada
        if (isset($_POST['senha']) && !empty($_POST['senha'])) {
            $senha = $_POST['senha'];

            // Verifica a senha do administrador
            $sql = "SELECT senha FROM Funcionario WHERE id_funcionario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_SESSION['id_funcionario']);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            if (password_verify($senha, $admin['senha'])) {
                // Exclui o funcionário
                $sql = "DELETE FROM Funcionario WHERE id_funcionario = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $id_funcionario);

                if ($stmt->execute()) {
                    $exclusao_sucesso = true;
                } else {
                    $mensagem_erro = "Erro ao excluir funcionário: " . $stmt->error;
                }
            } else {
                $mensagem_erro = "Senha incorreta. Tente novamente.";
            }
        } else {
            $mensagem_erro = "Por favor, insira sua senha para confirmar a exclusão.";
        }
    }
}

$conn->close();
?>

<?php include '../HTML/excluir_funcionario.html'; ?>