<template>
    <div class="pagination">
        <Bootstrap5Pagination
            :limit="limit"
            :data="data"
            :keep-length="true"
            @pagination-change-page="$emit('pagination-change-page', $event)" />
    </div>
</template>

<script>
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

export default {
    name: 'Pagination',
    components: {
        Bootstrap5Pagination
    },
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
