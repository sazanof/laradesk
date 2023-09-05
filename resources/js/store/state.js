export default {
    appName: null,
    appBg: null,
    appLogo: null,
    authenticated: false,
    collapsed: localStorage.getItem('collapsed') === null ? 'false' : localStorage.getItem('collapsed'),
    user: null,
    categories: [],
    fields: [],
    offices: null,
    tickets: null,
    ticket: null,
    thread: null,
    userTickets: null,
    notifications: null,
    counters: {
        approval: null,
        new: null,
        my: null
    },
    dashboard: {
        user: null,
        admin: null
    },
    ws: {
        connected: false,
        connecting: false,
        id: null,
        notifications: []
    }
}
