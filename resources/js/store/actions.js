import axios from 'axios'

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
    }
}
