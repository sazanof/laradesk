<template>
    <div class="filter">
        <div class="name">
            {{ $t('Filter') }}
        </div>
    </div>
</template>

<script>
export default {
    name: 'TicketsFilter',
    props: {
        filter: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            open: false
        }
    }
}
</script>

<style lang="scss" scoped>
.filter {
    position: sticky;
    background: var(--bs-white);
    top: 0;
    z-index: 20;
    border-bottom: 1px solid var(--bs-border-color);
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 10px;
    background: var(--bs-light);

    .name {
        margin-right: 6px;
        font-weight: bold;
        color: #777
    }
}
</style>
