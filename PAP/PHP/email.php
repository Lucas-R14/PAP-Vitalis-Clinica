<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Carrega o PHPMailer

function enviarEmailVerificacao($email, $nome, $codigo_verificacao) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'youremail@yourdomain.com';
        $mail->Password = 'yourpassword';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('youremail@yourdomain.com', 'Your Domain');
        $mail->addAddress($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de registo - Vitalis Clínica';

        // Extrai o primeiro e o último nome
        $nomes = explode(' ', $nome);
        $primeiro_nome = $nomes[0];
        $ultimo_nome = end($nomes);

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
        $mail->AltBody = "Olá, $primeiro_nome $ultimo_nome!\n\nObrigado por se registar na Vitalis Clínica.\n\nSeu código de verificação é: $codigo_verificacao\n\nPor favor, insira esse código na página de confirmação para concluir registo.\n\nAtenciosamente,\nEquipe Vitalis Clínica";

        return $mail->send();
    } catch (Exception $e) {
        return "Erro ao enviar o e-mail de verificação: " . $mail->ErrorInfo;
    }
}
?>
