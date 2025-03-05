<?php
session_start(); // Inicia a sessão

// Remove todas as variáveis de sessão
session_unset();

// Destrói a sessão
session_destroy();
?>

<?php include '../HTML/logout.html'; ?>