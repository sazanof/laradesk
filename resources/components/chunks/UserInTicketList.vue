<template>
    <VListItem
        v-if="user"
        density="compact">
        <UserItem
            :size="40"
            :user="user">
            <template #actions>
                <slot name="actions" />
            </template>
        </UserItem>
    </VListItem>
</template>

<script>
import UserItem from './UserItem.vue'

export default {
    name: 'UserInTicketList',
    components: {
        UserItem
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
</style>
