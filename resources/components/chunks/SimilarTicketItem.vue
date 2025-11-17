<template>
    <VListItem
        class="similar-ticket"
        :to="{name:'user.ticket', params:{number:ticket.id}}">
        <template #prepend>
            <Avatar
                v-if="ticket.requester"
                class="mr-2"
                :user="ticket.requester"
                border-radius="4px"
                :size="40" />
        </template>
        <template #title>
            <div class="text-subtitle-2 font-weight-bold">
                <span class="number">{{ number }} </span>{{ ticket.subject }}
            </div>
        </template>
        <template #subtitle>
            <VChip
                rounded="pill"
                size="small"
                density="comfortable"
                :color="statusColor(ticket.status)">
                {{ statusText }}
            </VChip>
            <VChip
                rounded="pill"
                size="small"
                class="ml-2"
                density="comfortable">
                {{ createdAt }}
            </VChip>
        </template>
    </VListItem>
</template>

<script>
import { statusClass, statusColor } from '../../js/helpers/ticketStatus.js'
import { formatDate } from '../../js/helpers/moment.js'

import Avatar from './Avatar.vue'

export default {
    name: 'SimilarTicketItem',
    components: {
        Avatar
    },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    computed: {
        statusText() {
            return this.$t(`status_${statusClass(this.ticket.status)}`)
        },
        classStr() {
            return statusClass(this.ticket.status)
        },
        createdAt() {
            return formatDate(this.ticket.created_at, 'DD.MM.YYYY HH:mm')
        },
        number() {
            return '#' + this.ticket.id.toString().padStart(10, '0')
        }
    },
    methods: { statusColor }
}
</script>

<style lang="scss" scoped>
.similar-ticket {
    width: 100%;
    cursor: pointer;
    padding: calc(var(--padding-box) / 2);
    border-radius: var(--border-radius);
    margin-bottom: 8px;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    flex-wrap: wrap;

    &:hover {
        background: var(--bs-gray-200);
    }

    .pic {
        width: 40px;
    }

    .ticket-inner {
        padding-left: 4px;
        width: calc(100% - 40px);

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            .created_at {
                font-size: var(--font-small);
            }
        }

        .subject {
            width: 350px;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;

            .number {
                display: inline-block;
                margin-right: 6px;
                font-weight: bold;
                color: var(--bs-secondary)
            }
        }
    }


}
</style>
