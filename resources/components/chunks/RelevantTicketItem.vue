<script>
import { formatDate } from '../../js/helpers/moment.js'
import { statusClass } from '../../js/helpers/ticketStatus.js'

export default {
    name: 'RelevantTicketItem',
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
        <span
            class="badge"
            :class="`status_${statusClass(ticket.status)}`">{{ statusText }}</span>
        <div class="badge bg-info ms-2">
            {{ $t('Similarity {per}', {per: ticket.relev.toFixed(2)}) }}%
        </div>
        <div class="title">
            {{ ticket.subject }}
        </div>
        <div class="create">
            {{ formatDate(ticket.created_at) }}
        </div>
    </div>
</template>

<style scoped lang="scss">
.progress {
    height: 4px;
}
</style>
