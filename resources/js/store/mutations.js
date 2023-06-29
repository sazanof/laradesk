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
    }
}
