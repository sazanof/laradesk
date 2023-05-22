import axios from 'axios'

export default {
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
    }
}
