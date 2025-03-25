document.addEventListener('DOMContentLoaded', function () {
    const botoesCancelar = document.querySelectorAll('.btn-cancelar');
    const botoesAlterar = document.querySelectorAll('.btn-alterar');
    const modalSenha = document.getElementById('modalSenha');
    const modalAlterar = document.getElementById('modalAlterar');
    const closeButtonSenha = modalSenha.querySelector('.close-button');
    const closeButtonAlterar = modalAlterar.querySelector('.close-button-alterar');
    const confirmarButton = document.getElementById('confirmarCancelamento');
    const confirmarAlteracaoButton = document.getElementById('confirmarAlteracao');
    let consultaId;
    let medicoId;

    // Lógica para o dropdown dos três pontinhos
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

    // Inicializa o Flatpickr para o campo de data
    const flatpickrInstance = flatpickr("#dataAlteracao", {
        locale: "pt",
        dateFormat: "Y-m-d",
        minDate: "today",
        maxDate: new Date().fp_incr(90),
        onChange: function(selectedDates, dateStr) {
            if (dateStr && medicoId) {
                carregarHorarios(medicoId, dateStr);
            }
        }
    });

    function carregarHorarios(medico, data) {
        const horarioSelect = document.getElementById('horarioAlteracao');
        horarioSelect.innerHTML = '<option value="">Carregando horários...</option>';
        
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

    // Lógica para o botão Alterar
    botoesAlterar.forEach(botao => {
        botao.addEventListener('click', function() {
            consultaId = this.getAttribute('data-id');
            medicoId = this.getAttribute('data-medico');

            // Preenche as informações da consulta no modal
            const consultaItem = this.closest('.consulta-item');
            document.getElementById('medicoNome').textContent = consultaItem.querySelector('p:nth-child(3)').textContent.replace('Médico:', '').trim();
            document.getElementById('tipoConsulta').textContent = consultaItem.querySelector('p:nth-child(4)').textContent.replace('Tipo:', '').trim();
            document.getElementById('dataAtual').textContent = consultaItem.querySelector('p:nth-child(1)').textContent.replace('Data:', '').trim();
            document.getElementById('horaAtual').textContent = consultaItem.querySelector('p:nth-child(2)').textContent.replace('Hora:', '').trim();

            modalAlterar.style.display = 'block';
        });
    });

    // Lógica para o botão Cancelar
    botoesCancelar.forEach(botao => {
        botao.addEventListener('click', function () {
            consultaId = this.getAttribute('data-id');
            modalSenha.style.display = 'block';
        });
    });

    // Fechar modais
    closeButtonSenha.addEventListener('click', () => {
        modalSenha.style.display = 'none';
    });

    closeButtonAlterar.addEventListener('click', () => {
        modalAlterar.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === modalSenha) {
            modalSenha.style.display = 'none';
        }
        if (event.target === modalAlterar) {
            modalAlterar.style.display = 'none';
        }
    });

    // Confirmar cancelamento
    confirmarButton.addEventListener('click', () => {
        const senhaInput = document.getElementById('senhaInput');
        const erroSenha = document.getElementById('erroSenhaCancelamento');
        const senha = senhaInput.value;

        // Limpa estados de erro anteriores
        senhaInput.classList.remove('erro');
        erroSenha.textContent = '';

        if (!senha) {
            senhaInput.classList.add('erro');
            erroSenha.textContent = 'Por favor, insira sua senha.';
            return;
        }

        fetch(`cancelar_consulta.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: consultaId, senha: senha })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalSenha.style.display = 'none';
                alert('Consulta cancelada com sucesso!');
                window.location.reload();
            } else {
                if (data.message.includes('Senha incorreta')) {
                    senhaInput.classList.add('erro');
                    erroSenha.textContent = 'Senha incorreta. Por favor, tente novamente.';
                    senhaInput.value = ''; // Limpa o campo de senha
                } else {
                    alert('Erro ao cancelar a consulta: ' + data.message);
                    modalSenha.style.display = 'none';
                }
            }
        });
    });

    // Limpar mensagens de erro quando o usuário começa a digitar
    document.getElementById('senhaInput').addEventListener('input', function() {
        this.classList.remove('erro');
        document.getElementById('erroSenhaCancelamento').textContent = '';
    });

    // Limpar campos e erros ao fechar o modal de cancelamento
    closeButtonSenha.addEventListener('click', () => {
        modalSenha.style.display = 'none';
        const senhaInput = document.getElementById('senhaInput');
        senhaInput.value = '';
        senhaInput.classList.remove('erro');
        document.getElementById('erroSenhaCancelamento').textContent = '';
    });

    // Confirmar alteração
    confirmarAlteracaoButton.addEventListener('click', () => {
        const novaData = document.getElementById('dataAlteracao').value;
        const novoHorario = document.getElementById('horarioAlteracao').value;
        const senhaInput = document.getElementById('senhaAlteracao');
        const erroSenha = document.getElementById('erroSenhaAlteracao');
        const senha = senhaInput.value;

        // Limpa estados de erro anteriores
        senhaInput.classList.remove('erro');
        erroSenha.textContent = '';

        if (!novaData || !novoHorario) {
            alert('Por favor, selecione a nova data e horário.');
            return;
        }

        if (!senha) {
            senhaInput.classList.add('erro');
            erroSenha.textContent = 'Por favor, insira sua senha.';
            return;
        }

        fetch('alterar_consulta_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: consultaId,
                data: novaData,
                horario: novoHorario,
                senha: senha
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Consulta alterada com sucesso!');
                window.location.reload();
            } else {
                if (data.message.includes('Senha incorreta')) {
                    senhaInput.classList.add('erro');
                    erroSenha.textContent = 'Senha incorreta. Por favor, tente novamente.';
                    senhaInput.value = ''; // Limpa o campo de senha
                } else {
                    alert('Erro ao alterar a consulta: ' + data.message);
                    modalAlterar.style.display = 'none';
                }
            }
        });
    });

    // Limpar mensagens de erro quando o usuário começa a digitar
    document.getElementById('senhaAlteracao').addEventListener('input', function() {
        this.classList.remove('erro');
        document.getElementById('erroSenhaAlteracao').textContent = '';
    });

    // Limpar campos e erros ao fechar o modal
    closeButtonAlterar.addEventListener('click', () => {
        modalAlterar.style.display = 'none';
        const senhaInput = document.getElementById('senhaAlteracao');
        senhaInput.value = '';
        senhaInput.classList.remove('erro');
        document.getElementById('erroSenhaAlteracao').textContent = '';
    });
});