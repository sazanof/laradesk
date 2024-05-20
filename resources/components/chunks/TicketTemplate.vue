<template>
    <div
        v-if="ticket"
        :class="{'is-mobile': isMobile}"
        class="ticket">
        <TicketActions
            :ticket="ticket"
            @on-comment-add="onCommentAdd" />
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

        <SimpleBar
            ref="ticketContent"
            class="ticket-content"
            :style="`height:${height}px`">
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

            <div
                v-if="ticket.office !== null && (ticket.room !== null || ticket.custom_location !== null)"
                class="location">
                <div
                    class="label">
                    <MapMarkerIcon :size="18" />
                    {{ $t('Location') }}:
                </div>
                <div class="location-inner">
                    <div class="office">
                        {{ ticket.office !== null ? `${$t('Address')}: ${ticket.office.address}` : '' }}
                    </div>
                    <div class="room">
                        {{
                            ticket.room !== null ? $t('Room') : ticket.custom_location !== null ? $t('Custom location') : ''
                        }}:
                        {{
                            ticket.room !== null ? ticket.room.name : ticket.custom_location !== null ? ticket.custom_location : ''
                        }}
                    </div>
                </div>
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
                    ref="content"
                    class="ticket-body-content"
                    @click="openImage"
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
        </SimpleBar>

        <SimpleBar
            v-if="showParticipants"
            class="ticket-participants"
            :style="`height:${height}px`">
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
            <div
                v-if="ticket.files && ticket.files.length > 0"
                class="ticket-files">
                <TicketFiles :ticket="ticket" />
            </div>
            <div class="ticket-participants-group">
                <div class="label">
                    {{ $t('Requester') }}
                </div>
                <UserInTicketList :user="ticket.requester" />
            </div>
            <div
                class="ticket-participants-group">
                <div class="label">
                    {{ $t('Assignees') }}
                    <button
                        v-if="belongsToActiveDepartment"
                        class="btn btn-purple"
                        @click="openAssigneesSelect()">
                        <PlusIcon :size="18" />
                    </button>
                </div>
                <UserInTicketList
                    v-for="assignee in ticket.assignees"
                    :key="assignee.id"
                    :user="assignee">
                    <template #actions>
                        <button
                            v-if="canAddParticipant"
                            class="btn btn-link-danger"
                            @click.stop="deleteParticipant(assignee)">
                            {{ $t('Delete') }}
                        </button>
                    </template>
                </UserInTicketList>
            </div>
            <div
                class="ticket-participants-group">
                <div class="label">
                    {{ $t('Observers') }}
                    <button
                        v-if="canAddParticipant"
                        class="btn btn-purple"
                        @click="openObserversSelect()">
                        <PlusIcon :size="18" />
                    </button>
                </div>
                <UserInTicketList
                    v-for="observer in ticket.observers"
                    :key="observer.id"
                    :user="observer">
                    <template #actions>
                        <button
                            v-if="canAddParticipant"
                            class="btn btn-link-danger"
                            @click.stop="deleteParticipant(observer)">
                            {{ $t('Delete') }}
                        </button>
                    </template>
                </UserInTicketList>
            </div>
            <div
                class="ticket-participants-group">
                <div class="label">
                    {{ $t('Approvals') }}
                    <button
                        v-if="canAddParticipant"
                        class="btn btn-purple"
                        @click="openApprovalsSelect()">
                        <PlusIcon :size="18" />
                    </button>
                </div>
                <UserInTicketList
                    v-for="approval in ticket.approvals"
                    :key="approval.id"
                    :user="approval">
                    <template #actions>
                        <button
                            v-if="canAddParticipant"
                            class="btn btn-link-danger"
                            @click.stop="deleteParticipant(approval)">
                            {{ $t('Delete') }}
                        </button>
                    </template>
                </UserInTicketList>
            </div>
        </SimpleBar>

        <LightBox
            ref="lightbox"
            :images="images"
            :src="src"
            @on-close="src = null" />

        <div
            v-if="isMobile"
            class="toggle-participants">
            <button
                class="btn btn-purple w-100"
                @click="showParticipants = !showParticipants">
                {{ $t('Participants') }}
            </button>
        </div>

        <Modal
            ref="addParticipantModal"
            :footer="true"
            :title="participantTitle"
            @on-close="resetModal">
            <UsersMultiselect
                ref="usersSelect"
                :department="filterByDepartmentId"
                @on-users-changed="participantsChanged" />
            <template #footer-actions>
                <button
                    class="btn btn-purple"
                    @click="addParticipants">
                    <ContentSaveIcon :size="18" />
                    {{ $t('Save') }}
                </button>
            </template>
        </Modal>
        <ConfirmDialog ref="confirmDeleteParticipant" />
    </div>
