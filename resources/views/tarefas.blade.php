<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Tarefas</title>
    <link rel="stylesheet" href="{{ asset('css/tarefas.css') }}">
</head>
<body>
    @include('menu')
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
                <span id="data_inicio_formatada"></span>
            </label><br>
            <label>Data de Fim:
                <input type="date" id="data_fim">
                <span id="data_fim_formatada"></span>
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
        if (!token) window.location.href = "/login";

        const user = jwt.parseJwt(token);
        const isAdmin = user && user.role === 'admin';

        const tarefaUrl = `${window.location.origin}/api/tarefas`;
        const projetoUrl = `${window.location.origin}/api/projetos`;

        const formatarData = data => {
            if (!data) return '';
            const d = new Date(data);
            const localDate = new Date(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate());
            console.log('date', d, localDate, 'ret', `${String(localDate.getDate()).padStart(2, '0')}/${String(localDate.getMonth() + 1).padStart(2, '0')}/${localDate.getFullYear()}`);
            return `${String(localDate.getDate()).padStart(2, '0')}/${String(localDate.getMonth() + 1).padStart(2, '0')}/${localDate.getFullYear()}`;
        };

        const carregarProjetosSelect = async () => {
            const res = await fetch(projetoUrl, { headers: { Authorization: 'Bearer ' + token } });
            const projetos = await res.json();
            const selectProjeto = document.getElementById('id_projeto');
            const filtroProjeto = document.getElementById('filtro-projeto');

            selectProjeto.innerHTML = '<option value="">Selecione</option>';
            filtroProjeto.innerHTML = '<option value="">Todos</option>';

            projetos.forEach(p => {
                const opt = new Option(p.nome, p.id);
                selectProjeto.add(opt.cloneNode(true));
                filtroProjeto.add(opt);
            });
        };

        const carregarTarefas = async (url = tarefaUrl) => {
            const res = await fetch(url, { headers: { Authorization: 'Bearer ' + token } });
            const tarefas = await res.json();
            const tbody = document.querySelector('#tabela-tarefas tbody');
            tbody.innerHTML = '';

            tarefas.forEach(t => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${t.id}</td>
                    <td>${t.descricao}</td>
                    <td>${t.projeto && t.projeto.nome || ''}</td>
                    <td>${formatarData(t.data_inicio)}</td>
                    <td>${formatarData(t.data_fim)}</td>
                    <td>${t.predecessora && t.predecessora.descricao || ''}</td>
                    <td>${t.status === 'concluida' ? 'Concluída' : 'Não Concluída'}</td>
                    <td>
                        ${isAdmin ? `
                            <button onclick="editarTarefa(${t.id})">Editar</button>
                            <button onclick="excluirTarefa(${t.id})">Excluir</button>
                        ` : ''}
                    </td>`;
                tbody.appendChild(tr);
            });
        };

        if (!isAdmin) {
            document.getElementById('form-container').style.display = 'none';
        }

        const carregarPredecessoras = async (excluirId = null) => {
            const res = await fetch(`${tarefaUrl}/predecessoras`, {
                headers: { Authorization: 'Bearer ' + token }
            });
            const tarefas = await res.json();
            const select = document.getElementById('id_tarefa_predecessora');
            select.innerHTML = '<option value="">Nenhuma</option>';

            tarefas.forEach(t => {
                if (t.id !== excluirId) {
                    select.add(new Option(t.descricao, t.id));
                }
            });
        };

        const editarTarefa = async id => {
            const res = await fetch(`${tarefaUrl}/${id}`, {
                headers: { Authorization: 'Bearer ' + token }
            });
            if (!res.ok) return alert('Erro ao carregar tarefa.');

            const tarefa = await res.json();

            document.getElementById('form-title').innerText = 'Editar Tarefa';
            document.getElementById('id').value = tarefa.id;
            document.getElementById('descricao').value = tarefa.descricao;
            document.getElementById('id_projeto').value = tarefa.id_projeto;
            document.getElementById('status').value = tarefa.status;
            document.getElementById('data_inicio').value = tarefa.data_inicio;
            // document.getElementById('data_inicio_formatada').innerText = formatarData(tarefa.data_inicio);
            document.getElementById('data_fim').value = tarefa.data_fim;
            // document.getElementById('data_fim_formatada').innerText = formatarData(tarefa.data_fim);

            await carregarPredecessoras(tarefa.id);
            document.getElementById('id_tarefa_predecessora').value = tarefa.id_tarefa_predecessora || '';
        };

        const cancelarEdicao = () => {
            document.getElementById('form-title').innerText = 'Nova Tarefa';
            document.getElementById('form-tarefa').reset();
            document.getElementById('id').value = '';
            document.getElementById('data_inicio_formatada').innerText = '';
            document.getElementById('data_fim_formatada').innerText = '';
            carregarPredecessoras();
        };

        const excluirTarefa = async id => {
            if (!confirm('Excluir esta tarefa?')) return;
            const res = await fetch(`${tarefaUrl}/${id}`, {
                method: 'DELETE',
                headers: { Authorization: 'Bearer ' + token }
            });
            const json = await res.json();
            alert(json.message);
            carregarTarefas();
        };

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

            try {
                let res, json;
                if (id) {
                    res = await fetch(`${tarefaUrl}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                    json = await res.json();
                    if (!res.ok) throw json;

                    alert('Tarefa atualizada!');
                } else {
                    res = await fetch(tarefaUrl, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                    json = await res.json();
                    if (!res.ok) throw json;

                    alert('Tarefa criada!');
                }

                cancelarEdicao();
                carregarTarefas();
            } catch (err) {
                alert(err.message || 'Erro ao salvar.');
            }
        });

        document.getElementById('filtro-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const url = new URL(tarefaUrl);
            const params = new URLSearchParams();

            const filtroDescricao = document.getElementById('filtro-descricao').value;
            const filtroProjeto = document.getElementById('filtro-projeto').value;
            const filtroStatus = document.getElementById('filtro-status').value;

            if (filtroDescricao) params.append('descricao', filtroDescricao);
            if (filtroProjeto) params.append('id_projeto', filtroProjeto);
            if (filtroStatus) params.append('status', filtroStatus);

            url.search = params.toString();
            carregarTarefas(url);
        });


        carregarProjetosSelect();
        carregarTarefas();
        carregarPredecessoras();
    </script>
</body>
</html>
