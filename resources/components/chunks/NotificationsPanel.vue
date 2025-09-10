<template>
    <div>
        <VBtn
            density="compact"
            icon="mdi-bell"
            :color="hasNew ? 'orange' :'grey'"
            @click="toggle" />


        <Teleport to="body">
            <VNavigationDrawer
                v-model="isOpen"
                color="deep-purple"
                width="340"
                temporary
                class="top-0 fill-height overflow-hidden"
                location="right">
                <template #prepend>
                    <VBtn
                        density="comfortable"
                        :text="$t('Close')"
                        prepend-icon="mdi-close"
                        @click="toggle" />
                    <VBtn
                        prepend-icon="mdi-trash-can"
                        :text="$t('Delete all')"
                        @click="deleteAll" />
                    <div class="text-h6 text-center">
                        {{ $t('Notifications') }}
                    </div>
                </template>
                <template #default>
                    <SimpleBar
                        ref="inner"
                        class="notifications-inner">
                        <VSheet
                            v-if="notifications.length > 0"
                            color="transparent">
                            <NotificationAlert
                                v-for="notification in notifications"
                                :key="notification.id"
                                :notification="notification" />
                        </VSheet>
                        <div
                            v-else
                            class="empty">
                            {{ $t('Empty') }}
                        </div>
                    </SimpleBar>
                </template>
            </VNavigationDrawer>
        </Teleport>
    </div>
</template>

<script>
import SimpleBar from 'simplebar-vue'
import BellIcon from 'vue-material-design-icons/Bell.vue'
import NotificationAlert from '../elements/NotificationAlert.vue'

export default {
    name: 'NotificationsPanel',
    components: {
        NotificationAlert,
        BellIcon,
        SimpleBar
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
    mounted() {
        console.log(this.$refs)
        const top = this.$refs.inner.offsetTop
        this.height = window.outerHeight - top
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