</template>

<script>
import TicketFiles from './TicketFiles.vue'
import LightBox from './LightBox.vue'
import MapMarkerIcon from 'vue-material-design-icons/MapMarker.vue'
import SimpleBar from 'simplebar-vue'
import ConfirmDialog from '../elements/ConfirmDialog.vue'
import Modal from '../elements/Modal.vue'
import UsersMultiselect from '../elements/UsersMultiselect.vue'
import ArchiveArrowDownIcon from 'vue-material-design-icons/ArchiveArrowDown.vue'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import AccountPlusIcon from 'vue-material-design-icons/AccountPlus.vue'
import AccountMinusIcon from 'vue-material-design-icons/AccountMinus.vue'
import Popper from 'vue3-popper'
import TicketField from '../chunks/TicketField.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import AlertCircleIcon from 'vue-material-design-icons/AlertCircle.vue'
import TicketThread from '../chunks/TicketThread.vue'
import TicketActions from '../chunks/TicketActions.vue'
import UserInTicketList from '../chunks/UserInTicketList.vue'
import { formatDate } from '../../js/helpers/moment.js'
import { statusClass } from '../../js/helpers/ticketStatus.js'
import { useToast } from 'vue-toastification'
import { PARTICIPANT, STATUSES, TYPES } from '../../js/consts.js'

const toast = useToast()

