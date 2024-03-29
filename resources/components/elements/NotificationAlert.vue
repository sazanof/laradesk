<template>
    <div
        class="notification"
        :class="{'is-new' :isNew}">
        <div class="actions">
            <button
                class="btn btn-icon btn-sm text-warning"
                @click="readNotification">
                <EyeIcon
                    v-if="isNew"
                    :size="18"
                    :class="{'new' :isNew}" />
            </button>
            <button
                class="btn btn-icon btn-sm text-danger"
                @click="deleteNotification">
                <TrashCanIcon
                    :size="18" />
            </button>
        </div>

        <div>
            <div class="notification-item">
                <div class="notification-date">
                    {{ notification.created }}
                </div>
                <div class="notification-title">
                    {{ notification.title }}
                </div>
                <div class="notification-text">
                    {{ notification.text }}
                </div>
                <div
                    v-if="type === 'notification.comment.new'"
                    class="notification-details">
                    <div class="notification-details--content">
                        {{ notification.comment.content }}
                    </div>
                    <button
                        v-if="notification.belongsToDepartment && notification.isAssignee"
                        class="btn btn-purple btn-sm"
                        @click="$router.push({name: 'admin.ticket',params: {number:notification.comment.ticket.id}})">
                        <CommentIcon :size="16" />
                        {{ $t('View') }}
                    </button>
                    <button
                        v-else
                        class="btn btn-purple btn-sm"
                        @click="$router.push({name: 'user.ticket',params: {number:notification.comment.ticket.id}})">
                        <CommentIcon :size="16" />
                        {{ $t('View') }}
                    </button>
                </div>
                <div
                    v-if="type === 'notification.ticket.new' || type === 'notification.ticket.new'"
                    class="notification-details">
                    <button
                        v-if="notification.belongsToDepartment"
                        class="btn btn-purple btn-sm"
                        @click="$router.push({name: 'admin.ticket',params: {number:notification.ticket.id}})">
                        <NoteIcon :size="16" />
                        {{ $t('View') }}
                    </button>
                </div>
                <div
                    v-if="type === 'notification.export.finished'"
                    class="notification-details">
                    <a
                        class="btn btn-purple btn-sm"
                        :href="`/user/tickets/export/${notification.filename}`">
                        <LinkVariantIcon :size="16" />
                        {{ $t('Download link') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import EyeIcon from 'vue-material-design-icons/Eye.vue'
import NoteIcon from 'vue-material-design-icons/Note.vue'
import CommentIcon from 'vue-material-design-icons/Comment.vue'
import LinkVariantIcon from 'vue-material-design-icons/LinkVariant.vue'

export default {
    name: 'NotificationAlert',
    components: {
        LinkVariantIcon,
        CommentIcon,
        NoteIcon,
        TrashCanIcon,
        EyeIcon
    },
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
.notification {
    margin-bottom: 5px;
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    flex-direction: column;
    transition: 0.5s;
    padding: var(--padding-box);

    .actions {
        position: absolute;
        right: 3px;
        top: 3px;

        .btn {
            transition: var(--transition-duration);
            color: var(--bs-white);
            cursor: pointer;
        }
    }

    &:hover {
        background: rgba(255, 255, 255, 0.2);

    }

    &.is-new {

        .point {

            border-radius: 50%;
            background: var(--bs-yellow);
            cursor: pointer;
        }
    }

    .notification-title {
        font-weight: bold;
        margin-bottom: 6px;
    }

    .notification-text {
        font-size: var(--font-small);

        a {
            cursor: pointer;
            display: block;
            margin-top: 6px;
            color: var(--bs-white);
            transition: 0.5s;
            text-decoration: none;
            border-bottom: var(--bs-white);
            opacity: 0.6;

            &:hover {
                opacity: 1;
            }
        }
    }

    .notification-date {
        margin-top: 10px;
        font-size: var(--font-small);
        opacity: 0.7;
    }

    .notification-details {
        margin-top: 10px;
        font-size: var(--font-small);
        opacity: 0.7;

        .notification-details--content {
            margin-bottom: 6px;
        }
    }

    .point {
        width: 10px;
        height: 10px;
    }
}
</style>
