document.addEventListener('DOMContentLoaded', function() {
    let paginaAtual = 1;
    let searchTerm = '';
    let filterBy = 'id_consulta';
    let flatpickrInstance = null;
    
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

    // Configurar eventos de pesquisa
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');
    const searchFilter = document.getElementById('search-filter');

    // Evento de clique no botão de pesquisa
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            realizarPesquisa();
        });
    }

    // Evento de pressionar Enter no campo de pesquisa
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                realizarPesquisa();
            }
        });
    }

    // Evento de mudança no filtro
    if (searchFilter) {
        searchFilter.addEventListener('change', function() {
            filterBy = this.value;
            
            // Destruir instância anterior do flatpickr se existir
            if (flatpickrInstance) {
                flatpickrInstance.destroy();
                flatpickrInstance = null;
            }
            
            // Atualizar placeholder baseado no filtro selecionado
            if (searchInput) {
                switch (filterBy) {
                    case 'id_consulta':
                        searchInput.placeholder = 'Pesquisar por ID da consulta...';
                        searchInput.type = 'text';
                        break;
                    case 'tipo_consulta':
                        searchInput.placeholder = 'Pesquisar por tipo de consulta...';
                        searchInput.type = 'text';
                        break;
                    case 'nome_medico':
                        searchInput.placeholder = 'Pesquisar por nome do médico...';
                        searchInput.type = 'text';
                        break;
                    case 'data_consulta':
                        searchInput.placeholder = 'Selecionar data...';
                        searchInput.type = 'text';
                        // Inicializar Flatpickr para seleção de data
                        inicializarFlatpickr();
                        break;
                }
            }
        });
    }
    
    // Função para inicializar o Flatpickr
    function inicializarFlatpickr() {
        if (!searchInput) return;
        
        flatpickrInstance = flatpickr(searchInput, {
            dateFormat: "d/m/Y",
            locale: "pt",
            disableMobile: "true",
            allowInput: true,
            maxDate: new Date(),
            position: "auto",
            onChange: function(selectedDates, dateStr) {
                searchTerm = dateStr;
                // Se uma data for selecionada, realizar a pesquisa automaticamente
                if (dateStr) {
                    setTimeout(realizarPesquisa, 100);
                }
            }
        });
    }

    // Função para realizar a pesquisa
    function realizarPesquisa() {
        if (searchInput) {
            // Para data, usar o valor do flatpickr se estiver ativo
            if (filterBy === 'data_consulta' && flatpickrInstance) {
                searchTerm = flatpickrInstance.input.value.trim();
            } else {
                searchTerm = searchInput.value.trim();
            }
            
            paginaAtual = 1; // Resetar para primeira página
            carregarConsultas();
        }
    }

    // Carregar consultas iniciais
    carregarConsultas();

    // Função para carregar consultas
    function carregarConsultas() {
        const loading = document.getElementById('loading');
        const consultasWrapper = document.getElementById('consultas-wrapper');
        const semResultados = document.getElementById('sem-resultados');
        
        if (loading) loading.style.display = 'block';
        if (consultasWrapper) consultasWrapper.style.display = 'none';
        if (semResultados) semResultados.style.display = 'none';

        // Construir URL com parâmetros de pesquisa
        let url = `historico_consulta.php?pagina=${paginaAtual}`;
        if (searchTerm) {
            url += `&search=${encodeURIComponent(searchTerm)}&filter=${encodeURIComponent(filterBy)}`;
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (loading) loading.style.display = 'none';
            
            if (data.consultas.length === 0) {
                if (semResultados) semResultados.style.display = 'block';
                if (consultasWrapper) consultasWrapper.style.display = 'none';
                if (document.getElementById('paginacao')) {
                    document.getElementById('paginacao').innerHTML = '';
                }
                return;
            }

            if (semResultados) semResultados.style.display = 'none';
            if (consultasWrapper) consultasWrapper.style.display = 'block';
            renderizarConsultas(data.consultas);
            renderizarPaginacao(data.paginacao);
        })
        .catch(error => {
            console.error('Erro:', error);
            if (loading) loading.style.display = 'none';
            if (consultasWrapper) {
                consultasWrapper.innerHTML = '<p class="sem-consultas">Erro ao carregar consultas. Por favor, tente novamente.</p>';
                consultasWrapper.style.display = 'block';
            }
        });
    }

    // Função para renderizar consultas com formato melhorado e adaptado
    function renderizarConsultas(consultas) {
        const consultasWrapper = document.getElementById('consultas-wrapper');
        if (!consultasWrapper) return;
        
        consultasWrapper.innerHTML = consultas.map((consulta, index) => {
            // Formatar data e hora
            const dataFormatada = formatarData(consulta.data_consulta);
            const horaFormatada = formatarHora(consulta.hora_consulta);
            
            // Criar classe para o estado (removendo acentos para compatibilidade CSS)
            const estadoClasse = consulta.estado.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            
            return `
            <div class="consulta-card" onclick="abrirDetalhesConsulta(this)" data-id="${consulta.id_consulta}">
                <div class="consulta-header">
                    <div class="consulta-info-principal">
                        <div class="consulta-data-hora">
                            <span class="data">${dataFormatada}</span>
                            <span class="hora">${horaFormatada}</span>
                        </div>
                        <div class="consulta-tipo">${consulta.tipo_consulta}</div>
                    </div>
                    <div class="consulta-status ${estadoClasse}">${consulta.estado}</div>
                </div>

                <div class="consulta-corpo">
                    <div class="medico-info">
                        <h3>Médico</h3>
                        <div class="medico-detalhes">
                            <p class="nome-medico">Dr(a). ${consulta.nome_medico}</p>
                            <p class="especialidade">${consulta.especialidade}</p>
                        </div>
                    </div>

                    <div class="consulta-resumo">
                        <p class="ver-mais">Clique para ver detalhes completos</p>
                    </div>
                </div>

                <!-- Dados ocultos para o modal -->
                <div class="consulta-dados-completos" style="display: none;">
                    <div class="dado-consulta" data-label="ID da Consulta">${consulta.id_consulta}</div>
                    <div class="dado-consulta" data-label="Tipo de Consulta">${consulta.tipo_consulta}</div>
                    <div class="dado-consulta" data-label="Data">${dataFormatada}</div>
                    <div class="dado-consulta" data-label="Hora">${horaFormatada}</div>
                    <div class="dado-consulta" data-label="Estado">${consulta.estado}</div>
                    <div class="dado-consulta" data-label="Médico">Dr(a). ${consulta.nome_medico}</div>
                    <div class="dado-consulta" data-label="Especialidade">${consulta.especialidade}</div>
                    <div class="dado-consulta" data-label="Descrição">${consulta.descricao || 'Nenhuma descrição disponível'}</div>
                    <div class="dado-consulta" data-label="Observações">${consulta.observacoes || 'Nenhuma observação disponível'}</div>
                    <div class="dado-consulta" data-label="Prescrição">${consulta.prescricao || 'Nenhuma prescrição disponível'}</div>
                </div>
            </div>
            `;
        }).join('');

        // Adicionar eventos de clique após renderizar
        adicionarEventosClique();
    }

    // Função para formatar texto com quebras de linha
    function formatarTexto(texto) {
        if (!texto) return '';
        return texto
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/\n/g, '<br>');
    }

    // Função para renderizar paginação
    function renderizarPaginacao(paginacao) {
        const paginacaoElement = document.getElementById('paginacao');
        if (!paginacaoElement) return;
        
        let html = '';

        if (paginacao.total_paginas > 1) {
            // Botão anterior
            if (paginacao.pagina_atual > 1) {
                html += `<button class="pagina-btn" onclick="mudarPagina(${paginacao.pagina_atual - 1})">Anterior</button>`;
            }

            // Páginas
            for (let i = 1; i <= paginacao.total_paginas; i++) {
                html += `<button class="pagina-btn ${i === paginacao.pagina_atual ? 'ativo' : ''}" 
                        onclick="mudarPagina(${i})">${i}</button>`;
            }

            // Botão próximo
            if (paginacao.pagina_atual < paginacao.total_paginas) {
                html += `<button class="pagina-btn" onclick="mudarPagina(${paginacao.pagina_atual + 1})">Próximo</button>`;
            }
        }

        paginacaoElement.innerHTML = html;
    }

    // Função para mudar de página
    window.mudarPagina = function(pagina) {
        paginaAtual = pagina;
        carregarConsultas();
        window.scrollTo(0, 0);
    };

    // Funções auxiliares
    function formatarData(data) {
        return new Date(data).toLocaleDateString('pt-PT');
    }

    function formatarHora(hora) {
        return hora.substring(0, 5);
    }

    // Lógica de animação para o botão Voltar
    const btnVoltar = document.querySelector(".btn-voltar");

    if (btnVoltar) {
        btnVoltar.addEventListener("click", (e) => {
            e.preventDefault(); // Evita o comportamento padrão do link
            btnVoltar.classList.toggle("active");

            // Redireciona após a animação
            setTimeout(() => {
                window.location.href = btnVoltar.href;
            }, 300); // Tempo da animação
        });
    }

    // Função para abrir o modal com detalhes da consulta
    window.abrirDetalhesConsulta = function(elemento) {
        const modal = document.getElementById('modalDetalhes');
        const modalContent = modal.querySelector('.modal-detalhes-content');
        const dadosCompletos = elemento.querySelector('.consulta-dados-completos');
        const consultaId = elemento.getAttribute('data-id');
        
        if (!dadosCompletos) return;
        
        // Limpar conteúdo anterior
        const detalhesConteudo = document.getElementById('detalhes-conteudo');
        detalhesConteudo.innerHTML = '';
        
        // Preencher dados no modal
        const dadosItens = dadosCompletos.querySelectorAll('.dado-consulta');
        dadosItens.forEach(item => {
            const label = item.getAttribute('data-label');
            const valor = item.textContent;
            
            // Criar elementos para o modal
            const detalheItem = document.createElement('div');
            detalheItem.className = 'detalhe-item';
            
            const labelSpan = document.createElement('strong');
            labelSpan.textContent = label + ': ';
            
            const valorSpan = document.createElement('span');
            valorSpan.innerHTML = formatarTexto(valor);
            
            detalheItem.appendChild(labelSpan);
            detalheItem.appendChild(valorSpan);
            
            detalhesConteudo.appendChild(detalheItem);
        });
        
        // Atualizar o título do modal para incluir o ID
        const modalTitle = modalContent.querySelector('h2');
        modalTitle.textContent = `Detalhes da Consulta #${consultaId}`;
        
        // Mostrar o modal
        modal.style.display = 'block';
    };

    // Função para fechar o modal
    window.fecharModal = function() {
        const modais = document.querySelectorAll('.modal');
        modais.forEach(modal => {
            modal.style.display = 'none';
        });
    };

    // Fechar o modal ao clicar fora dele
    window.addEventListener('click', function(event) {
        const modais = document.querySelectorAll('.modal');
        modais.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Adicionar eventos de clique aos elementos
    function adicionarEventosClique() {
        document.querySelectorAll('.consulta-card').forEach(card => {
            card.addEventListener('click', function() {
                abrirDetalhesConsulta(this);
            });
        });
    }
}); 