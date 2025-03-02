<?php
session_start(); // Inicia a sessão

// Remove todas as variáveis de sessão
session_unset();

// Destrói a sessão
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="logout.css"> <!-- Link para o ficheiro CSS externo -->
</head>
<body>
    <div class="logout-message">
        <h2>Foi desconectado com sucesso!</h2>
        <p>A redirecionar para a página principal...</p>
    </div>

    <script>
        // Redireciona para a página principal após 2 segundos
        setTimeout(function() {
            window.location.href = "index.html";
        }, 2000); // 2000 milissegundos = 2 segundos
    </script>
</body>
</html>