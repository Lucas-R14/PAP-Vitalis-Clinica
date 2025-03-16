<?php
session_start();

require 'gerarIdCliente.php'; // Inclui a função gerarIdCliente
require_once 'db_connect.php'; // Inclui a conexão
require 'email.php'; // Inclui a função de envio de e-mail

// Mensagem de status para exibir após o envio
$status_message = "";

// Função para gerar um código de verificação de 6 dígitos
function gerarCodigoVerificacao() {
    return rand(100000, 999999);
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
    $historico_medico = $_POST['historico_medico'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        $status_message = "Erro: As senhas não coincidem.";
    } else {
        // Concatena os campos do endereço antes de armazenar
        $morada = $_POST['morada'];
        $apartamento = isset($_POST['apartamento']) ? $_POST['apartamento'] : "";
        $codigo_postal = $_POST['codigo_postal'];
        $distrito = $_POST['distrito'];
        $concelho = $_POST['concelho'];

        // Formatação do endereço completo
        $endereco = trim("$morada, " . ($apartamento ? "$apartamento, " : "") . "$codigo_postal, $distrito, $concelho");

        // Gera um código de verificação
        $codigo_verificacao = gerarCodigoVerificacao();

        // Armazena os dados do cliente e o código de verificação na sessão
        $_SESSION['dados_cliente'] = [
            'nome' => $nome,
            'nacionalidade' => $nacionalidade,
            'nif' => $nif,
            'cc' => $cc,
            'data_nascimento' => $data_nascimento,
            'sexo' => $sexo,
            'telefone' => $telefone,
            'email' => $email,
            'endereco' => $endereco,
            'senha' => $senha,
            'historico_medico' => $historico_medico
        ];
        $_SESSION['codigo_verificacao'] = $codigo_verificacao;

        // Chama a função de envio de e-mail
        $emailEnviado = enviarEmailVerificacao($email, $nome, $codigo_verificacao);

        if ($emailEnviado === true) {
            header("Location: ../PHP/verificacao_registo.php");
            exit();
        } else {
            $status_message = $emailEnviado; // Exibe a mensagem de erro do envio de e-mail
        }
    }
}

// Fecha a conexão com a base de dados
$conn->close();
?>

<?php include '../HTML/processar_registo.html'; ?>
