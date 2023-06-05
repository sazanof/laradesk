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
    }
}
