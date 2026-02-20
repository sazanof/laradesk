<script>
export default {
    name: 'CheckBoxList',
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    emits: [ 'update:model-value' ],
    data() {
        return {
            dataField: []
        }
    },
    computed: {
        options() {
            try {
                return JSON.parse(this.field.options)
            } catch (e) {
                return null
            }
        },
        fields() {
            try {
                return this.options?.fields
            } catch (e) {
                return null
            }
        }
    },
    watch: {},
    created() {
        this.dataField = this.modelValue
    },
    methods: {
        updateValue() {
            this.$emit('update:model-value', this.dataField)
        }
    }
}
</script>

<template>
    <VSheet v-if="options">
        <VCheckboxBtn
            v-for="item in fields"
            :key="item"
            v-model="dataField"
            multiple
            :value="item.name"
            inline
            :label="item.name ?? 'Unknown'"
            @update:model-value="updateValue" />
    </VSheet>
    <VAlert v-else>
        Wrong options data. Please contact administrator
    </VAlert>
</template>

<style scoped lang="scss">

</style>
