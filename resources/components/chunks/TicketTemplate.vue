<template>
    <VSheet
        v-if="ticket"
        max-width="1200"
        elevation="3"
        class="mx-auto my-4"
        rounded="lg"
        :class="{'is-mobile': isMobile}">
        <VContainer>
            <VRow>
                <VCol
                    v-if="iAmApproval"
                    cols="12"
                    md="12">
                    <VAlert
                        v-if="iAmApproval.approved === null"
                        icon="mdi-alert-circle"
                        color="info"
                        variant="tonal"
                        density="compact"
                        :text="$t('Ticket requires your approval')" />
                    <VAlert
                        v-else-if="iAmApproval.approved === 1"
                        icon="mdi-alert-circle"
                        color="success"
                        variant="tonal"
                        density="compact"
                        :text="$t('You approved this ticket')" />
                    <VAlert
                        v-else-if="iAmApproval.approved === 0"
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
                        <VCard
                            rounded="0"
                            variant="text">
                            <VCardActions class="py-0">
                                <VChip
                                    rounded="pill"
                                    variant="tonal"
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
                            <VCardTitle>
                                <VChip
                                    class="ps-0"
                                    rounded="pill">
                                    <template #prepend>
                                        <Avatar
                                            class="mr-2"
                                            :user="ticket.requester"
                                            :size="30" />
                                    </template>
                                    <template #default>
                                        {{ ticket.requester.full_name }}
                                    </template>
                                </VChip>
                            </VCardTitle>
                            <VDivider class="my-2" />
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
                                    <TicketFiles :ticket="ticket" />
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
                        </VCard>

                        <VSheet class="ps-4 mt-4 text-right d-flex justify-space-between align-center">
                            <div class="font-weight-bold opacity-70 text-subtitle-2">
                                {{ $t('Actions') }}
                            </div>

                            <TicketActions
                                :ticket="ticket"
                                @on-comment-add="onCommentAdd" />
                        </VSheet>
                        <VDivider class="ms-4 mt-4 mb-4" />


                        <TicketThread
                            class="ms-4 "
                            :ticket="ticket" />
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
                                block
                                :color="iAmAssignee ? 'warning': 'success'"
                                size="large"
                                variant="tonal"
                                class="mb-4"
                                :loading="loadAssigneeProcess"
                                :disabled="loadAssigneeProcess"
                                :prepend-icon="iAmAssignee ? 'mdi-account-minus':'mdi-account-plus'"
                                @click="iAmAssignee ? deleteMe() : assignMe()">
                                {{ iAmAssignee ? $t('Remove from work') : $t('Take in work') }}
                            </VBtn>

                            <VBtn
                                v-if="isAdmin && relevant?.data?.length > 0"
                                prepend-icon="mdi-content-copy"
                                block
                                color="default"
                                variant="tonal"
                                @click="openRelevantModal = true">
                                {{ $t('{count} similar tickets', {count: relevant.total}) }}
                            </VBtn>
                        </div>

                        <VCard variant="text">
                            <template #title>
                                {{ $t('Requester') }}
                            </template>
                            <template #text>
                                <UserItem
                                    :size="40"
                                    :user="ticket.requester" />
                            </template>
                        </VCard>

                        <TicketParticipantsGroup
                            :owner-can-add="false"
                            :label="$t('Assignees')"
                            :ticket="ticket"
                            :users="ticket.assignees"
                            @on-add-click="openAssigneesSelect" />
                        <TicketParticipantsGroup
                            :label="$t('Observers')"
                            :ticket="ticket"
                            :users="ticket.observers"
                            @on-add-click="openObserversSelect" />
                        <TicketParticipantsGroup
                            :label="$t('Approvals')"
                            :ticket="ticket"
                            :users="ticket.approvals"
                            @on-add-click="openApprovalsSelect" />
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

        <ModalDialog
            ref="addParticipantModal"
            :footer="true"
            :title="participantTitle"
            @on-close="resetModal">
            <UsersMultiselect
                ref="usersSelect"
                :department="filterByDepartmentId"
                @on-users-changed="participantsChanged" />
            <template #actions>
                <VBtn
                    prepend-icon="mdi-content-save"
                    :text="$t('Save')"
                    @click="addParticipants" />
            </template>
        </ModalDialog>
        <ModalDialog
            v-model="openRelevantModal"
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
        </ModalDialog>
        <ConfirmDialog ref="confirmDeleteParticipant" />
    </VSheet>
</template>

<script>
import TicketFiles from './TicketFiles.vue'
import LightBox from './LightBox.vue'
import SimpleBar from 'simplebar-vue'
import ConfirmDialog from '../elements/ConfirmDialog.vue'
import ModalDialog from './ModalDialog.vue'
import UsersMultiselect from '../elements/UsersMultiselect.vue'
import TicketField from '../chunks/TicketField.vue'
import TicketThread from '../chunks/TicketThread.vue'
import TicketActions from '../chunks/TicketActions.vue'
import UserItem from '../chunks/UserItem.vue'
import { formatDate } from '../../js/helpers/moment.js'
import { statusClass, statusColor } from '../../js/helpers/ticketStatus.js'
import { useToast } from 'vue-toastification'
import { PARTICIPANT, STATUSES, TYPES } from '../../js/consts.js'
import RelevantTicketItem from './RelevantTicketItem.vue'
import Pagination from './Pagination.vue'
import TicketParticipantsGroup from './TicketParticipantsGroup.vue'
import Avatar from './Avatar.vue'

const toast = useToast()

export default {
    name: 'TicketTemplate',
    components: {
        Avatar,
        TicketParticipantsGroup,
        Pagination,
        RelevantTicketItem,
        TicketFiles,
        ModalDialog,
        ConfirmDialog,
        UsersMultiselect,
        UserItem,
        TicketActions,
        TicketField,
        TicketThread,
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
            openRelevantModal: false,
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
            this.openRelevantModal = false
            await this.getRelevantTickets()
            this.$store.dispatch('getThread', this.ticket.id)
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
        }
    }

}
</script>

<style lang="scss" scoped>
</style>
