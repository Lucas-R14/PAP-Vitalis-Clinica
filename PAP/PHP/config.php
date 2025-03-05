<?php

// Verifica se o ficheiro .env existe / é só para teste ***************
if (!file_exists('../.env')) {
    die('Ficheiro .env não encontrado.');
}


$env = file('../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($env as $line) {
    
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    // Divide a linha em chave e valor
    list($key, $value) = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);

    // Define a variável de ambiente
    putenv("$key=$value");
}

// obter variáveis de ambiente
function env($key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}
?>