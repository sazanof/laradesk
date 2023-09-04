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
                this.connect()
            } else {
                this.ws.close()
            }
        }
    },
    async created() {
        this.$store.dispatch('initAppValuesFromHiddenFields')
        await this.$store.dispatch('getUser')
        this.visible = true
    },
    methods: {
        connect() {
            if (!this.authenticated) return false
            const that = this
            this.ws = new WebSocket(`${this.wsUrl}?user_id=${this.user.id}`)
            console.log(this.ws)
            this.ws.onopen = () => {
                this.$store.commit('updateConnectionState', true)
                this.$store.commit('updateConnectingState', false)
                console.log('[ws connected successfully]')
            }

            this.ws.onmessage = function (event) {
                const data = JSON.parse(event.data)
                console.log('[message] Data received from server', data)
                if (data.conn_id) {
                    that.$store.commit('updateConnectionId', data.conn_id)
                }
                if (data.noty) {
                    that.emitter.emit('notification.received', data)

                }
            }

            this.ws.onclose = function (event) {
                if (event.wasClean) {
                    console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`)
                } else {
                    that.$store.commit('updateConnectionState', false)
                }
                that.$store.commit('updateConnectingState', false)
                setTimeout(() => {
                    that.$store.commit('updateConnectingState', true)
                    setTimeout(that.connect, 3000)
                }, 800)

            }

            this.ws.onerror = function (error) {
                console.log('[ws error]')
                if (this.ws !== undefined) this.ws.close()
            }
        }
    }
}
</script>

<style scoped>
.hd-app {

}
</style>
