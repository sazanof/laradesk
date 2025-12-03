<template>
    <VSheet
        v-for="(category,index) in categories"
        :key="category.id"
        :class="[{'has-children': category.children}, `level-${level}`]"
        class="category">
        <div class="category-inner">
            <div class="position-relative">
                <div class="text-subtitle-1 font-weight-bold">
                    {{ category.name }}
                </div>
                <div class="text-subtitle-1 opacity-70">
                    {{ category.description }}
                </div>
                <div class="position-absolute top-0 right-0">
                    <VBtn
                        color="default"
                        icon="mdi-list-box"
                        density="comfortable"
                        variant="text"
                        @click="$router.push(`/admin/management/categories/${category.id}`)" />
                    <VBtn
                        color="default"
                        icon="mdi-pencil"
                        density="comfortable"
                        variant="text"
                        @click="openCategoryModal(category)" />
                    <VBtn
                        color="error"
                        icon="mdi-close"
                        density="comfortable"
                        variant="text"
                        class="btn btn-danger"
                        @click="deleteCategory(category)" />
                </div>
            </div>
        </div>
        <div
            v-if="category.children"
            class="children"
            :class="{last: index === categories.length - 1}">
            <CategoryTree
                :level="level+1"
                :categories="category.children"
                @on-category-edit-click="openCategoryModal($event)"
                @on-category-delete-click="deleteCategory($event)" />
        </div>
    </VSheet>
</template>

<script>
import { useToast } from 'vue-toastification'
import ListBoxIcon from 'vue-material-design-icons/ListBox.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'

const toast = useToast()
export default {
    name: 'CategoryTree',
    components: {},
    props: {
        categories: {
            type: Object,
            required: true
        },
        level: {
            type: Number,
            default: 0
        }
    },
    emits: [ 'on-category-edit-click', 'on-category-delete-click' ],
    methods: {
        openCategoryModal(c) {
            this.$emit('on-category-edit-click', c)
        },
        deleteCategory(c) {
            this.$emit('on-category-delete-click', c)
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
