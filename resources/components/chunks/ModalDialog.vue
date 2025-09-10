<script>
export default {
    name: 'ModalDialog',
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: null
        },
        subtitle: {
            type: String,
            default: null
        }
    },
    emits: [ 'update:model-value' ],
    data() {
        return {
            opened: false
        }
    },
    watch: {
        opened() {
            this.$emit('update:model-value', this.opened)
        },
        modelValue() {
            this.opened = this.modelValue
        }
    },
    created() {
        this.opened = this.modelValue
    }
}
</script>

<template>
    <VDialog
        v-model="opened"
        width="700"
        persistent>
        <VCard
            :title="title"
            :subtitle="subtitle">
            <template #text>
                <slot />
            </template>
            <template #append>
                <VBtn
                    color="default"
                    variant="plain"
                    icon="mdi-close"
                    @click="opened = false" />
            </template>
            <template
                v-if="$slots.actions"
                #actions>
                <slot name="actions" />
            </template>
        </VCard>
    </VDialog>
</template>

<style scoped>

</style>
