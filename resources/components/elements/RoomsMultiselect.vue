<template>
    <Multiselect
        v-model="selectedRoom"
        :no-options-text="$t('The list is empty')"
        :searchable="true"
        :filterResults="false"
        :options="filteredRooms"
        :object="true"
        label="name"
        value-prop="id"
        track-by="id"
        @search-change="onSearchChange"
        @select="onSelect($event)"
        @clear="onClear($event)">
        <template #option="{option}">
            <div class="option">
                <div class="option-title">
                    {{ option.name }}
                </div>
                <span class="option-text">{{ option.level }}, {{ option.description }}</span>
            </div>
        </template>
    </Multiselect>
</template>

<script>
import Multiselect from '@vueform/multiselect'

export default {
    name: 'RoomsMultiselect',
    components: {
        Multiselect
    },
    props: {
        modelValue: {
            type: Number,
            default: null
        }
    },
    emits: [ 'on-select', 'on-clear', 'update:model-value' ],
    data() {
        return {
            selectedRoom: null,
            filteredRooms: []
        }
    },
    computed: {
        rooms() {
            return this.$store.getters['getRooms']
        },
        user() {
            return this.$store.getters['getUser']
        }
    },
    watch: {
        modelValue() {
            this.selectedRoom = this.rooms.find(r => r.id === this.modelValue)
            console.log(this.selectedRoom)
        },
        rooms() {
            this.filteredRooms = this.rooms
            this.selectedRoom = this.rooms.find(r => r.id === this.modelValue)
            console.log(this.rooms, this.modelValue)
            this.onSearchChange('')
            console.log('ROOMS')
        }

    },
    created() {
        this.emitter.on('clear-room-value', () => {
            this.selectedRoom = null
            this.$emit('on-clear')
        })

    },
    methods: {
        onSelect(o) {
            this.$emit('on-select', o)
            this.$emit('update:model-value', o)
        },
        onClear() {
            this.$emit('on-clear')
        },
        onSearchChange(term) {
            this.filteredRooms = Object.assign(this.rooms.filter(room => room.name.startsWith(term)), {})

        }
    }
}
</script>

<style scoped lang="scss">
.option {
    .option-title {
        font-weight: bold;
    }

    .option-text {
        font-size: var(--font-small);
    }
}
</style>
