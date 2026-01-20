<template>
    <VCard
        draggable="true"
        class="mb-3"
        variant="tonal"
        @dragstart="handleDragStart"
        @dragend="handleDragEnd"
        @click="$emit('click', ticket)">
        <VCardText class="pa-3">
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
                <VCol cols="auto">
                    <VChip
                        size="x-small"
                        :color="priorityColor"
                        text-color="white">
                        Приоритет {{ ticket.priority }}
                    </VChip>
                </VCol>

                <!-- Дата -->
                <VCol cols="auto">
                    <VChip
                        size="x-small"
                        variant="outlined">
                        {{ formattedDate }}
                    </VChip>
                </VCol>
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
import Avatar from '@/components/chunks/Avatar.vue'

export default {
    name: 'KanbanCard',
    components: { Avatar },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    emits: [ 'click', 'drag-start', 'drag-end' ],
    computed: {
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
            return date.toLocaleDateString('ru-RU')
        },
        hasAssignees() {
            return this.ticket.assignees && this.ticket.assignees.length > 0
        }
    },
    methods: {
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
.kanban-card {
    cursor: grab;
    transition: all 0.2s ease;
    user-select: none;
}

.kanban-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.kanban-card:active {
    cursor: grabbing;
}

.kanban-card.dragging {
    opacity: 0.5;
}
</style>
