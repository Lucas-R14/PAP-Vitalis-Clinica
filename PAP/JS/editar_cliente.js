// editar_cliente.js

// Função para redirecionar após 2 segundos
function redirecionar() {
    setTimeout(function() {
        window.location.href = 'gerir_clientes.php';
    }, 2000); // 2000 milissegundos = 2 segundos
}

// Função para inicializar o Flatpickr
function inicializarFlatpickr() {
    // Inicializa o Flatpickr para o campo de data de nascimento
    flatpickr("#data_nascimento", {
        locale: "pt", // Define o idioma para português
        dateFormat: "Y-m-d", // Formato da data
        defaultDate: document.getElementById("data_nascimento").value, // Data padrão
    });
}

// Executa as funções quando o DOM estiver totalmente carregado
document.addEventListener("DOMContentLoaded", function() {
    // Verifica se a mensagem de sucesso está presente
    if (document.querySelector(".mensagem-sucesso")) {
        redirecionar(); // Redireciona após 2 segundos
    }

    // Inicializa o Flatpickr
    inicializarFlatpickr();
});