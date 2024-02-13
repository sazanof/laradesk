<template>
    <div class="error-page">
        <img src="@/img/sad-cat.png">
        <div
            v-if="title"
            class="title">
            {{ title }}
        </div>
        <div
            v-if="description"
            class="description">
            {{ description }}
        </div>
    </div>
</template>

<script>
export default {
    name: 'ErrorPage',
    props: {
        title: {
            type: String,
            default: null
        },
        description: {
            type: String,
            default: null
        }
    }
}
</script>

<style scoped lang="scss">
.error-page {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 50px;
    flex-direction: column;

    .title {
        font-weight: bold;
        font-size: 24px;
        margin: 16px 0;
        color: var(--bs-orange)
    }

    .description {
        color: var(--bs-gray)
    }

    img {
        opacity: 0.2;
    }
}
</style>
