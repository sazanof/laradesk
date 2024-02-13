import { createRouter, createWebHashHistory } from 'vue-router'
import Tickets from '../../components/pages/Tickets.vue'
import Administration from '../../components/pages/Adminitration/Administration.vue'
import AdmCategories from '../../components/pages/Adminitration/AdmCategories.vue'
import AdmFields from '../../components/pages/Adminitration/AdmFields.vue'
import FormManagement from '../../components/pages/Adminitration/FormManagement.vue'
import Profile from '../../components/pages/Profile.vue'
import CreateTicket from '../../components/pages/CreateTicket.vue'
import UserTickets from '../../components/pages/User/UserTickets.vue'
import UserTicket from '../../components/pages/User/UserTicket.vue'
import Ticket from '../../components/pages/Ticket.vue'
import Dashboard from '../../components/pages/Dashboard.vue'
import AdmAccess from '../../components/pages/Adminitration/AdmAccess.vue'
import AdmSettings from '../../components/pages/Adminitration/AdmSettings.vue'

const routes = [
    {
        path: '/',
        component: Dashboard,
        name: 'index'
    },
    {
        path: '/profile',
        component: Profile
    },
    {
        path: '/user',
        children: [
            {
                path: 'tickets',
                children: [
                    {
                        path: 'sent',
                        component: UserTickets,
                        props: {
                            criteria: 'sent'
                        }
                    },
                    {
                        path: 'approval',
                        component: UserTickets,
                        props: {
                            criteria: 'approval'
                        }
                    },
                    {
                        path: ':number(\\d+)',
                        component: UserTicket
                    }
                ]
            }
        ]
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
            },
            {
                path: ':number(\\d+)',
                component: Ticket
            }
        ]
    },
    {
        path: '/admin/settings',
        component: AdmSettings
    },
    {
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
                path: 'access',
                name: 'access',
                component: AdmAccess
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
