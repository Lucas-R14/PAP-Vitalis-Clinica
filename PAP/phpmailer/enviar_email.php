<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Certifique-se de que o autoload do Composer está correto

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coletar dados do formulário
    $destinatario = $_POST['destinatario'];
    $assunto = $_POST['assunto'];
    $corpo = $_POST['corpo'];

    // Criar uma nova instância do PHPMailer
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8'; // Definir a codificação de caracteres como UTF-8

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP do Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'vitalis.clinica25@gmail.com'; // Seu e-mail do Gmail
        $mail->Password   = 'bjau vskr fwus kssp'; // Senha de app do Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Criptografia TLS
        $mail->Port       = 587; // Porta do SMTP do Gmail

        // Remetente e destinatário
        $mail->setFrom('vitalis.clinica25@gmail.com', 'Vitalis Clinica'); // Remetente
        $mail->addAddress($destinatario); // Destinatário (coletado do formulário)

        // Conteúdo do e-mail
        $mail->isHTML(true); // Definir formato do e-mail como HTML

        // Adicionar meta tag para garantir UTF-8 no HTML do e-mail
        $corpoHTML = "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>$assunto</title>
        </head>
        <body>
            $corpo
        </body>
        </html>
        ";

        $mail->Subject = $assunto; // Assunto (coletado do formulário)
        $mail->Body    = $corpoHTML; // Corpo do e-mail em HTML
        $mail->AltBody = strip_tags($corpo); // Versão em texto simples do corpo

        // Enviar o e-mail
        $mail->send();
        echo 'E-mail enviado com sucesso para ' . $destinatario;
    } catch (Exception $e) {
        echo "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
    }
} else {
    echo "Acesso inválido ao script.";
}