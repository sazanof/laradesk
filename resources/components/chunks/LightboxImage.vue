<script>
export default {
    name: 'LightboxImage',
    data() {
        return {
            opened: false
        }
    },
    computed: {
        src() {
            return this.$store.getters['getLightboxSrc']
        }
    },
    watch: {
        src(v) {
            this.opened = v !== null
            console.log(v)
        },
        opened(v) {
            if (v === false) {
                this.$store.commit('setLightboxSrc', null)
            }
        }
    }
}
</script>

<template>
    <VDialog
        v-model="opened">
        <VSheet
            elevation="0"
            rounded="lg"
            color="transparent"
            class="d-flex flex-column align-center justify-center">
            <VImg
                v-if="src"
                rounded="lg"
                width="100%"
                max-width="1000"
                height="100%"
                max-height="500px"
                :src="src" />
            <VBtn
                :text="$t('Close')"
                rounded="pill"
                prepend-icon="mdi-close"
                class="close-light mt-2"
                @click="opened = false" />
        </VSheet>
    </VDialog>
</template>

<style lang="scss">
.thread-item-content img {
    cursor: pointer;
    border-radius: 16px;
    display: block
}

.close-light {

}
</style>
