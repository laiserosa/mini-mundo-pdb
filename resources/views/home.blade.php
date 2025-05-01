<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <h1>Bem-vindo(a) ao Mini-mundo!</h1>

    <p>Escolha uma opção:</p>

    <ul>
        <li><a href="/projetos">Gerenciar Projetos</a></li>
        <li><a href="/tarefas">Gerenciar Tarefas</a></li>
        <li><a href="#" onclick="logout()" class="btn btn-danger">Logout</a></li>
    </ul>

    <script src="{{ asset('js/jwt.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>
    <script>
        const token = jwt.getToken();
        if (!token) window.location.href = "/login";
    </script>
</body>
</html>
