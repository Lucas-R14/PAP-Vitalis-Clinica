<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Utilizador</title>
    <link rel="stylesheet" href="painel_utilizador.css">
</head>

<body>
    <div class="container">
        <header>
            <img src="images/logo_transparent.png" alt="" class="img-header">
            <h1 class="h1-header"> Vitalis Clínica</h1>
            <nav class="navbar">
                <div class="dropdown">
                    <button class="dropbtn">Consulta</button>
                    <div class="dropdown-content">
                        <a href="marcar_consulta.php">Marcar Consultas</a>
                        <a href="alterar_consulta.php">Alterar Consultas</a>
                        <a href="historico_consultas.php">Ver Histórico de Consultas</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn">•••</button>
                    <div class="dropdown-content">
                        <a href="conta.php">Conta</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <div class="welcome-box">
                <h2>Bem-vindo, <?php echo $_SESSION['nome_cliente']; ?>!</h2>
                <p>Esta é a sua área de utilizador. O que deseja fazer hoje?</p>
            </div>
        </main>

        <br><br><br><br>
        <footer class="footer">
            <p>Copyright &copy; 2025 Vitalis Clínica</p>
        </footer>
    </div>
</body>

</html>