document.addEventListener('DOMContentLoaded', function() {
    // Elementos dos modais
    const modalEditarDados = document.getElementById('modalEditarDados');
    const modalAlterarSenha = document.getElementById('modalAlterarSenha');
    const btnEditarDados = document.getElementById('btnEditarDados');
    const btnAlterarSenha = document.getElementById('btnAlterarSenha');
    const closeButtons = document.querySelectorAll('.close-button');

    // Formulários
    const formEditarDados = document.getElementById('formEditarDados');
    const formAlterarSenha = document.getElementById('formAlterarSenha');

    // Lógica para o dropdown dos três pontinhos
    const dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach((dropdown) => {
        const button = dropdown.querySelector(".dropbtn");
        const content = dropdown.querySelector(".dropdown-content");

        if (content) { // Verifica se existe content (para o botão voltar que não tem dropdown)
            const items = content.querySelectorAll(".dropdown-item");

            button.addEventListener("click", (e) => {
                e.preventDefault(); // Previne o comportamento padrão do link
                content.classList.toggle("show");
                button.classList.toggle("active");

                if (content.classList.contains("show")) {
                    // Animação de fade-in para cada item
                    items.forEach((item, index) => {
                        setTimeout(() => {
                            item.style.opacity = "1";
                            item.style.transform = "translateY(0)";
                        }, index * 100);
                    });
                } else {
                    // Resetar a animação ao fechar o dropdown
                    items.forEach((item) => {
                        item.style.opacity = "0";
                        item.style.transform = "translateY(-10px)";
                    });
                }
            });
        }
    });

    // Fechar o dropdown ao clicar fora
    window.addEventListener("click", (event) => {
        dropdowns.forEach((dropdown) => {
            const button = dropdown.querySelector(".dropbtn");
            const content = dropdown.querySelector(".dropdown-content");

            if (content && !dropdown.contains(event.target)) {
                content.classList.remove("show");
                button.classList.remove("active");

                // Resetar a animação ao fechar o dropdown
                const items = content.querySelectorAll(".dropdown-item");
                items.forEach((item) => {
                    item.style.opacity = "0";
                    item.style.transform = "translateY(-10px)";
                });
            }
        });
    });

    // Abrir modais
    btnEditarDados.addEventListener('click', () => {
        modalEditarDados.style.display = 'block';
    });

    btnAlterarSenha.addEventListener('click', () => {
        modalAlterarSenha.style.display = 'block';
    });

    // Fechar modais
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modalEditarDados.style.display = 'none';
            modalAlterarSenha.style.display = 'none';
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target === modalEditarDados) {
            modalEditarDados.style.display = 'none';
        }
        if (event.target === modalAlterarSenha) {
            modalAlterarSenha.style.display = 'none';
        }
    });

    // Submissão do formulário de edição de dados
    formEditarDados.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const dados = {
            nome: document.getElementById('editNome').value,
            nacionalidade: document.getElementById('editNacionalidade').value,
            email: document.getElementById('editEmail').value,
            telefone: document.getElementById('editTelefone').value,
            endereco: document.getElementById('editEndereco').value,
            historico_medico: document.getElementById('editHistoricoMedico').value,
            senha: document.getElementById('senhaAtual').value
        };

        fetch('atualizar_dados.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dados atualizados com sucesso!');
                window.location.reload();
            } else {
                document.getElementById('erroSenhaAtual').textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('erroSenhaAtual').textContent = 'Erro ao processar a requisição';
        });
    });

    // Submissão do formulário de alteração de senha
    formAlterarSenha.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const senhaAtual = document.getElementById('senhaAtualAlt').value;
        const novaSenha = document.getElementById('novaSenha').value;
        const confirmarSenha = document.getElementById('confirmarSenha').value;
        const erroSenha = document.getElementById('erroAlterarSenha');

        // Limpa mensagem de erro anterior
        erroSenha.textContent = '';

        // Validações básicas
        if (!senhaAtual || !novaSenha || !confirmarSenha) {
            erroSenha.textContent = 'Por favor, preencha todos os campos';
            return;
        }

        if (novaSenha !== confirmarSenha) {
            erroSenha.textContent = 'As senhas não coincidem';
            return;
        }

        // Dados para enviar
        const dados = {
            senhaAtual: senhaAtual,
            novaSenha: novaSenha
        };

        fetch('../PHP/alterar_senha.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Senha alterada com sucesso!');
                modalAlterarSenha.style.display = 'none';
                formAlterarSenha.reset();
            } else {
                erroSenha.textContent = data.message || 'Erro ao alterar a senha';
            }
        })
        .catch(error => {
            erroSenha.textContent = 'Erro ao processar a requisição. Por favor, tente novamente.';
            console.error('Erro:', error);
        });
    });
}); 