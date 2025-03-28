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

    // Código das partículas
    const particlesContainer = document.querySelector('.particles');
    const numberOfParticles = 80; // Aumentei significativamente o número de partículas
    const particles = [];
    
    // Símbolos médicos
    const medicalSymbols = ['⚕', '❤', '+', '🩺', '💊', '🏥'];

    // Criar partículas
    for (let i = 0; i < numberOfParticles; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Tamanho aleatório maior
        const size = Math.random() * 20 + 25; // Aumentei ainda mais o tamanho
        particle.style.fontSize = `${size}px`;
        
        // Símbolo aleatório
        const symbol = medicalSymbols[Math.floor(Math.random() * medicalSymbols.length)];
        particle.setAttribute('data-symbol', symbol);
        
        // Distribuição mais ampla verticalmente
        const x = Math.random() * 100;
        const y = Math.random() * 200; // Aumentei para 200 para corresponder ao height do container
        particle.style.left = `${x}%`;
        particle.style.top = `${y}%`;
        
        // Rotação aleatória inicial
        const rotation = Math.random() * 360;
        particle.style.transform = `rotate(${rotation}deg)`;
        
        particlesContainer.appendChild(particle);
        particles.push({
            element: particle,
            rotation: rotation
        });
    }

    // Mover partículas com o scroll
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        
        particles.forEach((particle, index) => {
            const speed = (index % 3 + 1) * 0.15; // Reduzi um pouco a velocidade
            const yPos = scrolled * speed;
            const rotation = particle.rotation + (scrolled * 0.05); // Reduzi a velocidade da rotação
            particle.element.style.transform = `translateY(${-yPos}px) rotate(${rotation}deg)`;
        });
    });

    // Função para revelar elementos durante o scroll
    function reveal() {
        const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
        
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
});