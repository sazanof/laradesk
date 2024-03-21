<template>
    <div
        class="notifications">
        <NotificationsPanel />
    </div>
</template>

<script>
import playNotificationSound from '../../js/helpers/playNotificationSound.js'
import NotificationsPanel from '../chunks/NotificationsPanel.vue'

export default {
    name: 'NotificationsWrapper',
    components: {
        NotificationsPanel
    },
    data() {
        return {
            lastTicket: null,
            exportLink: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        }
    },
    created() {
        // Subscribe on notifications
        const that = this
        Echo.private(`notifications.${this.user.id}`)
            .listen('.export.finished', (e) => {
                playNotificationSound()
                that.$store.commit('addNotification', {
                    type: 'export',
                    data: e
                })
            })
            .listen('.ticket.new', (e) => {
                playNotificationSound()
                that.$store.commit('addNotification', {
                    type: 'ticket',
                    data: e
                })
            })
    },
    methods: {
        closePopper() {
            console.log('Popper notifications close')
        }
    }
}
</script>

<style scoped lang="scss">
.notifications-inner {
    padding: 10px;
    min-width: 240px
}

</style>
