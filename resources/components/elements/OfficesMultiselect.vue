<template>
    <MultiselectElement
        v-model="selectedOffice"
        :options="offices"
        :object="true"
        label="name"
        value-prop="id"
        track-by="id"
        @select="onSelect($event)"
        @clear="onClear($event)" />
</template>

<script>
import MultiselectElement from './MultiselectElement.vue'

export default {
    name: 'OfficesMultiselect',
    components: {
        MultiselectElement
    },
    emits: [ 'on-select', 'on-clear' ],
    data() {
        return {
            selectedOffice: null
        }
    },
    computed: {
        offices() {
            return this.$store.getters['getOffices']
        },
        rooms() {
            return this.$store.getters['getRooms']
        },
        user() {
            return this.$store.getters['getUser']
        }
    },
    async created() {
        await this.getOffices()
        if (this.user.office !== null) {
            this.$store.commit('setRooms', this.offices?.find(o => o.id === this.user.office.id)?.rooms)
            this.selectedOffice = this.user.office
        }

    },
    methods: {
        async getOffices() {
            await this.$store.dispatch('getOffices')
        },
        onSelect(o) {
            this.$emit('on-select', o)
            this.$store.commit('setRooms', o?.rooms)
        },
        onClear() {
            this.$emit('on-clear')
            this.$store.commit('setRooms', [])
        }
    }
}
</script>

<style scoped>

</style>
