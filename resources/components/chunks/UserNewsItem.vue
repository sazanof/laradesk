<script>
import CloseIcon from 'vue-material-design-icons/Close.vue'
import SimpleBar from 'simplebar-vue'
import { formatDate } from '../../js/helpers/moment.js'

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
            return formatDate(this.article.updated_at, 'DD.MM.YYYY HH:mm') // NOTIFICATION date, not article!
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
        <VChip
            rounded="pill"
            class="mb-2"
            color="success"
            size="small">
            {{ created }}
        </VChip>
        <div class="title">
            {{ article.data.title }}
        </div>
        <SimpleBar
            class="text">
            <div v-html="article.data.text" />
        </SimpleBar>
        <div class="actions">
            <VBtn
                :text="$t('Close')"
                prepend-icon="mdi-close"
                @click="readNew" />
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
        max-height: 70vh;
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
