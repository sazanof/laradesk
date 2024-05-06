<template>
    <div class="editor">
        <QuillEditor
            v-model:content="value"
            :options="options"
            content-type="html"
            @update:content="$emit('on-update',$event)" />
    </div>
</template>

<script>
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { QuillEditor, Quill } from '@vueup/vue-quill'
import ImageUploader from 'quill-image-uploader'

if (!Quill.imports['modules/ImageUploader']) {
    Quill.register('modules/ImageUploader', ImageUploader)
}

export default {
    name: 'Editor',
    components: {
        QuillEditor
    },
    emits: [ 'on-update' ],
    data() {
        const that = this
        return {
            value: '',
            options: {
                modules: {
                    ImageUploader: {
                        upload(file) {
                            return that.$store.dispatch('uploadImageInEditor', file)
                        }
                    },
                    toolbar: [
                        [
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'link',
                            'blockquote'
                        ],
                        [
                            {
                                'color': []
                            },
                            {
                                'background': []
                            }
                        ]
                    ]
                },
                theme: 'snow'
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.editor {
    ::v-deep(.ql-container.ql-snow) {
        height: auto;
    }

    ::v-deep(.ql-editor) {
        height: 230px;
        overflow-y: scroll;
    }
}
</style>
