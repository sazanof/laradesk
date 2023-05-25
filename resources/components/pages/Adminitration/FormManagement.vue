<template>Form Man {{ id }}</template>

<script>
export default {
    name: 'FormManagement',
    computed: {
        id() {
            return parseInt(this.$route.params.id)
        }
    }
}
</script>

<style scoped>

</style>
