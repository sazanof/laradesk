<template>
    <Popper
        :arrow="true"
        :hover="true"
        placement="auto">
        <template #content>
            <div class="help">
                <slot />
            </div>
        </template>
        <div class="help-trigger">
            <slot name="trigger">
                <HelpCircleIcon :size="18" />
            </slot>
        </div>
    </Popper>
</template>

<script>
import Popper from 'vue3-popper'
import HelpCircleIcon from 'vue-material-design-icons/HelpCircle.vue'

export default {
    name: 'HelpComment',
    components: {
        HelpCircleIcon,
        Popper
    },
    data() {
        return {
            helpOpened: false
        }
    }
}
</script>

<style lang="scss" scoped>
.help {
    padding: var(--padding-box)
}
</style>
