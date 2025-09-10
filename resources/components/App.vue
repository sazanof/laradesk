<template>
    <VApp>
        <div
            v-if="visible"
            class="hd-app">
            <Login v-if="!authenticated" />
            <Page
                v-else
                :user="user" />
        </div>
    </VApp>
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
            visible: false,
            ws: null
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
        async authenticated() {
            if (this.authenticated) {
                await this.$store.dispatch('getCounters')
                await this.$store.dispatch('getDepartments')
            } else {
                this.ws.close()
            }
        }
    },
    mounted() {
        this.emitter.on('on-department-changed', async department => {
            await this.$store.dispatch('changeDepartment', department.id).then(async () => {
                await this.$store.dispatch('getCounters')
                this.emitter.emit('after-department-changed', department)
            })
        })
    },
    unmounted() {
        this.emitter.off('on-department-changed')
    },
    async created() {
        this.updateWidth(window.innerWidth)
        window.addEventListener('resize', (e) => {
            this.updateWidth(e.currentTarget.innerWidth)
        })
        this.$store.dispatch('initAppValuesFromHiddenFields')
        await this.$store.dispatch('getUser')
        this.visible = true
    },
    methods: {
        updateWidth(w) {
            this.$store.commit('updateCurrentWidth', w)
        }
    }
}
</script>

<style scoped>
.hd-app {

}
</style>
