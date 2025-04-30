<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Tarefas</title>
    <link rel="stylesheet" href="{{ asset('css/tarefas.css') }}">
</head>
<body>
    <h1>Gerenciar Tarefas</h1>

    <div id="form-container">
        <h2 id="form-title">Nova Tarefa</h2>
        <form id="form-tarefa">
            <input type="hidden" id="id" />
            <label>Descrição*:<input type="text" id="descricao" required></label><br>
            <label>Projeto*:
                <select id="id_projeto" required></select>
            </label><br>
            <label>Data de Início:
                <input type="date" id="data_inicio">
                <span id="data_inicio_formatada" style="margin-left: 10px; font-size: 0.9em; color: gray;"></span>
            </label><br>
            <label>Data de Fim:
                <input type="date" id="data_fim">
                <span id="data_fim_formatada" style="margin-left: 10px; font-size: 0.9em; color: gray;"></span>
            </label><br>
            <label>Tarefa Predecessora:
                <select id="id_tarefa_predecessora">
                    <option value="">Nenhuma</option>
                </select>
            </label><br>
            <label>Status*:
                <select id="status" required>
                    <option value="">Selecione</option>
                    <option value="concluida">Concluída</option>
                    <option value="nao_concluida">Não Concluída</option>
                </select>
            </label><br>
            <button type="submit">Salvar</button>
            <button type="button" onclick="cancelarEdicao()">Cancelar</button>
        </form>
    </div>

    <hr>

    <h2>Filtros de Pesquisa</h2>
    <form id="filtro-form">
        <label>Descrição:<input type="text" id="filtro-descricao"></label>
        <label>Projeto:
            <select id="filtro-projeto"></select>
        </label>
        <label>Status:
            <select id="filtro-status">
                <option value="">Todos</option>
                <option value="concluida">Concluída</option>
                <option value="nao_concluida">Não Concluída</option>
            </select>
        </label>
        <button type="submit">Filtrar</button>
    </form>

    <hr>

    <h2>Lista de Tarefas</h2>
    <table id="tabela-tarefas" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Projeto</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Predecessora</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script src="{{ asset('js/jwt.js') }}"></script>
    <script>
        const token = jwt.getToken();
        const tarefaUrl = `${window.location.origin}/api/tarefas`;
        const projetoUrl = `${window.location.origin}/api/projetos`;

        if (!token) window.location.href = "/login";
        const user = jwt.parseJwt(token);
        const isAdmin = user?.role === 'admin';

        async function carregarProjetosSelect() {
            const res = await fetch(projetoUrl, { headers: { Authorization: 'Bearer ' + token } });
            const projetos = await res.json();
            const selectProjeto = document.getElementById('id_projeto');
            const filtroProjeto = document.getElementById('filtro-projeto');

            selectProjeto.innerHTML = '<option value="">Selecione</option>';
            filtroProjeto.innerHTML = '<option value="">Todos</option>';
            projetos.forEach(proj => {
                const optionsSelect = new Option(proj.nome, proj.id);
                const optionsFiltro = new Option(proj.nome, proj.id);
                selectProjeto.add(optionsSelect);
                filtroProjeto.add(optionsFiltro);
            });
        }

        function formatarDataParaInput(data) {
            if (!data) return '';
            const d = new Date(data);
            const ano = d.getFullYear();
            const mes = String(d.getMonth() + 1).padStart(2, '0');
            const dia = String(d.getDate()).padStart(2, '0');
            return `${ano}-${mes}-${dia}`;
        }

        function carregarTarefas(tarefas) {
            const tbody = document.querySelector("#tabela-tarefas tbody");
            tbody.innerHTML = '';

            tarefas.forEach(tarefa => {
                const dataInicio = formatarDataParaInput(tarefa.data_inicio);
                const dataFim = formatarDataParaInput(tarefa.data_fim);

                const projetoNome = tarefa.projeto ? tarefa.projeto.nome : tarefa.id_projeto;
                const predecessora = tarefa.tarefa_predecessora ? tarefa.tarefa_predecessora.descricao : '';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${tarefa.id}</td>
                    <td>${tarefa.descricao || ''}</td>
                    <td>${projetoNome}</td>
                    <td>${dataInicio}</td>
                    <td>${dataFim}</td>
                    <td>${predecessora}</td>
                    <td>${tarefa.status === 'concluida' ? 'Concluída' : 'Não Concluída'}</td>
                    <td>
                        <button onclick="editarTarefa(${tarefa.id})">Editar</button>
                        <button onclick="excluirTarefa(${tarefa.id})">Excluir</button>
                    </td>`;
                tbody.appendChild(tr);
            });
        }

        async function buscarTarefas(url = tarefaUrl) {
            const res = await fetch(url, {
                headers: {
                    Authorization: 'Bearer ' + token
                }
            });

            if (!res.ok) {
                alert('Erro ao carregar tarefas');
                return;
            }

            const tarefas = await res.json();

            if (!Array.isArray(tarefas)) {
                console.error('Resposta inválida da API:', tarefas);
                alert('Erro ao carregar tarefas');
                return;
            }

            carregarTarefas(tarefas);
        }

        async function editarTarefa(id) {
            const response = await fetch(`/api/tarefas/${id}`, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            });

            if (!response.ok) {
                alert('Erro ao carregar tarefa');
                return;
            }

            const tarefa = await response.json();
            document.getElementById('descricao').value = tarefa.descricao;
            document.getElementById('status').value = tarefa.status;

            document.getElementById('data_inicio').value = tarefa.data_inicio_input && /^\d{4}-\d{2}-\d{2}$/.test(tarefa.data_inicio_input)
                ? tarefa.data_inicio_input
                : '';

            document.getElementById('data_fim').value = tarefa.data_fim_input && /^\d{4}-\d{2}-\d{2}$/.test(tarefa.data_fim_input)
                ? tarefa.data_fim_input
                : '';

            document.getElementById('data_inicio_formatada').textContent = tarefa.data_inicio_formatada;
            document.getElementById('data_fim_formatada').textContent = tarefa.data_fim_formatada;
        }

        function cancelarEdicao() {
            document.getElementById('form-title').innerText = 'Nova Tarefa';
            document.getElementById('form-tarefa').reset();
            document.getElementById('id').value = '';
        }

        async function excluirTarefa(id) {
            if (!confirm('Tem certeza que deseja excluir esta tarefa?')) return;
            const res = await fetch(`${tarefaUrl}/${id}`, {
                method: 'DELETE',
                headers: { Authorization: 'Bearer ' + token }
            });
            const json = await res.json();
            alert(json.message);
            buscarTarefas();
        }

        document.getElementById('form-tarefa').addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = document.getElementById('id').value;
            const body = {
                descricao: document.getElementById('descricao').value,
                id_projeto: document.getElementById('id_projeto').value,
                data_inicio: document.getElementById('data_inicio').value || null,
                data_fim: document.getElementById('data_fim').value || null,
                id_tarefa_predecessora: document.getElementById('id_tarefa_predecessora').value || null,
                status: document.getElementById('status').value
            };

            let res, json;
            try {
                res = await fetch(id ? `${tarefaUrl}/${id}` : tarefaUrl, {
                    method: id ? 'PUT' : 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                json = await res.json();

                if (!res.ok) {
                    console.error('Erro:', json);
                    alert(json.message || 'Erro ao salvar tarefa');
                    return;
                }

                alert(id ? 'Tarefa atualizada!' : 'Tarefa criada!');
                cancelarEdicao();
                buscarTarefas();
            } catch (err) {
                console.error('Erro inesperado:', err);
                alert('Erro inesperado ao salvar tarefa.');
            }
        });

        document.getElementById('filtro-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const descricao = document.getElementById('filtro-descricao').value;
            const id_projeto = document.getElementById('filtro-projeto').value;
            const status = document.getElementById('filtro-status').value;

            const url = new URL(tarefaUrl);
            const params = new URLSearchParams();
            if (descricao) params.append('descricao', descricao);
            if (id_projeto) params.append('id_projeto', id_projeto);
            if (status) params.append('status', status);
            url.search = params.toString();

            buscarTarefas(url);
        });

        carregarProjetosSelect();
        buscarTarefas();
    </script>
</body>
</html>
