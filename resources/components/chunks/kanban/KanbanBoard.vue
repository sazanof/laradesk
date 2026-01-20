<template>
    <VSheet
        ref="container"
        class="kanban-container d-flex overflow-x-auto"
        @mousedown="startDrag"
        @mousemove="onDrag"
        @mouseup="stopDrag"
        @mouseleave="stopDrag">
        <KanbanColumn
            v-for="kan in kanban"
            :key="kan.status"
            :status="kan.status"
            :tickets="kan.tickets"
            @drop="handleDrop"
            @card-click="$emit('card-click', $event)"
            @card-drag-start="$emit('card-drag-start', $event)"
            @card-drag-end="$emit('card-drag-end', $event)" />
    </VSheet>
</template>

<script>
import KanbanColumn from './KanbanColumn.vue'

export default {
    name: 'KanbanBoard',
    components: {
        KanbanColumn
    },
    props: {
        kanban: {
            type: Array,
            required: true,
            default: () => []
        },
        beforeDrop: {
            type: Function,
            default: null
        }
    },
    emits: [ 'update', 'card-click', 'card-drag-start', 'card-drag-end' ],

    data() {
        return {
            isDragging: false,
            startX: 0,
            scrollLeft: 0
        }
    },

    methods: {
        async handleDrop({ ticket, statusId, event }) {
            console.log('handleDrop Board')
            if (this.beforeDrop) {
                const result = await Promise.resolve(
                    this.beforeDrop({
                        ticket,
                        oldStatus: ticket.status,
                        newStatus: statusId
                    })
                )

                if (result === false) {
                    return
                }
            }

            this.$emit('update', {
                ticket: ticket,
                newStatus: statusId
            })
        },

        getContainerEl() {
            return this.$refs.container?.$el || null
        },

        startDrag(e) {
            const containerEl = this.getContainerEl()
            if (!containerEl) return

            // Проверяем, что клик НЕ на карточке (.kanban-card или её дочерних элементах)
            // И что это левая кнопка мыши
            if (e.button !== 0) return

            const target = e.target
            const isCardOrChild = target.closest('.kanban-card') ||
                target.closest('[draggable="true"]') ||
                target.hasAttribute('draggable')

            // Если клик на карточке или перетаскиваемом элементе - НЕ включаем drag-скролл
            if (isCardOrChild) {
                return
            }

            // Также проверяем, что не началось перетаскивание карточки
            if (e.dataTransfer?.effectAllowed === 'move') {
                return
            }

            // Только если клик на пустом месте контейнера - включаем drag-скролл
            this.isDragging = true
            const rect = containerEl.getBoundingClientRect()
            this.startX = e.pageX - rect.left
            this.scrollLeft = containerEl.scrollLeft
            containerEl.style.cursor = 'grabbing'
            e.preventDefault()
            e.stopPropagation() // Чтобы не мешать другим обработчикам
        },

        onDrag(e) {
            if (!this.isDragging || !this.$refs.container) return

            const container = this.$refs.container.$el
            const rect = container.getBoundingClientRect()
            const x = e.pageX - rect.left
            const walk = (x - this.startX) * 1.5 // Множитель для скорости скролла
            container.scrollLeft = this.scrollLeft - walk
        },

        stopDrag() {
            this.isDragging = false
            if (this.$refs.container) {
                this.$refs.container.$el.style.cursor = 'grab'
            }
        }
    }
}
</script>

<style scoped>
.kanban-container {
    gap: 16px;
    min-height: 600px;
    padding: 16px;
    scrollbar-width: thin;
    cursor: grab;
    user-select: none;
}

.kanban-container:active {
    cursor: grabbing;
}

.kanban-container::-webkit-scrollbar {
    height: 8px;
}

.kanban-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.kanban-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
</style>
