<template>
    <VListItem
        prepend-icon="mdi-file"
        :color="color"
        :title="file.name">
        <template #append>
            <VBtn
                variant="text"
                density="comfortable"
                color="default"
                target="_blank"
                :href="`/user/tickets/thread/${file.thread_id}/files/${file.id}`"
                icon="mdi-download" />
        </template>
    </VListItem>
</template>

<script>

export default {
    name: 'TicketThreadFile',
    components: {},
    props: {
        file: {
            type: Object,
            required: true
        },
        color: {
            type: String,
            default: 'default'
        }
    }
}
</script>

<style lang="scss" scoped>
</style>
