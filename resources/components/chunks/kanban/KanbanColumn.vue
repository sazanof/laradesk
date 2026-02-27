<template>
    <VCard
        ref="columnCard"
        min-width="320"
        width="320"
        variant="tonal"
        :color="color"
        rounded="lg">
        <!-- Заголовок колонки -->
        <template #title>
            <div :class="$vuetify.theme.name === 'light' ? 'text-black' : 'text-white'">
                {{ statusText }}
            </div>
        </template>
        <template #append>
            <VBadge
                :color="color"
                inline
                :content="tickets.total" />
        </template>

        <!-- Список карточек -->
        <VCardText class="h-100">
            <VSheet
                ref="columnContent"
                :style="contentStyle"
                class="fill-height overflow-auto"
                @dragover.prevent
                @drop="onDrop">
                <KanbanCard
                    v-for="ticket in tickets.data"
                    :key="ticket.id"
                    :ticket="ticket"
                    @click="$emit('card-click', ticket)"
                    @drag-start="$emit('card-drag-start', { ticket, statusId: status.status })"
                    @drag-end="$emit('card-drag-end', { ticket, statusId: status.status })" />
            </VSheet>
        </VCardText>
    </VCard>
</template>

<script>
import KanbanCard from './KanbanCard.vue'
import { statusClass, statusColor } from '../../../js/helpers/ticketStatus.js'

export default {
    name: 'KanbanColumn',
    components: {
        KanbanCard
    },
    props: {
        status: {
            type: Object,
            required: true
        },
        tickets: {
            type: Object,
            required: true
        }
    },
    emits: [ 'drop', 'card-click', 'card-drag-start', 'card-drag-end', 'card-drop' ],

    data() {
        return {
            contentHeight: 'auto'
        }
    },

    computed: {
        color() {
            return statusColor(this.status.status)
        },
        statusText() {
            return this.$t(`status_${statusClass(this.status.status)}`)
        },
        contentStyle() {
            return {
                maxHeight: this.contentHeight
            }
        }
    },

    mounted() {
        this.calculateHeight()
        window.addEventListener('resize', this.calculateHeight)
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.calculateHeight)
    },

    methods: {
        onDrop(e) {
            e.preventDefault()
            try {
                const ticketData = JSON.parse(e.dataTransfer.getData('application/json'))
                this.$emit('drop', {
                    ticket: ticketData,
                    statusId: this.status.id || this.status.status,
                    event: e
                })
            } catch (error) {
                console.warn('Ошибка при обработке drop:', error)
            }
        },

        calculateHeight() {
            if (!this.$refs.columnCard || !this.$el) return

            // Получаем канбан-контейнер (родительский элемент)
            const kanbanContainer = this.$el.closest('.kanban-container')
            if (!kanbanContainer) return

            // Получаем видимую область окна
            const windowHeight = window.innerHeight
            const containerRect = kanbanContainer.getBoundingClientRect()

            // Находим позицию заголовка колонки
            const titleElement = this.$refs.columnCard.$el.querySelector('.v-card-title')
            if (!titleElement) return

            const titleRect = titleElement.getBoundingClientRect()

            // Вычисляем доступную высоту от заголовка до низа окна
            // Минус отступы для комфортного просмотра
            const availableHeight = windowHeight - titleRect.bottom - 58 // 32px нижний отступ

            // Но не больше, чем высота от заголовка до низа контейнера канбана
            const maxContainerHeight = containerRect.bottom - titleRect.bottom - 16

            // Берем минимальную из доступных высот
            const finalHeight = Math.min(availableHeight, maxContainerHeight)

            // Устанавливаем минимальную высоту 300px и максимальную 700px
            this.contentHeight = `${Math.min(Math.max(300, finalHeight), 1700)}px`

            // Дебаг логи
            //console.log('Window height:', windowHeight)
            //console.log('Title bottom:', titleRect.bottom)
            //console.log('Available:', availableHeight)
            //console.log('Max container:', maxContainerHeight)
            //console.log('Final height:', finalHeight)
        }
    }
}
</script>
