<template>
    <div
        class="notification"
        :class="{'is-new' :notification.new}">
        <div
            :class="{'new' :notification.new}"
            class="point"
            @click="readNotification" />
        <div
            class="text"
            v-html="notification.text" />
    </div>
</template>

<script>
export default {
    name: 'NotificationAlert',
    components: {},
    props: {
        notification: {
            type: Object,
            required: true
        }
    },
    methods: {
        readNotification() {
            this.$store.commit('readNotification', this.notification)
        }
    }
}
</script>

<style scoped lang="scss">
.notification {
    margin-bottom: 5px;
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;

    &.is-new {

        .point {
            margin-top: 7px;
            border-radius: 50%;
            background: var(--bs-purple);
            cursor: pointer;
        }
    }

    .text {
        width: calc(100% - 16px);
    }

    .point {
        width: 10px;
        height: 10px;
    }
}
</style>
