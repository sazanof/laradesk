<template>
    <router-view />
</template>

<script>
import SimpleBar from 'simplebar-vue'

export default {
    name: 'ContentItem',
    components: {}
}
</script>

<style lang="scss" scoped>
.content {
    height: calc(100vh - var(--header-height));
}

@media print {
    .content {
        height: auto;
    }
}
</style>
