<template>
    <div
        class="similar-ticket"
        @click="$router.push({name:'user.ticket', params:{number:ticket.id}})">
        <Avatar
            v-if="ticket.requester"
            class="pic"
            :user="ticket.requester"
            border-radius="4px"
            :size="40" />
        <div class="ticket-inner">
            <div class="ticket-header">
                <span
                    class="badge"
                    :class="`status_${classStr}`">{{ statusText }}</span>
                <div class="created_at">
                    {{ createdAt }}
                </div>
            </div>
            <div class="subject">
                <span class="number">{{ number }} </span>{{ ticket.subject }}
            </div>
        </div>
    </div>
</template>

<script>
import { statusClass } from '../../js/helpers/ticketStatus.js'
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
    }
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
