<?php
session_start();

// Só processa o login se for um POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cc']) && isset($_POST['senha'])) {
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
        if (password_verify($senha_digitada, $row['senha'])) {
            // Login bem-sucedido: define a sessão
            $_SESSION['logado'] = true;
            $_SESSION['id_cliente'] = $row['id_cliente'];
            $_SESSION['nome_cliente'] = $row['nome'];
            header("Location: painel_cliente.php"); // Redireciona para o painel
            exit();
        } else {
            echo '<script>
                window.onload = function() {
                    document.getElementById("mensagem-erro").textContent = "Senha incorreta!";
                    document.getElementById("mensagem-erro").style.display = "block";
                }
            </script>';
        }
    } else {
        echo '<script>
            window.onload = function() {
                document.getElementById("mensagem-erro").textContent = "Cartão de Cidadão não encontrado!";
                document.getElementById("mensagem-erro").style.display = "block";
            }
        </script>';
    }

    $conn->close();
}

include '../HTML/login.html';
?>