document.addEventListener('DOMContentLoaded', function() {
    // Função para revelar elementos durante o scroll
    function reveal() {
        const reveals = document.querySelectorAll('.reveal');
        
        reveals.forEach(element => {
            const windowHeight = window.innerHeight;
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            } else {
                element.classList.remove('active');
            }
        });
    }

    window.addEventListener('scroll', reveal);
    reveal(); // Chamar uma vez para verificar elementos visíveis no carregamento inicial

    // Esconder mensagem de erro ao carregar a página
    const mensagemErro = document.getElementById('mensagem-erro');
    if (mensagemErro) {
        mensagemErro.style.display = 'none';
    }

    // Esconder mensagem de erro quando o usuário começar a digitar
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (mensagemErro) {
                mensagemErro.style.display = 'none';
            }
        });
    });
}); 