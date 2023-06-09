<template>
    <div class="ticket">
        {{ id }}
    </div>
</template>

<script>
export default {
    name: 'UserTicket',
    computed: {
        id() {
            return parseInt(this.$route.params.number)
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket {
    padding: var(--padding-box);
}
</style>
