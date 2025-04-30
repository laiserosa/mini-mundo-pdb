const jwt = {
    saveToken(token) {
        localStorage.setItem('jwt_token', token);
    },
    getToken() {
        return localStorage.getItem('jwt_token');
    },
    clearToken() {
        localStorage.removeItem('jwt_token');
    }
};
