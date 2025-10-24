<template>
    <VSheet
        v-if="files.length > 0"
        class="files mt-4">
        <div class="text-subtitle-1 font-weight-bold">
            {{ $t('Files') }}
        </div>
        <VList
            v-if="showFiles"
            class="files-inner">
            <TicketFile
                v-for="file in files"
                :key="file.id"
                :file="file" />
        </VList>
        <VBtn
            size="small"
            :href="`/ticket-files/${ticket.id}`"
            prepend-icon="mdi-download-multiple"
            :text="$t('Download all files')" />
        <VBtn
            size="small"
            class="ml-2"
            variant="text"
            :prepend-icon="showFiles? 'mdi-chevron-up' : 'mdi-paperclip'"
            @click="showFiles = !showFiles">
            <span v-if="!showFiles">{{ $t('Show') }} {{ $t('{count} files', {count: files.length}) }}</span>
            <span v-else>{{ $t('Hide files') }}</span>
        </VBtn>
    </VSheet>
</template>

<script>
import DownLoadIcon from 'vue-material-design-icons/Download.vue'
import TicketFile from './TicketFile.vue'

export default {
    name: 'TicketFiles',
    components: {
        TicketFile,
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
            showFiles: true
        }
    },
    computed: {
        title() {
            return this.showFiles ? this.$t('Hide files') : `${this.$t('Show')} ${this.$t('{count} files', { count: files.length })}`
        },
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
