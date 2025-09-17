<template>
    <VPagination
        rounded="lg"
        density="comfortable"
        active-color="deep-purple"
        :total-visible="11"
        :length="data.last_page"
        @update:model-value="$emit('pagination-change-page', $event)" />
</template>

<script>

export default {
    name: 'Pagination',
    props: {
        limit: {
            type: Number,
            default: 4
        },
        data: {
            type: Object,
            required: true
        }
    },
    emits: [ 'pagination-change-page' ]
}
</script>

<style lang="scss" scoped>
.pagination {
    position: relative;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 6px;
    height: var(--pagination-height);

    ::v-deep(ul) {
        margin: 0;
    }
}
</style>
