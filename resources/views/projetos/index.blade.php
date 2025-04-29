<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Projetos</title>
</head>
<body>
    <h1>Lista de Projetos</h1>

    <table id="tabela-projetos" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script src="{{ asset('js/jwt.js') }}"></script>
    <script>
        async function carregarProjetos() {
            const token = jwt.getToken();
            if (!token) {
                window.location.href = "/login";
                return;
            }

            const response = await fetch('/api/projetos', {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            });

            if (response.ok) {
                const projetos = await response.json();
                const tbody = document.querySelector("#tabela-projetos tbody");
                tbody.innerHTML = '';

                projetos.forEach(projeto => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${projeto.id}</td><td>${projeto.nome}</td><td>${projeto.status}</td>`;
                    tbody.appendChild(tr);
                });
            } else {
                alert('Sessão expirada. Faça login novamente.');
                window.location.href = "/login";
            }
        }

        carregarProjetos();
    </script>
</body>
</html>
