import axios from 'axios'

const MANAGEMENT_URL = '/admin/management'

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
    }
}
