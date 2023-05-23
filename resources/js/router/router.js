import { createRouter, createWebHashHistory } from 'vue-router'
import App from '../../components/App.vue'
import Tickets from '../../components/pages/Tickets.vue'

const routes = [
    {
        path: '/admin/tickets',
        children: [
            {
                path: '',
                component: Tickets,
                props: {
                    criteria: 'all'
                }
            },
            {
                path: 'open',
                component: Tickets,
                props: {
                    criteria: 'open'
                }
            }, {
                path: 'my',
                component: Tickets,
                props: {
                    criteria: 'my'
                }
            }, {
                path: 'approval',
                component: Tickets,
                props: {
                    criteria: 'approval'
                }
            }, {
                path: 'closed',
                component: Tickets,
                props: {
                    criteria: 'closed'
                }
            }
        ]
    }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes // short for `routes: routes`
})

export default router
