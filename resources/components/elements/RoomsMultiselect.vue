<template>
    <MultiselectElement
        v-model="selectedRoom"
        :searchable="true"
        :filterResults="false"
        :options="filteredRooms"
        :object="true"
        label="name"
        value-prop="id"
        track-by="id"
        @search-change="onSearchChange"
        @select="onSelect($event)"
        @clear="onClear($event)" />
</template>

<script>
import MultiselectElement from './MultiselectElement.vue'

export default {
    name: 'RoomsMultiselect',
    components: {
        MultiselectElement
    },
    emits: [ 'on-select', 'on-clear' ],
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
        rooms() {
            this.filteredRooms = this.rooms
        }
    },
    created() {
        this.emitter.on('clear-room-value', () => {
            this.selectedRoom = null
            this.$emit('on-clear')
        })
        this.selectedRoom = this.user.room
    },
    methods: {
        onSelect(o) {
            this.$emit('on-select', o)
        },
        onClear() {
            this.$emit('on-clear')
        },
        onSearchChange(term) {
            this.$nextTick(() => {
                this.filteredRooms = Object.assign(this.rooms.filter(room => room.name.startsWith(term)), {})
            })

        }
    }
}
</script>

<style scoped>

</style>
