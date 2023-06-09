<template>
    <div class="tickets-page sent-tickets">
        <ContentLoading v-if="loading" />
        <h3>{{ $t('Sent tickets') }}</h3>
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
                        :ticket="ticket" />
                </tbody>
            </table>
        </div>
        <Pagination
            :data="tickets"
            @pagination-change-page="switchPage" />
    </div>
</template>

<script>
import TicketsHeader from '../../chunks/TicketsHeader.vue'
import ContentLoading from '../../ elements/ContentLoading.vue'
import Pagination from '../../chunks/Pagination.vue'
import TicketListItem from '../../chunks/TicketListItem.vue'

export default {
    name: 'UserTickets',
    components: {
        ContentLoading,
        TicketListItem,
        Pagination,
        TicketsHeader
    },
    data() {
        return {
            loading: false,
            filter: {
                field: 'created_at',
                direction: 'desc',
                limit: 25,
                page: 1
            }
        }
    },
    computed: {
        tickets() {
            return this.$store.getters['getSentTickets']
        }
    },
    async created() {
        await this.getTickets()
    },
    methods: {
        switchPage(page) {
            this.filter.page = page
            this.getTickets()
        },
        async getTickets() {
            this.loading = true
            await this.$store.dispatch('getSentTickets', this.filter).catch(e => {
                alert(e.response.data.message)
            }).finally(() => {
                this.loading = false
            })
        },
        triggerFilter(data) {
            this.filter.field = data.field
            this.filter.dir = data.dir
            this.getTickets()
        }
    }
}
</script>

<style lang="scss" scoped>
.table {
    border-radius: var(--border-radius);
}
</style>
