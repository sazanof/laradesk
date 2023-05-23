export default {
    isAdmin(state) {
        return state.user?.is_admin
    },
    isAuthenticated(state) {
        return state.authenticated
    },
    getUser(state) {
        return state.user
    }
}
