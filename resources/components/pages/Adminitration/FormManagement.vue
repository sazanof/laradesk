<template>
    <div
        v-if="category"
        class="form-management">
        <h3>
            {{
                $t('Manage category {category}', {
                    category: category.name
                })
            }}
        </h3>
        <TipElement class="mt-3">
            {{
                $t('You can select the required fields for the application form and change the order of their location. Please note that the default fields cannot be deleted.')
            }}
        </TipElement>
        <div class="fields-draggable">
            <div class="fields-available">
                <div class="title">
                    {{ $t('Available fields') }}
                </div>
                <div class="fields-list">
                    <FieldItem
                        v-for="field in availableFields"
                        :key="field.id"
                        :field="field"
                        @on-field-click="addField" />
                </div>
            </div>
            <div class="fields-enabled">
                <div class="title">
                    {{ $t('Form fields') }}
                </div>
                <div class="fields-list">
                    <FieldItem
                        v-for="field in enabledFields"
                        :key="field.id"
                        :field="field">
                        <template #actions>
                            <button
                                :disabled="field.is_default"
                                class="btn btn-danger"
                                @click="deleteField(field)">
                                <TrashCanIcon :size="18" />
                            </button>
                        </template>
                    </FieldItem>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import FieldItem from '../../chunks/FieldItem.vue'
import TipElement from '../../ elements/TipElement.vue'

export default {
    name: 'FormManagement',
    components: {
        FieldItem,
        TipElement,
        TrashCanIcon
    },
    data() {
        return {
            category: null,
            availableFields: [],
            enabledFields: []
        }
    },
    computed: {
        id() {
            return parseInt(this.$route.params.id)
        },
        fields() {
            return this.$store.getters['getFields']
        },
        categoryFieldIds() {
            return Object.values(this.category.fields).map(_f => {
                return _f.id
            })
        }
    },
    async created() {
        this.getFields()
        await this.getCategory()

        this.availableFields = this.fields.filter(_field => {
            return this.categoryFieldIds.indexOf(_field.id) === -1
        })

        this.enabledFields = this.fields.filter(_field => {
            return this.categoryFieldIds.indexOf(_field.id) !== -1
        })
    },
    methods: {
        getFields() {
            if (this.fields.length === 0) {
                this.$store.dispatch('getFields')
            }
        },
        async getCategory() {
            this.category = await this.$store.dispatch('getCategoryWithFields', this.id)
            console.log()
        },
        async addField(field) {
            const data = {
                field_id: field.id,
                category_id: this.id
            }
            await this.$store.dispatch('linkField', data).then(() => {
                this.enabledFields.push(field)
                this.availableFields = this.sortByOrder(this.availableFields.filter(_f => _f.id !== field.id))
            })

        },
        async deleteField(field) {
            const data = {
                field_id: field.id,
                category_id: this.id
            }
            await this.$store.dispatch('unlinkField', data).then(() => {
                this.availableFields.push(field)
                this.enabledFields = this.sortByOrder(this.enabledFields.filter(_f => _f.id !== field.id))
            })

        },
        sortByOrder(arrayOfFields, direction = 'asc') {
            return arrayOfFields.sort((a, b) => {
                return direction === 'asc' ? a.order < b.order : a.order < b.order
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.fields-draggable {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;

    .title {
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
    }

    .fields-available {
        width: 50%;
        padding: var(--padding-box) var(--padding-box) var(--padding-box) 0;
        border-right: 1px solid var(--bs-border-color);
    }

    .fields-enabled {
        width: 50%;
        padding: var(--padding-box) 0 var(--padding-box) var(--padding-box);
    }
}
</style>
