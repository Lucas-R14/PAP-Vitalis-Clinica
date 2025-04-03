document.addEventListener('DOMContentLoaded', function () {
    // Adicionar classe "required" a todas as labels de campos obrigatórios
    document.querySelectorAll('input[required], select[required]').forEach(field => {
        const labelFor = field.id;
        const label = document.querySelector(`label[for="${labelFor}"]`);
        if (label) {
            label.classList.add('required');
        }
    });

    // Configuração do Flatpickr para o campo de data de nascimento
    flatpickr("#data_nascimento", {
        dateFormat: "Y-m-d", // Formato da data
        defaultDate: "2000-01-01", // Data padrão
        maxDate: "today", // Impede selecionar datas futuras
        locale: "pt", // Localização em português
        disableMobile: true, // Desativa o date picker nativo em dispositivos móveis
        onChange: function(selectedDates, dateStr, instance) {
            console.log("Data selecionada:", dateStr);
        }
    });

    // Resto do código de validação...
    const criarFuncionarioForm = document.getElementById('criarFuncionarioForm');
    const erroSenha = document.getElementById('erroSenha');
    const erroSenhaComprimento = document.getElementById('erroSenhaComprimento');

    // NIF e Telefone: Permitir apenas números e limitar a 9 dígitos
    ['nif', 'telefone'].forEach(id => {
        document.getElementById(id).addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    });

    // Função para validar se as senhas coincidem
    function validarSenhas() {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;

        if (senha !== confirmarSenha) {
            erroSenha.style.display = 'block'; // Mostra a mensagem de erro
            return false;
        } else {
            erroSenha.style.display = 'none'; // Oculta a mensagem de erro
            return true;
        }
    }

    // Função para validar o comprimento da senha
    function validarComprimentoSenha() {
        const senha = document.getElementById('senha').value;

        if (senha.length < 6) {
            erroSenhaComprimento.style.display = 'block'; // Mostra a mensagem de erro
            return false;
        } else {
            erroSenhaComprimento.style.display = 'none'; // Oculta a mensagem de erro
            return true;
        }
    }

    // Validação do código postal (xxxx-xxx)
    function validarCodigoPostal() {
        const codigoPostal = document.getElementById('codigo_postal').value;
        const codigoPostalValido = /^\d{4}-\d{3}$/.test(codigoPostal);

        if (!codigoPostalValido) {
            alert('O código postal deve estar no formato 1234-567.');
            return false;
        }
        return true;
    }

    // Formatação automática do Código Postal (xxxx-xxx)
    document.getElementById('codigo_postal').addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, ''); // Remove tudo que não for número

        if (valor.length > 4) {
            valor = valor.slice(0, 4) + '-' + valor.slice(4, 7);
        }

        this.value = valor;
    });

    // Validação de email
    document.getElementById('email').addEventListener('input', function () {
        const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
        this.setCustomValidity(emailValido ? '' : 'Por favor, insira um email válido.');
    });

    // Concatenação do endereço antes de enviar
    function concatenarEndereco() {
        const morada = document.getElementById('morada').value;
        const apartamento = document.getElementById('apartamento').value;
        const codigoPostal = document.getElementById('codigo_postal').value;
        const distrito = document.getElementById('distrito').value;
        const concelho = document.getElementById('concelho').value;

        let enderecoCompleto = `${morada}, ${apartamento ? apartamento + ', ' : ''}${codigoPostal}, ${distrito}, ${concelho}`;

        let campoEndereco = document.getElementById("endereco_completo");
        if (!campoEndereco) {
            campoEndereco = document.createElement("input");
            campoEndereco.type = "hidden";
            campoEndereco.id = "endereco_completo";
            campoEndereco.name = "endereco";
            criarFuncionarioForm.appendChild(campoEndereco);
        }
        campoEndereco.value = enderecoCompleto;
    }

    // Validação ao enviar o formulário
    criarFuncionarioForm.addEventListener('submit', function (event) {
        const senhasValidas = validarSenhas();
        const senhaComprimentoValido = validarComprimentoSenha();
        const codigoPostalValido = validarCodigoPostal();

        if (!senhasValidas || !senhaComprimentoValido || !codigoPostalValido) {
            event.preventDefault(); // Impede o envio do formulário se houver erros
        } else {
            concatenarEndereco();
        }
    });

    // Validação em tempo real das senhas
    document.getElementById('senha').addEventListener('input', function () {
        validarComprimentoSenha();
        validarSenhas();
    });

    document.getElementById('confirmar_senha').addEventListener('input', function () {
        validarSenhas();
    });

    // Adicione essa função no final do seu arquivo JS existente
    document.querySelectorAll('.form-section input, .form-section select').forEach(item => {
        const label = item.previousElementSibling;
        
        if (label && label.tagName === 'LABEL') {
            // Ao focar no campo
            item.addEventListener('focus', () => {
                label.style.color = '#292f7e';
                label.style.transform = 'translateX(5px)';
            });
            
            // Ao remover o foco
            item.addEventListener('blur', () => {
                label.style.color = '';
                label.style.transform = '';
            });
        }
    });
});