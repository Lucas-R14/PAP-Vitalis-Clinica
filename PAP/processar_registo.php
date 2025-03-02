<?php
session_start();
require 'gerarIdCliente.php'; // Inclui o arquivo com a função gerarIdCliente

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
    $endereco = $_POST['endereco'];
    $senha = $_POST['senha']; // Campo do formulário
    $confirmar_senha = $_POST['confirmar_senha']; // Campo do formulário
    $historico_medico = $_POST['historico_medico'];

    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        $status_message = "Erro: As senhas não coincidem.";
    } else {
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
            'senha' => $senha, // Mapeia para o campo "senha" no banco de dados
            'historico_medico' => $historico_medico
        ];
        $_SESSION['codigo_verificacao'] = $codigo_verificacao;

        // Envia o e-mail de verificação
        require 'vendor/autoload.php'; // Carrega o PHPMailer

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vitalis.clinica25@gmail.com';
        $mail->Password = 'bjau vskr fwus kssp';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8'; // Define o charset para UTF-8
        $mail->setFrom('vitalis.clinica25@gmail.com', 'Vitalis Clínica');
        $mail->addAddress($email, $nome);

        $mail->isHTML(true); // Permite o uso de HTML no e-mail
        $mail->Subject = 'Confirmação de Cadastro - Vitalis Clínica';

        // Extrai o primeiro e o último nome
        $nomes = explode(' ', $nome); // Divide o nome completo em partes
        $primeiro_nome = $nomes[0]; // Primeiro nome
        $ultimo_nome = end($nomes); // Último nome

        // Corpo do e-mail em HTML
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="pt-pt">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmação de Registo</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 1px solid #ddd;
                }
                .email-header h1 {
                    color: #292f7e;
                    font-size: 24px;
                    margin: 0;
                }
                .email-body {
                    padding: 20px 0;
                }
                .email-body p {
                    font-size: 16px;
                    line-height: 1.6;
                }
                .email-footer {
                    text-align: center;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                    font-size: 14px;
                    color: #777;
                }
                .codigo-verificacao {
                    font-size: 24px;
                    font-weight: bold;
                    color: #292f7e;
                    text-align: center;
                    margin: 20px 0;
                    padding: 10px;
                    background-color: #f0f4ff;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h1>Vitalis Clínica</h1>
                </div>
                <div class="email-body">
                    <p>Olá, <strong>' . $primeiro_nome . ' ' . $ultimo_nome . '</strong>!</p>
                    <p>Obrigado por se registar na <strong>Vitalis Clínica</strong>.</p>
                    <p>Seu código de verificação é:</p>
                    <div class="codigo-verificacao">' . $codigo_verificacao . '</div>
                    <p>Por favor, insira esse código na página de confirmação para concluir seu registo.</p>
                </div>
                <div class="email-footer">
                    <p>Atenciosamente,</p>
                    <p><strong>Equipe Vitalis Clínica</strong></p>
                </div>
            </div>
        </body>
        </html>
        ';

        // Corpo alternativo para clientes de e-mail que não suportam HTML
        $mail->AltBody = "Olá, $primeiro_nome $ultimo_nome!\n\nObrigado por se cadastrar na Vitalis Clínica.\n\nSeu código de verificação é: $codigo_verificacao\n\nPor favor, insira esse código na página de confirmação para concluir seu cadastro.\n\nAtenciosamente,\nEquipe Vitalis Clínica";

        if ($mail->send()) {
            header("Location: verificacao_registo.php");
            exit();
        } else {
            $status_message = "Erro ao enviar o e-mail de verificação: " . $mail->ErrorInfo;
        }
    }
}

// Fecha a conexão com a base de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Registo</title>
    <link rel="stylesheet" href="processar_registo.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="index.html"><img src="images/logo_transparent.png" alt="" class="img-header"> </a>
            <h1 class="h1-header"> Vitalis Clínica</h1>
            <nav class="navbar">
                <a class="btn-registar" href="registo.html">Registar-se</a>
                <a class="btn-entrar" href="login.php">Entrar</a>
            </nav>
        </header>

        <main class="main-content">
            <h2><?php echo $status_message; ?></h2>
            <a href="registo.html">Voltar ao formulário de registo</a>
        </main>

        <br><br><br><br>
        <footer class="footer">
            <p>Copyright &copy; 2025 Vitalis Clínica</p>
        </footer>
    </div>
</body>
</html>