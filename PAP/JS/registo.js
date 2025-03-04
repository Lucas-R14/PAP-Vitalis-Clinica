document.addEventListener('DOMContentLoaded', function () {
    const registoForm = document.getElementById('registoForm');
    const erroSenha = document.getElementById('erroSenha');

    // Função para validar as senhas ao enviar o formulário
    function validarSenhas() {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;

        if (senha !== confirmarSenha) {
            erroSenha.style.display = 'block'; // Mostra a mensagem de erro
            return false; // Impede o envio do formulário
        } else {
            erroSenha.style.display = 'none'; // Esconde a mensagem de erro
            return true; // Permite o envio do formulário
        }
    }

    // Validação em tempo real para senhas
    document.getElementById('confirmar_senha').addEventListener('input', function () {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = this.value;

        if (senha !== confirmarSenha) {
            erroSenha.style.display = 'block'; // Mostra a mensagem de erro
        } else {
            erroSenha.style.display = 'none'; // Esconde a mensagem de erro
        }
    });

    // Validação do NIF (exemplo simples)
    document.getElementById('nif').addEventListener('input', function () {
        const nif = this.value;
        const nifValido = /^\d{9}$/.test(nif); // Verifica se o NIF tem 9 dígitos

        if (!nifValido) {
            this.setCustomValidity('O NIF deve ter exatamente 9 dígitos.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validação do Telefone (exemplo simples)
    document.getElementById('telefone').addEventListener('input', function () {
        const telefone = this.value;
        const telefoneValido = /^\d{9}$/.test(telefone); // Verifica se o telefone tem 9 dígitos

        if (!telefoneValido) {
            this.setCustomValidity('O telefone deve ter exatamente 9 dígitos.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validação do Email (exemplo simples)
    document.getElementById('email').addEventListener('input', function () {
        const email = this.value;
        const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); // Verifica se o email é válido

        if (!emailValido) {
            this.setCustomValidity('Por favor, insira um email válido.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Adicionar evento de submit ao formulário
    registoForm.addEventListener('submit', function (event) {
        if (!validarSenhas()) {
            event.preventDefault(); // Impede o envio do formulário se as senhas não coincidirem
        }
    });
});