<template>
    <VCard
        rounded="0"
        hover
        variant="plain"
        :class="{'is-new' :isNew}">
        <template #subtitle>
            {{ notification.created }}
        </template>
        <template #append>
            <VBtn
                v-if="isNew"
                color="default"
                variant="plain"
                class="mr-2"
                density="comfortable"
                size="small"
                icon="mdi-eye"
                @click="readNotification" />
            <VBtn
                color="error"
                variant="text"
                density="comfortable"
                size="small"
                icon="mdi-close"
                @click="deleteNotification" />
        </template>
        <template #text>
            <div>
                <div class="notification-title">
                    {{ notification.title }}
                </div>
                <div class="notification-text">
                    {{ notification.text }}
                </div>
                <div
                    v-if="type === 'notification.comment.new'"
                    class="notification-details">
                    <div class="mt-1">
                        {{ notification.comment.content }}
                    </div>
                    <VBtn
                        v-if="notification.belongsToDepartment && notification.isAssignee"
                        variant="tonal"
                        size="small"
                        prepend-icon="mdi-comment"
                        :text="$t('View')"
                        @click="$router.push({name: 'admin.ticket',params: {number:notification.comment.ticket.id}})" />
                    <VBtn
                        v-else
                        variant="tonal"
                        size="small"
                        prepend-icon="mdi-comment"
                        :text="$t('View')"
                        @click="$router.push({name: 'user.ticket',params: {number:notification.comment.ticket.id}})" />
                </div>
                <div
                    v-if="type === 'notification.ticket.new' || type === 'notification.ticket.new'"
                    class="mt-1">
                    <VBtn
                        v-if="notification.belongsToDepartment"
                        variant="tonal"
                        size="small"
                        prepend-icon="mdi-note"
                        :text="$t('View')"
                        @click="$router.push({name: 'admin.ticket',params: {number:notification.ticket.id}})" />
                </div>
                <div
                    v-if="type === 'notification.export.finished'"
                    class="mt-2">
                    <VBtn
                        variant="tonal"
                        size="small"
                        prepend-icon="mdi-download"
                        target="_blank"
                        :text="$t('Download link')"
                        :href="`/user/tickets/export/${notification.filename}`" />
                </div>
            </div>
        </template>
    </VCard>
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
    computed: {
        type() {
            return this.notification.type
        },
        nData() {
            return this.notification.data
        },
        isNew() {
            return this.notification?.read_at === null
        }
    },
    methods: {
        readNotification() {
            this.$store.dispatch('readUserNotification', this.notification.id)
        },
        deleteNotification() {
            this.$store.dispatch('deleteUserNotification', this.notification.id)
        }
    }
}
</script>

<style scoped lang="scss">
</style>
