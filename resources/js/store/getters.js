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
    getTickets(state) {
        return state.tickets
    },
    getUserTickets(state) {
        return state.userTickets
    },
    getTicket(state) {
        return state.ticket
    },
    getApprovals(state) {
        return state.ticket.approvals
    },
    iAmApproval(state) {
        const approval = state.ticket.approvals.find(a => a.user_id === state.user.id)
        if (approval !== undefined) {
            return approval
        }
        return null
    }
}
