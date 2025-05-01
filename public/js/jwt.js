const jwt = {
    saveToken(token) {
        localStorage.setItem('jwt_token', token);
    },
    getToken() {
        return localStorage.getItem('jwt_token');
    },
    clearToken() {
        localStorage.removeItem('jwt_token');
    },    
    parseJwt(token) {
        try {
            return JSON.parse(atob(token.split('.')[1]));
        } catch (e) {
            return null;
        }
    },
    removeToken: function () {
        localStorage.removeItem('jwt_token');
    }
};
