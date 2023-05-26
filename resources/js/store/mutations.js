export default {
    setAuthenticated(state, a) {
        state.authenticated = a
    },
    setUser(state, user) {
        state.user = user
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
    }
}
