<template>
    <teleport to="body">
        <div
            v-show="opened"
            class="modal"
            :class="[{opened:opened}, size]"
            @keypress.esc="close">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ title }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                            @click="close" />
                    </div>
                    <div class="modal-body">
                        <slot />
                    </div>
                    <div
                        v-if="footer"
                        class="modal-footer">
                        <slot name="footer-actions" />
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
export default {
    name: 'Modal',
    props: {
        title: {
            type: String,
            default: 'Default title'
        },
        footer: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: 'small'
        }
    },
    emits: [ 'on-open', 'on-close' ],
    data() {
        return {
            opened: false
        }
    },
    methods: {
        open() {
            this.opened = true
            this.$emit('on-open')
        },
        close() {
            this.opened = false
            this.$emit('on-close')
        }
    }
}
</script>

<style lang="scss" scoped>
.modal {
    &.opened {
        display: flex
    }

    .modal-dialog {
        position: relative;
        z-index: 10;
    }

    &.small .modal-dialog {
        width: 360px;
    }

    &.medium .modal-dialog {
        width: 560px;
    }

    &.big .modal-dialog {
        width: 760px;
    }

    &:after {
        background-color: rgba(0, 0, 0, 0.4);
        position: absolute;
        z-index: 1;
        content: '';
        top: 0;
        bottom: 0;
        left: 0;
        right: 0
    }

}
</style>
