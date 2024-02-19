<template>
    <a
        :href="`/user/tickets/thread/${file.thread_id}/files/${file.id}`"
        target="_blank"
        class="file">
        <PaperclipIcon :size="14" />
        {{ file.name }}
    </a>
</template>

<script>
import PaperclipIcon from 'vue-material-design-icons/Paperclip.vue'

export default {
    name: 'TicketThreadFile',
    components: {
        PaperclipIcon
    },
    props: {
        file: {
            type: Object,
            required: true
        }
    }
}
</script>

<style lang="scss" scoped>
.file {
    text-decoration: none;
    display: inline-block;
    font-size: var(--font-small);
    margin-right: 3px;
    margin-bottom: 3px;
    background: var(--bs-purple);
    color: var(--bs-white);
    border-radius: 16px;
    padding: 2px 4px;

    &:hover {
        opacity: 0.5;
    }
}
</style>
