document.addEventListener('DOMContentLoaded', function () {
    const registoForm = document.getElementById('registoForm');
    const erroSenha = document.getElementById('erroSenha');
    const erroSenhaComprimento = document.getElementById('erroSenhaComprimento');

    // Lista de nacionalidades ordenadas
    const nacionalidades = [
        "Afegão(ã)", "Alemão(ã)", "Angolano(a)", "Argentino(a)", "Australiano(a)", 
        "Belga", "Boliviano(a)", "Brasileiro(a)", "Cabo-verdiano(a)", "Canadense", 
        "Chileno(a)", "Chinês(a)", "Colombiano(a)", "Coreano(a)", "Cubano(a)", 
        "Dinamarquês(a)", "Espanhol(a)", "Estadunidense", "Francês(a)", "Grego(a)", 
        "Guineense", "Holandês(a)", "Húngaro(a)", "Indiano(a)", "Inglês(a)", 
        "Italiano(a)", "Japonês(a)", "Moçambicano(a)", "Mexicano(a)", "Norueguês(a)", 
        "Paraguaio(a)", "Peruano(a)", "Português(a)", "Russo(a)", "Sueco(a)", 
        "Suíço(a)", "Timorense", "Uruguaio(a)", "Venezuelano(a)"
    ];
    const selectNacionalidade = document.getElementById("nacionalidade");

    // Adiciona nacionalidades dinamicamente
    nacionalidades.forEach(nacionalidade => {
        let option = document.createElement("option");
        option.value = nacionalidade;
        option.textContent = nacionalidade;
        selectNacionalidade.appendChild(option);
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

    // Validação das senhas
    function validarSenhas() {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;

        erroSenha.style.display = senha !== confirmarSenha ? 'block' : 'none';
        return senha === confirmarSenha;
    }

    // Validação do comprimento da senha
    function validarComprimentoSenha() {
        const senha = document.getElementById('senha').value;
        const senhaValida = senha.length >= 6;

        erroSenhaComprimento.style.display = senhaValida ? 'none' : 'block';
        return senhaValida;
    }

    document.getElementById('senha').addEventListener('input', validarComprimentoSenha);
    document.getElementById('confirmar_senha').addEventListener('input', validarSenhas);

    // NIF e Telefone: Permitir apenas números e limitar a 9 dígitos
    ['nif', 'telefone'].forEach(id => {
        document.getElementById(id).addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    });

    // Validação de email
    document.getElementById('email').addEventListener('input', function () {
        const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
        this.setCustomValidity(emailValido ? '' : 'Por favor, insira um email válido.');
    });

    // Formatação automática do Código Postal (xxxx-xxx)
    document.getElementById('codigo_postal').addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, ''); // Remove tudo que não for número

        if (valor.length > 4) {
            valor = valor.slice(0, 4) + '-' + valor.slice(4, 7);
        }

        this.value = valor;
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
            registoForm.appendChild(campoEndereco);
        }
        campoEndereco.value = enderecoCompleto;
    }

    registoForm.addEventListener('submit', function (event) {
        if (!validarSenhas() || !validarComprimentoSenha()) {
            event.preventDefault();
        } else {
            concatenarEndereco();
        }
    });
});