<template>
    <VTooltip placement="right">
        <template #popper>
            <div class="help">
                <slot />
            </div>
        </template>
        <slot name="trigger">
            <HelpCircleIcon :size="18" />
        </slot>
    </VTooltip>
</template>

<script>
import HelpCircleIcon from 'vue-material-design-icons/HelpCircle.vue'

export default {
    name: 'HelpComment',
    components: {
        HelpCircleIcon
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
    padding: var(--padding-box);
}

.help-trigger {
    cursor: pointer;
    color: var(--bs-purple)
}
</style>
