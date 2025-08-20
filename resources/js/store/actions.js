import axios from 'axios'
import { PARTICIPANT } from '../consts.js'

axios.interceptors.response.use(function (response) {
    // Optional: Do something with response data
    return response
}, function (error) {
    if (error.response.status === 419 || error.response.status === 401) {
        window.location.reload()
    }
    return Promise.reject(error)
})

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
        state.appLogo = document.getElementById('appLogo').value
        state.config.maxFileSize = parseInt(document.getElementById('maxFileSize').value)
        const mimes = document.getElementById('allowedMimes').value
        if (mimes) {
            state.config.allowedMimes = mimes.split(',')
        }
    },
    async getUser({ commit }) {
        return await axios.get('/user').then(res => {
            if (res.data === '') {
                commit('setAuthenticated', false)
                commit('setUser', null)
                commit('setUserDepartments', null)
            } else {
                commit('setAuthenticated', true)
                commit('setUser', res.data)
                commit('setUserDepartments', res.data.departments)
            }
        })
    },

    async logIn({ commit }, data) {
        await axios.post('/login', data).then(res => {
            if (typeof res.data === 'object' && res.data.id) {
                commit('setUser', res.data)
                commit('setAuthenticated', true)
                commit('setUserDepartments', res.data.departments)
            } else {
                commit('setUser', null)
                commit('setAuthenticated', false)
                commit('setUserDepartments', null)
            }
        })
    },

    async getCategories({ commit }, departmentId = null) {
        const url = departmentId !== null ? `${MANAGEMENT_URL}/${departmentId}` : `${MANAGEMENT_URL}`
        return await axios.get(url).then(res => {
            commit('setCategories', res.data)
        })
    },

    async getAdministrators({ _ }) {
        return await axios.get(`${MANAGEMENT_URL}/administrators`).then(res => {
            return res.data
        })
    },

    async addAccess({ _ }, data) {
        return await axios.post(`${MANAGEMENT_URL}/administrators/access`, data).then(res => {
            return res.data
        })
    },

    async deleteAccess({ _ }, data) {
        return await axios.put(`${MANAGEMENT_URL}/administrators/access`, data).then(res => {
            return res.data
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

    async createOffice({ commit }, data) {
        return await axios.post(`${MANAGEMENT_URL}/offices`, data).then(res => {
            commit('createOffice', res.data)
        })
    },

    async editOffice({ commit }, data) {
        return await axios.put(`${MANAGEMENT_URL}/offices/${data.id}`, data).then(res => {
            commit('editOffice', res.data)
        })
    },

    async deleteOffice({ commit }, id) {
        return await axios.delete(`${MANAGEMENT_URL}/offices/${id}`).then(() => {
            commit('deleteOffice', id)
        })
    },

    async getStatistics({ commit }, params) {
        return await axios.post('/admin/statistics', params).then(res => {
            return res.data
        })
    },

    async getOffices({ commit }) {
        return await axios.get('/offices').then(res => {
            commit('setOffices', res.data)
        })
    },

    /** ROOMS */
    async getOfficeRooms({ commit }) {
        return await axios.get(`/offices/${id}/rooms`).then(res => {
            commit('setRooms', res.data)
        })
    },

    async editProfile({ commit }, data) {
        const res = await axios.put('/profile', data).then(res => {
            commit('setUser', res.data)
        })
        if (res) {
            return res.data
        }
    },

    async updateNotificationsSettings({ commit }, data) {
        return await axios.post('/profile/notifications', data).then(res => {
            commit('setSystemNotifications', res.data)
        })
    },
    async requestUserInfoUpdates({ _ }, data) {
        return await axios.post('/profile/updates', data).then(res => {
            return res.data
        })
    },
    async getNotificationsSettings({ commit }, data) {
        return await axios.get('/profile/notifications').then(res => {
            commit('setSystemNotifications', res.data)
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

    async getDepartments({ commit }) {
        return await axios.get('/departments').then(res => {
            commit('setDepartments', res.data)
        })
    },

    async getTicketCategories({ commit }, departmentId) {
        return await axios.get(`${USER_TICKETS_URL}/categories/${departmentId}`).then(res => {
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

    async uploadImageInEditor({ _ }, file) {
        return await axios.post(`${USER_TICKETS_URL}/upload-image`, { image: file }, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data.url
        })
    },

    async searchUsers({ _ }, { term, department }) {
        return await axios.post(`${USER_TICKETS_URL}/search/users`, { term, department }).then(res => {
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
            return res.data
        })
    },

    async getTicket({ commit }, id) {
        return await axios.get(`/admin/tickets/${id}`).then(res => {
            commit('setTicket', res.data)
        })
    },

    async getRelevantTickets({ commit }, data) {
        const res = await axios.post(`/admin/tickets/${data.id}/relevant`, data)
        if (res) {
            return res.data
        }
    },

    async deleteTicket({ commit }, id) {
        return await axios.delete(`/admin/tickets/${id}`).then(res => {
            commit('deleteTicket', res.data)
        })
    },

    async getUserDashboard({ commit }) {
        return await axios.get('/user/dashboard').then(res => {
            commit('setUserDashboardData', res.data)
        })
    },

    async getUserLastNotifications({ commit }) {
        return await axios.get('/user/notifications/last').then(res => {
            commit('setUserNotifications', res.data)
        })
    },

    async readUserNotification({ commit }, id) {
        return await axios.put(`/user/notifications/${id}`).then(res => {
            commit('readUserNotification', id)
        })
    },

    async deleteUserNotification({ commit }, id) {
        return await axios.delete(`/user/notifications/${id}`).then(res => {
            commit('deleteUserNotification', id)
        })
    },
    async deleteUserNotifications({ state }) {
        return await axios.delete('/user/notifications').then(res => {
            state.userNotifications = []
        })
    },

    async getAdminDashboard({ commit }) {
        return await axios.get('/admin/dashboard').then(res => {
            commit('setAdminDashboardData', res.data)
        })
    },

    async changeDepartment({ commit }, id) {
        return await axios.post(`/admin/department/${id}`, id).then(res => {
            commit('setActiveDepartment', res.data)
        })
    },

    async getCounters({ commit, state }) {
        const url = '/counters'
        return await axios.get(url).then(res => {
            commit('setCounters', res.data)
        })
    },

    async addSolutionComment({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/solution`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },

    async addCloseComment({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/close`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },

    async addReopenComment({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/reopen`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },

    async addApproveComment({ commit }, data) {
        return await axios.post(`${USER_TICKETS_URL}/${data.ticket_id}/approve`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },

    async addDeclineComment({ commit }, data) {
        return await axios.post(`${USER_TICKETS_URL}/${data.ticket_id}/decline`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },

    async addComment({ commit }, data) {
        return await axios.post(`${USER_TICKETS_URL}/${data.ticket_id}/comment`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            return res.data
        })
    },

    async getThread({ commit }, id) {
        return await axios.get(`${USER_TICKETS_URL}/${id}/thread`).then(res => {
            commit('setThread', res.data)
        })
    },

    async addParticipant({ commit }, data) {
        return await axios.post(`/admin/tickets/${data.ticket_id}/participants`, data).then(res => {
            if (data.type === PARTICIPANT.ASSIGNEE) {
                commit('setAssignees', res.data)
            }
        })
    },

    async removeParticipant({ commit }, data) {
        return await axios.put(`/admin/tickets/${data.ticket_id}/participants`, data).then(res => {
            if (data.type === PARTICIPANT.ASSIGNEE) {
                commit('setAssignees', res.data)
            }
        })
    },

    async removeParticipantFromTicketOwner({ commit }, data) {
        return await axios.put(`${USER_TICKETS_URL}/${data.ticket_id}/participants`, data).then(res => {
            if (data.type === PARTICIPANT.ASSIGNEE) {
                commit('setAssignees', res.data)
            }
        })
    },

    async exportExcel({ state }, data) {
        const merged = Object.assign({
            user_id: state.user.id,
            conn_id: state.ws.id
        }, data)
        return await axios.post(`${USER_TICKETS_URL}/export/excel`, merged).then(res => {
            return res.data
        })
    },

    async getSimilarTickets({ _ }, { subject, userId }) {
        return await axios.post(`${USER_TICKETS_URL}/similar`, { subject, userId }).then(res => {
            return res.data
        })
    },

    async exportPdf({ _ }) {
        return await axios.get(`${USER_TICKETS_URL}/export/pdf`).then(res => {
            return res.data
        })
    },

    async addParticipantFromTicketOwner({ commit }, data) {
        return await axios.post(`${USER_TICKETS_URL}/${data.ticket_id}/participants`, data).then(res => {
            commit('setAssignees', res.data)
        })
    },

    /** SETTINGS **/

    async saveSettings({ commit }, data) {
        return await axios.post(`${MANAGEMENT_URL}/settings`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },

    /** NEWS **/

    async getNews({ commit }, page) {
        const res = await axios.get(`${MANAGEMENT_URL}/news/${page}`)
        return res.data
    },

    async addNew({ commit }, data) {
        commit('setLoading', true)
        const res = await axios.post(`${MANAGEMENT_URL}/news`, data).finally(() => {
            commit('setLoading', false)
        })
        return res.data
    },

    async updateNew({ commit }, data) {
        commit('setLoading', true)
        await axios.put(`${MANAGEMENT_URL}/news/${data.id}`, data).finally(() => {
            commit('setLoading', false)
        })
    },

    async deleteNew({ commit }, id) {
        commit('setLoading', true)
        await axios.delete(`${MANAGEMENT_URL}/news/${id}`).finally(() => {
            commit('setLoading', false)
        })
    },

    async publishNew({ commit }, id) {
        commit('setLoading', true)
        await axios.put(`${MANAGEMENT_URL}/news/${id}/publish`).finally(() => {
            commit('setLoading', false)
        })
    },

    async unPublishNew({ commit }, id) {
        commit('setLoading', true)
        await axios.put(`${MANAGEMENT_URL}/news/${id}/unpublish`).finally(() => {
            commit('setLoading', false)
        })
    },

    async getUserNews({ commit }) {
        const res = await axios.get('/news')
        if (res.data) {
            commit('setNews', res.data)
        }
    },

    async markNewAsRead({ commit }, id) {
        const res = await axios.put(`/news/${id}/read`)
        if (res) {
            commit('deleteNewsItemCaseMarkAsRead', id)
        }
    },

    /** DEPARTMENTS **/

    async addDepartment({ commit }, data) {
        const res = await axios.post(`${MANAGEMENT_URL}/department`, data)
        commit('addDepartment', Object.assign({ deleted_at: null }, res.data))
    },

    async updateDepartment({ _ }, data) {
        await axios.put(`${MANAGEMENT_URL}/department/${data.id}`, data)
    },

    async addMember({ _ }, { departmentId, memberId }) {
        return await axios.post(`${MANAGEMENT_URL}/department/${departmentId}/members`, {
            departmentId,
            memberId
        }).then(res => {
            return res.data
        })
    },

    async deleteMember({ _ }, { departmentId, memberId }) {
        await axios.delete(`${MANAGEMENT_URL}/department/${departmentId}/members/${memberId}`)
    },

    async enableDepartment({ _ }, id) {
        await axios.put(`${MANAGEMENT_URL}/department/${id}/on`)
    },

    async disableDepartment({ _ }, id) {
        await axios.put(`${MANAGEMENT_URL}/department/${id}/off`)
    },

    async getDepartmentMembers({ commit }, id) {
        return await axios.get(`${MANAGEMENT_URL}/department/${id}/members`)
    },

    /** FILES */
    async onUploadCsv({ commit }, data) {
        const res = await axios.post(`${MANAGEMENT_URL}/rooms/csv`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        if (res) {
            return res.data
        }
    },
    async uploadCsvRoomData({ _ }, { officeId, clearPrevious, data }) {
        await axios.post(`${MANAGEMENT_URL}/rooms/csv/start`, { officeId, clearPrevious, data })
    },
    /**
     * AUTOCOMPLETES
     */
    async addAutocompleteFieldValue({ _ }, { field_id, value }) {
        return await axios.post(`${USER_TICKETS_URL}/fields/autocomplete`, { field_id, value }).then(res => res.data)
    },

    async getAutocompleteFieldValues({ _ }, { field_id, term }) {
        return await axios.post(`${USER_TICKETS_URL}/fields/autocomplete/${field_id}`, { term: term }).then(res => res.data)
    },

    async removeAutocompleteFieldValue({ _ }, id) {
        await axios.delete(`${USER_TICKETS_URL}/fields/autocomplete/${id}`)
    },

    async getSurmWorkplacesByUsernameAndRoom({ _ }) {
        const res = await axios.get('/surm/workplaces')
        if (res) {
            return res.data
        }
    }
}
