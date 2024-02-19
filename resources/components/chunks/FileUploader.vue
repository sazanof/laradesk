<template>
    <div class="files">
        <HelpComment>
            <template #trigger>
                <button
                    class="btn btn-purple w-100"
                    @click="$refs.commentFiles.click()">
                    <UploadIcon :size="18" />
                    {{ $t('Upload files') }}
                </button>
            </template>
            <div>
                {{
                    $t('You can upload up to 5 files at a time. The size of each file should not exceed {size} kb', {size: maxFileSize})
                }}
            </div>
            <div>{{ $t('MIME types of files allowed to upload: {mimes}', {mimes: allowedMimes.join(', ')}) }}</div>
        </HelpComment>

        <input
            ref="commentFiles"
            type="file"
            class="form-control d-none"
            multiple
            @change="appendFiles">
        <div class="file-list">
            <div
                v-for="file in files"
                :key="file"
                class="uploaded-file">
                <div class="name">
                    {{ file.name }}
                </div>
                <div class="del">
                    <CloseIcon
                        :size="14"
                        @click="deleteFile(file)" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import HelpComment from './HelpComment.vue'
import { useToast } from 'vue-toastification'
import UploadIcon from 'vue-material-design-icons/Upload.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'

const toast = useToast()

export default {
    name: 'FileUploader',
    components: {
        HelpComment,
        UploadIcon,
        CloseIcon
    },
    emits: [ 'on-files-changed' ],
    data() {
        return {
            files: []

        }
    },
    computed: {
        allowedMimes() {
            return this.$store.getters['getAllowedMimes']
        },
        maxFileSize() {
            return this.$store.getters['getMaxFileSize']
        }
    },
    methods: {
        appendFiles() {
            const fileArray = Array.from(this.$refs.commentFiles.files)
            this.files = fileArray.filter((f) => {
                if (this.allowedMimes.indexOf(f.type) !== -1 && f.size <= this.maxFileSize * 1024) {
                    return true
                } else {
                    toast.error(this.$t('File {file} does not meet the requirements', { file: f.name }))
                    return false
                }
            })
            this.$refs.commentFiles.files = null
            this.$emit('on-files-changed', this.files)
        },
        deleteFile(file) {
            this.files = this.files.filter(f => f !== file)
            this.$emit('on-files-changed', this.files)
        },
        reset() {
            this.files = []
            this.$refs.commentFiles.files = null
        }
    }
}
</script>

<style lang="scss" scoped>
.file-list {
    margin-top: 8px;

    .uploaded-file {
        font-size: var(--font-small);
        color: var(--bs-gray);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-style: italic;
        margin-top: 4px;

        .del {
            cursor: pointer;
            color: var(--bs-danger)
        }

        .help {
            font-size: var(--font-small);
            font-style: italic;
            color: var(--bs-gray)
        }
    }
}
</style>
