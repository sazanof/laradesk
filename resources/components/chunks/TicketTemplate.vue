<template>
    <VSheet
        v-if="ticket"
        max-width="1200"
        class="ma-auto"
        :class="{'is-mobile': isMobile}">
        <VContainer>
            <VRow>
                <VCol
                    v-if="iAmApproval"
                    cols="12"
                    md="12">
                    <VAlert
                        v-if="iAmApproval !== null && iAmApproval.approved === null"
                        icon="mdi-alert-circle"
                        color="info"
                        variant="tonal"
                        density="compact"
                        :text="$t('Ticket requires your approval')" />
                    <VAlert
                        v-else-if="iAmApproval !== null && iAmApproval.approved === 1"
                        icon="mdi-alert-circle"
                        color="success"
                        variant="tonal"
                        density="compact"
                        :text="$t('You approved this ticket')" />
                    <VAlert
                        v-else-if="iAmApproval !== null && iAmApproval.approved === 0"
                        icon="mdi-alert-circle"
                        color="warning"
                        variant="tonal"
                        density="compact"
                        :text="$t('You decline this ticket')" />
                </VCol>
            </VRow>
            <VRow>
                <VCol
                    cols="12"
                    md="8">
                    <VSheet
                        ref="ticketContent"
                        class="fill-height"
                        :style="`height:${height}px`">
                        <VCard variant="tonal">
                            <VCardActions class="py-0">
                                <VChip
                                    rounded="pill"
                                    prepend-icon="mdi-circle"
                                    :text="$t(`status_${status}`)"
                                    :color="cssClass" />
                                <VChip
                                    class="ml-4"
                                    rounded="pill"
                                    prepend-icon="mdi-clock"
                                    :text="`${$t('Created at')} ${createdAt}`" />
                                <VSpacer />
                                <VBtn
                                    color="purple"
                                    :icon="showContent ? 'mdi-chevron-down' : 'mdi-chevron-up'"
                                    size="small"
                                    variant="tonal"
                                    density="comfortable"
                                    rounded="pill"
                                    @click="showContent = !showContent" />
                            </VCardActions>
                            <VCardSubtitle>
                                #{{ number }}
                            </VCardSubtitle>
                            <VCardTitle class="text-h5 font-weight-bold py-0 text-wrap">
                                {{ ticket.subject }}
                            </VCardTitle>
                            <VCardSubtitle
                                class="mt-1">
                                <VIcon
                                    icon="mdi-map-marker" />
                                {{ ticket.office !== null ? `${$t('Address')}: ${ticket.office?.address}` : '' }}
                            </VCardSubtitle>
                            <VCardSubtitle class="mt-1">
                                <VIcon
                                    icon="mdi-account-multiple" />
                                {{ $t('Department') }}: {{ ticket.department?.name }}
                            </VCardSubtitle>
                            <VCardSubtitle
                                v-if="ticket.room"
                                class="mt-1">
                                <VIcon
                                    icon="mdi-door" />
                                {{
                                    ticket.room !== null ? $t('Room') : ticket.custom_location !== null ? $t('Custom location') : ''
                                }}:
                                {{
                                    ticket.room !== null ? ticket.room.name : ticket.custom_location !== null ? ticket.custom_location : ''
                                }}
                            </VCardSubtitle>
                            <VCardSubtitle class="mt-1">
                                <VIcon
                                    icon="mdi-tag-text" />
                                {{ $t('Category') }}: {{ ticket.category?.name }}
                            </VCardSubtitle>

                            <VCardText v-if="showContent">
                                <div class="ticket-body">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">
                                        {{ $t('Content') }}
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
                                        <VBtn
                                            :href="`/user/tickets/${id}/files`"
                                            target="_blank"
                                            class="btn btn-primary">
                                            <ArchiveArrowDownIcon :size="20" />
                                            {{ $t('Download all files') }}
                                        </VBtn>
                                    </div>
                                </div>
                            </VCardText>
                            <VCardText
                                v-else
                                class="text-center">
                                <VBtn
                                    size="small"
                                    variant="tonal"
                                    :text="$t('Show')"
                                    append-icon="mdi-chevron-down"
                                    @click="showContent = true" />
                            </VCardText>
                            <VCardActions>
                                <TicketActions
                                    :ticket="ticket"
                                    @on-comment-add="onCommentAdd" />
                            </VCardActions>
                        </VCard>


                        <TicketThread :ticket="ticket" />
                    </VSheet>
                </VCol>
                <VCol
                    cols="12"
                    md="4">
                    <SimpleBar
                        v-if="showParticipants"
                        class="fill-height"
                        :style="`height:${height}px`">
                        <div
                            v-if="isAdmin"
                            class="assign">
                            <VBtn
                                v-if="!iAmAssignee"
                                :disabled="loadAssigneeProcess"
                                class="btn btn-success mb-2 w-100"
                                @click="assignMe">
                                <AccountPlusIcon :size="18" />
                                {{ $t('Take in work') }}
                            </VBtn>
                            <VBtn
                                v-else
                                :disabled="loadAssigneeProcess"
                                class="btn btn-danger mb-2 w-100"
                                @click="deleteMe">
                                <AccountMinusIcon :size="18" />
                                {{ $t('Remove from work') }}
                            </VBtn>
                            <VBtn
                                v-if="isAdmin && relevant?.data?.length > 0"
                                class="btn btn-danger w-100"
                                @click="openRelevantModal">
                                {{ $t('{count} similar tickets', {count: relevant.total}) }}
                            </VBtn>
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
                                <VBtn
                                    v-if="belongsToActiveDepartment"
                                    :disabled="disabled"
                                    class="btn btn-purple"
                                    @click="openAssigneesSelect()">
                                    <PlusIcon :size="18" />
                                </VBtn>
                            </div>
                            <UserInTicketList
                                v-for="assignee in ticket.assignees"
                                :key="assignee.id"
                                :user="assignee">
                                <template #actions>
                                    <VBtn
                                        v-if="canAddParticipant"
                                        :disabled="disabled"
                                        class="btn btn-link-danger"
                                        @click.stop="deleteParticipant(assignee)">
                                        {{ $t('Delete') }}
                                    </VBtn>
                                </template>
                            </UserInTicketList>
                        </div>
                        <div
                            class="ticket-participants-group">
                            <div class="label">
                                {{ $t('Observers') }}
                                <VBtn
                                    v-if="canAddParticipant"
                                    :disabled="disabled"
                                    class="btn btn-purple"
                                    @click="openObserversSelect()">
                                    <PlusIcon :size="18" />
                                </VBtn>
                            </div>
                            <UserInTicketList
                                v-for="observer in ticket.observers"
                                :key="observer.id"
                                :user="observer">
                                <template #actions>
                                    <VBtn
                                        v-if="canAddParticipant"
                                        :disabled="disabled"
                                        class="btn btn-link-danger"
                                        @click.stop="deleteParticipant(observer)">
                                        {{ $t('Delete') }}
                                    </VBtn>
                                </template>
                            </UserInTicketList>
                        </div>
                        <div
                            class="ticket-participants-group">
                            <div class="label">
                                {{ $t('Approvals') }}
                                <VBtn
                                    v-if="canAddParticipant"
                                    class="btn btn-purple"
                                    @click="openApprovalsSelect()">
                                    <PlusIcon :size="18" />
                                </VBtn>
                            </div>
                            <UserInTicketList
                                v-for="approval in ticket.approvals"
                                :key="approval.id"
                                :user="approval">
                                <template #actions>
                                    <VBtn
                                        v-if="canAddParticipant"
                                        class="btn btn-link-danger"
                                        @click.stop="deleteParticipant(approval)">
                                        {{ $t('Delete') }}
                                    </VBtn>
                                </template>
                            </UserInTicketList>
                        </div>
                    </SimpleBar>
                </VCol>
            </VRow>
        </VContainer>

        <LightBox
            ref="lightbox"
            :images="images"
            :src="src"
            @on-close="src = null" />

        <div
            v-if="isMobile"
            class="toggle-participants">
            <VBtn
                class="btn btn-purple w-100"
                @click="showParticipants = !showParticipants">
                {{ $t('Participants') }}
            </VBtn>
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
                <VBtn
                    class="btn btn-purple"
                    @click="addParticipants">
                    <ContentSaveIcon :size="18" />
                    {{ $t('Save') }}
                </VBtn>
            </template>
        </Modal>
        <Modal
            ref="relevantModal"
            scrollable
            size="large"
            :title="$t('Relevant tickets')">
            <template #default>
                <div v-if="relevant?.data?.length > 0">
                    <RelevantTicketItem
                        v-for="rel in relevant.data"
                        :key="rel.id"
                        :ticket="rel" />

                    <Pagination
                        :data="relevant"
                        @pagination-change-page="onRelevantPageChange" />
                </div>
            </template>
        </Modal>
        <ConfirmDialog ref="confirmDeleteParticipant" />
    </VSheet>
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
import TicketField from '../chunks/TicketField.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import AlertCircleIcon from 'vue-material-design-icons/AlertCircle.vue'
import TicketThread from '../chunks/TicketThread.vue'
import TicketActions from '../chunks/TicketActions.vue'
import UserInTicketList from '../chunks/UserInTicketList.vue'
import { formatDate } from '../../js/helpers/moment.js'
import { statusClass, statusColor } from '../../js/helpers/ticketStatus.js'
import { useToast } from 'vue-toastification'
import { PARTICIPANT, STATUSES, TYPES } from '../../js/consts.js'
import RelevantTicketItem from './RelevantTicketItem.vue'
import Pagination from './Pagination.vue'

