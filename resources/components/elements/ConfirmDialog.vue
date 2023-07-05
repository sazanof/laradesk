<template>
    <Teleport to="body">
        <transition
            name="fade"
            enter-active-class="fadeIn"
            leave-active-class="fadeOut">
            <div
                v-if="isVisible"
                class="popup-modal">
                <div class="window">
                    <h2>
                        {{ title }}
                    </h2>
                    <p>{{ message }}</p>
                    <slot />
                    <div class="btns">
                        <button
                            class="btn btn-secondary"
                            @click="_cancel">
                            <CloseIcon :size="20" />
                            {{ cancelButton }}
                        </button>
                        <button
                            class="btn"
                            :class="className"
                            @click="_confirm">
                            <slot name="okButtonIcon">
                                <TrashCanIcon :size="20" />
                            </slot>
                            {{ okButton }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>
<script>
import CloseIcon from 'vue-material-design-icons/Close.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'

export default {
    name: 'ConfirmDialogue',

    components: {
        CloseIcon,
        TrashCanIcon
    },

    props: {
        className: {
            type: String,
            default: 'btn-danger'
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
.popup-modal {
    animation-duration: 0.3s;
    background-color: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px;
    display: flex;
    align-items: center;
    z-index: 9999;
}

.window {
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    max-width: 480px;
    margin-left: auto;
    margin-right: auto;
    padding: 20px;
}

h2 {
    margin: 0 0 10px 0;
    text-align: center;
}

.btns {
    display: flex;
    justify-content: space-between;
    padding-top: 20px;

    .btn-secondary {
        margin-right: 10px
    }
}
</style>
