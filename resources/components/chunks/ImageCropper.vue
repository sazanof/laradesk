<template>
    <Modal
        ref="cropperModal"
        :title="$t('Change photo')"
        :footer="true"
        size="big">
        <Cropper
            v-if="file"
            :src="url"
            :stencil-props="{
                aspectRatio: 10/10
            }"
            @change="change" />
        <template #footer-actions>
            <button
                class="btn btn-purple"
                @click="savePhoto">
                <ContentSaveIcon :size="18" />
                {{ $t('Save') }}
            </button>
        </template>
    </Modal>
</template>

<script>
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import Modal from '../ elements/Modal.vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

export default {
    name: 'ImageCropper',
    components: {
        Modal,
        Cropper,
        ContentSaveIcon
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
