<template>
    <div class="user">
        <div class="pic">
            <Avatar
                :user="user"
                :size="24" />
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
