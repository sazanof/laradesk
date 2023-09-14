<template>
    <div
        v-if="ticket"
        class="ticket">
        <div
            v-if="iAmApproval !== null && iAmApproval.approved === null"
            class="note bg-warning">
            <AlertCircleIcon :size="14" />
            {{ $t('Ticket requires your approval') }}
        </div>
        <div
            v-else-if="iAmApproval !== null && iAmApproval.approved === 1"
            class="note bg-success">
            <AlertCircleIcon :size="14" />
            {{ $t('You approved this ticket') }}
        </div>
        <div
            v-else-if="iAmApproval !== null && iAmApproval.approved === 0"
            class="note bg-danger">
            <AlertCircleIcon :size="14" />
            {{ $t('You decline this ticket') }}
        </div>
        <div
            ref="ticketContent"
            class="ticket-content"
            :style="`height:${height}px`"
            data-simplebar>
            <div class="ticket-header">
                <div class="status">
                    <Popper
                        :hover="true"
                        placement="right"
                        :arrow="true">
                        <template #content>
                            <div class="status-text">
                                {{ status }}
                            </div>
                        </template>
                        <span :class="cssClass" />
                    </Popper>
                </div>
                <div class="subject">
                    {{ ticket.subject }} <span class="number">({{ number }})</span>
                </div>
            </div>
            <div class="date">
                {{ $t('Created at') }}: {{ createdAt }}
            </div>
            <div class="ticket-body">
                <div class="department">
                    <div class="label">
                        {{ $t('Department') }}:
                    </div>
                    <div class="department-name">
                        {{ ticket.department.name }}
                    </div>
                </div>
                <div class="category">
                    <div class="label">
                        {{ $t('Category') }}:
                    </div>
                    <div class="category-name">
                        {{ ticket.category.name }}
                    </div>
                </div>
                <div class="label">
                    {{ $t('Content') }}:
                </div>
                <div
                    class="ticket-body-content"
                    v-html="ticket.content" />
                <!-- FIELDS -->
                <div class="fields">
                    <TicketField
                        v-for="field in ticket.fields"
                        :key="field.id"
                        :field="field"
                        class="field" />
                </div>
                <!-- / FIELDS -->
                <div
                    v-if="files.length > 1"
                    class="download-all">
                    <a
                        :href="`/user/tickets/${id}/files`"
                        target="_blank"
                        class="btn btn-primary">
                        <ArchiveArrowDownIcon :size="20" />
                        {{ $t('Download all files') }}
                    </a>
                </div>
            </div>
            <TicketThread :ticket="ticket" />
        </div>
        <div
            class="ticket-participants"
            :style="`height:${height}px`"
            data-simplebar>
            <div
                v-if="isAdmin"
                class="assign">
                <button
                    v-if="!iAmAssignee"
                    :disabled="loadAssigneeProcess"
                    class="btn btn-success w-100"
                    @click="assignMe">
                    <AccountPlusIcon :size="18" />
                    {{ $t('Take in work') }}
                </button>
                <button
                    v-else
                    :disabled="loadAssigneeProcess"
                    class="btn btn-danger w-100"
                    @click="deleteMe">
                    <AccountMinusIcon :size="18" />
                    {{ $t('Remove from work') }}
                </button>
            </div>
            <div class="ticket-participants-group">
                <div class="label">
                    {{ $t('Requester') }}
                </div>
                <UserInTicketList :user="ticket.requester" />
            </div>
            <div
                v-if="ticket.assignees.length > 0"
                class="ticket-participants-group">
                <div class="label">
                    {{ $t('Assignees') }}
                </div>
                <UserInTicketList
                    v-for="user in ticket.assignees"
                    :key="user.id"
                    :user="user" />
            </div>
            <div
                v-if="ticket.observers.length > 0"
                class="ticket-participants-group">
                <div class="label">
                    {{ $t('Observers') }}
                </div>
                <UserInTicketList
                    v-for="user in ticket.observers"
                    :key="user.id"
                    :user="user" />
            </div>
            <div
                v-if="ticket.approvals.length > 0"
                class="ticket-participants-group">
                <div class="label">
                    {{ $t('Approvals') }}
                </div>
                <UserInTicketList
                    v-for="user in ticket.approvals"
                    :key="user.id"
                    :user="user" />
            </div>
        </div>
        <TicketActions
            :ticket="ticket"
            @on-comment-add="onCommentAdd" />
    </div>
</template>

