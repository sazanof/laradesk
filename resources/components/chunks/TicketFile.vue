<template>
    <VListItem :value="file.id">
        <template #prepend>
            <VIcon
                :size="34"
                :icon="icon" />
        </template>
        <template #append>
            <VBtn
                target="_blank"
                icon="mdi-download"
                variant="text"
                color="default"
                density="comfortable"
                :href="`/ticket-files/${file.path}`" />
        </template>
        <template #title>
            {{ file.name }}
        </template>
        <template #subtitle>
            {{ size }}
        </template>
    </VListItem>
</template>

<script>
import formatBytes from '../../js/helpers/formatBytes'

export default {
    name: 'TicketFile',
    components: {},
    props: {
        file: {
            type: Object,
            required: true
        }
    },
    computed: {
        ext() {
            return this.file.extension
        },
        icon() {
            if ([ 'doc', 'docx', 'odt', 'rtf' ].indexOf(this.ext) > -1) {
                return 'mdi-file-word-box'
            } else if ([ 'xls', 'xlsx', 'ods' ].indexOf(this.ext) > -1) {
                return 'mdi-file-excel-box'
            } else if (this.ext === 'pdf') {
                return 'mdi-file-pdf-box'
            } else {
                return 'mdi-text-box'
            }
        },
        size() {
            return formatBytes(this.file.size)
        }
    }
}
</script>

