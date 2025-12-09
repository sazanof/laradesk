<template>
    <VListItem
        class="border-b py-3"
        :value="field.id"
        :class="{disabled:disabled}">
        <template #title>
            <VChip
                rounded="pill"
                size="small"
                variant="tonal">
                {{ type.name }}
            </VChip>
            <VSheet class="mt-2">
                {{ field.name }}
            </VSheet>
        </template>
        <template #subtitle>
            <slot name="description">
                {{ field.description }}
            </slot>
        </template>
        <template #append>
            <slot name="actions">
                <VBtn
                    variant="tonal"
                    color="primary"
                    :disabled="disabled"
                    icon="mdi-chevron-double-right"
                    @click="$emit('on-field-click', field)" />
            </slot>
        </template>
    </VListItem>
</template>

<script>
import ChevronDoubleRightIcon from 'vue-material-design-icons/ChevronDoubleRight.vue'

export default {
    name: 'FieldItem',
    components: {
        ChevronDoubleRightIcon
    },
    props: {
        field: {
            type: Object,
            required: true
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    emits: [ 'on-field-click' ],
    computed: {
        type() {
            return {
                name: this.$t(this.field.type),
                value: this.field.type
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.form-field {
    padding: calc(var(--padding-box) / 2);
    border-radius: var(--border-radius);
    border: 1px solid var(--bs-border-color);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;

    &:hover {
        background: var(--bs-light);
    }

    .name {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .description {
        font-style: italic;
    }

    .type {
        margin-top: 10px;
        font-size: var(--font-small);
    }

    .actions {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
    }
}
</style>
