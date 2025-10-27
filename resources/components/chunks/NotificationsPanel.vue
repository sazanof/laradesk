<template>
    <VMenu
        width="400"
        max-height="600">
        <template #activator="{props}">
            <VBtn
                v-bind="props"
                density="compact"
                icon="mdi-bell"
                :color="hasNew ? 'orange' :'grey'"
                @click="toggle" />
        </template>
        <VSheet>
            <div class="pa-2 text-h6 text-center">
                {{ $t('Notifications') }}
            </div>

            <VList
                v-if="notifications.length > 0"
                max-height="300"
                color="transparent">
                <NotificationAlert
                    v-for="notification in notifications"
                    :key="notification.id"
                    :notification="notification" />
            </VList>
            <div
                v-else
                class="empty">
                {{ $t('Empty') }}
            </div>
            <VSheet
                width="100%"
                class="pa-2 d-flex align-center justify-content--between">
                <VBtn
                    variant="text"
                    color="default"
                    density="comfortable"
                    :text="$t('Close')"
                    prepend-icon="mdi-close"
                    @click="toggle" />
                <VBtn
                    variant="text"
                    color="error"
                    density="comfortable"
                    prepend-icon="mdi-trash-can"
                    :text="$t('Delete all')"
                    @click="deleteAll" />
            </VSheet>
        </VSheet>
    </VMenu>
</template>

<script>
import NotificationAlert from '../elements/NotificationAlert.vue'

export default {
    name: 'NotificationsPanel',
    components: {
        NotificationAlert
    },
    data() {
        return {
            height: 0
        }
    },
    computed: {
        notifications() {
            return this.$store.getters['getUserNotifications']
        },
        hasNew() {
            return this.notifications.filter(n => n?.read_at === null).length > 0
        },
        isOpen() {
            return this.$store.getters['isShowNotifications']
        }
    },

    methods: {
        toggle() {
            this.$store.commit('showNotifications', !this.isOpen)
        },
        deleteAll() {
            this.$store.dispatch('deleteUserNotifications')
        }
    }
}
</script>

<style scoped lang="scss">
.notifications-inner {
    position: absolute;
    top: 70px;
    bottom: 0;
    left: 0;
    right: 0;
}
</style>
