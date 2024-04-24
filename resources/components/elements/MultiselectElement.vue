<template>
    <Multiselect
        :no-options-text="$t('The list is empty')"
        :multiple-label="labelFN" />
</template>

<script>
import Multiselect from '@vueform/multiselect'

export default {
    name: 'MultiselectElement',
    components: {
        Multiselect
    },
    methods: {
        labelFN(val) {
            return this.$tc('{count} elements', { count: val.length })
        }
    }
}
</script>

<style scoped>

</style>
