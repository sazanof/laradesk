import 'simplebar' // or "import SimpleBar from 'simplebar';" if you want to use it manually.
import '../css/simplebar.css'
import 'animate.css'

import 'vue-toastification/dist/index.css'
import '@vueform/multiselect/themes/default.scss'
import mitt from 'mitt'
import ResizeObserver from 'resize-observer-polyfill'
import Toast from 'vue-toastification'
import 'bootstrap/dist/js/bootstrap.bundle.min'
import App from '../components/App.vue'
import { createApp } from 'vue'
import router from './router/router.js'
import store from './store/index.js'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { setupI18n, loadLocaleMessages, plural } from './i18n/i18n.js'

import './bootstrap'
import '../css/app.scss'

window.ResizeObserver = ResizeObserver

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
    app.use(Toast, {
        shareAppContext: true
    })
    app.mount('#app')

    console.log('App init')
})
