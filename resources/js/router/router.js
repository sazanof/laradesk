import { createRouter, createWebHashHistory } from 'vue-router'
import Tickets from '../../components/pages/Tickets.vue'
import Administration from '../../components/pages/Adminitration/Administration.vue'
import AdmCategories from '../../components/pages/Adminitration/AdmCategories.vue'
import AdmFields from '../../components/pages/Adminitration/AdmFields.vue'
import FormManagement from '../../components/pages/Adminitration/FormManagement.vue'
import Profile from '../../components/pages/Profile.vue'
import CreateTicket from '../../components/pages/CreateTicket.vue'

const routes = [
    {
        path: '/profile',
        component: Profile
    },
    {
        path: '/tickets',
        children: [
            {
                path: 'create',
                component: CreateTicket
            }
        ]
    },
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
    }, {
        path: '/admin/management',
        component: Administration,
        children: [
            {
                path: '',
                name: 'categories',
                component: AdmCategories
            },
            {
                path: 'fields',
                name: 'fields',
                component: AdmFields
            },
            {
                path: 'categories/:id(\\d+)',
                component: FormManagement
            }
        ]
    }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes // short for `routes: routes`
})

export default router
