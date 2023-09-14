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
    getActiveDepartment(state) {
        return state.activeDepartment
    },
    getUser(state) {
        return state.user
    },
    getUserDepartments(state) {
        return state.userDepartments
    },
    getCategories(state) {
        return state.categories
    },
    getDepartments(state) {
        return state.departments
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
    },
    getThread(state) {
        return state.thread
    },
    getDashboardData(state) {
        return state.dashboard
    },
    getConnectionState(state) {
        return state.ws.connected
    },
    getConnectingState(state) {
        return state.ws.connecting
    },
    getConnectionId(state) {
        return state.ws.id
    },
    getNotifications(state) {
        return state.ws.notifications
    }
}
