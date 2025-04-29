<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form id="loginForm">
        <input type="text" name="cpf" placeholder="CPF" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>

    <script src="{{ asset('js/jwt.js') }}"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {
                cpf: formData.get('cpf'),
                senha: formData.get('senha')
            };

            const response = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                jwt.saveToken(result.token);
                window.location.href = "/projetos";
            } else {
                alert(result.message || 'Falha no login');
            }
        });
    </script>
</body>
</html>
