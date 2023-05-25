<template>
    <Popper
        offset-distance="5"
        :show="show"
        :arrow="true">
        <slot name="trigger" />
        <template #content>
            <slot />
        </template>
    </Popper>
</template>

<script>
import Popper from 'vue3-popper'

export default {
    name: 'DropdownElement',
    components: {
        Popper
    },
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
