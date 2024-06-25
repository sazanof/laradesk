<template>
    <TicketTemplate
        v-if="!denied && ticket"
        :ticket="ticket" />
    <div
        v-else
        class="alert alert-warning">
        {{ $t('Ticket not found, or you haven\'t access to it') }}
    </div>
</template>

<script>
import TicketTemplate from '../../chunks/TicketTemplate.vue'

export default {
    name: 'UserTicket',
    components: {
        TicketTemplate
    },
    data() {
        return {
            denied: false
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
    watch: {
        id() {
            this.getTicket()
        }
    },
    async created() {
        await this.getTicket()
    },
    methods: {
        async getTicket() {
            await this.$store.dispatch('getUserTicket', this.id).then(() => {
                this.denied = false
            }).catch(e => {
                this.denied = true
            })
        }
    }
}
</script>
<style lang="scss" scoped>
.alert {
    margin: 20px;
}
</style>
