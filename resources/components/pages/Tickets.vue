<template>
    <div
        v-if="activeDepartment"
        class="tickets">
        <ContentLoading v-if="loading" />
        <TicketsFilter
            :loading="loading"
            :title="pageTitle"
            :filter="filter"
            @export-click="exportExcel($event)"
            @apply-filter="addCriteria($event)" />
        <SimpleBar
            v-if="tickets !== null"
            class="tickets-data">
            <table
                class="table table-striped table-hover">
                <TicketsHeader
                    :filter="filter"
                    @on-row-click="triggerFilter" />
                <tbody>
                    <TicketListItem
                        v-for="ticket in tickets.data"
                        :key="ticket"
                        :link="`/admin/tickets/${ticket.id}`"
                        :show-info="false"
                        :ticket="ticket" />
                </tbody>
            </table>
        </SimpleBar>
        <Pagination
            v-if="tickets"
            :data="tickets"
            @pagination-change-page="switchPage" />
    </div>
    <div
        v-else
        class="tickets">
        <div class="alert alert-warning">
            {{ $t('You are not affiliated with any division. Contact your system administrator or try again later') }}
        </div>
    </div>
</template>

<script>
import SimpleBar from 'simplebar-vue'
import TicketsHeader from '../chunks/TicketsHeader.vue'
import TicketsFilter from '../chunks/TicketsFilter.vue'
import ContentLoading from '../elements/ContentLoading.vue'
import TicketListItem from '../chunks/TicketListItem.vue'
import Pagination from '../chunks/Pagination.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

export default {
    name: 'Tickets',
    components: {
        ContentLoading,
        Pagination,
        TicketListItem,
        TicketsFilter,
        TicketsHeader,
        SimpleBar
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
        criteria() {
            this.filter.criteria = this.criteria
            this.getTickets()
        },
        async activeDepartment() {
            this.filter.department = this.activeDepartment.id
            await this.getTickets()
        }
    },
    async mounted() {
        if (this.isAdmin && this.activeDepartment !== null) {
            this.filter.department = this.activeDepartment.id
        }

    },

    methods: {
        async addCriteria(query) {
            this.filter = Object.assign(this.filter, query)
            await this.getTickets()
        },
        async getTickets() {
            this.loading = true
            await this.$store.dispatch('getTickets', this.filter).catch(e => {
                toast.error(this.$t(e.response.data.message))
            }).finally(() => {
                this.loading = false
            })

        },
        switchPage(page) {
            this.filter.page = page
            this.getTickets()
        },
        triggerFilter(data) {
            this.filter.field = data.field
            this.filter.dir = data.dir
            this.getTickets()
        },
        exportExcel(query) {
            this.$store.dispatch('exportExcel', Object.assign({ criteria: this.criteria }, query)).then(() => {
                toast.info(this.$t('Export has started. You will receive an email to your email about its readiness'))
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.tickets {
    position: relative;

    .alert {
        margin: 20px;
    }

    .tickets-data {
        height: calc(100vh - var(--header-height) - var(--pagination-height) * 2);
    }
}
</style>
