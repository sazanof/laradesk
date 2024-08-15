<script>
import Modal from '../../../components/elements/Modal.vue'
import Editor from '../../../components/elements/Editor.vue'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import { formatDate } from '../../../js/helpers/moment.js'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import ConfirmDialog from '../../../components/elements/ConfirmDialog.vue'
import EyeIcon from 'vue-material-design-icons/Eye.vue'
import EyeOffIcon from 'vue-material-design-icons/EyeOff.vue'

export default {
    name: 'NewsManagement',
    components: {
        Modal,
        Editor,
        PlusIcon,
        ContentSaveIcon,
        PencilIcon,
        TrashCanIcon,
        ConfirmDialog,
        EyeIcon,
        EyeOffIcon
    },
    data() {
        return {
            page: 1,
            news: {},
            model: {
                id: null,
                title: null,
                text: null
            }
        }
    },
    computed: {
        disabled() {
            return this.model.text === null || this.model.title === null
        },
        loading() {
            return this.$store.getters['isLoading']
        }
    },
    created() {
        this.getNews()
    },
    methods: {
        async getNews() {
            this.news = await this.$store.dispatch('getNews', this.page)
        },
        openModal() {
            this.$refs.addNew.open()
        },
        async save() {
            await this.$store.dispatch(this.model.id === null ? 'addNew' : 'updateNew', this.model)
            this.$refs.addNew.close()
            this.model.id = null
            this.model.title = null
            this.model.text = null
            this.$refs.editor.setContent(this.model.text)
            await this.getNews()
        },
        formatDate(date) {
            return formatDate(date, 'DD.MM.YYYY HH:mm')
        },
        editNew(n) {
            this.model.id = n.id
            this.model.title = n.title
            this.model.text = n.text
            this.$refs.editor.setContent(this.model.text)
            this.$refs.addNew.open()
        },
        async deleteNew(n) {
            const ok = await this.$refs.deleteNewDialog.show({
                title: this.$t('Delete article'),
                message: this.$t('Delete article? Article also will be deleted from users too.'),
                okButton: this.$t('Delete')
            })
            if (ok) {
                await this.$store.dispatch('deleteNew', n.id)
                await this.getNews()
            }

        },
        async publishNew(n) {
            const ok = await this.$refs.publishDialog.show({
                title: this.$t('Publish article'),
                message: this.$t('Article will be shown to all users.'),
                okButton: this.$t('Publish')
            })
            if (ok) {
                await this.$store.dispatch('publishNew', n.id)
                await this.getNews()
            }
        },
        async unPublishNew(n) {
            const ok = await this.$refs.publishDialog.show({
                title: this.$t('Unpublish'),
                message: this.$t('Article also will be deleted from users.'),
                okButton: this.$t('Unpublish')
            })
            if (ok) {
                await this.$store.dispatch('unPublishNew', n.id)
                await this.getNews()
            }
        }
    }
}
</script>

<template>
    <div class="news">
        <div class="actions">
            <button
                class="btn btn-purple"
                @click="openModal">
                <PlusIcon :size="20" />
                {{ $t('Add') }}
            </button>
        </div>
        <div class="news-wrapper">
            <div class="list-group mt-3">
                <div
                    v-for="n in news.data"
                    :key="n.id"
                    class="list-group-item news-item">
                    <div class="badge bg-secondary">
                        {{ formatDate(n.created_at) }}
                    </div>
                    <div class="title">
                        {{ n.title }}
                    </div>
                    <div class="actions">
                        <button
                            v-if="!n.published"
                            class="btn btn-purple btn-sm"
                            @click="publishNew(n)">
                            <EyeIcon :size="14" />
                            {{ $t('Publish') }}
                        </button>
                        <button
                            v-else
                            class="btn btn-warning btn-sm"
                            @click="unPublishNew(n)">
                            <EyeOffIcon :size="14" />
                            {{ $t('Unpublish') }}
                        </button>
                        <button
                            class="btn btn-secondary ms-2 btn-sm"
                            @click="editNew(n)">
                            <PencilIcon :size="14" />
                        </button>
                        <button
                            class="btn btn-danger btn-icon ms-2 btn-sm"
                            @click="deleteNew(n)">
                            <TrashCanIcon :size="14" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <Modal
            ref="addNew"
            :title="$t('News management')"
            :footer="true"
            size="big">
            <div class="form-group">
                <label>{{ $t('Subject') }}</label>
                <input
                    v-model="model.title"
                    class="form-control"
                    type="text">
            </div>
            <div class="form-group">
                <label>{{ $t('Text') }}</label>
                <Editor
                    ref="editor"
                    @on-update="model.text = $event" />
            </div>
            <template #footer-actions>
                <button
                    :disabled="loading || disabled"
                    class="btn btn-success"
                    @click="save">
                    <ContentSaveIcon :size="20" />
                    {{ $t('Save') }}
                </button>
            </template>
        </Modal>
        <ConfirmDialog ref="deleteNewDialog" />
        <ConfirmDialog
            ref="publishDialog"
            class-name="btn-purple">
            <template #okButtonIcon>
                <ContentSaveIcon :size="20" />
            </template>
        </ConfirmDialog>
    </div>
</template>

<style scoped lang="scss">
.news-item {
    position: relative;

    .actions {
        text-align: right;
        position: absolute;
        right: 4px;
        top: 4px;

        .btn {
            padding: 2px 6px;
        }
    }
}
</style>
