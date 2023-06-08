<template>{{ number }}</template>

<script>
export default {
    name: 'UserTicket',
    computed: {
        number() {
            return this.$route.params.number
        }
    }
}
</script>

<style scoped>

</style>
