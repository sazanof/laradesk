<template>
    <div class="notifications">
        <Popper
            ref="popperNotifications"
            placement="auto"
            :arrow="true"
            @close:popper="closePopper">
            <template #content>
                <div class="notifications-inner">
                    <NotificationAlert
                        v-for="notification in notifications"
                        :key="notification.id"
                        :notification="notification" />
                </div>
            </template>
            <div class="trigger">
                <span
                    v-if="hasNew"
                    class="has-new" />
                <BellIcon :size="24" />
            </div>
        </Popper>
        <transition
            enter-active-class="animate__animated animate__bounceInDown"
            leave-active-class="animate__animated animate__bounceOutUp">
            <div
                v-if="last"
                class="last-one-noty">
                <div class="bell">
                    <BellIcon :size="36" />
                </div>
                <div
                    class="text"
                    v-html="last.text" />
                <div
                    class="close"
                    @click="last = null">
                    <CloseIcon :size="20" />
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import NotificationAlert from './NotificationAlert.vue'
import Popper from 'vue3-popper'
import BellIcon from 'vue-material-design-icons/Bell.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'

export default {
    name: 'NotificationsWrapper',
    components: {
        BellIcon,
        CloseIcon,
        Popper,
        NotificationAlert
    },
    data() {
        return {
            last: null
        }
    },
    computed: {
        notifications() {
            return this.$store.getters['getNotifications']
        },
        hasNew() {
            return this.notifications.filter(n => n.new).length > 0
        }
    },
    created() {
        this.emitter.on('notification.received', (data) => {
            const audio = new Audio('/sounds/notification.mp3')
            audio.play()
            this.$store.commit('addNotification', data)
            this.last = data
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

.trigger {
    cursor: pointer;
    color: var(--bs-orange);
    position: relative;

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

.last-one-noty {
    border: 1px solid var(--bs-yellow);
    background: var(--bs-gray-100);
    box-shadow: var(--box-shadow);
    position: fixed;
    top: 15px;
    border-radius: var(--bs-border-radius-lg);
    padding: var(--padding-box);
    left: 50%;
    margin-left: -200px;
    width: 400px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    background: var(--bs-yellow);
    color: var(--color-text);

    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        color: var(--color-text);
        cursor: pointer;
    }

    .bell {
        width: 48px;
        color: var(--bs-orange);
    }

    .text {
        width: calc(100% - 48px);
        padding: 10px;

        a {
            display: block;
        }
    }
}
</style>
