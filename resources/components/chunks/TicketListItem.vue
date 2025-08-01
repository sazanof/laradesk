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
                    <span class="dot" />
                </VTooltip>
                <div class="small">
                    {{ ticket.id.toString().padStart(10, "0") }}
                </div>
            </div>
        </td>
        <td>
            <div class="subject">
                <button
                    class="btn btn-sm subject-btn btn-transparent d-inline-flex"
                    @click.stop="showMore = !showMore">
                    <ChevronUp
                        v-if="showMore"
                        :size="14" />
                    <ChevronDown
                        v-else
                        :size="14" />
                </button>
                <router-link
                    class="btn btn-transparent subject-btn"
                    target="_blank"
                    :to="link ? link : `/user/tickets/${ticket.id}`"
                    @click.stop="">
                    <OpenInNewIcon :size="14" />
                </router-link>
                <VTooltip
                    width="400"
                    placement="right">
                    <template #popper>
                        <div
                            style="max-width:400px"
                            v-html="ticket.content" />
                    </template>
                    <span>{{ ticket.subject }}</span>
                </VTooltip>
            </div>
        </td>
        <td class="category">
            {{ ticket.category.name }}
        </td>
        <td>
            <div class="participants">
                <div
                    class="participants-block">
                    <div class="requester-block">
                        <VTooltip>
                            <AccountEditIcon
                                :size="20"
                                class="me-1" />
                            <template #popper>
                                {{ $t('Requester') }}
                            </template>
                        </VTooltip>
                        <UserInTicketList
                            :show-info="false"
                            :user="requester" />
                    </div>
                </div>
                <!-- participants popper -->
                <div
                    v-if="participantsCount > 0"
                    class="participants-count">
                    <VDropdown
                        v-model="showParticipants"
                        :auto-hide="true">
                        <button
                            class="btn btn-purple"
                            @click.stop="showParticipants = !showParticipants">
                            <AccountMultipleIcon :size="16" />
                            {{ participantsCount }}
                        </button>
                        <template #popper>
                            <SimpleBar class="participants-popper">
                                <div
                                    v-if="assignees.length > 0"
                                    class="participants-block">
                                    <div class="participants-title">
                                        {{ $t('Assignees') }}
                                    </div>
                                    <UserInTicketList
                                        v-for="as in assignees"
                                        :key="as.id"
                                        :user="as" />
                                </div>
                                <div
                                    v-if="approvals.length > 0"
                                    class="participants-block">
                                    <div
                                        class="participants-title">
                                        {{ $t('Approvals') }}
                                    </div>

                                    <UserInTicketList
                                        v-for="a in approvals"
                                        :key="a.id"
                                        :user="a" />
                                </div>

                                <div
                                    v-if="observers.length > 0"
                                    class="participants-block">
                                    <div class="participants-title">
                                        {{ $t('Observers') }}
                                    </div>
                                    <UserInTicketList
                                        v-for="o in observers"
                                        :key="o.id"
                                        :user="o" />
                                </div>
                            </SimpleBar>
                        </template>
                    </VDropdown>
                </div>
                <!-- participants popper -->
            </div>
        </td>
        <td class="created_at">
            {{ createdAt }}
        </td>
        <td class="created_at">
            {{ solvedAt }}
        </td>
        <td class="created_at">
            {{ closedAt }}
        </td>
    </tr>
    <tr v-if="ticket.fields.length > 0 && showMore">
        <td
            colspan="7">
            <TicketField
                v-for="field in ticket.fields"
                :key="field.id"
                mini
                :field="field" />
        </td>
    </tr>
</template>

<script>
import { formatDate } from '../../js/helpers/moment.js'
import OpenInNewIcon from 'vue-material-design-icons/OpenInNew.vue'
import ChevronDown from 'vue-material-design-icons/ChevronDown.vue'
import ChevronUp from 'vue-material-design-icons/ChevronUp.vue'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'
import AccountEditIcon from 'vue-material-design-icons/AccountEdit.vue'
import UserInTicketList from './UserInTicketList.vue'
import { statusClass } from '../../js/helpers/ticketStatus.js'

import SimpleBar from 'simplebar-vue'
import TicketField from './TicketField.vue'

export default {
    name: 'TicketListItem',
    components: {
        TicketField,
        AccountMultipleIcon,
        AccountEditIcon,
        UserInTicketList,
        OpenInNewIcon,
        SimpleBar,
        ChevronDown,
        ChevronUp
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
    data() {
        return {
            showMore: false,
            showParticipants: false
        }
    },
    computed: {
        participantsCount() {
            return this.observers.length + this.approvals.length + this.assignees.length
        },
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
            return formatDate(this.ticket.created_at, 'DD.MM.YYYY HH:mm')
        },
        closedAt() {
            return this.ticket.closed_at !== null ? formatDate(this.ticket.closed_at, 'DD.MM.YYYY HH:mm') : null
        },
        solvedAt() {
            return this.ticket.solved_at !== null ? formatDate(this.ticket.solved_at, 'DD.MM.YYYY HH:mm') : null
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

        .dot {
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
        min-width: 290px;
        max-width: 450px;
        width: 100%;
        display: flex;
        align-items: start;

        .subject-btn {
            padding: 0 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            color: var(--bs-gray-500);
            transition: var(--transition-duration);

            &:hover {
                color: var(--bs-purple-darker);
            }
        }

        & > span {
            width: 100%;
            display: inline-block;

        }
    }

    .category {
        width: 170px;
    }

    .participants {
        position: relative;
        width: 250px;
        display: flex;
        align-items: center;
        justify-content: space-between;

        .requester-block {
            display: flex;
            align-items: center;
        }

        .participants-count {
            .btn-purple {
                border-radius: 20px;
                padding: 3px 8px;
                font-size: var(--font-small);
            }
        }


    }

    &.new {
        .status {
            .dot {
                background: var(--ticket-color-new);
            }
        }
    }

    &.in_work {
        .status {
            .dot {
                background: var(--ticket-color-in-work);
            }
        }
    }

    &.waiting {
        .status {
            .dot {
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
            .dot {
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
            .dot {
                background: var(--ticket-color-closed);
            }
        }
    }

    &.in_approval {
        .status {
            .dot {
                background: var(--ticket-color-in-approval);
            }
        }
    }

    &.approved {
        .status {
            .dot {
                background: var(--ticket-color-approved);
            }
        }
    }
}

.participants-popper {
    padding: 8px 16px;
    min-width: 400px;
    max-height: 300px;
    overflow-y: auto;

    .participants-block {
        position: relative;
        margin-top: 6px;

        .participants-title {
            color: var(--bs-secondary);
            font-weight: bold;
            margin: 2px 0;
        }

        ::v-deep(.user) {

            .name {
                width: 170px;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
                margin-top: 2px;
            }
        }

        .line {
            padding: 4px;
            min-width: 140px;
        }
    }
}
</style>
