<template>
    <VSheet
        class="notifications">
        <NotificationsPanel />
    </VSheet>
</template>

<script>
import { useToast } from 'vue-toastification'
import playNotificationSound from '../../js/helpers/playNotificationSound.js'
import NotificationsPanel from '../chunks/NotificationsPanel.vue'

const toast = useToast()

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
                await playNotificationSound()
                toast.info(notification.title)
                that.$store.commit('addUserNotification', notification)
                await this.$store.dispatch('getCounters')
                this.emitter.emit('on-notification-received', notification)
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
