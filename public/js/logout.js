async function logout() {
    const token = jwt.getToken();
    if (!token) return window.location.href = "/login";

    try {
        const response = await fetch(`${window.location.origin}/api/logout`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });

        const json = await response.json();
        alert(json.message || 'Logout realizado');

        jwt.removeToken();
        window.location.href = "/login";
    } catch (error) {
        console.error('Erro ao fazer logout:', error);
        alert('Erro ao tentar sair. Tente novamente.');
    }
}
