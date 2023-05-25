<template>
    <div
        class="content"
        data-simplebar>
        <router-view />
    </div>
</template>

<script>
export default {
    name: 'ContentItem'
}
</script>

<style lang="scss" scoped>
.content {
    height: calc(100vh - var(--header-height));
}
</style>
