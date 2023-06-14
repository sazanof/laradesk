<template>
    <div
        v-if="visible"
        class="hd-app">
        <Login v-if="!authenticated" />
        <Page
            v-else
            :user="user" />
    </div>
</template>

<script>
import Page from './pages/Page.vue'
import Login from './pages/Login.vue'

export default {
    name: 'App',
    components: {
        Login,
        Page
    },
    data() {
        return {
            visible: false
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        authenticated() {
            return this.$store.getters['isAuthenticated']
        },
        isAdmin() {
            return this.$store.getters['isAdmin']
        }
    },
    watch: {
        '$route.path': {
            //Set default url to admin or user
            handler: function (path) {
                if (this.isAdmin && path === '/') {
                    this.$router.push('/admin/tickets')
                }
            },
            deep: true,
            immediate: true
        },
        async authenticated() {
            if (this.authenticated) {
                await this.$store.dispatch('getCounters')
            }
        }
    },
    async created() {
        this.$store.dispatch('initAppValuesFromHiddenFields')
        await this.$store.dispatch('getUser')
        this.visible = true
    }
}
</script>

<style scoped>
.hd-app {

}
</style>
