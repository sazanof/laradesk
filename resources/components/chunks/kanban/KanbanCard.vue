<template>
    <VCard
        draggable="true"
        class="mb-3"
        variant="tonal"
        @dragstart="handleDragStart"
        @dragend="handleDragEnd"
        @click="$emit('click', ticket)">
        <template #prepend>
            <VChip
                prepend-icon="mdi-clock"
                size="x-small"
                variant="tonal">
                {{ formattedDate }}
            </VChip>
        </template>
        <template #append>
            <VBtn
                size="small"
                icon="mdi-open-in-new"
                :to="`/admin/tickets/${ticket.id}`"
                target="_blank"
                density="comfortable"
                rounded="pill"
                variant="tonal" />
        </template>
        <VCardActions class="position-absolute bottom-0 right-0">
            <VMenu
                v-model="menuOpened"
                width="240"
                :close-on-content-click="false">
                <template #activator="{props}">
                    <VBtn
                        rounded="pill"
                        density="comfortable"
                        v-bind="props"
                        color="default"
                        icon="mdi-dots-vertical"
                        variant="tonal"
                        size="small" />
                </template>
                <VCard :subtitle="$t('Move to')">
                    <VCardText>
                        <VSelect
                            :items="kanbanStatuses"
                            :label="$t('Status')"
                            @update:model-value="applyStatus" />
                    </VCardText>
                </VCard>
            </VMenu>
        </VCardActions>
        <VCardText class="pa-2">
            <div class="text-body-2 font-weight-medium">
                {{ ticket.subject }}
            </div>

            <div class="text-caption text-medium-emphasis mt-1">
                #{{ ticket.id }}
            </div>

            <!-- Приоритет -->
            <VRow
                align="center"
                class="mt-2"
                dense>
                <!--                <VCol cols="auto">
                                    <VChip
                                        size="x-small"
                                        :color="priorityColor"
                                        text-color="white">
                                        Приоритет {{ ticket.priority }}
                                    </VChip>
                                </VCol>-->

                <!-- Дата -->
            </VRow>

            <!-- Отдел -->
            <div
                v-if="ticket.department?.name"
                class="text-caption text-truncate mt-2">
                {{ ticket.department.name }}
            </div>

            <!-- Исполнители -->
            <VSheet class="d-flex mt-2">
                <Avatar
                    v-if="ticket.requester"
                    :user="ticket.requester"
                    class="mr-n5"
                    :style="`z-index:100` "
                    :size="28" />
                <div
                    v-if="hasAssignees">
                    <Avatar
                        v-for="(assignee, index) in ticket.assignees.slice(0, 3)"
                        :key="assignee.id"
                        :user="assignee"
                        class="mr-n5"
                        :style="`z-index:${ticket.assignees.length - index}` "
                        :size="28" />
                    <span
                        v-if="ticket.assignees.length > 3"
                        class="text-caption ml-1">
                        +{{ ticket.assignees.length - 3 }}
                    </span>
                </div>
            </VSheet>
        </VCardText>
    </VCard>
</template>

<script>
import Avatar from '../Avatar.vue'
import { statusColor } from '../../../js/helpers/ticketStatus.js'
import { formatDate } from '../../../js/helpers/moment.js'

export default {
    name: 'KanbanCard',
    components: { Avatar },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    emits: [ 'click', 'drag-start', 'drag-end', 'status-changed' ],
    data() {
        return {
            menuOpened: false
        }
    },
    computed: {
        kanban() {
            return this.$store.getters['getKanban']
        },
        kanbanStatuses() {
            return this.kanban.map(k => {
                return {
                    title: this.$t(k.status.label),
                    value: k.status.status
                }
            })
        },
        priorityColor() {
            const colors = {
                1: 'green',
                2: 'yellow',
                3: 'orange',
                4: 'red'
            }
            return colors[this.ticket.priority] || 'grey'
        },
        formattedDate() {
            if (!this.ticket.created_at) return ''
            const date = new Date(this.ticket.created_at)
            return formatDate(date, 'DD.MM.YYYY HH:mm')
        },
        hasAssignees() {
            return this.ticket.assignees && this.ticket.assignees.length > 0
        }
    },
    methods: {
        applyStatus(s) {
            this.$emit('status-changed', { ticket: this.ticket, status: s })
            this.menuOpened = false
        },
        color(status) {
            return statusColor(status)
        },
        handleDragStart(e) {
            this.$emit('drag-start', this.ticket)
            e.dataTransfer.setData('application/json', JSON.stringify(this.ticket))
            e.dataTransfer.effectAllowed = 'move'
            this.$el.classList.add('dragging')
        },

        handleDragEnd() {
            this.$emit('drag-end', this.ticket)
            this.$el.classList.remove('dragging')
        },

        getInitials(name) {
            if (!name) return '?'
            return name
                .split(' ')
                .map(part => part[0])
                .join('')
                .toUpperCase()
                .slice(0, 2)
        }
    }
}
</script>

<style scoped>

</style>
