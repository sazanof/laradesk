import axios from 'axios'

const MANAGEMENT_URL = '/admin/management'
const USER_TICKETS_URL = '/user/tickets'

export default {
    setCollapsed({ state }, collapsed) {
        localStorage.setItem('collapsed', collapsed)
        state.collapsed = collapsed
    },
    initAppValuesFromHiddenFields({ state }) {
        state.appBg = document.getElementById('appBg').value
        state.appName = document.getElementById('appName').value
    },
    async getUser({ commit }) {
        return await axios.get('/user').then(res => {
            if (res.data === '') {
                commit('setAuthenticated', false)
                commit('setUser', null)
            } else {
                commit('setAuthenticated', true)
                commit('setUser', res.data)
            }
        })
    },

    async logIn({ commit }, data) {
        await axios.post('/login', data).then(res => {
            if (typeof res.data === 'object' && res.data.id) {
                commit('setUser', res.data)
                commit('setAuthenticated', true)
            } else {
                commit('setUser', null)
                commit('setAuthenticated', false)
            }
        })
    },

    async getCategories({ commit }) {
        return await axios.get(MANAGEMENT_URL).then(res => {
            commit('setCategories', res.data)
        })
    },

    async getCategoryWithFields({ _ }, id) {
        return await axios.get(`${MANAGEMENT_URL}/categories/${id}`).then(res => {
            return res.data
        })
    },

    async saveCategory({}, data) {
        return await axios.put(`${MANAGEMENT_URL}/categories/${data.id}`, data).then(res => {
            return res.data
        })
    },

    async createCategory({}, data) {
        return await axios.post(`${MANAGEMENT_URL}/categories`, data).then(res => {
            return res.data
        })
    },

    async deleteCategory({}, id) {
        return await axios.delete(`${MANAGEMENT_URL}/categories/${id}`)
    },

    async getFields({ commit }) {
        return await axios.get(`${MANAGEMENT_URL}/fields`).then(res => {
            commit('setFields', res.data)
        })
    },
    async addField({ commit }, data) {
        return await axios.post(`${MANAGEMENT_URL}/fields`, data).then(res => {
            commit('addField', res.data)
        })
    },

    async linkField({ _ }, data) {
        return await axios.post(`${MANAGEMENT_URL}/fields/link`, data).then(res => {
            return res.data
        })
    },
    async unlinkField({ _ }, data) {
        return await axios.post(`${MANAGEMENT_URL}/fields/unlink`, data).then(res => {
            return res.data
        })
    },

    async changeFieldOrder({ _ }, data) {
        return await axios.put(`${MANAGEMENT_URL}/fields/order`, data).then(res => {
            return res.data
        })
    },

    async makeFieldRequired({ _ }, data) {
        return await axios.put(`${MANAGEMENT_URL}/fields/required`, data).then(res => {
            return res.data
        })
    },

    async editField({ commit }, data) {
        return await axios.put(`${MANAGEMENT_URL}/fields/${data.id}`, data).then(res => {
            commit('editField', res.data)
        })
    },
    async deleteField({ commit }, id) {
        return await axios.delete(`${MANAGEMENT_URL}/fields/${id}`).then(res => {
            commit('deleteField', id)
        })
    },
    async getOffices({ commit }) {
        return await axios.get('/offices').then(res => {
            commit('setOffices', res.data)
        })
    },
    async editProfile({ commit }, data) {
        return await axios.put('/profile', data).then(res => {
            commit('setUser', res.data)
        })
    },

    async updateNotificationsSettings({ commit }, data) {
        return await axios.post('/profile/notifications', data).then(res => {
            commit('setNotifications', res.data)
        })
    },
    async getNotificationsSettings({ commit }, data) {
        return await axios.get('/profile/notifications').then(res => {
            commit('setNotifications', res.data)
        })
    },

    async updateAvatar({ commit }, data) {
        return await axios.post('/profile/avatar', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            commit('setUser', res.data)
        })
    },

    /** USERS **/

    async getTicketCategories({ commit }) {
        return await axios.get(`${USER_TICKETS_URL}/categories`).then(res => {
            commit('setCategories', res.data)
        })
    },

    async getCategoryFields({ _ }, id) {
        return await axios.get(`${USER_TICKETS_URL}/categories/${id}/fields`).then(res => {
            return res.data
        })
    },

    async sendTicket({ _ }, data) {
        return await axios.post(`${USER_TICKETS_URL}/create`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },

    async searchUsers({ _ }, term) {
        return await axios.get(`${USER_TICKETS_URL}/search/users/${term}`).then(res => {
            return res.data
        })
    },

    async getUserTickets({ commit }, filter) {
        return await axios.post(`${USER_TICKETS_URL}`, filter).then(res => {
            commit('setUserTickets', res.data)
        })
    },

    async getUserTicket({ commit }, id) {
        return await axios.get(`${USER_TICKETS_URL}/${id}`).then(res => {
            commit('setTicket', res.data)
        })
    },

    async getTickets({ commit }, filter) {
        return await axios.post('/admin/tickets', filter).then(res => {
            commit('setTickets', res.data)
        })
    },

    async getTicket({ commit }, id) {
        return await axios.get(`/admin/tickets/${id}`).then(res => {
            commit('setTicket', res.data)
        })
    },

    async deleteTicket({ commit }, id) {
        return await axios.delete(`/admin/tickets/${id}`).then(res => {
            commit('deleteTicket', res.data)
        })
    },


    async getCounters({ commit }) {
        return await axios.get('/counters').then(res => {
            commit('setCounters', res.data)
        })
    },

    async addSolutionComment({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/solution`, data).then(res => {
            return res.data
        })
    },

    async addCloseComment({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/close`, data).then(res => {
            return res.data
        })
    },

    async addReopenComment({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/reopen`, data).then(res => {
            return res.data
        })
    },

    async addApproveComment({ commit }, data) {
        return await axios.post(`/user/tickets/${data.ticket_id}/approve`, data).then(res => {
            return res.data
        })
    },

    async addDeclineComment({ commit }, data) {
        return await axios.post(`/user/tickets/${data.ticket_id}/decline`, data).then(res => {
            return res.data
        })
    },

    async getThread({ commit }, id) {
        return await axios.get(`/user/tickets/${id}/thread`).then(res => {
            commit('setThread', res.data)
        })
    },

    async addComment({ commit }, data) {
        return await axios.post(`/user/tickets/${data.ticket_id}/comment`, data).then(res => {
            return res.data
        })
    },

    async addParticipant({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/participants`, data).then(res => {
            commit('setAssignees', res.data)
        })
    },

    async removeParticipant({ commit }, data) {
        return await axios.put(`/admin/tickets/${data.ticket_id}/participants`, data).then(res => {
            commit('setAssignees', res.data)
        })
    }
}
