<script>
import Modal from '../elements/Modal.vue'
import UserNewsItem from '../chunks/UserNewsItem.vue'

export default {
    name: 'UserNews',
    components: {
        Modal,
        UserNewsItem
    },
    data() {
        return {
            activeSlide: 0
        }
    },
    computed: {
        news() {
            return this.$store.getters['getUserNews']
        }
    },
    watch: {
        news() {
            if (this.news.length > 0) {
                this.$refs.news.open()
            } else {
                this.$refs.news.close()
            }
        }
    },
    created() {
        console.log(this.height)
    },
    methods: {
        toggleSlide(i) {
            if (i < 0) {
                this.activeSlide = 0
            } else {
                this.activeSlide = (i >= (this.news.length - 1)) ? this.news.length - 1 : i
            }

        }
    }
}
</script>

<template>
    <Modal
        ref="news"
        :title="$t('News')"
        size="big">
        <div
            v-if="news.length>0"
            class="news">
            <UserNewsItem
                v-for="(n, i) in news"
                v-show="i === activeSlide"
                :key="n.id"
                :article="n" />
        </div>

        <div class="dots">
            <div
                v-for="(n, i) in news"
                :key="n.id"
                class="dot"
                :class="{'active': i === activeSlide}"
                @click="toggleSlide(i)" />
        </div>
    </Modal>
</template>

<style scoped lang="scss">

.news {
    ::v-deep(img) {
        display: block;
        margin: 0 auto;
        max-width: 100%;
        height: auto;
    }
}

.dots {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

    .dot {
        cursor: pointer;
        height: 16px;
        width: 16px;
        padding: 4px;
        margin: 0 4px;
        border-radius: 50%;
        background: var(--bs-secondary);
        transition: var(--transition-duration);

        &.active {
            background: var(--bs-purple);
        }

        &:hover {
            background: var(--bs-purple-darker);
        }
    }
}
</style>
