<template>
    <div
        class="notification"
        :class="{'is-new' :notification.new}">
        <div
            v-if="notification.new"
            :class="{'new' :notification.new}"
            class="point"
            @click="readNotification" />
        <div v-if="type === 'export'">
            <div class="notification-item">
                <div class="notification-title">
                    {{ $t('Export task was completed successfully') }}
                </div>
                <div class="notification-text">
                    <a
                        target="_blank"
                        :href="`/user/tickets/export/${nData.filename}`">
                        <LinkVariantIcon :size="16" />
                        {{ $t('Download link') }}
                    </a>
                </div>
            </div>
        </div>
        <div v-if="type === 'ticket'">
            <div class="notification-item">
                <div class="notification-title">
                    {{ $t('New ticket') }}
                </div>
                <div class="notification-text">
                    {{ nData.ticket.subject }}
                    <a
                        @click="$router.push({name:'admin_ticket',params:{number:nData.ticket.id}})">
                        <LinkVariantIcon :size="16" />
                        {{ $t('View') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LinkVariantIcon from 'vue-material-design-icons/LinkVariant.vue'

export default {
    name: 'NotificationAlert',
    components: {
        LinkVariantIcon
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
    flex-direction: column;
    transition: 0.5s;
    padding: var(--padding-box);

    &:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    &.is-new {

        .point {
            position: absolute;
            right: 18px;
            top: 18px;
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

    .point {
        width: 10px;
        height: 10px;
    }
}
</style>
