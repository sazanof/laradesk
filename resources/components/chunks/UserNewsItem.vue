<script>
import moment from 'moment'
import CloseIcon from 'vue-material-design-icons/Close.vue'

export default {
    name: 'UserNewsItem',
    components: {
        CloseIcon
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
        <div
            class="text"
            v-html="article.data.text" />
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
    img {
        max-width: 100%;
        width: 100%;
        height: auto;
    }

    .title {
        margin: 0 0 10px 0;
        font-size: 18px;
        font-weight: bold;
    }

    .actions {
        text-align: right;
    }
}
</style>
