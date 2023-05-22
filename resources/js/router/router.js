import { createRouter, createWebHashHistory } from 'vue-router'

const routes = [/*
    { path: '/', component: Home },
    { path: '/about', component: About }*/
]

const router = createRouter({
    history: createWebHashHistory(),
    routes // short for `routes: routes`
})

export default router
