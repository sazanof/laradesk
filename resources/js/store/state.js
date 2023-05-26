export default {
    appName: null,
    appBg: null,
    authenticated: false,
    collapsed: localStorage.getItem('collapsed') === null ? 'false' : localStorage.getItem('collapsed'),
    user: null,
    categories: [],
    fields: [],
    offices: null
}
