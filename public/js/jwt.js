const jwt = {
    saveToken(token) {
        console.log('token ao logar', token);
        localStorage.setItem('jwt_token', token);
    },
    getToken() {
        return localStorage.getItem('jwt_token');
    },
    clearToken() {
        localStorage.removeItem('jwt_token');
    }
};
