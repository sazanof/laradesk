<template>
    <TicketTemplate
        v-if="ticket"
        :admin="true"
        :ticket="ticket" />
    <ErrorPage
        v-else-if="error"
        :title="$t('Error loading ticket')"
        :description="$t('You may not have access to this application')" />
</template>

<script>
import ErrorPage from '../chunks/ErrorPage.vue'
import TicketTemplate from '../chunks/TicketTemplate.vue'

export default {
    name: 'Ticket',
    components: {
        TicketTemplate,
        ErrorPage
    },
    data() {
        return {
            error: null
        }
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
            .then(() => this.error = null)
            .catch(e => {
                this.error = e.response.data.message
            })
    }
}
</script>
