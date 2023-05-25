<template>
    <div class="categories">
        <div class="actions">
            <button
                class="btn btn-primary"
                @click="$refs.categoryModal.open()">
                <PlusIcon :size="18" />
                {{ $t('Add category') }}
            </button>
        </div>
        <div
            v-if="loading"
            class="alert alert-info mt-3">
            {{ $t('Loading, please wait...') }}
        </div>
        <div
            v-else
            class="categories-tree">
            <CategoryTree
                :level="0"
                :categories="categories"
                @on-category-edit-click="openCategoryModal"
                @on-category-delete-click="deleteCategory" />
        </div>
        <Modal
            ref="categoryModal"
            size="medium"
            :title="$t('Add category')"
            :footer="true">
            <template #footer-actions>
                <button
                    :disabled="disabled"
                    class="btn btn-primary"
                    @click="addCategory">
                    <ContentSaveIcon :size="18" />
                    {{ $t('Save') }}
                </button>
                <button
                    class="btn btn-outline-secondary"
                    @click="$refs.categoryModal.close()">
                    <CloseIcon :size="18" />
                    {{ $t('Cancel') }}
                </button>
            </template>
            <div class="form-group">
                <label for="">{{ $t('Name') }}</label>
                <input
                    v-model="name"
                    type="text"
                    class="form-control">
            </div>
            <div class="form-group">
                <label for="">{{ $t('Description') }}</label>
                <input
                    v-model="description"
                    type="text"
                    class="form-control">
            </div>
            <div class="form-group">
                <label for="">{{ $t('Parent') }}</label>
                <MultiselectElement
                    v-model="selectedCategory"
                    :object="true"
                    label="name"
                    value-prop="id"
                    track-by="id"
                    :options="categoriesToList" />
            </div>
        </Modal>
        <ConfirmDialog ref="categoryConfDialog" />
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import ConfirmDialog from '../../chunks/ConfirmDialog.vue'
import CategoryTree from '../../chunks/CategoryTree.vue'
import MultiselectElement from '../../ elements/MultiselectElement.vue'
import Modal from '../../ elements/Modal.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'

const toast = useToast()

export default {
    name: 'AdmCategories',
    components: {
        PlusIcon,
        CloseIcon,
        ContentSaveIcon,
        Modal,
        ConfirmDialog,
        MultiselectElement,
        CategoryTree
    },
    data() {
        return {
            loading: false,
            buttonDisabled: false,
            categoriesToList: [],
            selectedCategory: null,
            id: null,
            name: '',
            description: '',
            parent: 0,
            order: 0
        }
    },
    computed: {
        disabled() {
            return this.name.length < 3 || this.description.length < 3 || this.buttonDisabled
        },
        categories() {
            return this.$store.getters['getCategories']
        }
    },
    async created() {
        this.loading = true
        await this.$store.dispatch('getCategories')
        this.allCategories()
        this.loading = false
    },
    beforeCreate() {
        this.emitter.on('on-close', () => {
            this.id = null
            this.name = ''
            this.description = ''
            this.parent = 0
            this.order = 0
            this.selectedCategory = null
        })
    },
    methods: {
        async addCategory() {
            this.buttonDisabled = true
            const data = {
                id: this.id,
                name: this.name,
                parent: this.selectedCategory === undefined || this.selectedCategory === null ? 0 : this.selectedCategory.id,
                description: this.description,
                order: this.order
            }
            if (this.id > 0) {
                await this.$store.dispatch('saveCategory', data).catch(e => {
                    toast.error(this.$t('Error saving category'))
                    this.buttonDisabled = false
                })
            } else {
                await this.$store.dispatch('createCategory', data).catch(e => {
                    toast.error(this.$t('Error creating category'))
                    this.buttonDisabled = false
                })
            }
            await this.$store.dispatch('getCategories')
            this.buttonDisabled = false
            this.$refs.categoryModal.close()
            this.allCategories()
        },
        openCategoryModal(category) {
            this.id = category.id
            this.name = category.name
            this.parent = category.parent
            this.description = category.description
            this.order = category.order
            this.selectedCategory = this.categoriesToList.find(c => {
                return category.parent === c.id
            })
            this.$refs.categoryModal.open()
        },
        allCategories() {
            this.categoriesToList = []
            this.categories.map(cat => {
                this.categoriesToList.push({
                    id: cat.id,
                    parent: cat.parent,
                    name: cat.name
                })
                if (cat.children) {
                    this.addToCategoryList(cat, cat.name)
                }
            })
        },
        addToCategoryList(parentCategory, parentName = '') {
            parentCategory.children.map(cat => {
                const pName = parentName === '' ? cat.name : `${parentName} / ${cat.name}`
                this.categoriesToList.push({
                    id: cat.id,
                    parent: cat.parent,
                    name: pName
                })
                if (cat.children) {
                    this.addToCategoryList(cat, pName)
                }
            })
        },
        async deleteCategory(category) {
            if (category.children) {
                toast.error(this.$t('You can not delete a category with children'))
            } else {
                const ok = await this.$refs.categoryConfDialog.show({
                    title: this.$t('Delete category'),
                    message: this.$t('Are you sure you want to delete this category?'),
                    okButton: this.$t('Delete')
                })
                if (ok) {
                    await this.$store.dispatch('deleteCategory', category.id).then(() => {
                        toast.success(this.$t('Category deleted successfully'))
                        this.$store.dispatch('getCategories')
                    })
                }
            }

        }
    }
}
</script>

<style lang="scss" scoped>
.actions {
    position: sticky;
    top: 0;
    z-index: 11;
    background-color: var(--bs-white);
}

.categories-tree {
    margin-top: var(--padding-box);
}
</style>
