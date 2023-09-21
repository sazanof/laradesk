<template>
    <div
        v-if="user"
        class="user">
        <div class="pic">
            <Avatar
                :user="user"
                :size="size" />
        </div>
        <div class="info">
            <div
                v-if="showName"
                class="name">
                {{ user.firstname }} {{ user.lastname }}
            </div>
            <div
                v-if="showInfo"
                class="position">
                {{ user.position }}, {{ user.department }}
            </div>
            <div
                v-if="showInfo"
                class="email">
                {{ user.email }}
            </div>
            <slot name="actions" />
        </div>
    </div>
</template>

<script>
import Avatar from './Avatar.vue'

export default {
    name: 'UserInTicketList',
    components: {
        Avatar
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        showName: {
            type: Boolean,
            default: true
        },
        showInfo: {
            type: Boolean,
            default: true
        },
        size: {
            type: Number,
            default: 24
        }
    }
}
</script>

<style lang="scss" scoped>
.user {
    margin-bottom: 6px;
    display: flex;
    align-items: flex-start;

    &:last-child {
        margin-bottom: 0;
    }

    .pic {
        margin-right: 4px;
    }

    .info {
        .name {
            font-weight: bold;
        }

        .position {
            font-size: var(--font-small);
        }

        .email {
            font-size: var(--font-small);
            color: var(--bs-primary)
        }
    }
}
</style>
