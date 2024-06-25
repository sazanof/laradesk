<template>
    <SimpleBar class="similar">
        <SimilarTicketItem
            v-for="ticket in tickets.data"
            :key="ticket.id"
            :ticket="ticket" />
    </SimpleBar>
</template>

<script>
import SimpleBar from 'simplebar-vue'
import SimilarTicketItem from './SimilarTicketItem.vue'

export default {
    name: 'SimilarTickets',
    components: {
        SimpleBar,
        SimilarTicketItem
    },
    props: {
        tickets: {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>
.similar {
    padding: calc(var(--padding-box) / 2);
    width: 420px;
    max-height: 380px;
}
</style>
