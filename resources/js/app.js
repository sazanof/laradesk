import '../css/app.scss'
import './bootstrap'
import mitt from 'mitt'
import 'bootstrap/dist/js/bootstrap.bundle.min'
import App from '../components/App.vue'
import { createApp } from 'vue'
import router from './router/router.js'
import store from './store/index.js'
import { setupI18n, loadLocaleMessages, plural } from './i18n/i18n.js'

const emitter = mitt()

const i18n = setupI18n({
    locale: 'ru',
    pluralizationRules: {
        ru: plural
    }
})
loadLocaleMessages(i18n, i18n.global.locale).then(() => {
    const app = createApp(App)
    app.config.globalProperties.emitter = emitter
    app.use(router)
    app.use(store)
    app.use(i18n)
    app.mount('#app')

    console.log('App init')
})
