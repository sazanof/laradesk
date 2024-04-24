export default {
    appName: null,
    appBg: null,
    appLogo: null,
    appWidth: window.screen.width,
    config: {
        allowedMimes: [],
        maxFileSize: null
    },
    additionalCriteria: null,
    isMobile: false,
    authenticated: false,
    collapsed: localStorage.getItem('collapsed') === null ? 'false' : localStorage.getItem('collapsed'),
    user: null,
    userDepartments: null,
    activeDepartment: localStorage.getItem('activeDepartment') === null
        ? null
        : JSON.parse(localStorage.getItem('activeDepartment')),
    departments: [],
    categories: [],
    fields: [],
    offices: [],
    rooms: [],
    tickets: null,
    ticket: null,
    thread: null,
    userTickets: null,
    userNotifications: [],
    systemNotifications: null,
    showNotifications: false,
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
        notifications: [],
        id: null
    }
}
