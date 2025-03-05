<?php
session_start();

require_once 'db_connect.php'; // Inclui a conexão

// Verifica se o utilizador está logado e é um administrador
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'funcionario' || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header("Location: ../HTML/login_funcionario.html");
    exit();
}

$id_cliente = $_GET['id'];

// Busca os dados do cliente
$sql = "SELECT id_cliente, nome, nacionalidade, nif, cc, data_nascimento, sexo, telefone, email, endereco, data_registo FROM Cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

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
                // Exclui o cliente
                $sql = "DELETE FROM Cliente WHERE id_cliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $id_cliente);

                if ($stmt->execute()) {
                    $exclusao_sucesso = true;
                } else {
                    $mensagem_erro = "Erro ao excluir cliente: " . $stmt->error;
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

<?php include '../HTML/excluir_cliente.html'; ?>