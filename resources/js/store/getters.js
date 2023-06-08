export default {
    isAdmin(state) {
        return state.user?.is_admin
    },
    isSuperAdmin(state) {
        return state.user?.is_super_admin
    },
    isAuthenticated(state) {
        return state.authenticated
    },
    getUser(state) {
        return state.user
    },
    getCategories(state) {
        return state.categories
    },
    getFields(state) {
        return state.fields
    },
    getOffices(state) {
        return state.offices
    },
    getSentTickets(state) {
        return state.sentTickets
    }
}
