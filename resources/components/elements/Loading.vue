<template>
    <LoadingIcon
        class="loading"
        :size="size" />
</template>

<script>
import LoadingIcon from 'vue-material-design-icons/Loading.vue'

export default {
    name: 'Loading',
    components: {
        LoadingIcon
    },
    props: {
        size: {
            type: Number,
            default: 20
        }
    }
}
</script>

<style lang="scss" scoped>
.loading {
    display: inline-block;
    -webkit-animation: spin 1s linear infinite;
    -moz-animation: spin 1s linear infinite;
    animation: spin 1s linear infinite;
}

@-moz-keyframes spin {
    100% {
        --moz-transform: rotate(360deg);
    }
}

@-webkit-keyframes spin {
    100% {
        --webkit-transform: rotate(360deg);
    }
}

@keyframes spin {
    100% {
        --webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}
</style>
