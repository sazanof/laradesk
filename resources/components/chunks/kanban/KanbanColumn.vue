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
                rounded="pill"
                :content="tickets.total" />
            <VBadge
                :color="term?.length > 0 ? 'error' : 'default'"
                inline
                rounded="pill"
                icon="mdi-magnify"
                class="cursor-pointer"
                @click.stop="showSearch=!showSearch" />
        </template>

        <!-- Список карточек -->
        <VCardText class="h-100 pb-1 px-2">
            <VSheet
                ref="columnContent"
                :style="contentStyle"
                class="fill-height overflow-auto"
                @dragover.prevent
                @drop="onDrop">
                <VTextField
                    v-if="showSearch || term?.length > 0"
                    v-model="term"
                    autocomplete="off"
                    density="compact"
                    variant="outlined"
                    append-inner-icon="mdi-magnify"
                    clearable
                    class="mb-2"
                    @mousedown.stop
                    @dragstart.stop
                    @click.stop />

                <KanbanCard
                    v-for="ticket in tickets.data"
                    :key="ticket.id"
                    :ticket="ticket"
                    @status-changed="$emit('status-changed', $event)"
                    @click="$emit('card-click', ticket)"
                    @drag-start="$emit('card-drag-start', { ticket, statusId: status.status })"
                    @drag-end="$emit('card-drag-end', { ticket, statusId: status.status })" />
            </VSheet>
            <VPagination
                v-if="tickets.last_page > 1"
                density="compact"
                size="small"
                class="pt-1"
                :total-visible="3"
                :length="tickets.last_page"
                @update:model-value="updatePage" />
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
    emits: [ 'drop', 'card-click', 'card-drag-start', 'card-drag-end', 'card-drop', 'status-changed' ],

    data() {
        return {
            showSearch: false,
            page: 1,
            contentHeight: 'auto',
            term: null
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
        },
        activeDepartment() {
            return this.$store.getters['getActiveDepartment']
        }
    },

    watch: {
        async term() {
            await this.updatePage(1)
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
        async updatePage(page) {
            this.page = page
            await this.$store.dispatch('getTicketsByStatus', {
                term: this.term,
                page: this.page,
                limit: 50,
                status: this.status.status,
                department_id: this.activeDepartment.id
            })
        },
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
            const availableHeight = windowHeight - titleRect.bottom - 78 // 32px нижний отступ

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
