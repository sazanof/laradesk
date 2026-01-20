<script>
import KanbanBoard from '../../components/chunks/kanban/KanbanBoard.vue'
import ConfirmDialog from '../../components/elements/ConfirmDialog.vue'

export default {
    name: 'KanbanPage',
    components: {
        ConfirmDialog,
        KanbanBoard
    },
    data() {
        return {
            loading: false,
            kanban: [],
            fromKanban: null,
            toKanban: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        department() {
            return this.$store.getters['getActiveDepartment']
        },
        hasActiveDepartment() {
            return this.user?.departments?.find(d => d.department.id === this.department?.id)
        },
        isAdminAndHasDepartment() {
            return this.user.is_admin && this.hasActiveDepartment
        }
    },
    watch: {
        async isAdminAndHasDepartment(v) {
            if (v) {
                await this.getAdminKanban()
            }
        }
    },
    async mounted() {
        if (this.isAdminAndHasDepartment) {
            await this.getAdminKanban()
        }
    },
    methods: {
        async updateTicketStatus(ticket, newStatus) {
            if (this.fromKanban !== null && this.toKanban !== null) {
                const index = this.fromKanban.tickets.data.findIndex(t => t.id === ticket.id)
                console.log(ticket.status, newStatus)
                if (index > -1 && ticket.status !== newStatus) {
                    this.fromKanban.tickets.data[index].status = newStatus
                    const updatedTicket = { ...{}, ...this.fromKanban.tickets.data[index] }
                    this.toKanban.tickets.data.unshift(updatedTicket)
                    this.fromKanban.tickets.data = this.fromKanban.tickets.data.filter((t, i) => i !== index)
                }
            }
        },
        async getAdminKanban() {
            this.loading = true
            this.kanban = await this.$store.dispatch('getAdminKanban', {
                department_id: this.department.id
            })
            this.loading = false
        },
        handleStatusUpdate({ ticket, newStatus }) {
            // Отправка на сервер
            this.updateTicketStatus(ticket, newStatus)
        },
        openTicketModal() {

        },
        async handleBeforeDrop({ ticket, oldStatus, newStatus }) {
            if (oldStatus === newStatus) return
            const ok = await this.$refs.confirm.show({
                title: this.$t('Move card?'),
                message: this.$t('Are you sure you want  change status?'),
                okButton: this.$t('Move')
            }).catch(e => {
                console.log('Cancel dragging')
                return false
            })
            if (ok) {
                this.toKanban = this.kanban.find(k => k.status.status === newStatus) // data.ticket.status now is updated
            }
        },
        handleDragStart(data) {
            console.log(data)
            const status = data.ticket.status
            this.fromKanban = this.kanban.find(k => k.status.status === status)
            console.log('Начало перетаскивания:', data)
        },

        handleDragEnd(data) {
            console.log('Конец перетаскивания:', data)
            //this.fromKanban = this.kanban.find(k=>k.status.status === status)

        }
    }
}
</script>

<template>
    <VEmptyState v-if="loading">
        <template #media>
            <VProgressCircular indeterminate />
        </template>
    </VEmptyState>
    <VSheet v-else>
        <KanbanBoard
            ref="kanban"
            :kanban="kanban"
            :before-drop="handleBeforeDrop"
            @update="handleStatusUpdate"
            @card-click="openTicketModal"
            @card-drag-start="handleDragStart"
            @card-drag-end="handleDragEnd" />
        <ConfirmDialog ref="confirm" />
    </VSheet>
</template>

<style scoped lang="scss">

</style>
