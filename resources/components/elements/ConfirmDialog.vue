<template>
    <Teleport to="body">
        <ModalDialog
            v-model="isVisible"
            :title="title">
            <VSheet>
                <p>{{ message }}</p>
                <slot />
            </VSheet>
            <template #actions>
                <VBtn
                    color="default"
                    prepend-icon="mdi-close"
                    :text="cancelButton"
                    @click="_cancel" />
                <VBtn
                    :color="okColor"
                    :prepend-icon="okIcon"
                    :text="okButton"
                    @click="_confirm" />
            </template>
        </ModalDialog>
    </Teleport>
</template>
<script>
import ModalDialog from '../chunks/ModalDialog.vue'

export default {
    name: 'ConfirmDialogue',

    components: {
        ModalDialog
    },

    props: {
        className: {
            type: String,
            default: 'btn-danger'
        },
        okIcon: {
            type: String,
            default: 'mdi-trash-can'
        },
        okColor: {
            type: String,
            default: 'error'
        }
    },

    data: () => ({
        // Parameters that change depending on the type of dialogue
        isVisible: false,
        title: undefined,
        message: undefined, // Main text content
        okButton: undefined, // Text for confirm button; leave it empty because we don't know what we're using it for
        cancelButton: undefined, // Text for confirm button; leave it empty because we don't know what we're using it for

        // Private variables
        resolvePromise: undefined,
        rejectPromise: undefined
    }),

    methods: {
        open() {
            this.isVisible = true
        },

        close() {
            this.isVisible = false
        },
        show(opts = {}) {
            this.title = opts.title
            this.message = opts.message
            this.okButton = opts.okButton
            if (opts.cancelButton) {
                this.cancelButton = opts.cancelButton
            } else {
                this.cancelButton = this.$i18n.t('Cancel')
            }
            // Once we set our config, we tell the popup modal to open
            this.open()
            // Return promise so the caller can get results
            return new Promise((resolve, reject) => {
                this.resolvePromise = resolve
                this.rejectPromise = reject
            })
        },

        _confirm() {
            this.close()
            this.resolvePromise(true)
        },

        _cancel() {
            this.close()
            //this.resolvePromise(false)
            // Or you can throw an error
            this.rejectPromise(new Error('User cancelled the dialogue'))
        }
    }
}
</script>
<style lang="scss" scoped>
</style>
