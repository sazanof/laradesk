<template>
    <VAutocomplete
        v-model="selectedRoom"
        :label="$t('Room')"
        :no-options-text="$t('The list is empty')"
        :items="filteredRooms"
        :return-object="true"
        item-title="name"
        item-value="id"
        @update:model-value="onSelect($event)"
        @click:clear="onClear($event)">
        <template #item="{props,item}">
            <VListItem v-bind="props">
                <template #title>
                    {{ item.raw.name }}
                </template>
                <template #subtitle>
                    {{ item.raw.level }}, {{ item.raw.description }}
                </template>
            </VListItem>
        </template>
    </VAutocomplete>
</template>

<script>

export default {
    name: 'RoomsMultiselect',
    components: {},
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
        },
        rooms() {
            this.filteredRooms = this.rooms
            this.selectedRoom = this.rooms.find(r => r.id === this.modelValue)
            console.log(this.rooms, this.modelValue)
            this.onSearchChange('')
            console.log('ROOMS')
        }

    },
    mounted() {
        this.filteredRooms = this.rooms
        this.selectedRoom = this.rooms.find(r => r.id === this.modelValue)
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
            this.$emit('update:model-value', o.id)
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
