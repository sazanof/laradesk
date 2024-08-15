export default {
    getAppName(state) {
        return state.appName
    },
    getAppLogo(state) {
        return state.appLogo
    },
    getAppBg(state) {
        return state.appBg
    },
    getAllowedMimes(state) {
        return state.config.allowedMimes
    },
    getMaxFileSize(state) {
        return state.config.maxFileSize
    },
    getAdditionalCriteria(state) {
        return state.additionalCriteria
    },
    isAdmin(state) {
        return state.user?.is_admin
    },
    isMobile(state) {
        return state.isMobile
    },
    getAppWidth(state) {
        return state.appWidth
    },
    isShowNotifications(state) {
        return state.showNotifications
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
    getUserNews(state) {
        return state.news
    },
    getUserDepartments(state) {
        return state.userDepartments
    },
    userBelongsToDepartment: (state) => (id) => {
        return typeof state.userDepartments.find(dep => dep.id === id) === 'object'
    },
    getCategories(state) {
        return state.categories
    },
    getDepartments(state) {
        return state.departments
    },
    getActiveDepartments(state) {
        return state.departments.filter(d => {
            if (d.hasOwnProperty('deleted_at')) {
                return d.deleted_at === null
            }
            return true
        })
    },
    getFields(state) {
        return state.fields
    },
    getOffices(state) {
        return state.offices
    },
    getRooms(state) {
        return state.rooms
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
    iAmObserver(state) {
        const observer = state.ticket.observers.find(a => a.user_id === state.user.id)
        if (observer !== undefined) {
            return observer
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
    getUserNotifications(state) {
        return state.userNotifications
    },
    getCopyTicketData(state) {
        return state.copyTicketData
    },
    isLoading(state) {
        return state.loading
    }
}
