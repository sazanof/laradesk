<template>
    <tr
        class="ticket"
        :class="`${statusClass}`"
        @click="$router.push(link ? link : `/user/tickets/${ticket.id}`)">
        <td class="status">
            <div class="d-flex">
                <VTooltip
                    placement="right">
                    <template #popper>
                        <div class="status-text">
                            {{ statusText }}
                        </div>
                    </template>
                    <span />
                </VTooltip>
                <div class="small">
                    {{ ticket.id.toString().padStart(10, "0") }}
                </div>
            </div>
        </td>
        <td class="subject">
            <span>{{ ticket.subject }}</span>
        </td>
        <td class="category">
            {{ ticket.category.name }}
        </td>
        <td class="requester">
            <UserInTicketList
                :show-info="false"
                :user="requester" />
        </td>
        <td class="assignees">
            <UserInTicketList
                v-if="assignees.length > 0"
                :show-info="false"
                :user="assignees[0]" />
            <span
                v-if="assignees.length > 1"
                class="more-users">
                <VTooltip>
                    <template #popper>
                        <div
                            v-for="assignee in assignees"
                            :key="assignee.id"
                            class="line">
                            {{ assignee.firstname }} {{ assignee.lastname }}
                        </div>
                    </template>
                    <AccountMultipleIcon :size="16" />
                </VTooltip>
            </span>
        </td>
        <td class="approvals">
            <UserInTicketList
                v-if="approvals.length > 0"
                :show-info="false"
                :user="approvals[0]" />
            <span
                v-if="approvals.length > 1"
                class="more-users">
                <VTooltip
                    :hover="true"
                    :arrow="true">
                    <template #popper>
                        <div
                            v-for="approval in approvals"
                            :key="approval.id"
                            class="line">
                            {{ approval.firstname }} {{ approval.lastname }}
                        </div>
                    </template>
                    <AccountMultipleIcon :size="16" />
                </VTooltip>
            </span>
        </td>
        <td class="observers">
            <UserInTicketList
                v-if="observers.length > 0"
                :show-info="false"
                :user="observers[0]" />
            <span
                v-if="observers.length > 1"
                class="more-users">
                <VTooltip>
                    <template #popper>
                        <div
                            v-for="observer in observers"
                            :key="observer.id"
                            class="line">
                            {{ observer.firstname }} {{ observer.lastname }}
                        </div>
                    </template>
                    <AccountMultipleIcon :size="16" />
                </VTooltip>
            </span>
        </td>
        <td class="created_at">
            {{ createdAt }}
        </td>
    </tr>
</template>

<script>
import { formatDate } from '../../js/helpers/moment.js'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'
import UserInTicketList from './UserInTicketList.vue'
import { statusClass } from '../../js/helpers/ticketStatus.js'

export default {
    name: 'TicketListItem',
    components: {
        AccountMultipleIcon,
        UserInTicketList
    },
    props: {
        ticket: {
            type: Object,
            required: true
        },
        link: {
            type: String,
            default: null
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
        },
        createdAt() {
            return formatDate(this.ticket.created_at)
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket {
    cursor: pointer;
    font-size: var(--font-medium);

    .small {
        font-size: var(--font-medium);
    }

    .status {
        .status-text {
            padding: 5px;
        }

        span {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--bs-light);
            margin-right: 4px;
        }
    }

    .subject {
        font-weight: bold;
        width: 270px;

        span {
            width: 250px;
            display: inline-block;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
    }

    .category {
        width: 170px;
    }

    .requester, .observers, .approvals, .assignees {
        position: relative;
        width: 220px;

        ::v-deep(.user) {
            align-items: center;

            .name {
                width: 170px;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
            }
        }

        .more-users {
            position: absolute;
            right: 10px;
            top: 8px;
            z-index: 1;
        }

        .line {
            padding: 4px;
            min-width: 140px;
        }
    }

    &.new {
        .status {
            span {
                background: var(--ticket-color-new);
            }
        }
    }

    &.in_work {
        .status {
            span {
                background: var(--ticket-color-in-work);
            }
        }
    }

    &.waiting {
        .status {
            span {
                background: var(--ticket-color-waiting);
            }
        }
    }

    &.solved {
        opacity: 0.5;
        transition: var(--transition-duration);

        &:hover {
            opacity: 1;
        }

        .status {
            span {
                background: var(--ticket-color-solved);
            }
        }
    }

    &.closed {
        opacity: 0.5;
        transition: var(--transition-duration);

        &:hover {
            opacity: 1;
        }

        .status {
            span {
                background: var(--ticket-color-closed);
            }
        }
    }

    &.in_approval {
        .status {
            span {
                background: var(--ticket-color-in-approval);
            }
        }
    }

    &.approved {
        .status {
            span {
                background: var(--ticket-color-approved);
            }
        }
    }
}
</style>
