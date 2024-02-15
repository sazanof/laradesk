<template>
    <div class="user-list-item">
        <div class="pic">
            <Avatar
                :user="user"
                :size="48" />
        </div>
        <div class="user-info">
            <div class="name">
                {{ user.firstname }} {{ user.lastname }}
            </div>
            <div class="position">
                {{ user.department }}, {{ user.position }}
            </div>
            <div class="actions">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>

<script>
import Avatar from './Avatar.vue'

export default {
    name: 'UserListItem',
    components: {
        Avatar
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    }
}
</script>

<style lang="scss" scoped>
.user-list-item {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    justify-content: flex-start;
    margin-bottom: 16px;
    position: relative;

    .pic {
        width: 64px;
    }

    .user-info {
        width: calc(100% - 64px);;

        .name {
            font-weight: bold;
            color: var(--bs-purple);
        }
    }

    .actions {
        position: absolute;
        top: 4px;
        right: 6px
    }
}
</style>
