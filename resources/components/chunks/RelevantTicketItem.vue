<script>
import { formatDate } from '../../js/helpers/moment.js'
import { statusClass, statusColor } from '../../js/helpers/ticketStatus.js'
import Avatar from './Avatar.vue'

export default {
    name: 'RelevantTicketItem',
    components: {
        Avatar
    },
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
        statusColor,
        formatDate
    }
}
</script>

<template>
    <VCard
        class="mb-4">
        <VCardTitle>
            <VChip
                rounded="pill"
                class="mr-2 ps-0"
                size="small"
                density="comfortable"
                variant="tonal"
                :text="ticket.requester?.full_name">
                <template #prepend>
                    <Avatar
                        :size="22"
                        class="mr-2"
                        :user="ticket.requester" />
                </template>
            </VChip>
            <VChip
                prepend-icon="mdi-clock"
                rounded="pill"
                class="mr-2"
                size="small"
                density="comfortable"
                variant="tonal"
                :text="formatDate(ticket.created_at)" />
            <VChip
                rounded="pill"
                class="badge"
                size="small"
                density="comfortable"
                variant="tonal"
                :color="statusColor(ticket.status)"
                :text="statusText" />
            <VBtn
                rounded="pill"
                size="small"
                density="comfortable"
                class="position-absolute right-0 mr-2"
                :to="{name:'admin.ticket', params:{number: ticket.id}}"
                icon="mdi-chevron-right" />
            <VSheet>
                {{ ticket.subject }}
            </VSheet>
        </VCardTitle>
        <VCardSubtitle class="mb-2">
            <VContainer class="pa-0">
                <VRow class="align-center">
                    <VCol
                        cols="12"
                        sm="6">
                        {{ $t('Similarity {per}', {per: ticket.relev.toFixed(2)}) }}%
                    </VCol>
                    <VCol
                        cols="12"
                        sm="6">
                        <VProgressLinear
                            style="max-width: 300px;"
                            height="4"
                            color="primary"
                            max="100"
                            min="0"
                            :model-value="ticket.relev.toFixed(2)" />
                    </VCol>
                </VRow>
            </VContainer>
        </VCardSubtitle>
    </VCard>
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