const toast = useToast()

export default {
    name: 'TicketTemplate',
    components: {
        Pagination,
        RelevantTicketItem,
        TicketFiles,
        Modal,
        ConfirmDialog,
        UsersMultiselect,
        UserInTicketList,
        TicketActions,
        ContentSaveIcon,
        AccountPlusIcon,
        AccountMinusIcon,
        ArchiveArrowDownIcon,
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
            showContent: true,
            page: 1,
            disabled: false,
            filterByDepartmentId: null,
            src: null,
            height: null,
            loadAssigneeProcess: false,
            add: null,
            addUserIds: null,
            showParticipants: false,
            relevant: []
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
            return this.ticket?.id?.toString().padStart(10, '0')
        },
        cssClass() {
            return `${statusColor(this.ticket.status)}`
        },
        status() {
            return statusClass(this.ticket.status)
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
        async ticket() {
            this.page = 1
            this.$refs.relevantModal.close()
            await this.getRelevantTickets()
        }
    },
    async created() {
        this.showParticipants = !this.isMobile
        await this.getRelevantTickets()
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
            this.disabled = true
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
            this.disabled = false
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
                this.disabled = true
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
                this.disabled = false
            }
        },
        async getRelevantTickets() {
            if (this.isAdmin) {
                this.relevant = await this.$store.dispatch('getRelevantTickets', {
                    id: this.id,
                    page: this.page
                })
            }
        },
        async onRelevantPageChange(e) {
            this.page = e
            await this.getRelevantTickets()
        },
        openRelevantModal() {
            this.$refs.relevantModal.open()
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

@media print {

    .ticket {

        .assign {
            display: none;
        }

        .ticket-header, .date {
            justify-content: flex-start !important;
            text-align: left !important;

            .status {
                display: none !important;
            }
        }

        .location {
            display: block !important;

            .location-inner {
                text-align: left !important;
            }
        }
    }

}
</style>
