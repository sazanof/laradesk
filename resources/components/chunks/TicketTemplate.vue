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
            v-else-if="iAmApproval !== null && iAmApproval.approved === true"
            class="note bg-success">
            <AlertCircleIcon :size="14" />
            {{ $t('You approved this ticket') }}
        </div>
        <div
            v-else-if="iAmApproval !== null && iAmApproval.approved === false"
            class="note bg-danger">
            <AlertCircleIcon :size="14" />
            {{ $t('You decline this ticket') }}
        </div>
        <div class="ticket-content">
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
            </div>
        </div>
        <div class="ticket-participants">
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
        <TicketActions :ticket="ticket" />
    </div>
</template>

<script>
import { formatDate } from '../../js/helpers/moment.js'
import Popper from 'vue3-popper'
import TicketField from '../chunks/TicketField.vue'
import AlertCircleIcon from 'vue-material-design-icons/AlertCircle.vue'
import TicketThreadItem from '../chunks/TicketThreadItem.vue'
import TicketActions from '../chunks/TicketActions.vue'
import UserInTicketList from '../chunks/UserInTicketList.vue'
import { statusClass } from '../../js/helpers/ticketStatus.js'

export default {
    name: 'TicketTemplate',
    components: {
        Popper,
        UserInTicketList,
        TicketActions,
        AlertCircleIcon,
        TicketThreadItem,
        TicketField
    },
    props: {
        ticket: {
            type: Object,
            required: true
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
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket {
    display: flex;
    flex-wrap: wrap;

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

            .category {
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