export default {
    name: 'TicketTemplate',
    components: {
        TicketFiles,
        Popper,
        Modal,
        ConfirmDialog,
        UsersMultiselect,
        UserInTicketList,
        TicketActions,
        AlertCircleIcon,
        ContentSaveIcon,
        AccountPlusIcon,
        AccountMinusIcon,
        ArchiveArrowDownIcon,
        MapMarkerIcon,
        TicketField,
        TicketThread,
        PlusIcon,
        SimpleBar,
        LightBox
    },
    props: {
        admin: {
            type: Boolean,
            default: false
        },
        ticket: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            filterByDepartmentId: null,
            src: null,
            height: null,
            loadAssigneeProcess: false,
            add: null,
            addUserIds: null,
            showParticipants: false
        }
    },
    computed: {
        participantTitle() {
            switch (this.add) {
                case PARTICIPANT.OBSERVER:
                    return this.$t('Add observers')
                case PARTICIPANT.APPROVAL:
                    return this.$t('Add approvals')
                default:
                    return this.$t('Add assignees')
            }
        },
        images() {
            let ar = []
            const fakeContent = document.createElement('div')
            fakeContent.innerHTML = this.ticket.content
            const imgs = fakeContent.querySelectorAll('img')
            if (imgs !== null) {
                imgs.forEach(img => {
                    ar.push(img.src)
                })
            }
            return ar
        },
        isMobile() {
            return this.$store.getters['isMobile']
        },
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
        belongsToActiveDepartment() {
            return this.$store.getters.userBelongsToDepartment(this.ticket.department_id)
        },
        isAdmin() {
            return this.user.is_admin && this.$store.getters.userBelongsToDepartment(this.ticket.department_id)
        },
        iAmOwner() {
            return this.user.id === this.ticket.user_id
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
        },
        canAddParticipant() {
            return (this.admin || this.iAmOwner) && (this.ticket.status !== STATUSES.CLOSED && this.ticket.status !== STATUSES.SOLVED)
        }
    },
    watch: {
        isMobile() {
            if (!this.isMobile) {
                this.showParticipants = true
            }
        }
    },
    created() {
        this.showParticipants = !this.isMobile
    },
    methods: {
        openImage(e) {
            if (e.target.nodeName === 'IMG') {
                this.src = e.target.src
                this.$refs.lightbox.open(this.images.indexOf(this.src))
            }
        },
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
                id: this.iAmAssignee.id,
                ticket_id: this.id,
                user_id: this.user.id,
                type: PARTICIPANT.ASSIGNEE
            }).finally(() => {
                this.loadAssigneeProcess = false
            })
        },
        openApprovalsSelect() {
            this.filterByDepartmentId = null
            this.add = PARTICIPANT.APPROVAL
            this.$refs.addParticipantModal.open()
        },
        openObserversSelect() {
            this.filterByDepartmentId = null
            this.$refs.addParticipantModal.open()
            this.add = PARTICIPANT.OBSERVER
        },
        openAssigneesSelect() {
            this.filterByDepartmentId = this.ticket.department_id
            this.$refs.addParticipantModal.open()
            this.add = PARTICIPANT.ASSIGNEE
        },
        resetModal() {
            this.add = null
            this.addUserIds = null
            this.$refs.usersSelect.clear()
        },
        participantsChanged(p) {
            this.addUserIds = p.map(user => {
                return user.id
            })
        },
        async addParticipants() {
            const data = {
                ticket_id: this.ticket.id,
                type: this.add,
                user_id: this.addUserIds
            }
            if (this.admin && this.isAdmin) {
                await this.$store.dispatch('addParticipant', data).catch(e => {
                    toast.error(this.$t(e.response.data.message))
                })
                await this.$store.dispatch('getTicket', this.ticket.id)
            } else if (this.ticket.user_id === this.user.id) {
                await this.$store.dispatch('addParticipantFromTicketOwner', data).catch(e => {
                    toast.error(this.$t(e.response.data.message))
                })
                await this.$store.dispatch('getUserTicket', this.ticket.id)
            }
            this.resetModal()
            this.$refs.addParticipantModal.close()
        },
        async deleteParticipant(participant) {
            const ok = await this.$refs.confirmDeleteParticipant.show({
                title: this.$t('Delete participant'),
                message: this.$t('Are you sure you want to delete participant {name}?', {
                    name: `${participant.firstname} ${participant.lastname}`
                }),
                okButton: this.$t('Delete')
            })
            if (ok) {
                const data = {
                    ticket_id: this.ticket.id,
                    id: participant.id,
                    type: participant.type
                }
                if (this.admin && this.isAdmin) {
                    await this.$store.dispatch('removeParticipant', data)
                    await this.$store.dispatch('getTicket', this.ticket.id)
                } else if (this.user.id === this.ticket.user_id) {
                    await this.$store.dispatch('removeParticipantFromTicketOwner', data)
                    await this.$store.dispatch('getUserTicket', this.ticket.id)
                }
            }
        }
    }

}
</script>

<style lang="scss" scoped>
.ticket {
    display: flex;
    flex-wrap: wrap;

    .ticket-body-content {
        ::v-deep(img) {
            max-width: 100%;
            height: auto;
            cursor: pointer;
        }
    }

    .assign {
        padding: var(--padding-box) var(--padding-box) var(--padding-box) 0;
    }

    .toggle-participants {
        padding: 8px 24px;
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
        width: calc(100% - 320px);
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

        .location {
            display: flex;
            padding: var(--padding-box);
            border-radius: var(--bs-border-radius);
            margin: 10px auto;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: var(--bs-light);

            .location-inner {
                text-align: center;
            }

            .label {
                font-weight: bold;
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
        width: 320px;
        padding: 0 16px;

        .ticket-participants-group {
            padding: var(--padding-box) 0;

            .label {
                font-weight: bold;
                color: var(--bs-gray);
                margin-bottom: 6px;
                padding-right: 6px;
                display: flex;
                align-items: center;
                justify-content: space-between;

                .btn-purple {
                    text-decoration: none;
                    font-size: var(--font-small);
                    padding: 4px;
                    margin-bottom: 4px;

                    .material-design-icon {
                        margin: 0;
                        top: 0
                    }
                }
            }

            .btn-link-danger {
                text-decoration: none;
                font-size: var(--font-small);
                padding: 4px 0;
                color: var(--bs-danger);

                .material-design-icon {
                    margin: 0;
                    top: 0;
                }
            }

        }

    }

    &.is-mobile {
        flex-direction: column-reverse;

        .assign {
            padding: 8px;
        }

        .ticket-content {
            width: 100%;
        }

        .ticket-participants {
            width: 100%;
        }
    }

}
</style>
