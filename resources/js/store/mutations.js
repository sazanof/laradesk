export default {
    setAuthenticated(state, a) {
        state.authenticated = a
    },
    setUser(state, user) {
        state.user = user
    },
    updateAvatar(state, blobUrl) {
        state.user.photo = blobUrl
    },
    setNotifications(state, notifications) {
        state.notifications = notifications
    },
    setCategories(state, categories) {
        state.categories = categories
    },
    setFields(state, fields) {
        state.fields = fields
    },
    addField(state, field) {
        state.fields.push(field)
    },
    editField(state, field) {
        state.fields = state.fields.map(_f => {
            if (_f.id === field.id) {
                return Object.assign(_f, field)
            }
            return _f
        })
    },
    deleteField(state, id) {
        state.fields = state.fields.filter(field => id !== field.id)
    },
    setOffices(state, offices) {
        state.offices = offices
    },
    setTickets(state, tickets) {
        state.tickets = tickets
    },
    setUserTickets(state, tickets) {
        state.userTickets = tickets
    },
    setTicket(state, ticket) {
        state.ticket = ticket
    },
    deleteTicket(state) {
        state.ticket = null
    },
    updateTicket(state, newState) {
        if (typeof newState !== 'object') {
            alert('newState must be an object')
        }
        state.ticket = Object.assign(state.ticket, newState)
    },
    updateApprovalStatus(state, status) {
        state.ticket.approvals = state.ticket.approvals.map(approval => {
            if (approval.user_id === state.user.id) {
                approval.approved = status
            }
            return approval
        })
    },
    setCounters(state, counters) {
        state.counters = {
            new: counters.new,
            my: counters.my,
            approval: counters.approval
        }
    },
    setThread(state, thread) {
        state.thread = thread
    },
    setAssignees(state, assignees) {
        state.ticket.assignees = assignees
    },
    setUserDashboardData(state, res) {
        state.dashboard.user = res
    },
    setAdminDashboardData(state, res) {
        state.dashboard.admin = res
    },
    updateConnectionState(state, status) {
        state.ws.connected = status
    },
    updateConnectingState(state, status) {
        state.ws.connecting = status
    },
    updateConnectionId(state, id) {
        state.ws.id = id
    },
    addNotification(state, noty) {
        state.ws.notifications.push(Object.assign({
            id: state.ws.notifications.length + 1,
            new: true
        }, noty))
    },
    readNotification(state, noty) {
        state.ws.notifications = state.ws.notifications.map(n => {
            if (noty.id === n.id) {
                n.new = false
            }
            return n
        })
    }
}
