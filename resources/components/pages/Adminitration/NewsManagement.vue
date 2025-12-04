<script>
import ModalDialog from '../../chunks/ModalDialog.vue'
import Editor from '../../../components/elements/Editor.vue'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import { formatDate } from '../../../js/helpers/moment.js'
import ConfirmDialog from '../../../components/elements/ConfirmDialog.vue'

export default {
    name: 'NewsManagement',
    components: {
        ModalDialog,
        Editor,
        ContentSaveIcon,
        ConfirmDialog
    },
    data() {
        return {
            page: 1,
            news: {},
            model: {
                id: null,
                title: null,
                text: null,
                only_admins: false
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
            this.model.only_admins = false
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
            this.model.only_admins = n.only_admins === 1
            this.$refs.addNew.open()
            this.$nextTick(() => {
                this.$refs.editor.setContent(this.model.text)
            })
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
        <div class="mb-4">
            <VBtn
                :text="$t('Add')"
                prepend-icon="mdi-plus"
                @click="openModal" />
        </div>
        <VSheet>
            <VSheet>
                <VCard
                    v-for="n in news.data"
                    :key="n.id"
                    class="mb-4"
                    variant="tonal">
                    <VCardTitle>
                        <VIcon
                            v-if="n.only_admins"
                            icon="mdi-crown"
                            color="orange" />
                        <VIcon
                            v-else
                            icon="mdi-account-multiple" />
                        {{ n.title }}
                    </VCardTitle>
                    <VCardSubtitle>
                        {{ formatDate(n.created_at) }}
                    </VCardSubtitle>
                    <VCardSubtitle class="pb-2">
                        <VBtn
                            v-if="!n.published"
                            size="small"
                            prepend-icon="mdi-eye"
                            :text="$t('Publish')"
                            @click="publishNew(n)" />
                        <VBtn
                            v-else
                            size="small"
                            color="warning"
                            prepend-icon="mdi-eye-off"
                            :text="$t('Unpublish')"
                            @click="unPublishNew(n)" />
                        <VBtn
                            size="small"
                            density="comfortable"
                            class="ml-2"
                            icon="mdi-pencil"
                            @click="editNew(n)" />
                        <VBtn
                            size="small"
                            density="comfortable"
                            class="ml-2"
                            icon="mdi-trash-can"
                            color="error"
                            @click="deleteNew(n)" />
                    </VCardSubtitle>
                </VCard>
            </VSheet>
        </VSheet>
        <ModalDialog
            ref="addNew"
            :title="$t('News management')">
            <VTextField
                v-model="model.title"
                :label="$t('Subject')" />
            <div class="mt-4">
                <label>{{ $t('Text') }}</label>
                <Editor
                    ref="editor"
                    @on-update="model.text = $event" />
            </div>
            <VCheckbox
                id="only_admins"
                v-model="model.only_admins"
                :label="$t('Only for admins')" />
            <template #actions>
                <VBtn
                    prepend-icon="mdi-content-save"
                    :text="$t('Save')"
                    :disabled="loading || disabled"
                    @click="save" />
            </template>
        </ModalDialog>
        <ConfirmDialog ref="deleteNewDialog" />
        <ConfirmDialog
            ref="publishDialog"
            class-name="btn-purple"
            ok-icon="mdi-check"
            ok-color="success" />
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
