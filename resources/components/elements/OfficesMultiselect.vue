<template>
    <MultiselectElement
        v-model="selectedOffice"
        :options="offices"
        :object="true"
        label="name"
        value-prop="id"
        track-by="id"
        @select="$emit('on-select', $event)"
        @clear="$emit('on-clear')" />
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
            return this.getOffices()
        }
    },
    methods: {
        getOffices() {
            const offices = this.$store.getters['getOffices']
            if (offices === null) {
                this.$store.dispatch('getOffices').then(() => {
                    return this.$store.getters['getOffices']
                })

            } else {
                return offices
            }
        }
    }
}
</script>

<style scoped>

</style>
