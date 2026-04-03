<template>
    <tr
        class="ticket"
        :class="`${statusClass} ${rowColor}`"
        @click="$router.push(link ? link : `/user/tickets/${ticket.id}`)">
        <td class="status">
            <VChip
                v-tooltip="statusText"
                rounded="pill"
                size="small"
                :text="ticket.id.toString().padStart(10, '0')">
                <template #prepend>
                    <div class="dot" />
                </template>
            </VChip>
        </td>
        <td class="category">
            <VChip
                v-if="ticket.category"
                size="small"
                prepend-icon="mdi-tag-text"
                variant="text">
                {{ ticket.category.name }}
            </VChip>
        </td>
        <td>
            <div class="d-flex align-center">
                <VBtn
                    size="small"
                    density="comfortable"
                    rounded="pill"
                    variant="tonal"
                    target="_blank"
                    icon="mdi-open-in-new"
                    :to="link ? link : `/user/tickets/${ticket.id}`"
                    @click.stop="" />
                <VBtn
                    v-if="ticket.thread_count > 0"
                    class="ml-2"
                    :text="`${ticket.thread_count}`"
                    size="small"
                    rounded="pill"
                    variant="tonal"
                    prepend-icon="mdi-comment"
                    @click.stop="" />
                <VMenu
                    open-on-hover
                    width="400">
                    <template #activator="{props}">
                        <div
                            class="pa-1 pl-3"
                            v-bind="props">
                            {{ ticket.subject }}
                        </div>
                    </template>
                    <VCard
                        width="400"
                        max-height="300">
                        <template #text>
                            <div class="text-subtitle-1 font-weight-bold">
                                {{ ticket.subject }}
                            </div>
                            <VSheet v-html="ticket.content" />
                            <VDivider class="mt-2 mb-4" />
                            <VSheet v-if="ticket.fields.length > 0">
                                <TicketField
                                    v-for="field in ticket.fields"
                                    :key="field.id"
                                    mini
                                    :field="field" />
                            </VSheet>
                        </template>
                    </VCard>
                </VMenu>
            </div>
        </td>
        <td>
            <div class="participants">
                <div
                    class="participants-block">
                    <div class="requester-block">
                        <!--                        <VTooltip>-->
                        <!--                            <AccountEditIcon-->
                        <!--                                :size="20"-->
                        <!--                                class="me-1" />-->
                        <!--                            <template #popper>-->
                        <!--                                {{ $t('Requester') }}-->
                        <!--                            </template>-->
                        <!--                        </VTooltip>-->
                        <VChip
                            v-if="requester"
                            variant="text"
                            :text="requester.full_name">
                            <template #prepend>
                                <Avatar
                                    :size="30"
                                    class="ml-n2 mr-2"
                                    :user="requester" />
                            </template>
                        </VChip>
                    </div>
                </div>
                <!-- participants popper -->
                <div
                    v-if="participantsCount > 0"
                    class="participants-count">
                    <VMenu
                        width="400"
                        open-on-hover>
                        <template #activator="{props}">
                            <VBtn
                                v-bind="props"
                                size="small"
                                rounded="pill"
                                variant="tonal"
                                prepend-icon="mdi-account-multiple"
                                :text="`${participantsCount}`"
                                @click.stop="showParticipants = !showParticipants" />
                        </template>
                        <VCard>
                            <VCardText class="pa-0">
                                <SimpleBar class="participants-popper">
                                    <VList
                                        v-if="assignees.length > 0">
                                        <VListSubheader>
                                            {{ $t('Assignees') }}
                                        </VListSubheader>
                                        <UserInTicketList
                                            v-for="as in assignees"
                                            :key="as.id"
                                            :user="as" />
                                    </VList>
                                    <VList
                                        v-if="approvals.length > 0">
                                        <VListSubheader>
                                            {{ $t('Approvals') }}
                                        </VListSubheader>

                                        <UserInTicketList
                                            v-for="a in approvals"
                                            :key="a.id"
                                            :user="a" />
                                    </VList>

                                    <VList
                                        v-if="observers.length > 0">
                                        <VListSubheader>
                                            {{ $t('Observers') }}
                                        </VListSubheader>
                                        <UserInTicketList
                                            v-for="o in observers"
                                            :key="o.id"
                                            :user="o" />
                                    </VList>
                                </SimpleBar>
                            </VCardText>
                        </VCard>
                    </VMenu>
                </div>
                <!-- participants popper -->
            </div>
        </td>
        <td class="created_at">
            {{ createdAt }}
            <VIcon
                v-if="solvedAt"
                v-tooltip="`${$t('Solved at')} ${solvedAt}`"
                size="small"
                class="mr-2"
                color="success"
                icon="mdi-clock-check-outline" />
            <VIcon
                v-if="closedAt"
                v-tooltip="`${$t('Closed at')} ${closedAt}`"
                size="small"
                color="error"
                icon="mdi-clock-check-outline" />
        </td>
    </tr>
</template>

<script>
import { formatDate } from '../../js/helpers/moment.js'
import UserInTicketList from './UserInTicketList.vue'
import Avatar from './Avatar.vue'
import { statusClass } from '../../js/helpers/ticketStatus.js'

import SimpleBar from 'simplebar-vue'
import TicketField from './TicketField.vue'

export default {
    name: 'TicketListItem',
    components: {
        TicketField,
        UserInTicketList,
        SimpleBar,
        Avatar
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
        },
        rowColor() {
            switch (this.ticket.priority) {
                case 2:
                    return 'yellow'
                case 3:
                    return 'error'
                default:
                    return ''
            }
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
        max-width: 350px;
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

tr.error {
    background-color: rgba(176, 0, 32, 0.4);
}

tr.yellow {
    background-color: rgba(255, 152, 0, 0.4);
}


</style>
