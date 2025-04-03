document.addEventListener('DOMContentLoaded', function() {
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroData = document.getElementById('filtroData');
    const tabelaBody = document.getElementById('tabela-consultas-body');

    // Definir a data atual no filtro de data
    filtroData.value = new Date().toISOString().split('T')[0];

    // Carregar consultas iniciais
    carregarConsultas();

    function carregarConsultas() {
        fetch('../PHP/minhas_consultas.php?action=getConsultas')
            .then(response => response.json())
            .then(consultas => {
                preencherTabela(consultas);
            })
            .catch(error => console.error('Erro ao carregar consultas:', error));
    }

    function preencherTabela(consultas) {
        tabelaBody.innerHTML = '';
        
        consultas.forEach(consulta => {
            const row = document.createElement('tr');
            row.className = 'consulta-row';
            row.setAttribute('data-estado', consulta.estado);
            
            row.innerHTML = `
                <td>${consulta.data_consulta}</td>
                <td>${consulta.hora_consulta}</td>
                <td>${consulta.nome_cliente}</td>
                <td>${consulta.tipo_consulta}</td>
                <td>${consulta.estado}</td>
                <td>
                    <button onclick="verDetalhes('${consulta.id_consulta}')" class="btn-detalhes">
                        Ver Detalhes
                    </button>
                    ${consulta.estado === 'Agendada' ? `
                        <button onclick="iniciarConsulta('${consulta.id_consulta}')" class="btn-iniciar">
                            Iniciar Consulta
                        </button>
                    ` : ''}
                </td>
            `;
            
            tabelaBody.appendChild(row);
        });

        aplicarFiltros();
    }

    function aplicarFiltros() {
        const estado = filtroEstado.value;
        const data = filtroData.value;
        const rows = document.querySelectorAll('.consulta-row');

        rows.forEach(row => {
            const rowEstado = row.getAttribute('data-estado');
            const rowData = row.querySelector('td:first-child').textContent;
            
            const matchEstado = estado === 'todos' || estado === rowEstado;
            const matchData = !data || formatarData(rowData) === data;

            row.style.display = matchEstado && matchData ? '' : 'none';
        });
    }

    function formatarData(dataStr) {
        const [dia, mes, ano] = dataStr.split('/');
        return `${ano}-${mes}-${dia}`;
    }

    filtroEstado.addEventListener('change', aplicarFiltros);
    filtroData.addEventListener('change', aplicarFiltros);
});

// Funções para os botões
function verDetalhes(idConsulta) {
    window.location.href = `../PHP/detalhes_consulta.php?id=${idConsulta}`;
}

function iniciarConsulta(idConsulta) {
    window.location.href = `../PHP/realizar_consulta.php?id=${idConsulta}`;
} 