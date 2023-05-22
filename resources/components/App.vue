<template>
    <div class="hd-app">
        <Login v-if="!authenticated" />
    </div>
</template>

<script>
import Login from './pages/Login.vue'

export default {
    name: 'App',
    components: {
        Login
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        authenticated() {
            return this.$store.getters['isAuthenticated']
        }
    },
    async created() {
        await this.$store.dispatch('getUser')
    }
}
</script>

<style scoped>
.hd-app {

}
</style>
