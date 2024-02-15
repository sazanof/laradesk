<template>
    <div class="actions-line">
        <slot />
    </div>
</template>

<script>
export default {
    name: 'SubSidebar'
}
</script>

<style scoped lang="scss">
.actions-line {
    border-right: 1px solid var(--bs-border-color);
    max-width: 300px;
}
</style>
