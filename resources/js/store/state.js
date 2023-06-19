export default {
    appName: null,
    appBg: null,
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
    counters: {
        approval: null,
        new: null,
        my: null
    }
}
