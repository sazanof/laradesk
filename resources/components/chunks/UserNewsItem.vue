<script>
import CloseIcon from 'vue-material-design-icons/Close.vue'
import SimpleBar from 'simplebar-vue'

export default {
    name: 'UserNewsItem',
    components: {
        CloseIcon,
        SimpleBar
    },
    props: {
        article: {
            type: Object,
            required: true
        }
    },
    computed: {
        created() {
            return moment(this.article.updated_at).format('DD.MM.YYYY HH:mm') // NOTIFICATION date, not article!
        }
    },
    methods: {
        async readNew() {
            await this.$store.dispatch('markNewAsRead', this.article.id)
        }
    }
}
</script>

<template>
    <div class="news-item">
        <div class="badge mb-2 bg-success">
            {{ created }}
        </div>
        <div class="title">
            {{ article.data.title }}
        </div>
        <SimpleBar
            class="text">
            <div v-html="article.data.text" />
        </SimpleBar>
        <div class="actions">
            <button
                class="btn btn-purple btn-sm"
                @click="readNew">
                <CloseIcon :size="14" />
                {{ $t('Close') }}
            </button>
        </div>
    </div>
</template>

<style scoped lang="scss">
.news-item {
    position: relative;

    img {
        max-width: 100%;
        width: auto;
        height: auto;

    }

    .title {
        margin: 0 0 10px 0;
        font-size: 18px;
        font-weight: bold;
    }

    .text {
        max-height: 80vh;
        padding-bottom: 40px;
    }

    .actions {
        position: absolute;
        bottom: 10px;
        right: 10px;
        text-align: right;
    }
}
</style>
