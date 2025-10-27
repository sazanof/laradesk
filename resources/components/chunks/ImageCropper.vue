<template>
    <ModalDialog
        ref="cropperModal"
        :title="$t('Change photo')">
        <Cropper
            v-if="file"
            :src="url"
            :stencil-props="{
                aspectRatio: 10/10
            }"
            @change="change" />
        <template #actions>
            <VBtn
                prepend-icon="mdi-content-save"
                @click="savePhoto">
                {{ $t('Save') }}
            </VBtn>
        </template>
    </ModalDialog>
</template>

<script>
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import ModalDialog from './ModalDialog.vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

export default {
    name: 'ImageCropper',
    components: {
        Cropper,
        ContentSaveIcon,
        ModalDialog
    },
    props: {
        file: {
            type: Blob,
            required: true
        }
    },
    emits: [ 'on-save' ],
    data() {
        return {
            coordinates: null,
            canvas: null
        }
    },
    computed: {
        url() {
            return URL.createObjectURL(this.file)
        }
    },
    methods: {
        open() {
            this.$refs.cropperModal.open()
        },
        close() {
            this.$refs.cropperModal.close()
        },
        change({ coordinates, canvas }) {
            console.log(coordinates, canvas)
            this.coordinates = coordinates
            this.canvas = canvas
        },
        savePhoto() {
            this.$emit('on-save', {
                coordinates: this.coordinates,
                canvas: this.canvas
            })
            this.close()
        }
    }
}
</script>

<style scoped>

</style>
