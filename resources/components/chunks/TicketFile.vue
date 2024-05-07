<template>
    <div class="ticket-file">
        <FileWordBoxIcon
            v-if="['doc','docx','odt', 'rtf'].indexOf(ext) !== -1" />
        <FileExcelBoxIcon
            v-else-if="['xls','xlsx','ods'].indexOf(ext) !== -1" />
        <FilePdfBoxIcon
            v-else-if="ext === 'pdf'" />
        <TextBoxIcon
            v-else />
        <div class="file-info">
            <div
                class="name">
                <a
                    target="_blank"
                    :href="`/ticket-files/${file.path}`">{{ file.name }}</a>
            </div>
            <div class="size">
                {{ size }}
            </div>
        </div>
    </div>
</template>

<script>
import formatBytes from '../../js/helpers/formatBytes'
import FileWordBoxIcon from 'vue-material-design-icons/FileWordBox.vue'
import FileExcelBoxIcon from 'vue-material-design-icons/FileExcelBox.vue'
import FilePdfBoxIcon from 'vue-material-design-icons/FilePdfBox.vue'
import TextBoxIcon from 'vue-material-design-icons/TextBox.vue'

export default {
    name: 'TicketFile',
    components: {
        FileWordBoxIcon,
        FileExcelBoxIcon,
        FilePdfBoxIcon,
        TextBoxIcon
    },
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
        size() {
            return formatBytes(this.file.size)
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket-file {
    display: flex;
    margin-bottom: 16px;
    align-items: flex-start;

    .file-info {
        margin-left: 6px;

        .size {
            font-size: var(--font-small);
            color: var(--bs-gray);
        }
    }
}
</style>
