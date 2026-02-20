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
    <VContainer
        v-if="options"
        class="pa-0">
        <VRow>
            <VCol
                v-for="item in fields"
                :key="item"
                class="d-inline"
                md="3"
                sm="6">
                <VCheckboxBtn
                    v-model="dataField"
                    density="comfortable"
                    class="mr-4"
                    multiple
                    :value="item.name"
                    :label="item.name ?? 'Unknown'"
                    @update:model-value="updateValue" />
            </VCol>
        </VRow>
    </VContainer>
    <VAlert v-else>
        Wrong options data. Please contact administrator
    </VAlert>
</template>

<style scoped lang="scss">

</style>
