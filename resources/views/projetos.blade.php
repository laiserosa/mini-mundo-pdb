<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Projetos</title>
    <link rel="stylesheet" href="{{ asset('css/projetos.css') }}">
</head>
<body>
    <h1>Gerenciar Projetos</h1>

    <!-- Formulário de Projeto -->
    <div id="form-container">
        <h2 id="form-title">Novo Projeto</h2>
        <form id="form-projeto">
            <input type="hidden" id="id" />
            <label>Nome*: <input type="text" id="nome" required></label><br>
            <label>Descrição: <input type="text" id="descricao"></label><br>
            <label>Status*:
                <select id="status" required>
                    <option value="">Selecione</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </label><br>
            <label>Orçamento: <input type="number" id="orcamento" step="0.01" min="0"></label><br>
            <button type="submit">Salvar</button>
            <button type="button" onclick="cancelarEdicao()">Cancelar</button>
        </form>
    </div>

    <hr>

    <!-- Formulário de Filtros -->
    <h2>Filtros de Pesquisa</h2>
    <form id="filtro-form">
        <div class="filtro-linha">
            <div class="filtro-nome">
                <label>Nome do Projeto:</label>
                <input type="text" id="filtro-nome">
            </div>
            <div class="filtro-status">
                <label>Status:</label>
                <select id="filtro-status">
                    <option value="">Selecione</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
        </div>

        <div class="filtro-linha">
            <div class="filtro-orcamento-min">
                <label>Orçamento (mín):</label>
                <input type="number" id="orcamento-min" step="0.01" min="0">
            </div>
            <div class="filtro-orcamento-max">
                <label>Orçamento (máx):</label>
                <input type="number" id="orcamento-max" step="0.01" min="0">
            </div>
            <div class="filtro-botao">
                <button type="submit">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- <hr>

Container principal com filtro e botão lado a lado
<div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
    
    <form id="filtro-form" style="flex: 3; min-width: 300px;">
        <h2>Filtros de Pesquisa</h2>

        <label>Nome do Projeto:</label>
        <input type="text" id="filtro-nome">

        <label>Status:</label>
        <select id="filtro-status">
            <option value="">Selecione</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
        </select>

        <label>Orçamento (mín):</label>
        <input type="number" id="orcamento-min" step="0.01" min="0">

        <label>Orçamento (máx):</label>
        <input type="number" id="orcamento-max" step="0.01" min="0">

        <button type="submit" style="margin-top: 10px;">Filtrar</button>
    </form>

    
    <div style="flex: 1; min-width: 200px; display: flex; justify-content: flex-end; align-items: flex-start;">
        <a href="/projetos/criar">
            <button style="background-color: #28a745;">Criar Novo Projeto</button>
        </a>
    </div>
</div> -->

    <hr>

    <!-- Tabela de Projetos -->
    <h2>Lista de Projetos</h2>
    <table id="tabela-projetos" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Orçamento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script src="{{ asset('js/jwt.js') }}"></script>
    <script>
        const token = jwt.getToken();
        const apiUrl = `${window.location.origin}/api/projetos`;

        if (!token) window.location.href = "/login";

        const user = jwt.parseJwt(token);
        const isAdmin = user?.role === 'admin';

        async function carregarProjetos(url = apiUrl) {
            const response = await fetch(url, {
                headers: { 'Authorization': 'Bearer ' + token }
            });

            if (!response.ok) return alert('Erro ao carregar projetos');

            const projetos = await response.json();
            const tbody = document.querySelector("#tabela-projetos tbody");
            tbody.innerHTML = '';

            projetos.forEach(projeto => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${projeto.id}</td>
                    <td>${projeto.nome}</td>
                    <td>${projeto.descricao || ''}</td>
                    <td>${projeto.status === 'ativo' ? 'Ativo' : 'Inativo'}</td>
                    <td>${projeto.orcamento ? 'R$ ' + parseFloat(projeto.orcamento).toFixed(2).replace('.', ',') : ''}</td>
                    <td>
                        ${isAdmin ? `
                            <button onclick="editarProjeto(${projeto.id})">Editar</button>
                            <button onclick="excluirProjeto(${projeto.id})">Excluir</button>
                        ` : ''}
                    </td>`;
                tbody.appendChild(tr);
            });

            // Esconder o formulário se não for admin
            if (!isAdmin) {
                document.getElementById('form-container').style.display = 'none';
            }
        }

        async function editarProjeto(id) {
            const response = await fetch(`${apiUrl}/${id}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const projeto = await response.json();
            document.getElementById('form-title').innerText = 'Editar Projeto';
            document.getElementById('id').value = projeto.id;
            document.getElementById('nome').value = projeto.nome;
            document.getElementById('descricao').value = projeto.descricao || '';
            document.getElementById('status').value = projeto.status;
            document.getElementById('orcamento').value = projeto.orcamento ? projeto.orcamento.toFixed(2) : '';
        }

        function cancelarEdicao() {
            document.getElementById('form-title').innerText = 'Novo Projeto';
            document.getElementById('form-projeto').reset();
            document.getElementById('id').value = '';
        }

        async function excluirProjeto(id) {
            if (!confirm('Tem certeza que deseja excluir este projeto?')) return;
            const response = await fetch(`${apiUrl}/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const json = await response.json();
            alert(json.message);
            carregarProjetos();
        }

        document.getElementById('form-projeto').addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = document.getElementById('id').value;
            const body = {
                nome: document.getElementById('nome').value,
                descricao: document.getElementById('descricao').value,
                status: document.getElementById('status').value,
                orcamento: document.getElementById('orcamento').value
                    ? parseFloat(document.getElementById('orcamento').value.replace(',', '.'))
                    : null
            };

            let response;
            let json;

            try {
                if (id) {
                    response = await fetch(`${apiUrl}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                } else {
                    response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                }

                json = await response.json();

                if (!response.ok) {
                    console.error('Erro:', json);
                    alert(json.message || 'Erro ao salvar projeto');
                    return;
                }

                alert(id ? 'Projeto atualizado!' : 'Projeto criado!');
                cancelarEdicao();
                carregarProjetos();

            } catch (err) {
                console.error('Erro inesperado:', err);
                alert('Erro inesperado ao salvar projeto.');
            }
        });

        document.getElementById('filtro-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const nome = document.getElementById('filtro-nome').value;
            const status = document.getElementById('filtro-status').value;
            const orcamentoMin = document.getElementById('orcamento-min').value;
            const orcamentoMax = document.getElementById('orcamento-max').value;

            const url = new URL(apiUrl);
            const params = new URLSearchParams();

            if (nome) params.append('nome', nome);
            if (status) params.append('status', status);
            if (orcamentoMin) params.append('orcamento_min', orcamentoMin);
            if (orcamentoMax) params.append('orcamento_max', orcamentoMax);

            url.search = params.toString();
            carregarProjetos(url);
        });

        carregarProjetos();
    </script>
</body>
</html>
