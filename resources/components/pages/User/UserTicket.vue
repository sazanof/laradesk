<template>
    <TicketTemplate :ticket="ticket" />
</template>

<script>
import TicketTemplate from '../../chunks/TicketTemplate.vue'

export default {
    name: 'UserTicket',
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
        await this.$store.dispatch('getUserTicket', this.id)
    }
}
</script>
