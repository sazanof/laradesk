<template>
    <tr
        class="ticket"
        :class="`${statusClass}`"
        @click="$router.push(`/user/tickets/${ticket.number}`)">
        <td class="status">
            <div class="status-text">
                <span />
                <div class="small">
                    {{ statusText }}
                </div>
            </div>
        </td>
        <td class="number">
            {{ ticket.number }}
        </td>
        <td class="subject">
            {{ ticket.subject }}
        </td>
        <td class="category">
            {{ ticket.category.name }}
        </td>
        <td class="requester">
            <UserInTicketList
                :user="requester" />
        </td>
        <td class="assignees">
            <UserInTicketList
                v-for="user in assignees"
                :key="user.id"
                :user="user" />
        </td>
        <td class="approvals">
            <UserInTicketList
                v-for="user in approvals"
                :key="user.id"
                :user="user" />
        </td>
        <td class="observers">
            <UserInTicketList
                v-for="user in observers"
                :key="user.id"
                :user="user" />
        </td>
        <td class="created_at">
            {{ ticket.created_at }}
        </td>
    </tr>
</template>

<script>
import UserInTicketList from './UserInTicketList.vue'
import { statusClass } from '../../js/helpers/ticketStatus.js'

export default {
    name: 'TicketListItem',
    components: {
        UserInTicketList
    },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    computed: {
        statusClass() {
            return statusClass(this.ticket.status)
        },
        statusText() {
            return this.$t(`status_${statusClass(this.ticket.status)}`)
        },
        category() {
            return this.ticket.category
        },
        requester() {
            return this.ticket.requester
        },
        observers() {
            return this.ticket.observers
        },
        approvals() {
            return this.ticket.approvals
        },
        assignees() {
            return this.ticket.assignees
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket {
    cursor: pointer;
    font-size: var(--font-medium);

    .status {
        .status-text {
            display: flex;
            align-items: center;
        }

        span {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--bs-light);
            margin-right: 6px;
        }
    }

    .subject {
        font-weight: bold;
    }

    &.new {
        .status {
            span {
                background: var(--bs-green);
            }
        }
    }

    &.in_work {
        .status {
            span {
                background: var(--bs-purple);
            }
        }
    }

    &.waiting {
        .status {
            span {
                background: var(--bs-orange);
            }
        }
    }

    &.solved {
        .status {
            span {
                background: (var(--bs-cyan));
            }
        }
    }

    &.closed {
        .status {
            span {
                background: (var(--bs-danger));
            }
        }
    }

    &.in_approval {
        .status {
            span {
                background: var(--bs-pink);
            }
        }
    }
}
</style>
