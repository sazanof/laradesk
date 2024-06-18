<template>
    <div
        v-if="files.length > 0"
        class="files mt-3">
        <button
            class="show-files btn w-100 btn-light"
            @click="showFiles = !showFiles">
            <PaperclipIcon :size="20" />
            <span v-if="!showFiles">{{ $t('Show') }} {{ $t('{count} files', {count: files.length}) }}</span>
            <span v-else>{{ $t('Hide files') }}</span>
        </button>
        <div
            v-if="showFiles"
            class="files-inner">
            <TicketFile
                v-for="file in files"
                :key="file.id"
                :file="file" />
            <a
                class="btn btn-sm btn-light w-100"
                :href="`/ticket-files/${ticket.id}`">
                <DownLoadIcon :size="14" />
                {{ $t('Download all files') }}
            </a>
        </div>
    </div>
</template>

<script>
import DownLoadIcon from 'vue-material-design-icons/Download.vue'
import PaperclipIcon from 'vue-material-design-icons/Paperclip.vue'
import TicketFile from './TicketFile.vue'

export default {
    name: 'TicketFiles',
    components: {
        TicketFile,
        PaperclipIcon,
        DownLoadIcon
    },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            showFiles: false
        }
    },
    computed: {
        files() {
            return this.ticket.files
        }
    }
}
</script>

<style lang="scss" scoped>
.files {
    .files-inner {
        padding: 16px 0;
    }
}
</style>
