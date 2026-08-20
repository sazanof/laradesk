<script>
import UserItem from './UserItem.vue'
import { STATUSES } from '../../js/consts.js'
import ConfirmDialog from '../elements/ConfirmDialog.vue'

export default {
    name: 'TicketParticipantsGroup',
    components: { ConfirmDialog, UserItem },
    props: {
        ticket: {
            type: Object,
            required: true
        },
        users: {
            type: Array,
            required: true
        },
        label: {
            type: String,
            default: null
        },
        ownerCanAdd: {
            type: Boolean,
            default: true
        }
    },
    emits: [ 'on-add-click', 'on-delete-click' ],
    computed: {
        current_user() {
            return this.$store.getters['getUser']
        },
        isAdmin() {
            return this.current_user.is_admin && this.$store.getters.userBelongsToDepartment(this.ticket.department_id)
        },
        iAmOwner() {
            return this.current_user.id === this.ticket.user_id
        },
        belongsToActiveDepartment() {
            return this.$store.getters.userBelongsToDepartment(this.ticket.department_id)
        },
        ownerCanAddParticipant() {
            return (this.isAdmin || this.iAmOwner) && (this.ticket.status !== STATUSES.CLOSED && this.ticket.status !== STATUSES.SOLVED)
        }
    },
    methods: {
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
                if (this.isAdmin) {
                    await this.$store.dispatch('removeParticipant', data)
                    await this.$store.dispatch('getTicket', this.ticket.id)
                } else if (this.current_user.id === this.ticket.user_id) {
                    await this.$store.dispatch('removeParticipantFromTicketOwner', data)
                    await this.$store.dispatch('getUserTicket', this.ticket.id)
                }
                this.disabled = false
            }
        }
    }
}
</script>

<template>
    <VCard
        variant="text"
        :title="label">
        <template #append>
            <VBtn
                v-if="belongsToActiveDepartment || (iAmOwner && ownerCanAdd)"
                icon="mdi-plus"
                size="small"
                density="comfortable"
                variant="tonal"
                color="primary"
                rounded="pill"
                @click="$emit('on-add-click')" />
        </template>
        <template #text>
            <UserItem
                v-for="_user in users"
                :key="_user.id"
                :size="40"
                :show-email="false"
                :user="_user">
                <template #actions>
                    <VListItem
                        v-if="ownerCanAddParticipant"
                        base-color="error"
                        size="small"
                        density="comfortable"
                        prepend-icon="mdi-trash-can"
                        :title="$t('Delete')"
                        @click.stop="deleteParticipant(_user)" />
                </template>
            </UserItem>
            <div
                v-if="users.length === 0"
                class="text-subtitle-2 opacity-50">
                {{ $t('No participants') }}
            </div>
            <ConfirmDialog ref="confirmDeleteParticipant" />
        </template>
    </VCard>
</template>

<style scoped lang="scss">

</style>
