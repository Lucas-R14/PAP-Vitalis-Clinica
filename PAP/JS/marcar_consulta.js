document.addEventListener('DOMContentLoaded', function () {
    const especialidadeSelect = document.getElementById('especialidade');
    const medicoSelect = document.getElementById('medico');
    const dataInput = document.getElementById('data');
    const horarioSelect = document.getElementById('horario');
    const formAgendamento = document.getElementById('form-agendamento');
    const modal = document.getElementById('modalConfirmacao');
    const closeButton = modal.querySelector('.close-button');
    const confirmarButton = document.getElementById('confirmarAgendamento');

    // Inicializa o Flatpickr para o campo de data
    flatpickr("#data", {
        locale: "pt", // Define o idioma para português
        dateFormat: "Y-m-d", // Formato da data
        minDate: "today", // Impede seleção de datas passadas
        maxDate: new Date().fp_incr(90), // Permite agendamento até 3 meses no futuro (90 dias)
    });

    // Carrega as especialidades ao carregar a página
    fetch('get_especialidades.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(especialidade => {
                const option = document.createElement('option');
                option.value = especialidade;
                option.textContent = especialidade;
                especialidadeSelect.appendChild(option);
            });
        });

    // Carrega os médicos ao selecionar uma especialidade
    especialidadeSelect.addEventListener('change', function () {
        const especialidade = this.value;
        medicoSelect.disabled = !especialidade;

        if (especialidade) {
            fetch(`get_medicos.php?especialidade=${especialidade}`)
                .then(response => response.json())
                .then(data => {
                    medicoSelect.innerHTML = '<option value="">Selecione um médico</option>';
                    data.forEach(medico => {
                        const option = document.createElement('option');
                        option.value = medico.id_funcionario;
                        option.textContent = medico.nome;
                        medicoSelect.appendChild(option);
                    });
                });
        }
    });

    // Carrega os horários disponíveis ao selecionar data e médico
    dataInput.addEventListener('change', function () {
        const data = this.value;
        const medico = medicoSelect.value;
        horarioSelect.disabled = !data || !medico;

        if (data && medico) {
            fetch(`get_horarios.php?medico=${medico}&data=${data}`)
                .then(response => response.json())
                .then(data => {
                    horarioSelect.innerHTML = '<option value="">Selecione um horário</option>';
                    data.forEach(horario => {
                        const option = document.createElement('option');
                        option.value = horario;
                        option.textContent = horario;
                        horarioSelect.appendChild(option);
                    });
                });
        }
    });

    // Intercepta o envio do formulário para mostrar o modal
    formAgendamento.addEventListener('submit', function(e) {
        e.preventDefault();

        const especialidade = especialidadeSelect.options[especialidadeSelect.selectedIndex].text;
        const medico = medicoSelect.options[medicoSelect.selectedIndex].text;
        const data = dataInput.value;
        const hora = horarioSelect.value;

        if (!especialidade || !medico || !data || !hora) {
            alert('Por favor, preencha todos os campos.');
            return;
        }

        // Preenche as informações no modal
        document.getElementById('especialidadeConfirm').textContent = especialidade;
        document.getElementById('medicoConfirm').textContent = medico;
        document.getElementById('dataConfirm').textContent = new Date(data).toLocaleDateString('pt-PT');
        document.getElementById('horaConfirm').textContent = hora;

        // Mostra o modal
        modal.style.display = 'block';
    });

    // Fechar modal
    closeButton.addEventListener('click', () => {
        modal.style.display = 'none';
        limparErros();
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
            limparErros();
        }
    });

    // Função para limpar erros
    function limparErros() {
        const senhaInput = document.getElementById('senhaConfirmacao');
        const erroSenha = document.getElementById('erroSenhaConfirmacao');
        senhaInput.value = '';
        senhaInput.classList.remove('erro');
        erroSenha.textContent = '';
    }

    // Limpar mensagens de erro quando o usuário começa a digitar
    document.getElementById('senhaConfirmacao').addEventListener('input', function() {
        this.classList.remove('erro');
        document.getElementById('erroSenhaConfirmacao').textContent = '';
    });

    // Confirmar agendamento
    confirmarButton.addEventListener('click', () => {
        const senhaInput = document.getElementById('senhaConfirmacao');
        const erroSenha = document.getElementById('erroSenhaConfirmacao');
        const senha = senhaInput.value;

        if (!senha) {
            senhaInput.classList.add('erro');
            erroSenha.textContent = 'Por favor, insira sua senha.';
            return;
        }

        // Prepara os dados do formulário
        const formData = {
            medico: medicoSelect.value,
            data: dataInput.value,
            horario: horarioSelect.value,
            especialidade: especialidadeSelect.value,
            senha: senha
        };

        // Envia os dados para o servidor
        fetch('marcar_consulta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.style.display = 'none';
                // Cria e mostra a mensagem de sucesso
                const mensagemSucesso = document.createElement('div');
                mensagemSucesso.className = 'mensagem-sucesso';
                mensagemSucesso.innerHTML = `
                    <p>Consulta agendada com sucesso!</p>
                    <p>Redirecionando para o painel...</p>
                `;
                
                // Remove qualquer mensagem de sucesso anterior se existir
                const mensagemAnterior = document.querySelector('.mensagem-sucesso');
                if (mensagemAnterior) {
                    mensagemAnterior.remove();
                }
                
                // Adiciona a nova mensagem após o header
                const header = document.querySelector('header');
                header.insertAdjacentElement('afterend', mensagemSucesso);

                // Redireciona após 2 segundos
                setTimeout(() => {
                    window.location.href = 'painel_cliente.php';
                }, 2000);
            } else {
                if (data.message.includes('Senha incorreta')) {
                    senhaInput.classList.add('erro');
                    erroSenha.textContent = 'Senha incorreta. Por favor, tente novamente.';
                    senhaInput.value = '';
                } else {
                    alert('Erro ao agendar consulta: ' + data.message);
                    modal.style.display = 'none';
                }
            }
        });
    });

    // Adicionar funcionalidade do dropdown
    const dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach((dropdown) => {
        const button = dropdown.querySelector(".dropbtn");
        const content = dropdown.querySelector(".dropdown-content");
        const items = content.querySelectorAll(".dropdown-item");

        button.addEventListener("click", () => {
            content.classList.toggle("show");
            button.classList.toggle("active");

            if (content.classList.contains("show")) {
                // Animação de fade-in para cada item
                items.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.opacity = "1";
                        item.style.transform = "translateY(0)";
                    }, index * 100); // Delay de 100ms entre cada item
                });
            } else {
                // Resetar a animação ao fechar o dropdown
                items.forEach((item) => {
                    item.style.opacity = "0";
                    item.style.transform = "translateY(-10px)";
                });
            }
        });
    });

    // Fechar o dropdown ao clicar fora
    window.addEventListener("click", (event) => {
        dropdowns.forEach((dropdown) => {
            const button = dropdown.querySelector(".dropbtn");
            const content = dropdown.querySelector(".dropdown-content");
            const items = content.querySelectorAll(".dropdown-item");

            if (!dropdown.contains(event.target)) {
                content.classList.remove("show");
                button.classList.remove("active");

                // Resetar a animação ao fechar o dropdown
                items.forEach((item) => {
                    item.style.opacity = "0";
                    item.style.transform = "translateY(-10px)";
                });
            }
        });
    });

    // Lógica de animação para o botão Voltar
    const btnVoltar = document.querySelector(".btn-voltar");

    btnVoltar.addEventListener("click", (e) => {
        e.preventDefault(); // Evita o comportamento padrão do link
        btnVoltar.classList.toggle("active");

        // Redireciona após a animação
        setTimeout(() => {
            window.location.href = btnVoltar.href;
        }, 300); // Tempo da animação
    });
});