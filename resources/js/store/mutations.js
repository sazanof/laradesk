export default {
    updateConfigMaxFileSize(state, size) {
        state.config.maxFileSize = size
    },
    updateConfigAllowedMimes(state, mimes) {
        state.config.allowedMimes = typeof mimes === 'string' ? mimes.split(',') : mimes
    },
    setAdditionalCriteria(state, criteria) {
        state.additionalCriteria = criteria
    },
    updateCurrentWidth(state, w) {
        state.appWidth = w
        state.isMobile = w <= 860
    },
    setAuthenticated(state, a) {
        state.authenticated = a
    },
    setUser(state, user) {
        state.user = user
    },
    setUserDepartments(state, departments) {
        if (departments === null) return false
        state.userDepartments = departments.map(d => {
            return {
                id: d.department_id,
                name: d.department.name,
                admin_id: d.admin_id
            }
        })
    },
    updateDepartment(state, department) {
        state.departments.map(d => {
            if (d.id === department.id) {
                d = Object.assign(d, department)
            }
            return d
        })
    },
    addDepartment(state, department) {
        state.departments.push(department)
    },
    setActiveDepartment(state, department) {
        if (department === null || typeof department === 'undefined') {
            localStorage.removeItem('activeDepartment')
        } else if (department.admin_id !== state.user.id) {
            state.activeDepartment = null
            localStorage.removeItem('activeDepartment')
        } else {
            localStorage.setItem('activeDepartment', JSON.stringify(department))
        }
        state.activeDepartment = department
    },
    updateAvatar(state, blobUrl) {
        state.user.photo = blobUrl
    },
    setSystemNotifications(state, notifications) {
        state.systemNotifications = notifications
    },
    setCategories(state, categories) {
        state.categories = categories
    },
    setDepartments(state, departments) {
        state.departments = departments
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
    setRooms(state, rooms) {
        state.rooms = rooms
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
    showNotifications(state, show) {
        state.showNotifications = show
    },
    setUserNotifications(state, notifications) {
        state.userNotifications = notifications
    },
    addUserNotification(state, notification) {
        if (!notification.hasOwnProperty('read_at')) {
            notification.read_at = null
        }
        state.userNotifications.unshift(notification)
    },
    readUserNotification(state, id) {
        state.userNotifications = state.userNotifications.map(n => {
            if (id === n.id) {
                n.read_at = new Date().toLocaleDateString()
            }
            return n
        })
    },
    deleteUserNotification(state, id) {
        state.userNotifications = state.userNotifications.filter(n => id !== n.id)
    },
    createOffice(state, office) {
        const founded = state.offices.find(o => o.id === office.id)
        if (!founded) {
            state.offices.unshift(office)
        }
    },
    editOffice(state, office) {
        state.offices.map(o => {
            if (o.id === office.id) {
                o = Object.assign(o, office)
            }
            return o
        })
    },
    deleteOffice(state, id) {
        state.offices = state.offices.filter(o => o.id !== id)
    },
    setCopyTicketData(state, ticket) {
        state.copyTicketData = ticket
    },
    clearCopyTicketData(state) {
        state.copyTicketData = null
    }
}
