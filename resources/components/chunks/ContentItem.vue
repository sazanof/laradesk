<template>
    <SimpleBar class="content">
        <div>
            <router-view />
        </div>
    </SimpleBar>
</template>

<script>
import SimpleBar from 'simplebar-vue'

export default {
    name: 'ContentItem',
    components: {
        SimpleBar
    }
}
</script>

<style lang="scss" scoped>
.content {
    height: calc(100vh - var(--header-height));
}
</style>
