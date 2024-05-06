<template>
    <teleport to="body">
        <transition
            name="fade"
            enter-active-class="animate__animated animate__fadeIn"
            leave-active-class="animate__animated animate__fadeOut">
            <div
                v-if="opened"
                class="image-box">
                <div
                    class="image-box-overflow"
                    @click="close" />
                <img
                    class="image-box-image"
                    :src="url"
                    alt="">
                <div
                    v-if="images !== null && images.length > 1"
                    class="thumbs">
                    <div
                        v-for="(image, i) in images"
                        :key="image"
                        class="thumb">
                        <img
                            :src="image"
                            class="thumb"
                            :class="{active: i === index}"
                            @click="switchImage(image, i)">
                    </div>
                </div>
                <div class="nav">
                    <div
                        v-if="index > 0"
                        class="prev"
                        @click="prev">
                        <ChevronLeftIcon :size="50" />
                    </div>
                    <div
                        v-if="index < (images.length - 1)"
                        class="next"
                        @click="next">
                        <ChevronRightIcon
                            :size="50" />
                    </div>
                    <div
                        class="close"
                        @click="close">
                        <CloseIcon :size="50" />
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script>
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue'
import ChevronLeftIcon from 'vue-material-design-icons/ChevronLeft.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'

export default {
    name: 'LightBox',
    components: {
        ChevronRightIcon,
        ChevronLeftIcon,
        CloseIcon
    },
    props: {
        src: {
            type: String,
            default: null
        },
        images: {
            type: Array,
            default: null
        }
    },
    emits: [ 'on-close', 'on-index-changed' ],
    data() {
        return {
            url: null,
            index: 0,
            opened: false
        }
    },
    watch: {
        src() {
            this.url = this.src
        }
    },
    created() {
        this.url = this.src
    },
    methods: {
        open(atIndex = 0) {
            this.index = atIndex
            this.opened = true
            this.switchImage(this.url, atIndex)
        },
        close() {
            this.opened = false
            this.index = 0
            this.url = null
            this.$emit('on-close')
        },
        switchImage(image, i) {
            this.url = image
            this.index = i
            this.$emit('on-index-changed', this.index)
        },
        prev() {
            this.index = this.index > 0 ? this.index - 1 : 0
            this.url = this.images[this.index]
            this.$emit('on-index-changed', this.index)
        },
        next() {
            this.index = this.index < (this.images.length - 1) ? this.index + 1 : this.index
            this.url = this.images[this.index]
            this.$emit('on-index-changed', this.index)
        }
    }
}
</script>

<style lang="scss" scoped>
.image-box {
    --animate-duration: 0.4s;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 120;
    padding: 40px;
    display: flex;
    align-items: center;
    justify-content: center;

    .image-box-overflow {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 5;
        background: rgba(0, 0, 0, 0.5);
    }

    .image-box-image {
        position: relative;
        z-index: 10;
        max-width: 1600px;
        max-height: 1000px;
    }

    .thumbs {
        position: absolute;
        z-index: 10;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        align-items: center;
        justify-content: center;

        .thumb {
            margin: 0 5px;

            img {
                height: 80px;
                border: 4px solid rgba(255, 255, 255, 0.5);
                width: auto;
                cursor: pointer;
                opacity: 0.4;
                transition: var(--transition-duration);

                &:hover, &.active {
                    opacity: 1;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
                }
            }
        }
    }

    .nav {
        position: relative;

        .prev, .next {
            cursor: pointer;
            position: fixed;
            z-index: 21;
            top: 0;
            bottom: 0;
            width: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff
        }

        .close {
            position: fixed;
            z-index: 21;
            top: 20px;
            right: 20px;
            color: #fff;
            cursor: pointer;
        }

        .prev {
            left: 0;
        }

        .next {
            right: 0;
        }
    }
}
</style>
