<template>
    <VDropdown
        :show="show">
        <slot name="trigger" />
        <template #popper>
            <slot />
        </template>
    </VDropdown>
</template>

<script>
export default {
    name: 'DropdownElement',
    props: {
        show: {
            type: Boolean,
            default: false
        }
    }

}
</script>

<style lang="scss" scoped>

</style>
