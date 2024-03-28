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
                class="notification-list">
                <button
                    class="btn btn-icon"
                    :title="$t('Close')"
                    @click="toggle">
                    <CloseIcon :size="30" />
                </button>
                <button
                    class="btn btn-purple btn-sm delete-all"
                    :title="$t('Delete all')"
                    @click="deleteAll">
                    <TrashCanIcon :size="18" />
                    {{ $t('Delete all') }}
                </button>
                <h4 class="px-3">
                    {{ $t('Notifications') }}
                </h4>
                <SimpleBar
                    ref="inner"
                    class="notifications-inner">
                    <div
                        v-if="notifications.length > 0">
                        <NotificationAlert
                            v-for="notification in notifications"
                            :key="notification.id"
                            :notification="notification" />
                    </div>
                    <div
                        v-else
                        class="empty">
                        {{ $t('Empty') }}
                    </div>
                </SimpleBar>
            </div>
        </transition>
    </div>
</template>

<script>
import SimpleBar from 'simplebar-vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import BellIcon from 'vue-material-design-icons/Bell.vue'
import NotificationAlert from '../elements/NotificationAlert.vue'

export default {
    name: 'NotificationsPanel',
    components: {
        NotificationAlert,
        BellIcon,
        CloseIcon,
        SimpleBar,
        TrashCanIcon
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

    .notifications-inner {
        position: absolute;
        top: 100px;
        right: 0;
        left: 0;
        bottom: 0;

        .empty {
            padding: var(--padding-box);
        }
    }

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

        &.delete-all {
            left: auto;
            right: 10px;
            padding: 2px;
        }
    }
}
</style>
