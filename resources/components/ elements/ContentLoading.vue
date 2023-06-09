<template>
    <div class="content-loading">
        <Loading :size="64" />
    </div>
</template>

<script>
import Loading from './Loading.vue'

export default {
    name: 'ContentLoading',
    components: {
        Loading
    }
}
</script>

<style lang="scss" scoped>
.content-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.5);
}
</style>
