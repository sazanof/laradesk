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
        <div
            class="fields-draggable">
            <div class="fields-available">
                <div class="title">
                    {{ $t('Available fields') }}
                </div>
                <div class="fields-list">
                    <FieldItem
                        v-for="field in availableFields"
                        :key="field.id"
                        :disabled="clickedFieldId === field.id"
                        :field="field"
                        @on-field-click="addField" />
                </div>
            </div>
            <div class="fields-enabled">
                <div class="title">
                    {{ $t('Form fields') }}
                </div>
                <div class="fields-list">
                    <Draggable
                        v-model="enabledFields"
                        :disabled="drag"
                        group="people"
                        item-key="id"
                        @start="onDragStart"
                        @sort="onSort"
                        @end="onDragEnd">
                        <template #item="{element}">
                            <FieldItem
                                ref="sortingList"
                                :data-category-field-id="element.category_field_id"
                                :disabled="clickedFieldId === element.id"
                                :field="element">
                                <template #actions>
                                    <button
                                        :class="{'text-warning':element.required}"
                                        class="btn btn-light"
                                        @click="makeRequired(element)">
                                        <StarIcon :size="18" />
                                    </button>
                                    <button
                                        :disabled="element.is_default || element.id === clickedFieldId"
                                        class="btn btn-danger"
                                        @click="deleteField(element)">
                                        <TrashCanIcon :size="18" />
                                    </button>
                                </template>
                            </FieldItem>
                        </template>
                    </Draggable>
                </div>
            </div>
        </div>
    </div>
    <div
        v-else
        class="alert alert-info">
        {{ $t('Loading, please wait...') }}
    </div>
</template>

<script>
import Draggable from 'vuedraggable'
import StarIcon from 'vue-material-design-icons/Star.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import FieldItem from '../../chunks/FieldItem.vue'
import TipElement from '../../ elements/TipElement.vue'

export default {
    name: 'FormManagement',
    components: {
        FieldItem,
        TipElement,
        TrashCanIcon,
        StarIcon,
        Draggable
    },
    data() {
        return {
            drag: false,
            category: null,
            availableFields: [],
            enabledFields: [],
            clickedFieldId: null
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
        await this.getFields()
        await this.getCategory()

        this.availableFields = this.fields.filter(_field => {
            return this.categoryFieldIds.indexOf(_field.id) === -1
        })

        const filteredEn = this.fields.filter(_field => {
            return this.categoryFieldIds.indexOf(_field.id) !== -1
        })
        this.enabledFields = this.category.fields.map(_field => {
            const fc = filteredEn.find(fc => _field.field_id === fc.id)
            return Object.assign(_field, {
                type: fc.type
            })
        })
    },
    methods: {
        async getFields() {
            if (this.fields.length === 0) {
                await this.$store.dispatch('getFields')
            }
        },
        async getCategory() {
            this.category = await this.$store.dispatch('getCategoryWithFields', this.id)
            //TODO order in draggable list
            // move enabledFields to category.fields
        },
        async addField(field) {
            this.clickedFieldId = field.id
            const data = {
                field_id: field.id,
                category_id: this.id,
                order: this.availableFields.length + 1
            }
            await this.$store.dispatch('linkField', data).then(() => {
                this.enabledFields.push(Object.assign(field, {
                    order: data.order
                }))
                this.availableFields = this.sortByOrder(this.availableFields.filter(_f => _f.id !== field.id))
                this.clickedFieldId = null
            }).catch(() => {
                this.clickedFieldId = null
            })

        },
        async deleteField(field) {
            this.clickedFieldId = field.id
            delete field.order
            delete field.category_field_id
            const data = {
                field_id: field.id,
                category_id: this.id
            }
            await this.$store.dispatch('unlinkField', data).then(() => {
                this.availableFields.push(field)
                this.enabledFields = this.sortByOrder(this.enabledFields.filter(_f => _f.id !== field.id))
                this.clickedFieldId = null
            }).catch(() => {
                this.clickedFieldId = null
            })

        },
        sortByOrder(arrayOfFields, direction = 'asc') {
            return arrayOfFields.sort((a, b) => {
                return direction === 'asc' ? a.order < b.order : a.order < b.order
            })
        },
        onDragStart(e) {
        },
        onDragEnd(e) {

        },
        async onSort(e) {
            this.drag = true
            /* const newIndex = e.newIndex
             const id = parseInt(e.item.dataset.categoryFieldId)*/

            this.enabledFields.map(async (f, i) => {
                await this.$store.dispatch('changeFieldOrder', {
                    id: f.category_field_id,
                    order: i
                }).catch(e => {
                    alert(e.response.data.message)
                    this.drag = false
                })
                f.order = i
            })
            this.drag = false

        },
        async makeRequired(el) {
            const required = !el.required
            await this.$store.dispatch('makeFieldRequired', {
                id: el.category_field_id,
                required: !el.required
            })
            el.required = required
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

    .btn {
        margin-left: 4px;
    }
}
</style>
