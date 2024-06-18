<template>
    <div
        class="files"
        :class="{draggable:drag}"
        @dragover.prevent="drag=true"
        @dragenter.prevent="drag=true"
        @dragleave.prevent="drag=false"
        @click="$refs.commentFiles.click()"
        @drop.prevent="dropFiles">
        <HelpComment>
            <template #trigger>
                <span>
                    <UploadIcon :size="18" />
                    {{ $t('Drag and drop files or choose from computer') }}
                </span>
            </template>
            <div class="helper">
                {{
                    $t('You can upload up to 5 files at a time. The size of each file should not exceed {size} kb', {size: maxFileSize})
                }}
                <span class="mimes">{{ allowedMimes.join(', ') }}</span>
            </div>
        </HelpComment>

        <input
            ref="commentFiles"
            type="file"
            class="form-control d-none"
            multiple
            @change="appendFiles(null)">
        <div
            v-if="files.length > 0"
            class="file-list">
            <div
                v-for="file in files"
                :key="file"
                class="uploaded-file">
                <div class="name">
                    {{ file.name }}
                </div>
                <div class="del">
                    <CloseIcon
                        :size="20"
                        @click.stop="deleteFile(file)" />
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
            drag: false,
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
        appendFiles(files = null) {
            const fileArray = files === null ? Array.from(this.$refs.commentFiles.files) : files
            const newFiles = fileArray.filter((f) => {
                if (this.allowedMimes.indexOf(f.name.split('.').pop().toLowerCase()) !== -1 && f.size <= this.maxFileSize * 1024) {
                    let duplicate = false
                    this.files.map(_file => {
                        if (_file.name === f.name) {
                            duplicate = true
                        }
                    })
                    console.log(duplicate)
                    return !duplicate
                } else {
                    toast.error(this.$t('File {file} does not meet the requirements', { file: f.name }))
                    return false
                }
            })
            if (newFiles.length > 0) {
                newFiles.map(f => {
                    this.files.push(f)
                    return f
                })
            }
            console.log(newFiles, this.files)
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
        },
        dropFiles(e) {
            this.drag = false
            const files = Array.from(e.dataTransfer.files)
            this.appendFiles(files)
        }
    }
}
</script>

<style lang="scss" scoped>
.files {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: var(--padding-box);
    border: 2px dashed var(--bs-border-color);
    background: var(--bs-light);
    cursor: pointer;

    &.draggable {
        border-color: var(--bs-purple);
        background: var(--bs-purple-o2);
    }

    .helper {
        text-align: center;

        .mimes {
            display: block;
            font-size: var(--font-small);
            margin-top: 16px;
        }
    }

    .file-list {
        margin-top: 8px;

        .uploaded-file {
            color: var(--bs-gray);
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            font-style: italic;
            margin: 4px;

            .del {
                cursor: pointer;
                color: var(--bs-danger);
                margin: -2px 3px 0;
            }

            .help {
                font-size: var(--font-small);
                font-style: italic;
                color: var(--bs-gray)
            }
        }
    }
}

</style>
