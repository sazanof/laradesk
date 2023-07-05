<template>
    <div
        :class="`tip alert alert-${type}`">
        <div class="icon">
            <slot name="icon">
                <LightbulbOnIcon :size="48" />
            </slot>
        </div>
        <div class="content">
            <slot />
        </div>
    </div>
</template>

<script>
import LightbulbOnIcon from 'vue-material-design-icons/LightbulbOn.vue'

export default {
    name: 'TipElement',
    components: {
        LightbulbOnIcon
    },
    props: {
        type: {
            type: String,
            default: 'info'
        }
    }
}
</script>

<style lang="scss" scoped>
.tip {
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--padding-box);

    .icon {
        margin-right: 10px;
    }

    .content {
    }
}
</style>
