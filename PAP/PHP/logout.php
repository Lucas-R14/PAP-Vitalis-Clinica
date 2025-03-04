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
    <link rel="stylesheet" href="../CSS/logout.css"> <!-- Link para o ficheiro CSS externo -->
</head>
<body>
    <div class="logout-message">
        <h2>Foi desconectado com sucesso!</h2>
        <p>A redirecionar para a página principal...</p>
    </div>

    <script src="../JS/logout.js"></script>
</body>
</html>