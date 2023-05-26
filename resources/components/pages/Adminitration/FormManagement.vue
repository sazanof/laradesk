<template>Form Man {{ id }}</template>

<script>
export default {
    name: 'FormManagement',
    data() {
        return {
            category: null
        }
    },
    computed: {
        id() {
            return parseInt(this.$route.params.id)
        },
        fields() {
            return this.$store.getters['getFields']
        }
    },
    created() {
        this.getFields()
        this.getCategory()
    },
    methods: {
        getFields() {
            if (this.fields.length === 0) {
                this.$store.dispatch('getFields')
            }
        },
        async getCategory() {
            return this.$store.dispatch('getCategoryWithFields', this.id)
        }
    }
}
</script>

<style scoped>

</style>
