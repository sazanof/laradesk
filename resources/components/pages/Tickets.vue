<template>
    <div
        class="tickets">
        <ContentLoading v-if="loading" />
        <TicketsFilter
            :filter="filter"
            @export-click="exportExcel($event)"
            @apply-filter="addCriteria($event)" />
        <div
            v-if="tickets !== null"
            class="tickets-data"
            data-simplebar="">
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
        </div>
        <Pagination
            v-if="tickets"
            :data="tickets"
            @pagination-change-page="switchPage" />
    </div>
</template>

<script>
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
        }
    },
    watch: {
        criteria() {
            this.filter.criteria = this.criteria
            this.getTickets()
        }
    },
    created() {
        this.getTickets()
    },
    methods: {
        async addCriteria(query) {
            this.filter = Object.assign(this.filter, query)
            await this.getTickets()
        },
        async getTickets() {
            this.loading = true
            await this.$store.dispatch('getTickets', this.filter).catch(e => {
                alert(e.response.data.message)
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
                toast.info(this.$t('The export has started. You will receive an email to your email about its readiness'))
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.tickets-data {

}
</style>
