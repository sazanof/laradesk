<template>Tickets {{ criteria }}</template>

<script>
export default {
    name: 'Tickets',
    props: {
        criteria: {
            type: String,
            default: 'all'
        }
    }
}
</script>

<style scoped>

</style>
