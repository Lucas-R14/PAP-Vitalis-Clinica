// Função para atualizar o contador regressivo
function updateCountdown(seconds) {
    const countdownElement = document.getElementById('countdown');
    countdownElement.textContent = seconds;
}

// Redireciona para a página principal após 2 segundos
let secondsLeft = 2;

const countdownInterval = setInterval(function() {
    secondsLeft--;
    updateCountdown(secondsLeft);

    if (secondsLeft <= 0) {
        clearInterval(countdownInterval); // Para o contador
        window.location.href = "../HTML/index.html"; // Redireciona
    }
}, 1000); // Atualiza a cada 1 segundo (1000 milissegundos)