<script>
import ArchiveArrowDownIcon from 'vue-material-design-icons/ArchiveArrowDown.vue'
import AccountPlusIcon from 'vue-material-design-icons/AccountPlus.vue'
import AccountMinusIcon from 'vue-material-design-icons/AccountMinus.vue'
import { formatDate } from '../../js/helpers/moment.js'
import Popper from 'vue3-popper'
import TicketField from '../chunks/TicketField.vue'
import AlertCircleIcon from 'vue-material-design-icons/AlertCircle.vue'
import TicketThread from '../chunks/TicketThread.vue'
import TicketActions from '../chunks/TicketActions.vue'
import UserInTicketList from '../chunks/UserInTicketList.vue'
import { statusClass } from '../../js/helpers/ticketStatus.js'
import { PARTICIPANT, TYPES } from '../../js/consts.js'

export default {
    name: 'TicketTemplate',
    components: {
        Popper,
        UserInTicketList,
        TicketActions,
        AlertCircleIcon,
        AccountPlusIcon,
        AccountMinusIcon,
        ArchiveArrowDownIcon,
        TicketField,
        TicketThread
    },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            height: null,
            loadAssigneeProcess: false
        }
    },
    computed: {
        id() {
            return parseInt(this.$route.params.number)
        },
        number() {
            return this.ticket.id.toString().padStart(10, '0')
        },
        cssClass() {
            return `status_${statusClass(this.ticket.status)}`
        },
        status() {
            return this.$t(this.cssClass)
        },
        createdAt() {
            return formatDate(this.ticket.created_at)
        },
        user() {
            return this.$store.getters['getUser']
        },
        isAdmin() {
            return this.user.is_admin
        },
        iAmApproval() {
            return this.$store.getters['iAmApproval']
        },
        isApproved() {
            if (this.iAmApproval !== null) {
                return this.iAmApproval.approved
            }
            return null
        },
        iAmAssignee() {
            return this.ticket.assignees.find(assignee => assignee.user_id === this.user.id)
        },
        files() {
            return this.ticket.fields.filter(field => field.field_type === TYPES.TYPE_FILE)
        }
    },
    watch: {
        ticket() {
            if (typeof this.ticket === 'object') {
                this.$nextTick(() => {
                    const rect = this.$refs.ticketContent.getBoundingClientRect()
                    console.log(rect)
                    this.height = document.body.clientHeight - rect.top - 70
                })
            }
        }
    },
    created() {
        this.$nextTick(() => {
            const rect = this.$refs.ticketContent.getBoundingClientRect()
            console.log(rect)
            this.height = document.body.clientHeight - rect.top - 70
        })
    },
    methods: {
        onCommentAdd() {
            this.$store.dispatch('getThread', this.ticket.id)
        },
        async assignMe() {
            this.loadAssigneeProcess = true
            await this.$store.dispatch('addParticipant', {
                ticket_id: this.id,
                user_id: this.user.id,
                type: PARTICIPANT.ASSIGNEE
            }).finally(() => {
                this.loadAssigneeProcess = false
            })
        },
        async deleteMe() {
            this.loadAssigneeProcess = true
            await this.$store.dispatch('removeParticipant', {
                ticket_id: this.id,
                user_id: this.user.id,
                type: PARTICIPANT.ASSIGNEE
            }).finally(() => {
                this.loadAssigneeProcess = false
            })
        }
    }

}
</script>

<style lang="scss" scoped>
.ticket {
    display: flex;
    flex-wrap: wrap;

    .assign {
        padding: var(--padding-box) var(--padding-box) var(--padding-box) 0;
    }

    .note {
        width: 100%;
        padding: 4px 10px;
        font-size: var(--font-small);
        color: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;

        .material-design-icon {
            position: relative;
            top: -1px;
            margin-right: 4px;
        }
    }

    .ticket-content {
        width: calc(100% - 250px);
        padding: var(--padding-box);

        .fields {
            margin-top: 16px;
        }

        .date {
            margin-bottom: 16px;
            color: var(--bs-gray);
            text-align: center;
        }

        .ticket-body {
            background: var(--bs-light);
            border-radius: var(--border-radius);
            padding: 10px;

            .label {
                font-weight: bold;
            }

            .category, .department {
                display: flex;
                margin-bottom: 10px;

                .label {
                    margin-right: 4px;
                }
            }
        }

        .ticket-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;

            .status {
                display: flex;
                align-items: center;

                .status-text {
                    padding: 6px;
                }

                span {
                    margin-right: 8px;
                    margin-top: 5px;
                    display: inline-block;
                    width: 16px;
                    height: 16px;
                    border-radius: 50%;
                }
            }

            .number {
                color: var(--bs-gray)
            }

            .subject {
                font-weight: bold;
                font-size: 20px;
            }
        }

    }

    .ticket-participants {
        width: 250px;
        padding-left: 6px;

        .ticket-participants-group {
            padding: var(--padding-box) 0;

            .label {
                font-weight: bold;
                color: var(--bs-gray);
                margin-bottom: 6px;
            }

        }
    }


}
</style>
