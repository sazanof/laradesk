<template>
    <VSheet
        v-if="activeDepartment"
        class="pa-4 pb-0 fill-height">
        <TicketsFilter
            :admin="true"
            :loading="loading"
            :title="pageTitle"
            :filter="filter"
            color="grey-lighten-3"
            class="mx-n4 mt-n4 position-sticky"
            style="top:0 !important;z-index:100"
            @export-click="exportExcel($event)"
            @apply-filter="addCriteria($event)" />
        <ContentLoading v-if="loading" />
        <VSheet
            v-if="tickets !== null"
            class="mx-n4">
            <VTable
                ref="table"
                fixed-header
                fixed-footer
                hover>
                <TicketsHeader
                    :filter="filter"
                    @on-row-click="triggerFilter" />
                <tbody>
                    <TicketListItem
                        v-for="ticket in tickets.data"
                        :key="ticket"
                        :link="`/admin/tickets/${ticket.id}`"
                        :ticket="ticket" />
                </tbody>
                <template #bottom>
                    <Pagination
                        v-if="tickets"
                        :data="tickets"
                        @pagination-change-page="switchPage" />
                </template>
            </VTable>
        </VSheet>
    </VSheet>
    <div
        v-else
        class="tickets">
        <div class="alert alert-warning">
            {{ $t('You are not affiliated with any division. Contact your system administrator or try again later') }}
        </div>
    </div>
</template>

<script>
import TicketsHeader from '../chunks/TicketsHeader.vue'
import TicketsFilter from '../chunks/TicketsFilter.vue'
import ContentLoading from '../elements/ContentLoading.vue'
import TicketListItem from '../chunks/TicketListItem.vue'
import Pagination from '../chunks/Pagination.vue'
import { createErrorNotification, createSuccessNotification } from '../../js/helpers/notificationHelper.js'

export default {
    name: 'Tickets',
    components: {
        ContentLoading,
        Pagination,
        TicketListItem,
        TicketsFilter,
        TicketsHeader
    },
    props: {
        criteria: {
            type: String,
            default: 'all'
        }
    },
    data() {
        return {
            loading: false,
            filter: {
                criteria: this.criteria,
                field: 'created_at',
                dir: 'desc',
                page: 1,
                limit: 25
            }
        }
    },
    computed: {
        tickets() {
            return this.$store.getters['getTickets']
        },
        activeDepartment() {
            return this.$store.getters['getActiveDepartment']
        },
        isAdmin() {
            return this.$store.getters['isAdmin']
        },
        pageTitle() {
            return this.$t(`dashboard_${this.criteria}`)
        }
    },
    watch: {
        async criteria() {
            this.$store.commit('setAdditionalCriteria', null)
            this.filter.criteria = this.criteria
            //await this.getTickets()
        },
        async activeDepartment() {
            this.filter.department = this.activeDepartment.id
            //await this.getTickets()
        }
    },
    async mounted() {
        if (this.isAdmin && this.activeDepartment !== null) {
            this.filter.department = this.activeDepartment.id
        }

        this.emitter.on('on-notification-received', async notification => {
            if (notification.type === 'notification.ticket.new') {
                await this.getTickets()
                console.log('Refreshing tickets')
            }
        })
    },

    unmounted() {
        this.emitter.on('on-notification-received')
    },

    methods: {
        updateHeight() {
            this.$nextTick(() => {
                const height = window.innerHeight
                const offset = this.$refs.table.$el.offsetTop
                const newHeight = height - offset
                this.$refs.table.$el.style.height = `${newHeight}px`
            })
        },
        async addCriteria(query) {
            this.filter = Object.assign(this.filter, query)
            await this.getTickets()
        },
        async getTickets() {
            this.loading = true
            await this.$store.dispatch('getTickets', this.filter).catch(e => {
                this.$store.commit(
                    'addNotification',
                    createErrorNotification(this.$t(e.response.data.message))
                )
            }).finally(() => {
                this.loading = false
                this.updateHeight()
            })

        },
        async switchPage(page) {
            this.filter.page = page
            await this.getTickets()
        },
        async triggerFilter(data) {
            this.filter.field = data.field
            this.filter.dir = data.dir
            await this.getTickets()
        },
        exportExcel(query) {
            this.$store.dispatch('exportExcel', Object.assign({ criteria: this.criteria }, query)).then(() => {
                this.$store.commit(
                    'addNotification',
                    createSuccessNotification(this.$t('Export has started. You will receive an email to your email about its readiness'))
                )
            })
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
