import '../css/app.scss'
import './bootstrap'
import App from '../components/App.vue'
import { createApp } from 'vue'
import router from './router/router.js'
import store from './store/index.js'
import { setupI18n, plural } from './i18n/i18n.js'

const i18n = setupI18n({
    locale: 'ru',
    pluralizationRules: {
        ru: plural
    }
})

const app = createApp(App)
app.use(router)
app.use(store)
app.use(i18n)
app.mount('#app')

console.log('App init')
