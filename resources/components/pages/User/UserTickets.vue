<template>
    <div class="tickets sent-tickets">
        <ContentLoading v-if="loading" />
        <TicketsFilter
            :loading="loading"
            :title="pageTitle"
            :filter="filter"
            @export-click="exportExcel($event)"
            @apply-filter="addCriteria($event)" />
        <div
            v-if="tickets"
            class="tickets-list">
            <table class="table table-striped table-responsive table-hover">
                <TicketsHeader
                    :filter="filter"
                    @on-row-click="triggerFilter" />
                <tbody>
                    <TicketListItem
                        v-for="ticket in tickets.data"
                        :key="ticket.id"
                        :link="`/user/tickets/${ticket.id}`"
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
import TicketsFilter from '../../chunks/TicketsFilter.vue'
import TicketsHeader from '../../chunks/TicketsHeader.vue'
import ContentLoading from '../../elements/ContentLoading.vue'
import Pagination from '../../chunks/Pagination.vue'
import TicketListItem from '../../chunks/TicketListItem.vue'

export default {
    name: 'UserTickets',
    components: {
        TicketsFilter,
        ContentLoading,
        TicketListItem,
        Pagination,
        TicketsHeader
    },
    props: {
        criteria: {
            type: String,
            default: 'sent'
        }
    },
    data() {
        return {
            loading: false,
            filter: {
                criteria: this.criteria,
                field: 'created_at',
                direction: 'desc',
                limit: 25,
                page: 1
            }
        }
    },
    computed: {
        tickets() {
            return this.$store.getters['getUserTickets']
        },
        pageTitle() {
            switch (this.criteria) {
                case 'sent' :
                    return this.$t('Sent tickets')
                case 'approval':
                    return this.$t('Approval tickets')
                default :
                    return this.$t('I am observer')
            }
        }
    },
    watch: {
        async criteria() {
            this.filter.criteria = this.criteria
            await this.getTickets()
        }
    },

    methods: {
        switchPage(page) {
            this.filter.page = page
            this.getTickets()
        },
        async getTickets() {
            this.loading = true
            await this.$store.dispatch('getUserTickets', this.filter).catch(e => {
                alert(e.response.data.message)
            }).finally(() => {
                this.loading = false
            })
        },
        triggerFilter(data) {
            this.filter.field = data.field
            this.filter.dir = data.dir
            this.getTickets()
        },
        async addCriteria(query) {
            this.filter = Object.assign(this.filter, query)
            await this.getTickets()
        },
        exportExcel(query) {
            this.$store.dispatch('exportExcel', {
                criteria: this.criteria,
                query
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.tickets-list {
    height: calc(100vh - var(--header-height) - var(--pagination-height) * 2);
}

.table {
    border-radius: var(--border-radius);
}
</style>
