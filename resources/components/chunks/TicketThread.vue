<template>
    <div
        v-if="loading"
        class="loading">
        <Loading :size="32" />
    </div>
    <div
        v-else
        class="thread">
        <TicketThreadItem
            v-for="comment in thread"
            :key="comment.id"
            :comment="comment" />
    </div>
</template>

<script>
import Loading from '../ elements/Loading.vue'
import TicketThreadItem from './TicketThreadItem.vue'
import loading from '../ elements/Loading.vue'

export default {
    name: 'TicketThread',
    components: {
        TicketThreadItem,
        Loading
    },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            loading: false
        }
    },
    computed: {
        id() {
            return parseInt(this.$route.params.number)
        },
        thread() {
            return this.$store.getters['getThread']
        }
    },
    async created() {
        this.loading = true
        await this.$store.dispatch('getThread', this.id).finally(() => {
            this.loading = false
        })
    }
}
</script>

<style lang="scss" scoped>
.loading {
    text-align: center;
    margin-top: 6px;
}

.thread {
    display: flex;
    flex-direction: column;
}
</style>
