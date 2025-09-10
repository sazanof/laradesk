import 'simplebar-vue/dist/simplebar.min.css'
import 'floating-vue/dist/style.css'
import 'animate.css'

import '@vueform/multiselect/themes/default.scss'
import mitt from 'mitt'
import ResizeObserver from 'resize-observer-polyfill'
import 'bootstrap/dist/js/bootstrap.bundle.min'
import App from '../components/App.vue'
import { createApp } from 'vue'
import router from './router/router.js'
import store from './store/index.js'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { setupI18n, loadLocaleMessages, plural } from './i18n/i18n.js'

// Vuetify
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'


import {
    // Directives
    vTooltip,
    vClosePopper,
    // Components
    Dropdown,
    Tooltip,
    Menu
} from 'floating-vue'

import './bootstrap'
import '../css/app.scss'


window.ResizeObserver = ResizeObserver

const vuetify = createVuetify()

const address = import.meta.env.VITE_WS_ADDRESS

const emitter = mitt()
const wsUrl = `${address}/front`

const i18n = setupI18n({
    locale: 'ru',
    pluralizationRules: {
        ru: plural
    }
})
loadLocaleMessages(i18n, i18n.global.locale).then(() => {
    const app = createApp(App)
    app.config.globalProperties.emitter = emitter
    app.config.globalProperties.wsUrl = wsUrl
    app.use(router)
    app.use(store)
    app.use(i18n)
    app.component('VueDatePicker', VueDatePicker)
    app.directive('tooltip', vTooltip)
    app.directive('close-popper', vClosePopper)

    app.component('VDropdown', Dropdown)
    app.component('VTooltip', Tooltip)
    app.component('VMenu', Menu)
    app.use(vuetify)
    app.mount('#app')
})
