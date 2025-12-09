<template>
    <NotificationsPanel />
</template>

<script>
import playNotificationSound from '../../js/helpers/playNotificationSound.js'
import NotificationsPanel from '../chunks/NotificationsPanel.vue'
import { createInfoNotification } from '../../js/helpers/notificationHelper.js'

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
        Echo.private('users.' + this.user.id)
            .notification(async notification => {
                console.log(notification)
                await playNotificationSound()
                this.$store.commit('addNotification', createInfoNotification(notification.title))
                that.$store.commit('addUserNotification', notification)
                await this.$store.dispatch('getCounters')
                this.emitter.emit('on-notification-received', notification)
            })
    }
}
</script>

<style scoped lang="scss">
</style>
