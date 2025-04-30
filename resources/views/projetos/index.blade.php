<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Projetos</title>
</head>
<body>
    <h1>Gerenciar Projetos</h1>

    <!-- Formulário de Projeto -->
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

        async function carregarProjetos() {
            const response = await fetch(apiUrl, {
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
                        <button onclick="editarProjeto(${projeto.id})">Editar</button>
                        <button onclick="excluirProjeto(${projeto.id})">Excluir</button>
                    </td>`;
                tbody.appendChild(tr);
            });
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
                    // Requisição PUT (atualização)
                    console.log('Enviando PUT para:', `${apiUrl}/${id}`);
                    response = await fetch(`${apiUrl}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                } else {
                    // Requisição POST (criação)
                    console.log('Enviando POST para:', apiUrl);
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

        carregarProjetos();
    </script>
</body>
</html>
