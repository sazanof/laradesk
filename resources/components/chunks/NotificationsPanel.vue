<template>
    <div class="z-3">
        <div
            class="trigger"
            @click="toggle">
            <span
                v-if="hasNew"
                class="has-new" />
            <BellIcon :size="24" />
        </div>
        <transition
            enter-active-class="animate__animated animate__slideInRight"
            leave-active-class="animate__animated animate__slideOutRight">
            <div
                v-show="isOpen"
                class="notification-list"
                data-simplebar>
                <button
                    class="btn btn-icon"
                    :title="$t('Close')"
                    @click="toggle">
                    <CloseIcon :size="30" />
                </button>
                <h4 class="px-3">
                    {{ $t('Notifications') }}
                </h4>

                <div
                    v-if="notifications.length > 0"
                    class="notifications-inner">
                    <NotificationAlert
                        v-for="notification in notifications"
                        :key="notification.id"
                        :notification="notification" />
                </div>
                <div
                    v-else
                    class="notifications-inner empty">
                    {{ $t('Empty') }}
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import CloseIcon from 'vue-material-design-icons/Close.vue'
import BellIcon from 'vue-material-design-icons/Bell.vue'
import NotificationAlert from '../elements/NotificationAlert.vue'

export default {
    name: 'NotificationsPanel',
    components: {
        NotificationAlert,
        BellIcon,
        CloseIcon
    },
    computed: {
        notifications() {
            return this.$store.getters['getNotifications']
        },
        hasNew() {
            return this.notifications.filter(n => n.new).length > 0
        },
        isOpen() {
            return this.$store.getters['isShowNotifications']
        }
    },
    methods: {
        toggle() {
            this.$store.commit('showNotifications', !this.isOpen)
        }
    }
}
</script>

<style scoped lang="scss">
.notifications-inner {
    &.empty {
        padding: var(--padding-box);
    }
}

.trigger {
    cursor: pointer;
    color: var(--bs-orange);
    position: relative;
    z-index: 10;

    .has-new {
        position: absolute;
        z-index: 3;
        top: 4px;
        right: 2px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--bs-danger);
        outline: 2px solid var(--bs-white);
    }
}

.notification-list {
    width: 300px;
    background: var(--bs-purple);
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 10;
    --animate-duration: 0.4s;
    color: var(--bs-white);
    box-shadow: 0 -30px 140px rgba(0, 0, 0, 0.3);
    padding: calc(var(--padding-box) * 3) 0 var(--padding-box) 0;

    .btn {
        color: var(--bs-white);
        opacity: 0.5;
        transition: 0.3s;
        outline: none;
        border: none;
        position: absolute;
        padding: 0;
        left: 10px;
        top: 10px;

        &:hover {
            opacity: 1;
        }
    }
}
</style>
