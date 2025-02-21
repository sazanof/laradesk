<script>
import { formatDate } from '../../js/helpers/moment.js'
import { statusClass } from '../../js/helpers/ticketStatus.js'
import UserInTicketList from './UserInTicketList.vue'

export default {
    name: 'RelevantTicketItem',
    components: { UserInTicketList },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    }, computed: {
        statusText() {
            return this.$t(`status_${statusClass(this.ticket.status)}`)
        }
    },
    methods: {
        formatDate,
        statusClass
    }
}
</script>

<template>
    <div
        class="relevant mb-2">
        <div class="left">
            <div class="title">
                <router-link :to="{name:'admin.ticket', params:{number: ticket.id}}">
                    {{ ticket.subject }}
                </router-link>
            </div>
            <span
                class="badge"
                :class="`status_${statusClass(ticket.status)}`">{{ statusText }}</span>
            <div class="badge bg-info ms-2">
                {{ $t('Similarity {per}', {per: ticket.relev.toFixed(2)}) }}%
            </div>
        </div>

        <div>
            <UserInTicketList
                :show-info="false"
                :user="ticket.requester" />
            <small class="create">
                {{ formatDate(ticket.created_at) }}
            </small>
        </div>
    </div>
</template>

<style scoped lang="scss">
.relevant {
    padding: calc(var(--padding-box) / 2);
    background: var(--bs-light);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: space-between;

    .title a {
        font-weight: bold;
        text-decoration: none;
        color: var(--bs-purple)
    }
}
</style>
