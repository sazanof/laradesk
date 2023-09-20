<template>
    <TicketTemplate
        v-if="ticket"
        :admin="true"
        :ticket="ticket" />
</template>

<script>
import TicketTemplate from '../chunks/TicketTemplate.vue'

export default {
    name: 'Ticket',
    components: {
        TicketTemplate
    },
    computed: {
        id() {
            return parseInt(this.$route.params.number)
        },
        ticket() {
            return this.$store.getters['getTicket']
        }
    },
    async created() {
        await this.$store.dispatch('getTicket', this.id)
    }
}
</script>
