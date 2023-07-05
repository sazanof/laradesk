<template>
    <div class="tickets-page sent-tickets">
        <ContentLoading v-if="loading" />
        <h3>{{ criteria === 'sent' ? $t('Sent tickets') : $t('Approval tickets') }}</h3>
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
import TicketsHeader from '../../chunks/TicketsHeader.vue'
import ContentLoading from '../../elements/ContentLoading.vue'
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
        }
    },
    watch: {
        async criteria() {
            this.filter.criteria = this.criteria
            await this.getTickets()
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
        }
    }
}
</script>

<style lang="scss" scoped>
.table {
    border-radius: var(--border-radius);
}
</style>
