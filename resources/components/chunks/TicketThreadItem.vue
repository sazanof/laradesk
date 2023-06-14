<template>
    <div class="thread-item">
        <div class="author">
            <UserInTicketList :user="author" />
        </div>
        <div
            class="comment"
            :class="type">
            {{ data }}
        </div>
    </div>
</template>

<script>
import UserInTicketList from './UserInTicketList.vue'

export default {
    name: 'TicketThreadItem',
    components: {
        UserInTicketList
    },
    props: {
        author: {
            type: Object,
            required: true
        },
        data: {
            type: String,
            required: true
        },
        ticket: {
            type: Object,
            required: true
        },
        type: {
            type: String,
            default: 'comment' // available types: "comment", "close-comment', "solved-comment"
        }
    }
}
</script>

<style scoped>

</style>
