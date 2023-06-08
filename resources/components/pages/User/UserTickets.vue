<template>
    <div class="tickets-page sent-tickets">
        <h3>{{ $t('Sent tickets') }}</h3>
        <div
            v-if="tickets"
            class="tickets-list">
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th scope="col">
                            {{ $t('Status') }}
                        </th>
                        <th scope="col">
                            {{ $t('Number') }}
                        </th>
                        <th scope="col">
                            {{ $t('Subject') }}
                        </th>
                        <th scope="col">
                            {{ $t('Category') }}
                        </th>
                        <th scope="col">
                            {{ $t('Requester') }}
                        </th>
                        <th scope="col">
                            {{ $t('Assignees') }}
                        </th>
                        <th scope="col">
                            {{ $t('Approvals') }}
                        </th>
                        <th scope="col">
                            {{ $t('Observers') }}
                        </th>
                        <th scope="col">
                            {{ $t('Created at') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <TicketListItem
                        v-for="ticket in tickets.data"
                        :key="ticket.id"
                        :ticket="ticket" />
                </tbody>
            </table>

            <Bootstrap5Pagination
                :data="tickets"
                @pagination-change-page="switchPage" />
        </div>
    </div>
</template>

<script>
import { Bootstrap5Pagination } from 'laravel-vue-pagination'
import TicketListItem from '../../chunks/TicketListItem.vue'

export default {
    name: 'UserTickets',
    components: {
        TicketListItem,
        Bootstrap5Pagination
    },
    data() {
        return {
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
            await this.$store.dispatch('getSentTickets', this.filter)
        }
    }
}
</script>

<style lang="scss" scoped>
.table {
    border-radius: var(--border-radius);
}
</style>
