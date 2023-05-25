<template>
    <div
        v-for="(category,index) in categories"
        :key="category.id"
        :class="[{'has-children': category.children}, `level-${level}`]"
        class="category">
        <div class="category-inner">
            <div class="category-label">
                <div class="name">
                    {{ category.name }}
                </div>
                <div class="description">
                    {{ category.description }}
                </div>
                <div class="actions">
                    <button
                        class="btn btn-purple"
                        @click="$router.push(`/admin/management/categories/${category.id}`)">
                        <ListBoxIcon :size="18" />
                    </button>
                    <button
                        class="btn btn-purple"
                        @click="openCategoryModal(category)">
                        <PencilIcon :size="18" />
                    </button>
                    <button
                        class="btn btn-purple"
                        @click="deleteCategory(category)">
                        <TrashCanIcon :size="18" />
                    </button>
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
    </div>
</template>

<script>
import ListBoxIcon from 'vue-material-design-icons/ListBox.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'

export default {
    name: 'CategoryTree',
    components: {
        PencilIcon,
        TrashCanIcon,
        ListBoxIcon
    },
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
.category {
    position: relative;
    margin-bottom: var(--padding-box);
    border-radius: var(--border-radius);
    border: 1px solid var(--bs-border-color);

    .category-inner {
        .category-label {
            background-color: var(--bs-white);
            position: relative;
            z-index: 10;
            padding: calc(var(--padding-box) * 0.5);
            border-radius: var(--border-radius);
            transition: var(--transition-duration);

            .name {
                font-weight: bold;
                margin-bottom: 2px;
            }

            .description {
                font-size: 12px;
            }

            .actions {
                position: absolute;
                right: 10px;
                top: 0;
                bottom: 0;
                display: flex;
                align-items: center;
                justify-content: center;

                .btn {
                    margin-left: 6px;

                    .material-design-icon {
                        margin-right: 0;
                    }
                }
            }

            &:hover {
                background-color: var(--bs-purple);
                border-color: var(--bs-purple-hover);
                color: var(--bs-white)
            }
        }

        &:not(:last-child) {
            margin-bottom: 16px;
        }


    }

    /* &:not(.level-0) .category-inner:after {
         content: "";
         position: absolute;
         top: -75px;
         height: 110px;
         left: -16px;
         width: 16px;
         border-bottom: 1px solid var(--bs-border-color);
         border-left: 1px solid var(--bs-border-color);
     }*/

    &.has-children {
        .children {
            padding: 0 10px 0 0;
        }

        & > .category-inner {
            .category-label {
                border-radius: var(--border-radius) var(--border-radius) 0 0;
                border-bottom: 1px solid var(--bs-border-color);

            }
        }
    }

    /*&.has-children:not(:last-child) {
        &:after {
            content: "";
            position: absolute;
            bottom: -16px;
            top: 0;
            left: 16px;
            right: 20px;
            border-left: 1px solid var(--bs-border-color);
        }
    }*/

    .children {
        margin-left: calc(var(--padding-box) * 2);

    }
}

</style>
