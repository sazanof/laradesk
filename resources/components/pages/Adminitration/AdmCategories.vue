<template>
    <div class="categories">
        <div class="actions">
            <VSelect
                v-model="selectedDepartment"
                :placeholder="$t('Filter by department')"
                :return-object="true"
                item-title="name"
                item-value="id"
                :items="departments"
                @select="selectedDepartment = $event"
                @clear="selectedDepartment = null">
                <template #append>
                    <VBtn
                        :text="$t('Add category')"
                        prepend-icon="mdi-plus"
                        @click="$refs.categoryModal.open()" />
                </template>
            </VSelect>
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
        <ModalDialog
            ref="categoryModal"
            size="medium"
            :title="$t('Add category')"
            :footer="true"
            @on-close="resetData">
            <template #actions>
                <VBtn
                    :disabled="disabled"
                    prepend-icon="mdi-content-save"
                    :text="$t('Save')"
                    @click="addCategory" />
                <VBtn
                    class="btn btn-outline-secondary"
                    prepend-icon="mdi-close"
                    :text="$t('Cancel')"
                    @click="$refs.categoryModal.close()" />
            </template>
            <VTextField
                v-model="name"
                :label="$t('Name')" />
            <VTextField
                v-model="description"
                class="mt-4"
                :label="$t('Description')" />
            <VSelect
                v-model="selectedCategory"
                class="mt-4"
                :label="$t('Parent')"
                :return-object="true"
                item-title="name"
                item-value="id"
                :items="categoriesToList" />
        </ModalDialog>
        <ConfirmDialog ref="categoryConfDialog" />
    </div>
</template>

<script>
import ConfirmDialog from '../../elements/ConfirmDialog.vue'
import CategoryTree from '../../chunks/CategoryTree.vue'
import ModalDialog from '../../chunks/ModalDialog.vue'
import { createErrorNotification, createSuccessNotification } from '@/js/helpers/notificationHelper.js'


export default {
    name: 'AdmCategories',
    components: {
        ModalDialog,
        ConfirmDialog,
        CategoryTree
    },
    data() {
        return {
            loading: false,
            buttonDisabled: false,
            categoriesToList: [],
            selectedCategory: null,
            selectedDepartment: null,
            id: null,
            name: '',
            description: '',
            parent: 0,
            order: 0
        }
    },
    computed: {
        departments() {
            return this.$store.getters['getDepartments']
        },
        disabled() {
            return this.name.length < 3 || this.description.length < 3 || this.buttonDisabled
        },
        categories() {
            return this.$store.getters['getCategories']
        },
        activeDepartment() {
            return this.$store.getters['getActiveDepartment']
        }
    },
    watch: {
        async selectedDepartment() {
            if (this.selectedDepartment !== null) {
                await this.$store.dispatch('getCategories', this.selectedDepartment?.id)
                this.allCategories()
            }
        }
    },
    async mounted() {
        this.loading = true
        this.loading = false
        this.selectedDepartment = this.activeDepartment
    },
    methods: {
        async addCategory() {
            this.buttonDisabled = true
            const data = {
                id: this.id,
                department_id: this.selectedDepartment?.id ?? null,
                name: this.name,
                parent: this.selectedCategory === undefined || this.selectedCategory === null ? 0 : this.selectedCategory.id,
                description: this.description,
                order: this.order
            }
            if (this.id > 0) {
                await this.$store.dispatch('saveCategory', data).catch(e => {
                    this.$store.commit('addNotification', createErrorNotification(this.$t('Error saving category')))
                    this.buttonDisabled = false
                })
            } else {
                await this.$store.dispatch('createCategory', data).catch(e => {
                    this.$store.commit('addNotification', createErrorNotification(this.$t('Error creating category')))
                    this.buttonDisabled = false
                })
            }
            await this.$store.dispatch('getCategories', this.selectedDepartment.id)
            this.buttonDisabled = false
            this.$refs.categoryModal.close()
            this.allCategories()
        },
        openCategoryModal(category) {
            this.id = category.id
            this.selectedDepartment = this.departments.find(d => {
                return d.id === category.department_id
            })
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
                this.$store.commit('addNotification', createErrorNotification(this.$t('You can not delete a category with children')))
            } else {
                const ok = await this.$refs.categoryConfDialog.show({
                    title: this.$t('Delete category'),
                    message: this.$t('Are you sure you want to delete this category?'),
                    okButton: this.$t('Delete')
                })
                if (ok) {
                    await this.$store.dispatch('deleteCategory', category.id).then(() => {
                        this.$store.commit('addNotification', createSuccessNotification(this.$t('Category deleted successfully')))
                        this.$store.dispatch('getCategories', this.selectedDepartment.id)
                    })
                }
            }
        },
        resetData() {
            this.id = null
            this.name = ''
            this.description = ''
            this.parent = 0
            this.order = 0
            this.selectedCategory = null
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
    display: flex;
    align-items: center;
    justify-content: space-between;

    & > .multiselect {
        width: 300px;
        margin: 0;
    }
}

.categories-tree {
    margin-top: var(--padding-box);
}
</style>
