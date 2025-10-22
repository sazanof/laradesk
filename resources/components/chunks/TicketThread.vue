<template>
    <div
        v-if="loading"
        class="loading">
        <Loading :size="32" />
    </div>
    <div
        v-else>
        <VSheet
            v-if="thread !== null && thread.length >0"
            class="text-subtitle-1 font-weight-bold my-4 d-flex">
            {{ $t('Ticket thread') }}
            <VSpacer />
            <VBtn
                color="purple"
                :icon="compact ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                size="small"
                variant="tonal"
                density="comfortable"
                rounded="pill"
                @click="compact = !compact" />
        </VSheet>
        <VDivider class="mb-4" />
        <VSheet v-if="!compact">
            <TicketThreadItem
                v-for="comment in thread"
                :key="comment.id"
                :comment="comment" />
        </VSheet>
        <VBtn
            v-else
            prepend-icon="mdi-chevron-down"
            :text="$t('Show {count} comments',{count:thread.length})"
            @click="compact = false" />
    </div>
</template>

<script>
import Loading from '../elements/Loading.vue'
import TicketThreadItem from './TicketThreadItem.vue'
import loading from '../elements/Loading.vue'

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
            loading: false,
            compact: false
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

    .ticket-thread-title {
        margin-top: 12px;
        font-weight: bold;
        font-size: 18px;
    }
}
</style>
